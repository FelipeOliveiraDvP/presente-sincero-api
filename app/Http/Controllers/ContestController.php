<?php

namespace App\Http\Controllers;

use App\Enums\NumberStatus;
use App\Models\Contact;
use App\Models\Contest;
use App\Models\Gallery;
use App\Models\Number;
use App\Models\Reward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ContestController extends Controller
{
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
                'full_description',
                'start_date',
                'days_to_end',
                'contest_date'
            ]);

        return response()->json($contests);
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
            'title'                 => 'required|string',
            'short_description'     => 'required|string',
            'full_description'      => 'required|string',
            'start_date'            => 'required|date|after:now',
            'days_to_end'           => 'required|numeric',
            'contest_date'          => 'required|date|after:start_date',
            'number_start'          => 'required|numeric|gte:0',
            'number_price'          => 'required|numeric|gte:5',
            'number_quantity'       => 'required|numeric|gte:1',
            'number_per_customer'   => 'required|numeric|gte:1',
            'rewards.*.number'      => 'required|numeric|gte:1',
            'rewards.*.description' => 'required|string',
            'contacts.*.name'       => 'required|string',
            'contacts.*.value'      => 'required|string',
            'gallery.*.image_path'  => 'required|string',
            'gallery.*.thumbnail'   => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Ocorreu um erro ao cadastrar o sorteio',
                'errors'  => $validator->errors()
            ], 400);
        }

        $contest = [
            'title'             => $request->title,
            'slug'              => Str::slug($request->title),
            'short_description' => $request->short_description,
            'full_description'  => $request->full_description,
            'start_date'        => $request->start_date,
            'days_to_end'       => $request->days_to_end,
            'contest_date'      => $request->contest_date,
        ];

        $contest_created = Contest::create($contest);

        $numbers = [
            'contest_id'    => $contest_created->id,
            'price'         => (float) $request->number_price,
            'start'         => (int) $request->number_start,
            'quantity'      => (int) $request->number_quantity,
            'per_customer'  => (int) $request->number_per_customer,
            'numbers'       => $this->generateNumbers($request->number_start, $request->number_quantity)
        ];

        $numbers_created = Number::create($numbers);

        foreach ($request->rewards as $reward) {
            Reward::create([
                'contest_id'  => $contest_created->id,
                'number'      => $reward['number'],
                'description' => $reward['description'],
            ]);
        }

        foreach ($request->contacts as $contact) {
            Contact::create([
                'contest_id' => $contest_created->id,
                'name'       => $contact['name'],
                'value'      => $contact['value'],
            ]);
        }

        foreach ($request->gallery as $image) {
            Gallery::create([
                'contest_id' => $contest_created->id,
                'image_path' => $image['image_path'],
                'thumbnail'  => isset($image['thumbnail']) ? $image['thumbnail'] : false,
            ]);
        }

        return response()->json([
            'contest' => $contest_created,
            'numbers' => $numbers_created,
        ], 201);
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
        return response()->json(['message' => 'Ok'], 200);
    }

    /**
     * Exibe os detalhes do sorteio pelo slug.
     * 
     * @param string $slug
     * 
     * @return JsonResponse
     */
    public function getContestBySlug(string $slug)
    {
        return response()->json(['message' => 'Ok'], 200);
    }

    /**
     * Editar as informações do sorteio
     * 
     * @param Request $request Corpo da requisição.
     * 
     * @return JsonResponse
     */
    public function edit(Request $request)
    {
        return response()->json(['message' => 'Ok'], 200);
    }

    /**
     * Editar as informações do sorteio
     * 
     * @param int $id.
     * 
     * @return JsonResponse
     */
    public function finishContest(int $id)
    {
        return response()->json(['message' => 'Ok'], 200);
    }

    /**
     * Gera os números do sorteio e converte em JSON
     * 
     * @param int $start Número inicial do sorteio.
     * @param int $quantity Quantidade de números do sorteio.
     * 
     * @return string|boolean Retorna um JSON com os números.
     */
    private function generateNumbers(int $start, int $quantity)
    {
        $numbers = [];

        for ($j = $start; $j <= $quantity; $j++) {
            $number_length = strlen((string) $quantity);

            $number = json_encode([
                'number'        => str_pad($j, $number_length, '0', STR_PAD_LEFT),
                'status'        => NumberStatus::FREE,
                'customer_id'   => null,
            ]);

            $numbers[] = $number;
        }

        return json_encode($numbers);
    }
}
