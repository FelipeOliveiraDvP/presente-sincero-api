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
use Illuminate\Validation\Rule;

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
            ->orWhere('username', '=', $request->user)
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

        if ($exists->blocked) {
            return response()->json([
                'message' => 'Sua conta está bloqueada. Por favor, entre em contato com o suporte.'
            ], 403);
        }

        $user = User::find($exists->id)->makeHidden(['mp_access_token', 'blocked', 'seller_approved']);

        $token = $user->createToken('auth_token', $this->getUserAbilities($user->role))->plainTextToken;

        return response()->json([
            'user'  => $user,
            'token' => $token
        ]);
    }

    /**
     * Verifica se um usuário já está cadastrado, e se não estiver cadastra um cliente simples.
     * 
     * @param Request $request Corpo da requisição.
     * 
     * @return JsonResponse
     */
    public function simpleLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'whatsapp'    => 'required|alpha_num|min:10|max:11',
            'new_account' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Informe um número válido!',
                'errors' => $validator->errors()
            ], 400);
        }

        $user_exists = User::where('whatsapp', '=', $request->whatsapp)->first();

        if (empty($user_exists) && $request->new_account == false) {
            return response()->json([
                'message' => 'Você ainda não possui uma conta!',
                'new_account' => true
            ], 200);
        }

        if (!empty($user_exists)) {
            $user_exists_token = $user_exists->createToken('auth_token', $this->simpleCustomerAbilities())->plainTextToken;

            return response()->json([
                'user'  => $user_exists->makeHidden(['mp_access_token', 'blocked', 'seller_approved'])->toArray(),
                'token' => $user_exists_token
            ]);
        }

        if (empty($request->name) && $request->new_account == true) {
            return response()->json([
                'message' => 'Informe o seu nome completo.',
            ], 400);
        }

        $user = User::create([
            'name' => $request->name,
            'whatsapp' => $request->whatsapp,
            'role' => $this->getCustomerRole()
        ]);

        $token = $user->createToken('auth_token', $this->simpleCustomerAbilities())->plainTextToken;

        return response()->json([
            'user'  => $user->makeHidden(['mp_access_token', 'blocked', 'seller_approved'])->toArray(),
            'token' => $token
        ]);
    }

    /**
     * Realiza o cadastro de um novo vendedor e retorna um token de acesso
     * 
     * @param Request $request Corpo da requisição.
     * 
     * @return JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'required',
            'username'  => 'required|alpha_num|unique:users,username',
            'whatsapp'  => 'required|alpha_num|unique:users,whatsapp|min:10|max:11',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|confirmed'
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
            'username'  => $request->username,
            'email'     => $request->email,
            'password'  => $request->password ? Hash::make($request->password) : null,
            'role'      => $this->getSellerRole(),
        ]);

        $token = $created->createToken('auth_token', $this->sellerAbilities())->plainTextToken;

        return response()->json([
            'user'  => $created->makeHidden(['mp_access_token', 'blocked', 'seller_approved'])->toArray(),
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
                'message' => 'Informe seu usuário para recuperar a senha',
                'errors'  => $validator->errors()
            ], 400);
        }

        $exists = User::where('whatsapp', '=', $request->user)
            ->orWhere('username', '=', $request->user)
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
            'user'  => $user->makeHidden(['mp_access_token', 'blocked', 'seller_approved'])->toArray(),
            'token' => $token
        ]);
    }

    /**
     * Retorna as informações do usuário atual
     * 
     * @return JsonResponse
     */
    public function getProfile()
    {
        $user = User::find(auth('sanctum')->id());

        return response()->json([
            'message' => 'Ok',
            'user' => $user
        ], 200);
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
            'whatsapp'  => [
                'alpha_num',
                'min:10',
                'max:11',
                Rule::unique('users')->ignore(auth('sanctum')->id()),
            ],
            'username'  => [
                'alpha_num',
                Rule::unique('users')->ignore(auth('sanctum')->id()),
            ],
            'email'     => [
                'email',
                Rule::unique('users')->ignore(auth('sanctum')->id()),
            ],
            'password'  => 'confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Ocorreu um erro ao atualizar as informações',
                'errors'  => $validator->errors()
            ], 400);
        }

        $user = User::find($request->user->id);

        $user->name = $request->name ?? $user->name;
        $user->whatsapp = $request->whatsapp ?? $user->whatsapp;
        $user->email = $request->email ?? $user->email;
        $user->username = $request->username ?? $user->username;
        $user->password = $request->password ? Hash::make($request->password) : $user->password;
        $user->avatar = $request->avatar ?? $user->avatar;

        $user->update();

        return response()->json([
            'message' => 'Perfil atualizado com sucesso!',
            'user' => $user->makeHidden(['mp_access_token', 'blocked', 'seller_approved'])->toArray()
        ], 200);
    }
}
