<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
            'password' => bcrypt($password)
        ]);

        $response = $this->post('/api/login', [
           'email' => $email,
            'password' => 'nenenen ne'
        ]);
        $response->assertJson(['success' => false]);
        self::assertFalse(Auth::check());

        $response = $this->post('/api/login', [
            'email' => $email,
            'password' => $password
        ]);
        $response->assertJson(['success' => true]);
        self::assertTrue(Auth::check());

        // logout
        $response = $this->post('api/logout');
        $response->assertOk();
        $response->assertJson(['success' => true]);
        //self::assertFalse(auth('sanctum')->check());
    }
}
