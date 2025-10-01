<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function authenticated_user_can_update_profile()
    {
        $user = User::factory()->create([
            'name' => '古い名前',
            'email' => 'old@example.com'
        ]);
        $token = $user->createToken('api-token')->plainTextToken;

        $updateData = [
            'name' => '新しい名前',
            'email' => 'new@example.com'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->putJson('/api/user/profile', $updateData);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => ['user'],
                     'message'
                 ])
                 ->assertJson([
                     'success' => true,
                     'message' => 'プロフィールを更新しました',
                     'data' => [
                         'user' => [
                             'name' => '新しい名前',
                             'email' => 'new@example.com'
                         ]
                     ]
                 ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => '新しい名前',
            'email' => 'new@example.com'
        ]);
    }

    /** @test */
    public function authenticated_user_cannot_update_profile_with_invalid_data()
    {
        $user = User::factory()->create();
        $token = $user->createToken('api-token')->plainTextToken;

        $updateData = [
            'name' => '',
            'email' => 'invalid-email'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->putJson('/api/user/profile', $updateData);

        $response->assertStatus(422)
                 ->assertJsonStructure([
                     'message',
                     'errors'
                 ]);
    }

    /** @test */
    public function authenticated_user_cannot_update_profile_with_existing_email()
    {
        $user1 = User::factory()->create(['email' => 'user1@example.com']);
        $user2 = User::factory()->create(['email' => 'user2@example.com']);
        $token = $user1->createToken('api-token')->plainTextToken;

        $updateData = [
            'name' => '新しい名前',
            'email' => 'user2@example.com' // 既に存在するメールアドレス
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->putJson('/api/user/profile', $updateData);

        $response->assertStatus(422)
                 ->assertJsonStructure([
                     'message',
                     'errors'
                 ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_update_profile()
    {
        $updateData = [
            'name' => '新しい名前',
            'email' => 'new@example.com'
        ];

        $response = $this->putJson('/api/user/profile', $updateData);

        $response->assertStatus(401);
    }

    /** @test */
    public function authenticated_user_can_change_password()
    {
        $user = User::factory()->create([
            'password' => bcrypt('oldpassword')
        ]);
        $token = $user->createToken('api-token')->plainTextToken;

        $changeData = [
            'current_password' => 'oldpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson('/api/user/change-password', $changeData);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data',
                     'message'
                 ])
                 ->assertJson([
                     'success' => true,
                     'message' => 'パスワードを変更しました'
                 ]);
    }

    /** @test */
    public function authenticated_user_cannot_change_password_with_wrong_current_password()
    {
        $user = User::factory()->create([
            'password' => bcrypt('oldpassword')
        ]);
        $token = $user->createToken('api-token')->plainTextToken;

        $changeData = [
            'current_password' => 'wrongpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson('/api/user/change-password', $changeData);

        $response->assertStatus(422)
                 ->assertJsonStructure([
                     'message',
                     'errors'
                 ]);
    }

    /** @test */
    public function authenticated_user_cannot_change_password_with_invalid_data()
    {
        $user = User::factory()->create();
        $token = $user->createToken('api-token')->plainTextToken;

        $changeData = [
            'current_password' => 'oldpassword',
            'password' => '123',
            'password_confirmation' => '456'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson('/api/user/change-password', $changeData);

        $response->assertStatus(422)
                 ->assertJsonStructure([
                     'message',
                     'errors'
                 ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_change_password()
    {
        $changeData = [
            'current_password' => 'oldpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123'
        ];

        $response = $this->postJson('/api/user/change-password', $changeData);

        $response->assertStatus(401);
    }

    /** @test */
    public function authenticated_user_can_delete_account()
    {
        $user = User::factory()->create();
        $token = $user->createToken('api-token')->plainTextToken;

        $deleteData = [
            'password' => 'password'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->deleteJson('/api/user/account', $deleteData);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data',
                     'message'
                 ])
                 ->assertJson([
                     'success' => true,
                     'message' => 'アカウントを削除しました'
                 ]);

        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    /** @test */
    public function authenticated_user_cannot_delete_account_with_wrong_password()
    {
        $user = User::factory()->create();
        $token = $user->createToken('api-token')->plainTextToken;

        $deleteData = [
            'password' => 'wrongpassword'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->deleteJson('/api/user/account', $deleteData);

        $response->assertStatus(422)
                 ->assertJsonStructure([
                     'message',
                     'errors'
                 ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_delete_account()
    {
        $deleteData = [
            'password' => 'password'
        ];

        $response = $this->deleteJson('/api/user/account', $deleteData);

        $response->assertStatus(401);
    }
}