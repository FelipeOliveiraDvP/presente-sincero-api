<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Contest;
use App\Models\Order;
use App\Models\User;
use App\Traits\MercadoPagoHelper;
use App\Traits\NumbersHelper;
use App\Traits\WhatsApp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NumberController extends Controller
{
    use NumbersHelper, WhatsApp, MercadoPagoHelper;

    /**
     * Visualizar os números do sorteio
     */
    public function index(int $contest_id)
    {
        return response()->json($this->getContestNumbers($contest_id));
    }

    /**
     * Marca os números como FREE
     * 
     * @param int $contest_id
     * @param Request $request
     * 
     * @return JsonResponse
     */
    public function free(int $contest_id, Request $request)
    {
        $contest = Contest::find($contest_id);

        if (empty($contest)) {
            return response()->json([
                'message' => 'O sorteio informado não está mais disponível'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'numbers.*' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Ocorreu um erro ao liberar os números',
                'errors'  => $validator->errors()
            ], 400);
        }

        $user = auth('sanctum')->user();

        $numbers = $this->setContestNumbersAsFree($contest_id, $request->numbers, $user);

        if ($numbers == false) {
            return response()->json([
                'message' => 'Ocorreu um erro ao liberar os números',
            ], 400);
        }

        $contest->numbers = $numbers;

        $contest->update();

        return response()->json(['message' => 'Números liberados com sucesso'], 200);
    }

    /**
     * Marca os números como RESERVED
     * 
     * @param int $contest_id
     * @param Request $request
     * 
     * @return JsonResponse
     */
    public function reserved(int $contest_id, Request $request)
    {
        $contest = Contest::find($contest_id);

        if (empty($contest)) {
            return response()->json([
                'message' => 'O sorteio informado não está mais disponível'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'total'     => 'required|gte:0.5',
            'numbers.*' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Ocorreu um erro ao reservar os números',
                'errors'  => $validator->errors()
            ], 400);
        }

        $user = auth('sanctum')->user();

        $numbers = $this->setContestNumbersAsReserved($contest_id, $request->numbers, $user);

        if ($numbers == false) {
            return response()->json([
                'message' => 'Ocorreu um erro ao reservar os números',
            ], 400);
        }

        $contest->numbers = $numbers;

        $contest->update();

        Order::where('user_id', '=', $user->id)
            ->where('contest_id', '=', $contest_id)
            ->update(['status' => OrderStatus::CANCELED]);

        $order = Order::create([
            'contest_id' => $contest_id,
            'user_id' => $user->id,
            'total' => $request->total,
            'numbers' => json_encode($request->numbers)
        ]);

        $payment = $this->createPayment($order, $user, $contest);

        $this->sendReservationMessage($user, $contest, $payment);

        $order->transaction_code = $payment['payment_id'];
        $order->update();

        return response()->json([
            'message' => 'Números reservados com sucesso',
            'payment' => $payment
        ], 200);
    }

    /**
     * Marca os números como PAID
     *      
     * @param Request $request
     * 
     * @return JsonResponse
     */
    public function webhook(Request $request)
    {
        $callback = $this->callback($request);

        if ($callback != false) {
            $order = Order::find($callback);
            $contest = Contest::find($order->contest_id);
            $user = User::find($order->user_id);

            if (empty($order) || empty($contest) || empty($user)) {
                return response()->json([
                    'message' => 'O pedido informado não está mais disponível'
                ], 404);
            }

            $numbers = $this->setContestNumbersAsPaid($contest->id, $order->numbers, $user);

            if ($numbers == false) {
                return response()->json([
                    'message' => 'Ocorreu um erro ao marcar os números como pagos.',
                ], 400);
            }

            $contest->numbers = $numbers;

            $contest->update();

            $this->sendConfirmationMessage($user, $contest, $order);
            // TODO: Chamar o WebSocket para atualizar a página de pedido
            return response()->json(['message' => 'Pagamento dos números confirmado com sucesso'], 200);
        }

        return response()->json(['message' => 'Aguardando confirmação do pagamento'], 200);
    }

    /**
     * Retorna os números do cliente no sorteio através do WhatsApp.
     * 
     * @param int $contest_id
     * @param Request $request
     * 
     * @return JsonResponse
     */
    public function getCustomerNumbers(int $contest_id, Request $request)
    {
        $contest = Contest::find($contest_id);

        if (empty($contest)) {
            return response()->json([
                'message' => 'O sorteio informado não está mais disponível'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'whatsapp' => 'required|exists:users'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Ocorreu um erro ao verificar os números',
                'errors'  => $validator->errors()
            ], 400);
        }

        $user = User::where('whatsapp', '=', $request->whatsapp)->first();

        return response()->json($this->getContestNumbersByCustomer($contest_id, $user));
    }
}
