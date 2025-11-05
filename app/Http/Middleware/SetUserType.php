<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class SetUserType
{
    public function handle(Request $request, Closure $next)
    {
        try {
            if ($token = $request->header('Authorization')) {
                $user = JWTAuth::parseToken()->authenticate();
                if ($user && $user->credencial) {
                    session(['user_type' => $user->credencial->tipo_usuario]);
                }
            }
        } catch (\Exception $e) {
            // Si hay algún error con el token, simplemente continuamos
        }

        return $next($request);
    }
}