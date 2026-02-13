<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class ResetPasswordService
{
    
    public function resetPassword(array $data): array
    {
        $email = $data['email'] ?? null;
        $token = $data['token'] ?? null;
        $password = $data['password'] ?? null;
        $passwordConfirmation = $data['password_confirmation'] ?? null;

        if (! $email || ! $token || ! $password) {
            return ['success' => false, 'message' => 'Missing required fields.'];
        }

        if ($password !== $passwordConfirmation) {
            return ['success' => false, 'message' => 'Password confirmation does not match.'];
        }

        $reset = DB::table('password_resets')->where('email', $email)->first();

        if (! $reset) {
            return ['success' => false, 'message' => 'This password reset token is invalid.'];
        }

        $storedToken = $reset->token;

        $looksHashed = is_string($storedToken) && preg_match('/^\$(2y|2a|2b)\$|^\$argon2/', $storedToken);

        $tokenMatches = false;

        if ($looksHashed) {
            try {
                $tokenMatches = Hash::check($token, $storedToken);
            } catch (\Throwable $e) {
                // If Hash::check fails for any reason, fall back to strict equality
                $tokenMatches = hash_equals((string) $storedToken, (string) $token);
            }
        } else {
            // Plain token stored: compare directly (timing-safe)
            $tokenMatches = hash_equals((string) $storedToken, (string) $token);
        }

        if (! $tokenMatches) {
            return ['success' => false, 'message' => 'This password reset token is invalid.'];
        }

        // Find user
        $user = User::where('email', $email)->first();
        if (! $user) {
            return ['success' => false, 'message' => 'User not found.'];
        }

        $cacheKey = "password_changed:{$user->id}";
        if (Cache::has($cacheKey)) {
            return ['success' => false, 'message' => 'Password already changed within the last 24 hours.'];
        }

        $user->password = Hash::make($password);
        $user->remember_token = Str::random(60);
        $user->save();

        DB::table('password_resets')->where('email', $email)->delete();

        Cache::put($cacheKey, true, now()->addHours(24));

        return ['success' => true, 'message' => 'Password has been reset.'];
    }
}