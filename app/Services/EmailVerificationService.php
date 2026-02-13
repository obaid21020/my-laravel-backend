<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;

class EmailVerificationService
{
    public function verifyEmail(string $token, string $email): array
    {
        $user = User::where('email', $email)
                    ->where('remember_token', $token)  
                    ->first();

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Invalid verification link.',
            ];
        }

        $user->update([
            'email_verified_at' => Carbon::now(),
            'remember_token'    => null,   // clear token after use
        ]);

        return [
            'success' => true,
            'message' => 'Email verified successfully.',
        ];
    }
}