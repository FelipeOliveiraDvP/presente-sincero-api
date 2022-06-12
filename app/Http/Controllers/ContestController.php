<?php

namespace App\Http\Controllers;

use App\Enums\ContestStatus;
use App\Enums\NumberStatus;
use App\Models\Contest;
use App\Models\Gallery;
use App\Models\Sale;
use App\Models\User;
use App\Traits\NumbersHelper;
use App\Traits\WhatsApp;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
        $limit = $request->query('limit') ?? 10;

        $contests = Contest::with('gallery')
            ->where('title', 'LIKE', "%{$title}%")
            ->orderBy('created_at', 'desc')
            ->paginate($limit, [
                'id',
                'title',
                'slug',
                'short_description',
                'start_date',
                'price',
                'quantity'
            ]);

        return response()->json($contests);
    }

    /**
     * Retorna os detalhes de um sorteio através do slug
     * 
     * @param string $slug
     * 
     * @return JsonResponse
     */
    public function getContestBySlug(string $slug)
    {
        $contest = Contest::where('slug', '=', $slug)->first();

        return $this->details($contest->id);
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
        $title = $request->query('title');
        $limit = $request->query('limit') ?? 10;

        $contests = Contest::where('title', 'LIKE', "%{$title}%")
            ->where('user_id', '=', $request->user->id)
            ->orderBy('created_at', 'desc')
            ->paginate($limit, [
                'id',
                'title',
                'slug',
                'short_description',
                'start_date',
                'price',
                'quantity'
            ]);

        return response()->json($contests);
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
        $contest = Contest::with('gallery')
            ->with('bank_accounts')
            ->with('sales')
            ->find($id);

        if (empty($contest)) {
            return response()->json([
                'message' => 'O sorteio informado não existe.'
            ], 404);
        }

        $contest->makeHiddenIf(fn () => $contest->quantity >= 5000, ['numbers']);

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
            'start_date'        => 'required|date|after:now',
            'contest_date'      => 'nullable|date|after:now',
            'max_reserve_days'  => 'gte:1|lte:30',
            'price'             => 'required|gte:0.5',
            'quantity'          => 'required|gte:1',
            'short_description' => 'required|string',
            'full_description'  => 'required|string',
            'whatsapp_number'   => 'required|string',
            'whatsapp_group'    => 'url',
            'sales.*.quantity'  => 'required|gte:1',
            'sales.*.price'     => 'required|gte:0.5',
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
            'price'             => $request->price / 100,
            'quantity'          => $request->quantity,
            'short_description' => $request->short_description,
            'full_description'  => $request->full_description,
            'whatsapp_number'   => $request->whatsapp_number,
            'whatsapp_group'    => $request->whatsapp_group,
            'numbers'           => $numbers
        ];

        $contest_created = Contest::create($contest);

        $contest_created->bank_accounts()->sync($request->bank_accounts);

        foreach ($request->sales as $sale) {
            Sale::create([
                'contest_id'    => $contest_created->id,
                'quantity'      => $sale['quantity'],
                'price'         => $sale['price'] / 100,
            ]);
        }

        foreach ($request->gallery as $image) {
            if (!is_null($image)) {
                Gallery::create([
                    'contest_id' => $contest_created->id,
                    'path' => $image,
                ]);
            }
        }

        return response()->json([
            'contest' => $contest_created
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

        if (empty($contest)) {
            return response()->json([
                'message' => 'O sorteio informado não existe.'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title'             => 'required',
            'contest_date'      => 'date|after:now',
            'max_reserve_days'  => 'gte:1|lte:30',
            'price'             => 'required|gte:0.5',
            'short_description' => 'required|string',
            'full_description'  => 'required|string',
            'whatsapp_number'   => 'required|string',
            'whatsapp_group'    => 'url',
            'sales.*.quantity'  => 'required|gte:1',
            'sales.*.price'     => 'required|gte:0.5',
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
            'id'                => $id,
            'title'             => $request->title,
            'contest_date'      => $request->contest_date ?? null,
            'max_reserve_days'  => $request->max_reserve_days ?? 1,
            'short_description' => $request->short_description,
            'full_description'  => $request->full_description,
            'whatsapp_number'   => $request->whatsapp_number,
            'whatsapp_group'    => $request->whatsapp_group,
        ];

        $can_change_price = count($this->getContestNumbersByStatus($id, NumberStatus::PAID)) <= 0;

        if ($can_change_price) {
            $update['price'] = $request->price;
        }

        $contest->update($update);

        $contest->bank_accounts()->sync($request->bank_accounts);

        Sale::where('contest_id', '=', $contest->id)->delete();
        Gallery::where('contest_id', '=', $contest->id)->delete();

        foreach ($request->sales as $sale) {
            Sale::create([
                'contest_id' => $contest->id,
                'quantity'   => $sale['quantity'],
                'price'      => $sale['price'],
            ]);
        }

        foreach ($request->gallery as $image) {
            Gallery::create([
                'contest_id' => $contest->id,
                'path' => $image,
            ]);
        }

        return response()->json(['message' => 'Sorteio atualizado com sucesso'], 200);
    }

    /**
     * Editar as informações do sorteio
     * 
     * @param int $id.
     * @param Request $request.
     * 
     * @return JsonResponse
     */
    public function finishContest(int $id, Request $request)
    {
        $contest = Contest::find($id);

        if (empty($contest)) {
            return response()->json([
                'message' => 'O sorteio informado não existe.'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'number' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Ocorreu um erro ao finalizar o sorteio',
                'errors'  => $validator->errors()
            ], 400);
        }

        $winner_number = $this->getContestNumberByNumber($id, $request->number);

        if ($winner_number->status != NumberStatus::PAID) {
            return response()->json([
                'message' => 'Nenhum cliente comprou o número infomado.'
            ], 400);
        }

        $winner = User::find($winner_number->customer->id);

        $contest->status = ContestStatus::FINISHED;
        $contest->winner_id = $winner->id;

        $contest->update();

        $this->sendWinContestMessage($winner, $contest);

        return response()->json($winner_number, 200);
    }
}
