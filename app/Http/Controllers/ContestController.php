<?php

namespace App\Http\Controllers;

use App\Models\Contest;
use Illuminate\Http\Request;

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

        $contests = Contest::with('gallery')->where('title', 'LIKE', "%{$title}%")->paginate($limit, [
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
     * Exibe os detalhes do sorteio.
     * 
     * @param any $id
     * 
     * @return JsonResponse
     */
    public function details($id)
    {
    }
}
