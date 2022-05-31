<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NumberController extends Controller
{
    /**
     * Marca os números como FREE
     * 
     * @param Request $request Corpo da requisição.
     * 
     * @return JsonResponse
     */
    public function free(Request $request)
    {
        return response()->json(['message' => 'Ok'], 200);
    }

    /**
     * Marca os números como RESERVED
     * 
     * @param Request $request Corpo da requisição.
     * 
     * @return JsonResponse
     */
    public function reserved(Request $request)
    {
        return response()->json(['message' => 'Ok'], 200);
    }

    /**
     * Marca os números como PAID
     * 
     * @param Request $request Corpo da requisição.
     * 
     * @return JsonResponse
     */
    public function paid(Request $request)
    {
        return response()->json(['message' => 'Ok'], 200);
    }
}
