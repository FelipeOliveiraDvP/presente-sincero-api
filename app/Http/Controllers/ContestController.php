<?php

namespace App\Http\Controllers;

use App\Enums\ContestStatus;
use App\Enums\NumberStatus;
use App\Enums\OrderStatus;
use App\Models\Contest;
use App\Models\Gallery;
use App\Models\Order;
use App\Models\Sale;
use App\Models\User;
use App\Traits\NumbersHelper;
use App\Traits\WhatsApp;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ContestController extends Controller
{
    use NumbersHelper, WhatsApp;

    /**
     * Retorna todos os sorteios cadastrados.
     * 
     * @param Request $request
     * 
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $title = $request->query('title');
        $limit = $request->query('limit') ?? 12;

        $contests = Contest::with('seller:id,name,username')
            ->with('gallery')
            ->where('title', 'LIKE', "%{$title}%")
            ->orderBy('created_at', 'desc')
            ->paginate($limit, [
                'id',
                'title',
                'user_id',
                'slug',
                'short_description',
                'start_date',
                'price',
                'quantity',
                'paid_percentage'
            ]);

        return response()->json($contests);
    }

    /**
     * Retorna todos os sorteios do vendedor.
     * 
     * @param Request $request
     * 
     * @return JsonResponse
     */
    public function getContestsByUsername(string $username, Request $request)
    {
        $title = $request->query('title');
        $limit = $request->query('limit') ?? 12;
        $user = User::where('username', '=', $username)->first();

        if (empty($user) || $user->blocked) {
            return response()->json([
                'message' => 'O vendedor informado não existe!'
            ], 404);
        }

        $contests = Contest::with('seller:id,name,username')
            ->with('gallery')
            ->where('title', 'LIKE', "%{$title}%")
            ->where('user_id', '=', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate($limit, [
                'id',
                'title',
                'user_id',
                'slug',
                'short_description',
                'start_date',
                'price',
                'quantity',
                'paid_percentage'
            ]);

        return response()->json($contests);
    }

    /**
     * Retorna os detalhes de um sorteio do vendedor a partir de um slug
     * 
     * @param string $username
     * @param string $slug
     * 
     * @return JsonResponse
     */
    public function getContestBySlug(string $username, string $slug)
    {
        $user = User::where('username', '=', $username)->first();

        if (empty($user) || $user->blocked) {
            return response()->json([
                'message' => 'O vendedor informado não existe!'
            ], 404);
        }

        // TODO: Utilizar o mesmo método de detalhes
        // $contest = Contest::where('slug', '=', $slug)->first();

        // if (empty($contest)) {
        //     return response()->json([
        //         'message' => 'O sorteio informado não existe!'
        //     ], 404);
        // }

        // return $this->details($contest->id);

        $contest = Contest::with('seller:id,name,username')
            ->with('gallery')
            ->with('bank_accounts:id,name,main')
            ->with('sales')
            ->where('slug', '=', $slug)
            ->first();

        if (empty($contest)) {
            return response()->json([
                'message' => 'O sorteio informado não existe.'
            ], 404);
        }

        return response()->json($contest->makeHiddenIf(function ($value) {
            return $value->quantity >= 10000;
        }, ['numbers']));
    }

    /**
     * Lista os sorteios por usuário
     * 
     * @param Request $request
     * 
     * @return JsonResponse
     */
    public function getContestsByUser(Request $request)
    {
        $user = $request->user('sanctum');

        return $this->getContestsByUsername($user->username, $request);
    }

    /**
     * Lista os pedidos do sorteio
     * 
     * @param int $id
     * @param Request $request
     * 
     * @return JsonResponse
     */
    public function getContestOrders(int $id, Request $request)
    {
        $search = $request->query('search');

        $contest = Contest::find($id);

        if (empty($contest) || $contest->user_id != auth('sanctum')->id()) {
            return response()->json([
                'message' => 'O sorteio informado não existe.'
            ], 404);
        }

        $orders = Order::with('user:id,name,whatsapp')
            ->with('contest:id,title,price')
            ->where('contest_id', '=', $id)
            ->where('status', '=', OrderStatus::PENDING)
            ->whereIn('user_id', function ($query) use ($search) {
                $query->select('id')
                    ->from('users')
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('whatsapp', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20, [
                'id',
                'contest_id',
                'user_id',
                'total',
                'numbers',
                'status',
                'confirmed_at',
                'created_at',
                'updated_at'
            ]);

        return response()->json($orders);
    }

    /**
     * Exibe os detalhes do sorteio pelo id.
     * 
     * @param int $id
     * 
     * @return JsonResponse
     */
    public function details(int $id)
    {
        $contest = Contest::with('seller:id,name,username')
            ->with('gallery')
            ->with('bank_accounts:id,name,main')
            ->with('sales')
            ->find($id)
            ->makeHiddenIf(function ($value) {
                return $value->quantity >= 10000;
            }, ['numbers']);

        if (empty($contest) || $contest->user_id != auth('sanctum')->id()) {
            return response()->json([
                'message' => 'O sorteio informado não existe.'
            ], 404);
        }

        return response()->json($contest);
    }

    /**
     * Cria um novo sorteio
     * 
     * @param Request $request
     * 
     * @return JsonResponse
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'             => 'required|unique:contests,title',
            'start_date'        => 'required|date',
            'contest_date'      => 'nullable|date|after:now',
            'max_reserve_days'  => 'gte:1|lte:30',
            'price'             => 'required|gte:0.1',
            'quantity'          => 'required|gte:1',
            'short_description' => 'required|string',
            'whatsapp_number'   => 'required|string',
            'whatsapp_group'    => 'url',
            'sales.*.quantity'  => 'required|gte:1',
            'sales.*.price'     => 'required|gte:0.1',
            'bank_accounts.*'   => 'exists:bank_accounts,id',
            'gallery.*'         => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Ocorreu um erro ao cadastrar o sorteio',
                'errors'  => $validator->errors()
            ], 400);
        }

        $numbers = $this->generateContestNumbers($request->quantity);

        $contest = [
            'user_id'           => $request->user->id,
            'title'             => $request->title,
            'slug'              => Str::slug($request->title),
            'start_date'        => $request->start_date,
            'contest_date'      => $request->contest_date ?? null,
            'max_reserve_days'  => $request->max_reserve_days ?? 1,
            'price'             => $request->price,
            'quantity'          => $request->quantity,
            'short_description' => $request->short_description,
            'whatsapp_number'   => $request->whatsapp_number,
            'whatsapp_group'    => $request->whatsapp_group,
            'numbers'           => $numbers
        ];

        Cache::lock("create-contest-{$contest['slug']}")->get(function () use ($contest, $request) {
            $contest_created = Contest::create($contest);

            $contest_created->bank_accounts()->sync($request->bank_accounts);

            if ($request->has('sales')) {
                foreach ($request->sales as $sale) {
                    Sale::create([
                        'contest_id'    => $contest_created->id,
                        'quantity'      => $sale['quantity'],
                        'price'         => $sale['price'],
                    ]);
                }
            }

            foreach ($request->gallery as $image) {
                if (!is_null($image)) {
                    Gallery::create([
                        'contest_id' => $contest_created->id,
                        'path' => $image,
                    ]);
                }
            }
        });

        return response()->json([
            'message' => 'Sorteio criado com sucesso!'
        ], 201);
    }

    /**
     * Editar as informações do sorteio
     * 
     * @param int $id
     * @param Request $request
     * 
     * @return JsonResponse
     */
    public function edit(int $id, Request $request)
    {
        $contest = Contest::find($id);

        if (empty($contest) || $contest->user_id != auth('sanctum')->id()) {
            return response()->json([
                'message' => 'O sorteio informado não existe.'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'contest_date'      => 'nullable|date|after:now',
            'max_reserve_days'  => 'gte:1|lte:30',
            'price'             => 'gte:0.1',
            'short_description' => 'string',
            'whatsapp_number'   => 'string',
            'whatsapp_group'    => 'url',
            'sales.*.quantity'  => 'gte:1',
            'sales.*.price'     => 'gte:0.1',
            'bank_accounts.*'   => 'exists:bank_accounts,id',
            'gallery.*'         => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Ocorreu um erro ao atualizar o sorteio',
                'errors'  => $validator->errors()
            ], 400);
        }

        $update = [
            'title'                 => $request->title ?? $contest->title,
            'contest_date'          => $request->contest_date ?? $contest->contest_date,
            'max_reserve_days'      => $request->max_reserve_days ?? $contest->max_reserve_days,
            'show_percentage'       => $request->show_percentage ?? $contest->show_percentage,
            'use_custom_percentage' => $request->use_custom_percentage ?? $contest->use_custom_percentage,
            'custom_percentage'     => $request->custom_percentage ?? $contest->custom_percentage,
            'short_description'     => $request->short_description ?? $contest->short_description,
            'whatsapp_number'       => $request->whatsapp_number ?? $contest->whatsapp_number,
            'whatsapp_group'        => $request->whatsapp_group ?? $contest->whatsapp_group,
        ];

        $can_change_price = count($this->getContestNumbersByStatus($id, NumberStatus::PAID)) <= 0;

        if ($can_change_price) {
            $update['price'] = $request->price ?? $contest->price;
        }

        $contest->update($update);

        if ($request->bank_accounts) {
            $contest->bank_accounts()->sync($request->bank_accounts);
        }

        if ($request->sales) {
            Sale::where('contest_id', '=', $contest->id)->delete();

            foreach ($request->sales as $sale) {
                Sale::create([
                    'contest_id' => $contest->id,
                    'quantity'   => $sale['quantity'],
                    'price'      => $sale['price'],
                ]);
            }
        }

        if ($request->gallery) {
            Gallery::where('contest_id', '=', $contest->id)->delete();

            foreach ($request->gallery as $image) {
                Gallery::create([
                    'contest_id' => $contest->id,
                    'path' => $image,
                ]);
            }
        }

        return response()->json(['message' => 'Sorteio atualizado com sucesso'], 200);
    }

    /**
     * Finaliza um sorteio e define o vencedor.
     * 
     * @param int $id.
     * @param Request $request.
     * 
     * @return JsonResponse
     */
    public function finishContest(int $id, string $number, Request $request)
    {
        $contest = Contest::find($id);

        if (empty($contest) || $contest->user_id != auth('sanctum')->id()) {
            return response()->json([
                'message' => 'O sorteio informado não existe.'
            ], 404);
        }

        $validator = Validator::make(['number' => $number], [
            'number' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Ocorreu um erro ao finalizar o sorteio',
                'errors'  => $validator->errors()
            ], 400);
        }

        $winner_number = $this->getContestNumberByNumber($id, $request->number);

        if (empty($winner_number) || $winner_number->status != NumberStatus::PAID) {
            return response()->json([
                'message' => 'Nenhum cliente comprou o número infomado.'
            ], 400);
        }

        $winner = User::find($winner_number->customer->id);

        $contest->status = ContestStatus::FINISHED;
        $contest->winner_id = $winner->id;

        $contest->update();

        $this->sendWinContestMessage($winner, $contest);

        return response()->json(['message' => 'O sorteio foi finalizado com sucesso!'], 200);
    }
}
