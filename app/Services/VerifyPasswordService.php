<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class VerifyPasswordService
{
  
    public function verify(array $data, ?User $user): array
    {
        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found.'
            ];
        }

        // Verify password
        if (!Hash::check($data['password'], $user->password)) {
            return [
                'success' => false,
                'message' => 'Invalid password.'
            ];
        }

        // Optional: rehash if algorithm updated
        if (Hash::needsRehash($user->password)) {
            $user->password = Hash::make($data['password']);
            $user->save();
        }

        // Generate reset token if success
        $token = Str::random(64);
        DB::table('password_resets')->updateOrInsert(
            ['email' => $user->email],
            [
                'token'      => $token,
                'created_at' => Carbon::now(),
            ]
        );
        return [
            'success' => true,
            'message' => 'Password verified.',
            'token'   => $token,
        ];
    }
}
