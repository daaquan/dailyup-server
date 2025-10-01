<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function user_can_register_with_valid_data()
    {
        $userData = [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'success',
                     'data' => ['user', 'token'],
                     'message'
                 ])
                 ->assertJson([
                     'success' => true,
                     'message' => 'ユーザー登録成功'
                 ]);

        $this->assertDatabaseHas('users', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com'
        ]);
    }

    /** @test */
    public function user_cannot_register_with_invalid_data()
    {
        $userData = [
            'name' => '',
            'email' => 'invalid-email',
            'password' => '123',
            'password_confirmation' => '456'
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(400)
                 ->assertJsonStructure([
                     'success',
                     'errors',
                     'message'
                 ])
                 ->assertJson([
                     'success' => false
                 ]);
    }

    /** @test */
    public function user_cannot_register_with_duplicate_email()
    {
        User::factory()->create(['email' => 'test@example.com']);

        $userData = [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(400)
                 ->assertJsonStructure([
                     'success',
                     'errors',
                     'message'
                 ]);
    }

    /** @test */
    public function user_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123')
        ]);

        $loginData = [
            'email' => 'test@example.com',
            'password' => 'password123'
        ];

        $response = $this->postJson('/api/login', $loginData);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => ['user', 'token'],
                     'message'
                 ])
                 ->assertJson([
                     'success' => true,
                     'message' => 'ログイン成功'
                 ]);
    }

    /** @test */
    public function user_cannot_login_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123')
        ]);

        $loginData = [
            'email' => 'test@example.com',
            'password' => 'wrongpassword'
        ];

        $response = $this->postJson('/api/login', $loginData);

        $response->assertStatus(401)
                 ->assertJsonStructure([
                     'success',
                     'errors',
                     'message'
                 ])
                 ->assertJson([
                     'success' => false,
                     'message' => 'ログインに失敗しました'
                 ]);
    }

    /** @test */
    public function user_cannot_login_with_invalid_email_format()
    {
        $loginData = [
            'email' => 'invalid-email',
            'password' => 'password123'
        ];

        $response = $this->postJson('/api/login', $loginData);

        $response->assertStatus(400)
                 ->assertJsonStructure([
                     'success',
                     'errors',
                     'message'
                 ]);
    }

    /** @test */
    public function authenticated_user_can_logout()
    {
        $user = User::factory()->create();
        $token = $user->createToken('api-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson('/api/logout');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data',
                     'message'
                 ])
                 ->assertJson([
                     'success' => true,
                     'message' => 'ログアウト成功'
                 ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_logout()
    {
        $response = $this->postJson('/api/logout');

        $response->assertStatus(401);
    }

    /** @test */
    public function authenticated_user_can_get_user_info()
    {
        $user = User::factory()->create();
        $token = $user->createToken('api-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson('/api/user');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => ['user'],
                     'message'
                 ])
                 ->assertJson([
                     'success' => true,
                     'message' => 'ユーザー情報を取得しました',
                     'data' => [
                         'user' => [
                             'id' => $user->id,
                             'name' => $user->name,
                             'email' => $user->email
                         ]
                     ]
                 ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_get_user_info()
    {
        $response = $this->getJson('/api/user');

        $response->assertStatus(401);
    }
}