<?php

namespace App\Http\Controllers;

use App\Models\ResetPassword;
use App\Models\User;
use App\Traits\AbilitiesHelper;
use App\Traits\AuthHelper;
use App\Traits\WhatsApp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    use AbilitiesHelper, WhatsApp, AuthHelper;

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
            'user' => 'required',
            'password' => 'required'
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

        $user = User::find($exists->id);

        $token = $user->createToken('auth_token', $this->getUserAbilities($user->role))->plainTextToken;

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
            'password'  => $request->password ? Hash::make($request->password) : null,
            'role'      => $this->getCustomerRole(),
        ]);

        $token = $created->createToken('auth_token', [])->plainTextToken;

        return response()->json([
            'user'  => $created,
            'token' => $token
        ], 201);
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
        $validator = Validator::make($request->all(), [
            'user' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Informe todas as informações necessárias',
                'errors'  => $validator->errors()
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

        $reset = ResetPassword::create([
            'user_id' => $exists->id,
            'code' => random_int(100000, 999999),
        ]);

        $this->sendRecoveryMessage($exists, $reset->code);

        return response()->json([
            'message' => 'Código de recuperação enviado com sucesso!',
        ], 200);
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
        $validator = Validator::make(['code' => $code], [
            'code' => 'required|exists:reset_password',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Código inválido',
                'errors'  => $validator->errors()
            ], 400);
        }

        return response()->json(['message' => 'Código verificado'], 200);
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
        $validator = Validator::make($request->all(), [
            'code' => 'required|exists:reset_password',
            'password' => 'required|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Ocorreu um erro ao alterar a senha',
                'errors'  => $validator->errors()
            ], 400);
        }

        $reset = ResetPassword::where('code', '=', $request->code)->first();
        $user = User::find($reset->user_id);

        $user->password = Hash::make($request->password);

        $user->update();

        ResetPassword::where('user_id', '=', $user->id)->delete();

        $token = $user->createToken('auth_token', $this->getUserAbilities($user->role))->plainTextToken;

        return response()->json([
            'user'  => $user,
            'token' => $token
        ]);
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
        $user = $request->user;

        return response()->json(['message' => 'Ok', 'user' => $user], 200);
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
        $validator = Validator::make($request->all(), [
            'name'      => 'string',
            'whatsapp'  => 'alpha_num|numeric',
            'email'     => 'email',
            'password'  => 'confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Informe todas as informações necessárias',
                'errors'  => $validator->errors()
            ], 400);
        }

        $user = User::find($request->user->id);

        $user->name = $request->name ?? $user->name;
        $user->whatsapp = $request->whatsapp ?? $user->whatsapp;
        $user->email = $request->email ?? $user->email;
        $user->password = $request->password ? Hash::make($request->password) : $user->password;
        $user->avatar = $request->avatar ?? $user->avatar;

        $user->update();

        return response()->json([
            'message' => 'Perfil atualizado com sucesso!',
            'user' => $user
        ], 200);
    }
}
