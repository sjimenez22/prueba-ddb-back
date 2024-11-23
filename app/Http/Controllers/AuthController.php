<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Providers\JwtServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public $jwtService;

    public function __construct(JwtServiceProvider $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    /**
     * Validate login with email and password
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'c_email' => 'required|string|email',
            'c_password' => 'required|string',
        ], [
            'c_email.required' => 'El correo es requerido.',
            'c_email.string' => 'El correo debe ser un texto.',
            'c_email.email' => 'El correo debe tener un formato de correo electrónico valido.',
            'c_password.required' => 'La contraseña es requerida.',
            'c_password.string' => 'La contraseña debe ser un texto.'
        ]);

        if ($validator->fails()) return response()->json(['message' => $validator->errors()], 400);

        $user = User::where('c_email', $request->c_email)->first();

        if ($user) {
            if (!Hash::check($request->c_password, $user->c_password))
                return response()->json(['message' => 'Usuario o contraseña no coincide.'], 401);

            $payload = [
                'c_email' => $user->c_email
            ];

            $token = $this->jwtService->generateToken($payload);

            $cookie = cookie('Authorization', $token, 60 * 24 * 7, '/', 'localhost', false, false, false, 'None');

            return response()->json([
                'message' => 'Usuario logeado',
                'authorization' => [
                    'token' => $token
                ]
            ], 200)->withCookie($cookie);
        } else {
            return response()->json(['message' => 'Usuario o contraseña no coincide.'], 401);
        }
    }
}
