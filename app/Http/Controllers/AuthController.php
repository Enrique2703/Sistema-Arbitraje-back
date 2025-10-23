<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Credencial;
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
        
        // 🔍 Obtener la credencial con relaciones
        $credencial = Credencial::with(['participe', 'usuario'])
                                ->where('email', $credentials['email'])
                                ->first();

        if (!$credencial) {
            return response()->json([
                'message' => 'Este usuario no está registrado.',
            ], 403);
        }

        // 🔍 Intentar autenticar
        if (!JWTAuth::attempt($credentials)) {
            return response()->json([
                'message' => 'La contraseña ingresada es incorrecta.',
            ], 403);
        }

        // 🔍 Verificar estado según el tipo de usuario
        if ($credencial->tipo_usuario === 'participe' && $credencial->participe) {
            if (!$credencial->participe->estado || $credencial->participe->estado !== 'Activo') {
                return response()->json([
                    'message' => 'El usuario no está activo.',
                ], 403);
            }
        } elseif ($credencial->tipo_usuario !== 'participe' && $credencial->usuario) {
            if (!$credencial->usuario->estado || $credencial->usuario->estado !== 'Activo') {
                return response()->json([
                    'message' => 'El usuario no está activo.',
                ], 403);
            }
        }

        // 🔍 Generar token
        $token = JWTAuth::attempt($credentials);

        // 🔍 Obtener el nombre según el tipo de usuario
        $nombre = 'Usuario';
        if ($credencial->tipo_usuario === 'participe' && $credencial->participe) {
            $nombre = $credencial->participe->nombre ?? 'Usuario';
        } elseif ($credencial->usuario) {
            $nombre = $credencial->usuario->nombre ?? 'Usuario';
        }

        // Preparar datos del usuario para la respuesta
        $usuarioData = [
            'id' => $credencial->id,
            'nombre' => $nombre,
            'tipo_usuario' => $credencial->tipo_usuario,
            'correo' => $credencial->email,
        ];

        return response()->json([
            'message' => 'Inicio de sesión exitoso',
            'token' => $token,
            'usuario' => $usuarioData,
        ], 200);
    }

    public function logout(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            
            if ($user) {
                // Invalida el token
                JWTAuth::invalidate(JWTAuth::getToken());

                return response()->json(['message' => 'Sesión cerrada exitosamente'], 200);
            }

            return response()->json(['message' => 'Token no válido.'], 401);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al cerrar sesión: ' . $e->getMessage()], 401);
        }
    }
}