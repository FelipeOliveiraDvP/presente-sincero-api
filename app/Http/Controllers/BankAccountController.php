<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    /**
     * Lista todas as contas bancárias do usuário
     * 
     * @param Request $request Corpo da requisição.
     * 
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        return response()->json(['message' => 'Ok'], 200);
    }

    /**
     * Cadastra uma nova conta bancária
     * 
     * @param Request $request Corpo da requisição.
     * 
     * @return JsonResponse
     */
    public function create(Request $request)
    {
        return response()->json(['message' => 'Ok'], 200);
    }

    /**
     * Edita uma conta bancária
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
     * Remove uma conta bancária
     * 
     * @param int $id
     * 
     * @return JsonResponse
     */
    public function remove(int $id)
    {
        return response()->json(['message' => 'Ok'], 200);
    }
}
