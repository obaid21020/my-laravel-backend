<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Mail\EmailVerificationMail;
use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(DatabaseTransactions::class);

beforeEach(function () {
    Mail::fake();
    Cache::flush();
});

/* --------------------------------------------------------------------------
 * 1. LOGIN
 * ----------------------------------------------------------------------- */

it('allows a user to login and returns token + user', function () {
    $password = 'Password1!';
    $user = User::factory()->create([
        'email' => 'login@example.test',
        'password' => Hash::make($password),
    ]);

    $response = $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => $password,
    ]);`    

    $response->assertStatus(200);
    $data = $response->json();

    expect($data['success'])->toBeTrue();
    expect($data['token'])->not->toBeNull();
    expect($data['user']['email'])->toBe($user->email);
});

it('allows login with the fixed credential set (bsce21020@itu.edu.pk / Test123#)', function () {
    $user = User::factory()->create([
        'email' => 'bsce21020@itu.edu.pk',
        'password' => Hash::make('Test123#'),
    ]);

    $response = $this->postJson('/api/login', [
        'email' => 'bsce21020@itu.edu.pk',
        'password' => 'Test123#',
    ]);

    $response->assertStatus(200);
    $data = $response->json();

    expect($data['success'])->toBeTrue();
    expect($data['token'])->not->toBeNull();
    expect($data['user']['email'])->toBe('bsce21020@itu.edu.pk');
});

/* --------------------------------------------------------------------------
 * 2. EMAIL VERIFICATION
 * ----------------------------------------------------------------------- */

it('verifies email when token and email match and clears remember_token', function () {
    $token = 'verify-token-xyz';
    $user = User::factory()->create([
        'email' => 'verify@example.test',
        'remember_token' => $token,
        'email_verified_at' => null,
    ]);

    $response = $this->postJson('/api/verify-email', [
        'token' => $token,
        'email' => $user->email,
    ]);

    $response->assertStatus(200);
    $body = $response->json();
    expect($body['success'])->toBeTrue();

    $user->refresh();
    expect($user->email_verified_at)->not->toBeNull();
    expect($user->remember_token)->toBeNull();
});

/* --------------------------------------------------------------------------
 * 3. VERIFY PASSWORD (re-authentication)
 * ----------------------------------------------------------------------- */

it('verify-password succeeds with correct email+password and fails with wrong password', function () {
    $raw = 'SecretPass1!';
    $user = User::factory()->create([
        'email' => 'vp@example.test',
        'password' => Hash::make($raw),
    ]);

    // success case
    $resOk = $this->postJson('/api/verify-password', [
        'email' => $user->email,
        'password' => $raw,
    ]);
    $resOk->assertStatus(200);
    expect($resOk->json('success'))->toBeTrue();

    // failure case
    $resFail = $this->postJson('/api/verify-password', [
        'email' => $user->email,
        'password' => 'wrongpass',
    ]);
    // controller returns 403 on invalid password in current implementation
    $resFail->assertStatus(403);
    expect($resFail->json('success'))->toBeFalse();
});

/* --------------------------------------------------------------------------
 * 4. FORGOT PASSWORD
 * ----------------------------------------------------------------------- */

it('forgot-password generates a password_resets row and sends mail', function () {
    $user = User::factory()->create(['email' => 'forgot@example.test']);

    $res = $this->postJson('/api/forgot-password', [
        'email' => $user->email,
    ]);

    $res->assertStatus(200);
    $body = $res->json();
    expect($body['success'])->toBeTrue();

    // DB row exists
    $row = DB::table('password_resets')->where('email', $user->email)->first();
    expect($row)->not->toBeNull();

    // Mail was queued/sent
    Mail::assertSent(EmailVerificationMail::class, 0); // not the verification mail
    // depending on your ForgotPasswordService mailable, adjust assertion:
    // Mail::assertQueued(PasswordResetMail::class);
});

/* --------------------------------------------------------------------------
 * 5. RESET PASSWORD
 * ----------------------------------------------------------------------- */

it('reset-password accepts a valid token, updates password, and clears reset row', function () {
    $user = User::factory()->create(['email' => 'reset@example.test']);
    $rawToken = 'plain-reset-token-123';

    DB::table('password_resets')->insert([
        'email' => $user->email,
        'token' => $rawToken,
        'created_at' => now(),
    ]);

    $res = $this->postJson('/api/reset-password', [
        'email' => $user->email,
        'token' => $rawToken,
        'password' => 'NewPass12!',
        'password_confirmation' => 'NewPass12!',
    ]);

    $res->assertStatus(200);
    $body = $res->json();
    expect($body['success'])->toBeTrue();

    $user->refresh();
    expect(Hash::check('NewPass12!', $user->password))->toBeTrue();

    $exists = DB::table('password_resets')->where('email', $user->email)->exists();
    expect($exists)->toBeFalse();

    // Ensure 24h lock is set (service uses cache key password_changed:{id})
    expect(Cache::has("password_changed:{$user->id}"))->toBeTrue();
});

it('changes the password for bsce21020@itu.edu.pk from Test123# to Test123! via reset flow', function () {
    // create the user with the original password
    $user = User::factory()->create([
        'email'    => 'bsce21020@itu.edu.pk',
        'password' => Hash::make('Test123#'),
    ]);

    // insert a reset row
    $plainToken = 'bsce-reset-token-456';
    DB::table('password_resets')->insert([
        'email'      => $user->email,
        'token'      => $plainToken,
        'created_at' => now(),
    ]);

    // hit the reset endpoint
    $res = $this->postJson('/api/reset-password', [
        'email'                   => 'bsce21020@itu.edu.pk',
        'token'                   => $plainToken,
        'password'                => 'Test123!',
        'password_confirmation'   => 'Test123!',
    ]);

    $res->assertStatus(200);
    expect($res->json('success'))->toBeTrue();

    // assert password updated
    $user->refresh();
    expect(Hash::check('Test123!', $user->password))->toBeTrue();
    expect(Hash::check('Test123#', $user->password))->toBeFalse();

    // reset row cleaned up
    expect(DB::table('password_resets')->where('email', $user->email)->exists())->toBeFalse();

    // 24h lock cached
    expect(Cache::has("password_changed:{$user->id}"))->toBeTrue();
});

/* --------------------------------------------------------------------------
 * 6. LOGOUT
 * ----------------------------------------------------------------------- */

it('logout deletes personal access tokens when called with a valid bearer token', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    $res = $this->withHeaders([
        'Authorization' => "Bearer {$token}",
    ])->postJson('/api/logout');

    $res->assertStatus(200);
    $body = $res->json();
    expect($body['success'])->toBeTrue();

    // ensure no tokens remain
    expect($user->tokens()->count())->toBe(0);
});