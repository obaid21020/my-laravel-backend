<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class LoginService
{
   
    public function login(array $credentials): array
    {
        if (!Auth::attempt($credentials)) {
            return [
                'success' => false,
                'message' => 'Invalid credentials',
            ];
        }

        $user = Auth::user();
        $token = $user->createToken('authToken')->plainTextToken;

        return [
            'success' => true,
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token,
        ];
    }
}
