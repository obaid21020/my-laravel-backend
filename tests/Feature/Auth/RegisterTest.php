<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

it('registers a new user successfully', function () {
    $payload = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'secret123',
        'password_confirmation' => 'secret123',
    ];

    $response = postJson('/api/register', $payload);

    // Assert correct status code and JSON structure
    $response->assertStatus(201)
             ->assertJsonStructure([
                 'user' => ['id', 'name', 'email', 'created_at'],
                 'token',
             ]);

    // Assert the user was actually stored in DB
    $this->assertDatabaseHas('users', [
        'email' => 'john@example.com',
    ]);

    // Optionally check password hashing
    $user = User::whereEmail('john@example.com')->first();
    expect(Hash::check('secret123', $user->password))->toBeTrue();
});

it('fails when password confirmation does not match', function () {
    $payload = [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => 'secret123',
        'password_confirmation' => 'notthesame',
    ];

    $response = postJson('/api/register', $payload);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['password']);
});

it('fails when email already exists', function () {
    User::factory()->create(['email' => 'john@example.com']);

    $payload = [
        'name' => 'John Duplicate',
        'email' => 'john@example.com',
        'password' => 'secret123',
        'password_confirmation' => 'secret123',
    ];

    $response = postJson('/api/register', $payload);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['email']);
});
