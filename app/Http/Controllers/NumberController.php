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

    public function __construct()
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '2048M');
    }
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
            return response()->json([
                'message' => 'O cliente informado não existe',
            ], 400);
        }

        $orders = Order::where('contest_id', '=', $contest_id)
            ->where('user_id', '=', $customer->id)
            ->where('status', '=', OrderStatus::CONFIRMED)
            ->orWhere('status', '=', OrderStatus::PENDING)
            ->get();

        $numbers = [];
        foreach ($orders as $order) {
            $numbers[] = json_decode($order->numbers);
        }

        $customer_numbers = [];
        foreach ($this->getContestNumbersByNumbers($contest_id, array_merge_recursive(...$numbers)) as $customer_number) {
            $customer_numbers[] = $customer_number;
        }

        return response()->json($customer_numbers);
    }

    /**
     * Retorna a quantidade de cada número no sorteio.
     * 
     * @param int $contest_id           
     * 
     * @return JsonResponse
     */
    public function status(int $contest_id)
    {
        $contest = Contest::find($contest_id);

        if (empty($contest)) {
            return response()->json([
                'message' => 'O sorteio informado não existe',
            ], 404);
        }

        $free_numbers = $this->getContestNumbersByStatus($contest_id, NumberStatus::FREE);
        $reserved_numbers = $this->getContestNumbersByStatus($contest_id, NumberStatus::RESERVED);
        $paid_numbers = $this->getContestNumbersByStatus($contest_id, NumberStatus::PAID);

        return response()->json([
            'total'     => $contest->quantity,
            'free'      => count(iterator_to_array($free_numbers)),
            'reserved'  => count(iterator_to_array($reserved_numbers)),
            'paid'      => count(iterator_to_array($paid_numbers)),
        ], 200);
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

        if (empty($request->random) && empty($request->numbers)) {
            return response()->json([
                'message' => 'Informe um número random ou os números para reservar'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'random'    => 'required_if:numbers,null|integer|gte:1|nullable',
            'numbers'   => 'required_if:random,null|array|min:1|nullable',
            'numbers.*' => 'required|numeric|distinct'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Ocorreu um erro ao reservar os números',
                'errors'  => $validator->errors()
            ], 400);
        }

        $user = auth('sanctum')->user();

        // Excluir o sorteio aberto do usuário e liberar os números reservados
        $old_order = Order::where('user_id', '=', $user->id)
            ->where('contest_id', '=', $contest_id)
            ->where('status', '=', OrderStatus::PENDING)
            ->first();

        if (!empty($old_order)) {
            $free_numbers = $this->setContestNumbersAsFree($contest_id, $old_order, $user);

            $contest->numbers = $free_numbers['updated_numbers'];
            $contest->update();
            $old_order->delete();
        }

        // Cria um novo pedido.
        $order = Order::create([
            'contest_id' => $contest_id,
            'user_id'    => $user->id,
            'total'      => 0,
            'numbers'    => json_encode([])
        ]);

        $request_random = $request->random ?? 0;
        $request_numbers = $request->numbers ?? [];
        $numbers = $this->setContestNumbersAsReserved($contest_id, $request_random, $request_numbers, $order, $user);
        $order_total = $this->calcSaleDiscount(count($numbers['reserved_numbers']), $contest_id, $contest);

        $order->total = $order_total;
        $order->numbers = json_encode($numbers['reserved_numbers']);
        $order->update();

        $contest->numbers = $numbers['updated_numbers'];
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

        if (env('APP_ENV') != 'local') {
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
     * 
     * @return JsonResponse
     */
    public function free(int $contest_id)
    {
        $contest = Contest::find($contest_id);

        if (empty($contest)) {
            return response()->json([
                'message' => 'O sorteio informado não está mais disponível'
            ], 404);
        }

        $user = auth('sanctum')->user();
        $order = Order::where('user_id', '=', $user->id)
            ->where('contest_id', '=', $contest_id)
            ->where('status', '=', OrderStatus::PENDING)
            ->first();

        if (empty($order)) {
            return response()->json([
                'message' => 'Não existem números reservados',
            ], 200);
        }

        $numbers = $this->setContestNumbersAsFree($contest_id, $order, $user);

        $contest->numbers = $numbers['updated_numbers'];
        $contest->update();
        $order->delete();

        return response()->json(['message' => 'Números liberados com sucesso'], 200);
    }

    /**
     * Confirma o pagamento de um pedido
     *      
     * @param int $contest_id
     * @param int $order_id
     * 
     * @return JsonResponse
     */
    public function adminPaidOrder(int $contest_id, int $order_id)
    {
        $contest = Contest::find($contest_id);

        if (empty($contest) || $contest->user_id != auth('sanctum')->id()) {
            return response()->json([
                'message' => 'O sorteio informado não está mais disponível'
            ], 404);
        }

        $order = Order::find($order_id);

        if (empty($order) || $order->status != OrderStatus::PENDING) {
            return response()->json([
                'message' => 'O pedido informado não está mais disponível'
            ], 404);
        }

        $customer = User::find($order->user_id);
        $numbers = $this->setContestNumbersAsPaid($contest->id, $order, $customer);
        $paid_percentage = count($numbers['paid_numbers']) / $contest->quantity;

        $contest->numbers = $numbers['updated_numbers'];
        $contest->paid_percentage += round($paid_percentage, 2);
        $contest->update();

        $order->status = OrderStatus::CONFIRMED;
        $order->confirmed_at = Carbon::now();
        $order->update();

        $this->sendConfirmationMessage($customer, $contest, $order);

        return response()->json(['message' => 'O pedido foi confirmado com sucesso'], 200);
    }

    /**
     * Cancela um pedido e libera os números reservados
     *      
     * @param int $contest_id     
     * @param int $order_id     
     * 
     * @return JsonResponse
     */
    public function adminCancelOrder(int $contest_id, int $order_id)
    {
        $contest = Contest::find($contest_id);

        if (empty($contest) || $contest->user_id != auth('sanctum')->id()) {
            return response()->json([
                'message' => 'O sorteio informado não está mais disponível'
            ], 404);
        }

        $order = Order::find($order_id);

        if (empty($order) || $order->status != OrderStatus::PENDING) {
            return response()->json([
                'message' => 'O pedido informado não está mais disponível'
            ], 404);
        }

        $customer = User::find($order->user_id);
        $numbers = $this->setContestNumbersAsFree($contest_id, $order, $customer);

        $contest->numbers = $numbers['updated_numbers'];
        $contest->update();

        $order->status = OrderStatus::CANCELED;
        $order->update();

        return response()->json(['message' => 'O pedido foi cancelado com sucesso'], 200);
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
            $customer = User::find($order->user_id);

            if (empty($order) || empty($contest) || empty($customer)) {
                return response()->json([
                    'message' => 'O pedido informado não está mais disponível'
                ], 404);
            }

            if ($order->status != OrderStatus::PENDING) {
                return response()->json([
                    'message' => 'O pagamento já foi realizado',
                ], 400);
            }

            $numbers = $this->setContestNumbersAsPaid($contest->id, $order, $customer);
            $paid_percentage = count($numbers['paid_numbers']) / $contest->quantity;

            $contest->numbers = $numbers['updated_numbers'];
            $contest->paid_percentage += round($paid_percentage, 2);
            $contest->update();

            $order->status = OrderStatus::CONFIRMED;
            $order->confirmed_at = Carbon::now();
            $order->update();

            broadcast(new PaymentConfirmed($customer->id, $order->id));

            $this->sendConfirmationMessage($customer, $contest, $order);

            return response()->json(['message' => 'Pagamento do pedido confirmado com sucesso'], 200);
        }

        return response()->json(['message' => 'Aguardando confirmação do pagamento'], 200);
    }

    /**
     * Calcula o valor total com base na promoção correspondente do pedido.
     * 
     * @param int $quantity
     * @param int $contest_id
     * @param Contest $contest
     * 
     * @return float $partial
     */
    private function calcSaleDiscount(int $quantity, int $contest_id, Contest $contest)
    {
        $partial = 0;
        $length = $quantity;
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

        return $partial;
    }
}
