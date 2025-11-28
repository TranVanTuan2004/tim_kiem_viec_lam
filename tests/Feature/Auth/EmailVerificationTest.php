<?php

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;

test('email verification screen can be rendered', function () {
    $user = User::factory()->unverified()->create();

    $response = $this->actingAs($user)->get(route('verification.notice'));

    $response->assertStatus(200);
});

test('email verification screen can be rendered for guest users', function () {
    $response = $this->get(route('verification.notice'));

    $response->assertStatus(200);
});

test('email can be verified', function () {
    $user = User::factory()->unverified()->create();

    Event::fake();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->email)]
    );

    $response = $this->actingAs($user)->get($verificationUrl);

    Event::assertDispatched(Verified::class);
    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
    $response->assertRedirect(route('candidate.dashboard', absolute: false) . '?verified=1');
});

test('email is not verified with invalid hash', function () {
    $user = User::factory()->unverified()->create();

    Event::fake();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1('wrong-email')]
    );

    $this->actingAs($user)->get($verificationUrl);

    Event::assertNotDispatched(Verified::class);
    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});

test('email is not verified with invalid user id', function () {
    $user = User::factory()->unverified()->create();

    Event::fake();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => 123, 'hash' => sha1($user->email)]
    );

    $this->actingAs($user)->get($verificationUrl);

    Event::assertNotDispatched(Verified::class);
    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});

test('verified user is redirected to dashboard from verification prompt', function () {
    $user = User::factory()->create();

    Event::fake();

    $response = $this->actingAs($user)->get(route('verification.notice'));

    Event::assertNotDispatched(Verified::class);
    $response->assertRedirect(route('candidate.dashboard', absolute: false));
});

test('already verified user visiting verification link is redirected without firing event again', function () {
    $user = User::factory()->create();

    Event::fake();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->email)]
    );

    $this->actingAs($user)->get($verificationUrl)
        ->assertRedirect(route('candidate.dashboard', absolute: false) . '?verified=1');

    Event::assertNotDispatched(Verified::class);
    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
});

// ✅ New test cases for resend verification email

test('can resend verification email for unverified user', function () {
    $user = User::factory()->unverified()->create();

    $response = $this->post(route('verification.resend'), [
        'email' => $user->email
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Email xác thực đã được gửi lại'
        ]);
});

test('cannot resend verification email for already verified user', function () {
    $user = User::factory()->create(); // Already verified

    $response = $this->post(route('verification.resend'), [
        'email' => $user->email
    ]);

    $response->assertStatus(400)
        ->assertJson([
            'success' => false,
            'message' => 'Tài khoản này đã được xác thực'
        ]);
});

test('cannot resend verification email for non-existent email', function () {
    $response = $this->post(route('verification.resend'), [
        'email' => 'nonexistent@example.com'
    ]);

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'message' => 'Email không tồn tại trong hệ thống'
        ]);
});

test('resend verification email requires valid email format', function () {
    $response = $this->post(route('verification.resend'), [
        'email' => 'invalid-email'
    ]);

    $response->assertStatus(302); // Validation error redirects
    $response->assertSessionHasErrors('email');
});

test('resend verification email requires email field', function () {
    $response = $this->post(route('verification.resend'), []);

    $response->assertStatus(302); // Validation error redirects
    $response->assertSessionHasErrors('email');
});

test('guest user can resend verification email', function () {
    $user = User::factory()->unverified()->create();

    // Not authenticated
    $response = $this->post(route('verification.resend'), [
        'email' => $user->email
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Email xác thực đã được gửi lại'
        ]);
});

test('verification link with expired signature returns error', function () {
    $user = User::factory()->unverified()->create();

    // Create an expired signed URL (expired 1 minute ago)
    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->subMinute(),
        ['id' => $user->id, 'hash' => sha1($user->email)]
    );

    $response = $this->actingAs($user)->get($verificationUrl);

    // Should return 403 Forbidden for invalid signature
    $response->assertStatus(403);
});

test('manual verification endpoint returns success for valid link', function () {
    $user = User::factory()->unverified()->create();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify.manual',
        now()->addMinutes(60),
        ['id' => $user->id]
    );

    $response = $this->get($verificationUrl);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Xác thực tài khoản thành công, bạn có thể đăng nhập ngay bây giờ'
        ]);

    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
});

test('manual verification endpoint returns error for non-existent user', function () {
    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify.manual',
        now()->addMinutes(60),
        ['id' => 99999]
    );

    $response = $this->get($verificationUrl);

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'message' => 'Liên kết xác thực không hợp lệ hoặc đã hết hạn'
        ]);
});

test('manual verification endpoint returns error for already verified user', function () {
    $user = User::factory()->create(); // Already verified

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify.manual',
        now()->addMinutes(60),
        ['id' => $user->id]
    );

    $response = $this->get($verificationUrl);

    $response->assertStatus(400)
        ->assertJson([
            'success' => false,
            'message' => 'Tài khoản này đã được xác thực'
        ]);
});