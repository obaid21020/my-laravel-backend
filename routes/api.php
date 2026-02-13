<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/rooms/{id}/book', [RoomController::class, 'book']);
Route::delete('/rooms/{id}/cancel-booking', [RoomController::class, 'cancelBooking']);
Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');



Route::post('/register',        [AuthController::class, 'register']);
Route::post('/login',           [AuthController::class, 'login']);
Route::post('/logout',          [AuthController::class, 'logout']);
Route::post('/verify-email',    [AuthController::class, 'verifyEmail']); 
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password',  [AuthController::class, 'resetPassword']);


Route::middleware('auth:sanctum')->get('/user/verified', function (Request $request) {
     \Log::info('Verified check', [
        'email' => $request->user()->email,
        'verified' => (bool) $request->user()->email_verified_at,
    ]);
    return ['verified' => (bool) $request->user()->email_verified_at];
})->name('user.verified');
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
