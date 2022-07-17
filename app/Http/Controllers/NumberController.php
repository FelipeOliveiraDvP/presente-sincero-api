<?php

namespace App\Http\Controllers;

use App\Enums\NumberStatus;
use App\Enums\OrderStatus;
use App\Events\PaymentConfirmed;
use App\Models\Contest;
use App\Models\Order;
use App\Models\Sale;
use App\Models\User;
use App\Traits\MercadoPagoHelper;
use App\Traits\NumbersHelper;
use App\Traits\WhatsApp;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class NumberController extends Controller
{
    use NumbersHelper, WhatsApp, MercadoPagoHelper;

    /**
     * Visualizar todos os números de um cliente específico.
     * 
     * @param int $contest_id     
     * @param int $customer_id          
     * 
     * @return JsonResponse
     */
    public function index(int $contest_id, int $customer_id)
    {
        $contest = Contest::find($contest_id);

        if (empty($contest) || $contest->user_id != auth('sanctum')->id()) {
            return response()->json([
                'message' => 'O sorteio informado não existe',
            ], 404);
        }

        $customer = User::find($customer_id);

        if (empty($customer)) {
            return response()->json(['customer' => $customer], 200);
        }

        $orders = Order::where('contest_id', '=', $contest_id)
            ->where('user_id', '=', $customer->id)
            ->get();

        $numbers = [];

        foreach ($orders as $order) {
            $numbers[] = json_decode($order->numbers);
        }

        return response()->json($this->getContestNumbersByNumbers($contest_id, array_merge_recursive(...$numbers)));
    }

    /**
     * Marca os números como RESERVED
     * 
     * @param int $contest_id
     * @param Request $request
     * 
     * @return JsonResponse
     */
    public function reserve(int $contest_id, Request $request)
    {
        $contest = Contest::find($contest_id);

        if (empty($contest)) {
            return response()->json([
                'message' => 'O sorteio informado não está mais disponível'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'numbers'   => 'array|min:1',
            'numbers.*' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Ocorreu um erro ao reservar os números',
                'errors'  => $validator->errors()
            ], 400);
        }

        $user = auth('sanctum')->user();

        // Deletar todos os sorteios em aberto do usuário.
        Order::where('user_id', '=', $user->id)
            ->where('contest_id', '=', $contest_id)
            ->where('status', '=', OrderStatus::PENDING)
            ->delete();

        // Calcular o valor total do pedido com base nas promoções do sorteio.
        $partial = 0;
        $length = count($request->numbers);
        $sales = Sale::where('contest_id', $contest_id)->get();
        $current_sale = Arr::first($sales, function ($value) use ($length) {
            return $length >= $value->quantity;
        });

        for ($i = 0; $i < $length; $i++) {
            if (!empty($current_sale)) {
                $partial += $current_sale->price;
            } else {
                $partial += $contest->price;
            }
        }

        // Cria um novo pedido.
        $order = Order::create([
            'contest_id' => $contest_id,
            'user_id'    => $user->id,
            'total'      => $partial,
            'numbers'    => json_encode($request->numbers)
        ]);

        $reserved_numbers = $this->setContestNumbersAsReserved($contest_id, $request->numbers, $order, $user);

        $contest->numbers = $reserved_numbers;
        $contest->update();

        // Cria um novo pagamento no Mercado Pago.
        $payment = $this->createPayment($order, $user, $contest);

        // Caso aconteça algum erro no Mercado Pago, o checkout deve ser manual.
        if ($payment == false) {
            return response()->json([
                'message'       => 'Números reservados com sucesso',
                'mercado_pago'  => false,
            ], 200);
        }

        if (getenv('APP_ENV') != 'local') {
            $this->sendReservationMessage($user, $contest, $order, $payment);
        }

        $order->transaction_code = $payment['payment_id'];
        $order->update();

        return response()->json([
            'message'       => 'Números reservados com sucesso',
            'mercado_pago'  => true,
            'payment'       => $payment
        ], 200);
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

        Order::where('user_id', '=', $user->id)
            ->where('contest_id', '=', $contest_id)
            ->where('status', '=', OrderStatus::PENDING)
            ->delete();

        $contest->numbers = $numbers;
        $contest->update();

        return response()->json(['message' => 'Números liberados com sucesso'], 200);
    }


    /**
     * Marca todos os números RESERVED como FREE
     * 
     * @param int $contest_id     
     * 
     * @return JsonResponse
     */
    public function adminFreeNumbers(int $contest_id)
    {
        $contest = Contest::find($contest_id);

        if (empty($contest) || $contest->user_id != auth('sanctum')->id()) {
            return response()->json([
                'message' => 'O sorteio informado não está mais disponível'
            ], 404);
        }

        $user = auth('sanctum')->user();
        $reserved_numbers = $this->getContestNumbersByStatus($contest_id, NumberStatus::RESERVED);

        $mapped_numbers = collect($reserved_numbers)->map(function ($item) {
            return $item->number;
        })->all();

        $numbers = $this->setContestNumbersAsFree($contest_id, $mapped_numbers, $user);

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
     * Marca os números como PAID
     *      
     * @param int $contest_id
     * @param Request $request
     * 
     * @return JsonResponse
     */
    public function adminPaidNumbers(int $contest_id, Request $request)
    {
        $contest = Contest::find($contest_id);

        if (empty($contest) || $contest->user_id != auth('sanctum')->id()) {
            return response()->json([
                'message' => 'O sorteio informado não está mais disponível'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Ocorreu um erro ao marcar os números como pago.',
                'errors'  => $validator->errors()
            ], 400);
        }

        $customer = User::find($request->customer_id);
        $order = Order::where('contest_id', '=', $contest_id)
            ->where('user_id', '=', $customer->id)
            ->where('status', '=', OrderStatus::PENDING)
            ->first();

        $numbers = $this->setContestNumbersAsPaid($contest->id, json_decode($order->numbers), $customer);

        if ($numbers == false) {
            return response()->json([
                'message' => 'Ocorreu um erro ao marcar os números como pagos.',
            ], 400);
        }

        $paid_percentage = count(json_decode($order->numbers)) / $contest->quantity;

        $contest->numbers = $numbers;
        $contest->paid_percentage += $paid_percentage;
        $contest->update();

        $order->status = OrderStatus::CONFIRMED;
        $order->confirmed_at = Carbon::now();
        $order->update();

        $this->sendConfirmationMessage($customer, $contest, $order);

        return response()->json(['message' => 'Pagamento dos números confirmado com sucesso'], 200);
    }

    /**
     * Cancela um pedido e libera os números reservados
     *      
     * @param int $order_id     
     * 
     * @return JsonResponse
     */
    public function adminCancelOrder(int $order_id)
    {
        $order = Order::find($order_id);

        if (empty($order)) {
            return response()->json([
                'message' => 'O pedido informado não está mais disponível'
            ], 404);
        }

        $user = auth('sanctum')->user();
        $order_numbers = json_decode($order->numbers);
        $contest = Contest::find($order->contest_id);

        $numbers = $this->setContestNumbersAsFree($contest->id, $order_numbers, $user);

        if ($numbers == false || empty($contest)) {
            return response()->json([
                'message' => 'Ocorreu um erro ao cancelar o pedido',
            ], 400);
        }

        $contest->numbers = $numbers;
        $contest->update();

        $order->status = OrderStatus::CANCELED;
        $order->update();

        return response()->json(['message' => 'O pedido foi cancelado com sucesso'], 200);
    }

    /**
     * Recebe o WhatsApp do cliente e retorna todos os números comprados por ele. 
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
        $orders = Order::where('contest_id', '=', $contest_id)
            ->where('user_id', '=', $user->id)
            ->where('status', '=', OrderStatus::CONFIRMED)
            ->get(['id', 'numbers']);

        return response()->json(empty($orders) ? [] : $orders);
    }

    /**
     * Trata o callback da API de pagamento para atualizar as informações do pedido.
     *      
     * @param Request $request
     * 
     * @return JsonResponse
     */
    public function webhook(Request $request)
    {
        $cb_order_id = $this->callback($request);

        if ($cb_order_id != false) {
            $order = Order::find($cb_order_id);
            $contest = Contest::find($order->contest_id);
            $user = User::find($order->user_id);

            if (empty($order) || empty($contest) || empty($user)) {
                return response()->json([
                    'message' => 'O pedido informado não está mais disponível'
                ], 404);
            }

            if ($order->status == OrderStatus::CONFIRMED) {
                return response()->json([
                    'message' => 'O pagamento já foi realizado',
                ], 400);
            }

            $numbers = $this->setContestNumbersAsPaid($contest->id, json_decode($order->numbers), $user);

            if ($numbers == false) {
                return response()->json([
                    'message' => 'Ocorreu um erro ao marcar os números como pagos.',
                ], 400);
            }

            $paid_percentage = count(json_decode($order->numbers)) / $contest->quantity;

            $contest->numbers = $numbers;
            $contest->paid_percentage += $paid_percentage;
            $contest->update();

            $order->status = OrderStatus::CONFIRMED;
            $order->confirmed_at = Carbon::now();
            $order->update();

            broadcast(new PaymentConfirmed($user->id, $order->id));
            event(new PaymentConfirmed($user->id, $order->id));

            $this->sendConfirmationMessage($user, $contest, $order);

            return response()->json(['message' => 'Pagamento dos números confirmado com sucesso'], 200);
        }

        return response()->json(['message' => 'Aguardando confirmação do pagamento'], 200);
    }
}
