<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Valida o usuário e a senha e retorna um token de acesso.
     * 
     * @param Request $request Corpo da requisição.
     * 
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user'      => 'required',
            'password'  => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Informe usuário e senha.'
            ], 400);
        }

        $exists = User::where('whatsapp', '=', $request->user)
            ->orWhere('email', '=', $request->user)
            ->first();

        if (empty($exists)) {
            return response()->json([
                'message' => 'O usuário informado não existe.'
            ], 400);
        }

        if (!Hash::check($request->password, $exists->password)) {
            return response()->json([
                'message' => 'Usuário ou senha inválidos.'
            ], 400);
        }

        $user = User::with('role')->where('id', '=', $exists->id)->first();
        $token = $user->createToken('auth_token', [])->plainTextToken;

        return response()->json([
            'user'  => $user,
            'token' => $token
        ]);
    }

    /**
     * Realiza o cadastro de um novo usuário e retorna um token de acesso
     * 
     * @param Request $request Corpo da requisição.
     * 
     * @return JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'required',
            'whatsapp'  => 'required|alpha_num|numeric|unique:users,whatsapp',
            'email'     => 'email',
            'password'  => 'confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Informe todas as informações necessárias',
                'errors'  => $validator->errors()
            ], 400);
        }

        $created = User::create([
            'name'      => $request->name,
            'whatsapp'  => $request->whatsapp,
            'email'     => $request->email,
            'password'  => $request->password,
            'role_id'   => 2
        ]);

        $token = $created->createToken('auth_token', [])->plainTextToken;

        return response()->json([
            'user'  => $created,
            'token' => $token
        ]);
    }

    /**
     * Envia o código de recuperação para o WhatsApp do usuário
     * 
     * @param Request $request Corpo da requisição.
     * 
     * @return JsonResponse
     */
    public function forgot(Request $request)
    {
        return response()->json(['message' => 'Ok'], 200);
    }

    /**
     * Verifica o código enviado para o usuário na recuperação de senha
     * 
     * @param string $code Código de verificação.
     * 
     * @return JsonResponse
     */
    public function verify(string $code)
    {
        return response()->json(['message' => 'Ok'], 200);
    }

    /**
     * Resetar a senha do usuário informando o código de verificação
     * 
     * @param Request $request Corpo da requisição.
     * 
     * @return JsonResponse
     */
    public function reset(Request $request)
    {
        return response()->json(['message' => 'Ok'], 200);
    }

    /**
     * Retorna as informações do usuário atual
     * 
     * @param Request $request Corpo da requisição.
     * 
     * @return JsonResponse
     */
    public function getProfile(Request $request)
    {
        return response()->json(['message' => 'Ok'], 200);
    }

    /**
     * Editar as informações do usuário atual
     * 
     * @param Request $request Corpo da requisição.
     * 
     * @return JsonResponse
     */
    public function editProfile(Request $request)
    {
        return response()->json(['message' => 'Ok'], 200);
    }
}
