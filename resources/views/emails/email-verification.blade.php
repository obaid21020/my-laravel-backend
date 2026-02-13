<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verify Your Email</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; color:#333; line-height:1.4;">
    <h2>Hello {{ $user->name }},</h2>

    <p>Thanks for registering. Please verify your email by clicking the link below:</p>

    <p><a href="{{ $verificationUrl }}" style="display:inline-block;padding:10px 16px;background:#1a73e8;color:#fff;text-decoration:none;border-radius:4px;">Verify Email</a></p>

    <p>If the button doesn't work, copy and paste the following URL into your browser:</p>
    <p><a href="{{ $verificationUrl }}">{{ $verificationUrl }}</a></p>

    <p>If you did not create an account, you can ignore this email.</p>

    <p>Regards,<br>{{ config('app.name') }}</p>
</body>
</html>