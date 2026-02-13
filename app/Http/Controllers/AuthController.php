<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\VerifyPasswordRequest;
use App\Services\RegistrationService;
use App\Services\LoginService;
use App\Services\EmailVerificationService;
use App\Services\ForgotPasswordService;
use App\Services\ResetPasswordService;
use App\Services\VerifyPasswordService;
use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    protected RegistrationService $registrationService;
    protected LoginService $loginService;
    protected EmailVerificationService $emailVerificationService;
    protected ForgotPasswordService $forgotPasswordService;
    protected ResetPasswordService $resetPasswordService;
    protected VerifyPasswordService $verifyPasswordService;

    public function __construct(
        RegistrationService $registrationService,
        LoginService $loginService,
        EmailVerificationService $emailVerificationService,
        ForgotPasswordService $forgotPasswordService,
        ResetPasswordService $resetPasswordService,
        VerifyPasswordService $verifyPasswordService,
    ) {
        $this->registrationService = $registrationService;
        $this->loginService = $loginService;
        $this->emailVerificationService = $emailVerificationService;
        $this->forgotPasswordService = $forgotPasswordService;
        $this->resetPasswordService = $resetPasswordService;
        $this->verifyPasswordService = $verifyPasswordService;
    }

    public function register(RegisterUserRequest $request)
    {
        $registrationData = $this->registrationService->register($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully. Please verify your email.',
            'user' => $registrationData['user'],
            'token' => $registrationData['token'],
        ], 200);
    }

    public function login(Request $request)
    {
        $result = $this->loginService->login($request->only('email', 'password'));
        return response()->json($result, $result['success'] ? 200 : 401);
    }

    public function verifyEmail(Request $request)
    {
        $result = $this->emailVerificationService->verifyEmail($request->token, $request->email);
        return response()->json($result, $result['success'] ? 200 : 400);
    }
    
    public function verifyPassword(VerifyPasswordRequest $request)
    {
        $data = $request->validated();
        $user = $request->user();

        if (isset($data['email'])) {
            $user = User::where('email', $data['email'])->first();
        }

        $result = $this->verifyPasswordService->verify($data, $user);

        return response()->json($result, $result['success'] ? 200 : ($result['message'] === 'User not found.' ? 404 : 403));
    }


    public function forgotPassword(Request $request)
    {
        $result = $this->forgotPasswordService->sendResetLink($request->email);
        return response()->json($result, $result['success'] ? 200 : 400);
    }

    public function resetPassword(Request $request)
    {
        $data = $request->only('email', 'password', 'password_confirmation', 'token');
        $result = $this->resetPasswordService->resetPassword($data);

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    public function logout(Request $request)
    {
        if ($request->user()) {
        $request->user()->tokens()->delete();
        }
        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }
}
