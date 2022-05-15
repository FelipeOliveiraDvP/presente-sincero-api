<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
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

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'required',
            'whatsapp'  => 'required|alpha_num|numeric|unique:users,whatsapp'
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

    // TODO: Enviar e-mail de recuperação de senha
    // TODO: Formulário de alteração de senha
    // TODO: Alterar dados pessoais
}
