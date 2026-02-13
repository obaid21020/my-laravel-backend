<?php

namespace App\Services;

use Illuminate\Support\Facades\Password;

class ForgotPasswordService
{

    public function sendResetLink(string $email): array
    {
        $status = Password::sendResetLink(['email' => $email]);

        return $status === Password::RESET_LINK_SENT
            ? ['success' => true, 'message' => __($status)]
            : ['success' => false, 'message' => __($status)];
    }
}
