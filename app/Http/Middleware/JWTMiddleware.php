<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JWTMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        if (!$token) return response()->json(['message' => 'Token no proporcionado.'], 401);

        try {
            $secrect_key = env('JWT_SECRET_KEY');
            $credentials = JWT::decode($token, new Key($secrect_key, 'HS256'));
        } catch (Exception $e) {
            return response()->json(['message' => 'Token invalido.'], 401);
        }

        return $next($request);
    }
}
