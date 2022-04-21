<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use WithFaker;

    public function test_loginLogout() {
        $email = $this->faker->safeEmail();
        $password = Str::random();

        $user = User::factory()->create([
            'email' => $email,
            'password' => bcrypt($password),
        ]);

        $response = $this->post('/api/login', [
            'email' => $email,
            'password' => 'nenenen ne',
        ]);
        $response->assertJson(['success' => false]);
        self::assertFalse(Auth::check());

        $response = $this->post('/api/login', [
            'email' => $email,
            'password' => $password,
        ]);
        $response->assertJson(['success' => true]);
        self::assertTrue(Auth::check());

        // logout
        $response = $this->post('api/logout');
        $response->assertOk();
        $response->assertJson(['success' => true]);
    }

    public function test_passwordForgotten() {
        $user = User::inRandomOrder()->first();
        Notification::fake();
        $response = $this->json('post', '/api/forgot-password');
        $response->assertStatus(422);

        $response = $this->json('post', '/api/forgot-password', [
            'email' => $user->email,
        ]);
        $response->assertStatus(200);
        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_submitResetPassword() {
        $user = User::inRandomOrder()->first();
        $token = app('auth.password.broker')->createToken($user);
        $password = Str::random();

        $response = $this->json('post', '/api/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => $password,
            'password_confirmation' => $password,
        ]);
        $response->assertOk();
        $user->refresh();

        self::assertTrue(Hash::check($password, $user->password));
    }
}
