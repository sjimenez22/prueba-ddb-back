<?php

namespace App\Providers;

use App\Models\User;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;

class JwtServiceProvider
{
    public function generateToken($data)
    {
        $secrect_key = env('JWT_SECRET_KEY');

        $issuedAt = time();
        $expirationTime = $issuedAt + (3600 * 24) * 7; // Token valid for 7 days

        $payload = [
            'iss' => config('app.url'),
            'iat' => $issuedAt,
            'exp' => $expirationTime,
            'data' => $data
        ];

        return JWT::encode($payload, $secrect_key, 'HS256');
    }

    public function haveAccess(Request $request)
    {
        $token = $request->bearerToken();
        if (!$token) return response()->json(['message' => 'Token no proporcionado.'], 401);

        try {
            $secrect_key = env('JWT_SECRET_KEY');
            $credentials = JWT::decode($token, new Key($secrect_key, 'HS256'));

            $user = User::with('role')->where('c_email', $credentials->data->c_email)->first();
            if ($user && $user->role->pk_role === 1) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            return response()->json(['message' => 'Token invalido.'], 401);
        }
    }
}
