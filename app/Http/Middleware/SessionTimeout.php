<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SessionTimeout
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $token = JWTAuth::getToken();

            if (!$token) {
                return response()->json(['message' => 'Token no válido o no proporcionado.'], 401);
            }

            $user = JWTAuth::authenticate($token);
            if (!$user) {
                return response()->json(['message' => 'Token no válido o no corresponde a ningún usuario.'], 401);
            }

            $iat = JWTAuth::getPayload($token)->get('iat');
            $issuedAt = Carbon::createFromTimestamp($iat);

            if ($issuedAt->diffInMinutes(Carbon::now()) > 60) {
                // Invalidar el token
                JWTAuth::invalidate($token);

                $user->ultima_sesion = now();
                $user->save();

                return response()->json(['message' => 'La sesión ha expirado.'], 401);
            }

            return $next($request);
        } catch (TokenExpiredException $e) {
            return response()->json(['message' => 'Token expirado.'], 401);
        } catch (TokenInvalidException $e) {
            return response()->json(['message' => 'Token inválido.'], 401);
        } catch (JWTException $e) {
            return response()->json(['message' => 'Error con el token.'], 401);
        }
    }
}