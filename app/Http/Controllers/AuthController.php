<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Credencial;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');
        $user = Credencial::where('email', $credentials['email'])->first();

        if (!$user) {
            return response()->json([
                'message' => 'Este usuario no está registrado.',
            ], 403);
        }

        if (!JWTAuth::attempt($credentials)) {
            return response()->json([
                'message' => 'La contraseña ingresada es incorrecta.',
            ], 403);
        }

        if (!$user->usuario->estado || $user->usuario->estado !== 'Activo') {
            return response()->json([
                'message' => 'El usuario no está activo.',
            ], 403);
        }


        $token = JWTAuth::attempt($credentials);

        return response()->json([
            'message' => 'Inicio de sesión exitoso',
            'token' => $token,
            'usuario' => [
                'id' => $user->id,
                'tipo_usuario' => $user->tipo_usuario,
                'correo' => $user->email,
            ],
        ], 200);
    }

    public function logout(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if ($user) {
            // invalida el token
            JWTAuth::invalidate(JWTAuth::getToken());

            return response()->json(['message' => 'Sesión cerrada exitosamente'], 200);
        }

        return response()->json(['message' => 'Token no válido.'], 401);
    }
}
