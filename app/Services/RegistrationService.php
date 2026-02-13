<?php

namespace App\Services;

use App\Models\User;
use App\Mail\EmailVerificationMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class RegistrationService
{
    public function register(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name'                => $data['name'],
                'email'               => $data['email'],
                'password'            => Hash::make($data['password']),
                'authorization_level' => $data['authorization_level'] ?? 3,
                'remember_token'      => null,
            ]);

            $verificationToken = Str::random(64);
            $user->forceFill(['remember_token' => $verificationToken])->save();

            $verificationUrl = rtrim(config('app.frontend_url') ?? env('APP_FRONTEND_URL'), '/') .
                               '/admin/verify-email?token=' . $verificationToken .
                               '&email=' . urlencode($user->email);

            Mail::to($user->email)->queue(new EmailVerificationMail($user, $verificationUrl));

            $token = $user->createToken('authToken')->plainTextToken;

            return [
                'user'    => $user,
                'token'   => $token,
            ];
        });
    }
}
