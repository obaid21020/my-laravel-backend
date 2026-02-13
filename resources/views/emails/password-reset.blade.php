<!DOCTYPE html>
<html>
<head>
    <title>Password Reset</title>
</head>
<body>
    <h2>Password Reset Request</h2>
    <p>You requested a password reset. Click the link below to reset your password:</p>
    <a href="{{ $resetUrl }}">Reset Password</a>
    <p>If you didn't request this, please ignore this email.</p>
    <p>This link will expire in 24 hours.</p>
</body>
</html>