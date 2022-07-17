<?php

namespace App\Http\Controllers;

use App\Enums\BankTypes;
use App\Models\BankAccount;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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
        $search = $request->query('search');

        $accounts = BankAccount::where('user_id', '=', $request->user->id)
            ->where('name', 'like', '%' . $search . '%')
            ->paginate(10, ['*']);

        return response()->json($accounts, 200);
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
        $validator = Validator::make($request->all(), [
            'type'   => ['required', Rule::in([BankTypes::PIX, BankTypes::BANK])],
            "name"   => 'required|string',
            "cc"     => 'required_unless:type,PIX|unique:bank_accounts,cc',
            "agency" => 'required_unless:type,PIX',
            "dv"     => 'required_unless:type,PIX',
            "key"    => 'required_unless:type,BANK|unique:bank_accounts,key',
            "main"   => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Informe todas as informações necessárias',
                'errors'  => $validator->errors()
            ], 400);
        }

        if ($request->input('main') == 1) {
            BankAccount::where('user_id', auth('sanctum')->id())
                ->where('main', 1)
                ->update(['main' => 0]);
        }

        $bank_account = BankAccount::create([
            'user_id' => $request->user->id,
            'type'   => $request->type,
            "name"   => $request->name,
            "cc"     => $request->type == BankTypes::BANK ? $request->cc ?? null : null,
            "agency" => $request->type == BankTypes::BANK ? $request->agency ?? null : null,
            "dv"     => $request->type == BankTypes::BANK ? $request->dv ?? null : null,
            "key"    => $request->type == BankTypes::PIX ? $request->key ?? null : null,
            "main"   => $request->input('main'),
        ]);

        return response()->json([
            'message' => 'Conta cadastrada com sucesso!',
            'bank_account' => $bank_account
        ], 201);
    }

    /**
     * Edita uma conta bancária
     * 
     * @param int $id
     * @param Request $request Corpo da requisição.
     * 
     * @return JsonResponse
     */
    public function edit(int $id, Request $request)
    {
        $bank_account = BankAccount::find($id);

        if (empty($bank_account)) {
            return response()->json([
                'message' => 'A conta informada não existe',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'type'   => ['required', Rule::in([BankTypes::PIX, BankTypes::BANK])],
            "name"   => 'required|string',
            "cc"     => 'sometimes|required_unless:type,PIX|unique:bank_accounts,cc',
            "agency" => 'required_unless:type,PIX',
            "dv"     => 'required_unless:type,PIX',
            "key"    => 'sometimes|required_unless:type,BANK|unique:bank_accounts,key',
            "main"   => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Informe todas as informações necessárias',
                'errors'  => $validator->errors()
            ], 400);
        }

        if ($request->input('main') == 1) {
            BankAccount::where('user_id', auth('sanctum')->id())
                ->where('main', 1)
                ->update(['main' => 0]);
        }

        $bank_account->type = $request->type;
        $bank_account->name = $request->name;
        $bank_account->cc   = $request->cc ?? null;
        $bank_account->agency = $request->agency ?? null;
        $bank_account->dv   = $request->dv ?? null;
        $bank_account->key  = $request->key ?? null;
        $bank_account->main = $bank_account->main == 1 ? 1 : $request->input('main');

        $bank_account->update();

        return response()->json([
            'message' => 'Conta atualizada com sucesso!',
            'bank_account' => $bank_account
        ], 200);
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
        $bank_account = BankAccount::find($id);

        if (empty($bank_account)) {
            return response()->json([
                'message' => 'A conta informada não existe',
            ], 404);
        }

        $bank_account->delete();

        return response()->json(['message' => 'Conta removida com sucesso!'], 200);
    }

    /**
     * Salva o token de acesso do Mercado Pago do cliente
     * 
     * @param Request $request
     * 
     * @return JsonResponse
     */
    public function saveMPAccessToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mp_access_token' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Informe todas as informações necessárias',
                'errors'  => $validator->errors()
            ], 400);
        }

        $user = User::find(auth('sanctum')->user()->id);
        $user->mp_access_token = $request->mp_access_token;
        $user->update();

        return response()->json(['message' => 'Token salvo com sucesso!'], 200);
    }
}
