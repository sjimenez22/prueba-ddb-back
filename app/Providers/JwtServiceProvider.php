<?php

namespace App\Providers;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Request;

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
}
