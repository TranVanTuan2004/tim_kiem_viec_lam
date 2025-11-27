<?php

use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;
use Laravel\Fortify\Features;

test('login screen can be rendered', function () {
    $response = $this->get(route('login'));

    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->withoutTwoFactor()->create();

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('candidate.dashboard', absolute: false));
});

test('users with two factor enabled are redirected to two factor challenge', function () {
    if (!Features::canManageTwoFactorAuthentication()) {
        $this->markTestSkipped('Two-factor authentication is not enabled.');
    }

    Features::twoFactorAuthentication([
        'confirm' => true,
        'confirmPassword' => true,
    ]);

    $user = User::factory()->create();

    $user->forceFill([
        'two_factor_secret' => encrypt('test-secret'),
        'two_factor_recovery_codes' => encrypt(json_encode(['code1', 'code2'])),
        'two_factor_confirmed_at' => now(),
    ])->save();

    $response = $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('two-factor.login'));
    $response->assertSessionHas('login.id', $user->id);
    $this->assertGuest();
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('logout'));

    $this->assertGuest();
    $response->assertRedirect('/');
});

test('users are rate limited', function () {
    $user = User::factory()->create();

    RateLimiter::increment(implode('|', [$user->email, '127.0.0.1']), amount: 10);

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrors('email');

    $errors = session('errors');

    $this->assertStringContainsString('Quá nhiều lần đăng nhập', $errors->first('email'));
});

// ✅ New test cases for unverified user login flow

test('unverified user can login and is redirected to verification page', function () {
    $user = User::factory()->unverified()->withoutTwoFactor()->create();

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('verification.notice'));
});

test('verified candidate is redirected to candidate dashboard', function () {
    $user = User::factory()->withoutTwoFactor()->create();
    $user->assignRole('Candidate');

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('candidate.dashboard'));
});

test('verified employer is redirected to employer dashboard', function () {
    $user = User::factory()->withoutTwoFactor()->create();
    $user->assignRole('Employer');

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('employer.dashboard'));
});

test('verified admin is redirected to admin dashboard', function () {
    $user = User::factory()->withoutTwoFactor()->create();
    $user->assignRole('Admin');

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('admin.dashboard'));
});

test('login with empty email and password shows general error', function () {
    $response = $this->post(route('login.store'), [
        'email' => '',
        'password' => '',
    ]);

    $response->assertSessionHasErrors('general');
    $this->assertGuest();
});

test('login with invalid email format shows validation error', function () {
    $response = $this->post(route('login.store'), [
        'email' => 'invalid-email',
        'password' => 'password',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('login with short password shows validation error', function () {
    $response = $this->post(route('login.store'), [
        'email' => 'test@example.com',
        'password' => '1234567', // Less than 8 characters
    ]);

    $response->assertSessionHasErrors('password');
    $this->assertGuest();
});