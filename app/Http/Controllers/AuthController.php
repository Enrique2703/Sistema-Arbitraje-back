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

        // 🔍 Obtener el nombre y tipo según el tipo de usuario
        $nombres = 'Usuario';
        $tipoUsuario = $credencial->tipo_usuario;
        
        if ($credencial->tipo_usuario === 'participe' && $credencial->participe) {
            $nombres = $credencial->participe->nombres ?? 'Usuario';
        } elseif ($credencial->usuario) {
            $nombres = $credencial->usuario->nombres ?? 'Usuario';
            // Si el tipo está vacío en credencial, obtenerlo del usuario
            if (empty($tipoUsuario) && $credencial->usuario->nivel_usuario) {
                $tipoUsuario = $credencial->usuario->nivel_usuario;
            }
        }

        // Si aún está vacío, asignar un valor por defecto
        if (empty($tipoUsuario)) {
            $tipoUsuario = 'usuario';
        }

        // Preparar datos del usuario para la respuesta
        $usuarioData = [
            'id' => $credencial->id,
            'nombre' => $nombres,
            'tipo_usuario' => $tipoUsuario,
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