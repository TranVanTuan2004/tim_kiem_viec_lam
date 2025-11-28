<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertStatus(200);
});

test('new users can register', function () {
    Event::fake();

    $response = $this->post(route('register.store'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'phone' => '0123456789',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();

    // Should redirect to verification notice page
    $response->assertRedirect(route('verification.notice', absolute: false));

    // Verify user was created
    $this->assertDatabaseHas('users', [
        'email' => 'test@example.com',
        'name' => 'Test User',
        'phone' => '0123456789',
    ]);

    // Verify Registered event was fired (which triggers email sending)
    Event::assertDispatched(Registered::class);
});

test('new user is assigned candidate role by default', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'phone' => '0123456789',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $user = User::where('email', 'test@example.com')->first();

    expect($user->hasRole('Candidate'))->toBeTrue();
});

test('new user email is unverified by default', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'phone' => '0123456789',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $user = User::where('email', 'test@example.com')->first();

    expect($user->hasVerifiedEmail())->toBeFalse();
});

test('registration requires name', function () {
    $response = $this->post(route('register.store'), [
        'name' => '',
        'email' => 'test@example.com',
        'phone' => '0123456789',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasErrors('name');
    $this->assertGuest();
});

test('registration requires email', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'Test User',
        'email' => '',
        'phone' => '0123456789',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('registration requires valid email format', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'Test User',
        'email' => 'invalid-email',
        'phone' => '0123456789',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('registration requires unique email', function () {
    $existingUser = User::factory()->create(['email' => 'existing@example.com']);

    $response = $this->post(route('register.store'), [
        'name' => 'Test User',
        'email' => 'existing@example.com',
        'phone' => '0123456789',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('registration requires phone', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'phone' => '',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasErrors('phone');
    $this->assertGuest();
});

test('registration requires password', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'phone' => '0123456789',
        'password' => '',
        'password_confirmation' => '',
    ]);

    $response->assertSessionHasErrors('password');
    $this->assertGuest();
});

test('registration requires password confirmation', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'phone' => '0123456789',
        'password' => 'password',
        'password_confirmation' => 'different-password',
    ]);

    $response->assertSessionHasErrors('password');
    $this->assertGuest();
});

test('registration sends verification email', function () {
    Event::fake();

    $this->post(route('register.store'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'phone' => '0123456789',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    Event::assertDispatched(Registered::class, function ($event) {
        return $event->user->email === 'test@example.com';
    });
});

test('registration with very long name is rejected', function () {
    $response = $this->post(route('register.store'), [
        'name' => str_repeat('a', 256), // Exceeds max length
        'email' => 'test@example.com',
        'phone' => '0123456789',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasErrors('name');
    $this->assertGuest();
});

test('registration with very long email is rejected', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'Test User',
        'email' => str_repeat('a', 150) . '@example.com', // Exceeds max length
        'phone' => '0123456789',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});