<?php

namespace App\Http\Controllers;

use App\Enums\NumberStatus;
use App\Enums\OrderStatus;
use App\Jobs\JobFreeNumbers;
use App\Jobs\JobPaidNumbers;
use App\Jobs\JobReserveNumbers;
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

        $free_numbers = $this->countNumbersByStatus($contest_id, NumberStatus::FREE);
        $reserved_numbers = $this->countNumbersByStatus($contest_id, NumberStatus::RESERVED);
        $paid_numbers = $this->countNumbersByStatus($contest_id, NumberStatus::PAID);

        return response()->json([
            'total'     => $contest->quantity,
            'free'      => $free_numbers,
            'reserved'  => $reserved_numbers,
            'paid'      => $paid_numbers,
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

        $customer = auth('sanctum')->user();

        $old_order = Order::where('user_id', '=', $customer->id)
            ->where('contest_id', '=', $contest_id)
            ->where('status', '=', OrderStatus::PENDING)
            ->first();

        if (!empty($old_order)) {
            $this->dispatch(new JobFreeNumbers($contest, $old_order, $customer));
        }

        $request_random = $request->random ?? 0;
        $request_numbers = $request->numbers ?? [];

        $order = Order::create([
            'contest_id' => $contest->id,
            'user_id'    => $customer->id,
            'total'      => 0,
            'numbers'    => json_encode([])
        ]);

        $this->dispatch(new JobReserveNumbers($request_random, $request_numbers, $contest, $order, $customer));

        return response()->json([
            'message'    => 'Estamos processando o seu pedido.',
            'order_id'  => $order->id,
            'processing' => true,
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

        $this->dispatch(new JobFreeNumbers($contest, $order, $user));

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

        $this->dispatch(new JobPaidNumbers($contest, $order, $customer));

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

        $this->dispatch(new JobFreeNumbers($contest, $order, $customer, true));

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

            $this->dispatch(new JobPaidNumbers($contest, $order, $customer));

            return response()->json(['message' => 'Pagamento do pedido confirmado com sucesso'], 200);
        }

        return response()->json(['message' => 'Aguardando confirmação do pagamento'], 200);
    }
}
