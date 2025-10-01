<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function admin_can_get_users_list()
    {
        $admin = User::factory()->create(['roles' => 'admin']);
        $adminToken = $admin->createToken('api-token')->plainTextToken;

        User::factory()->count(5)->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $adminToken
        ])->getJson('/api/users');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'users',
                         'pagination' => [
                             'current_page',
                             'last_page',
                             'per_page',
                             'total'
                         ]
                     ],
                     'message'
                 ])
                 ->assertJson([
                     'success' => true,
                     'message' => 'ユーザー一覧を取得しました'
                 ]);
    }

    /** @test */
    public function non_admin_cannot_get_users_list()
    {
        $user = User::factory()->create(['roles' => 'user']);
        $token = $user->createToken('api-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson('/api/users');

        $response->assertStatus(403)
                 ->assertJsonStructure([
                     'success',
                     'errors',
                     'message'
                 ])
                 ->assertJson([
                     'success' => false,
                     'message' => 'アクセスが拒否されました'
                 ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_get_users_list()
    {
        $response = $this->getJson('/api/users');

        $response->assertStatus(401);
    }

    /** @test */
    public function admin_can_get_specific_user()
    {
        $admin = User::factory()->create(['roles' => 'admin']);
        $adminToken = $admin->createToken('api-token')->plainTextToken;

        $targetUser = User::factory()->create([
            'name' => '対象ユーザー',
            'email' => 'target@example.com'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $adminToken
        ])->getJson('/api/users/' . $targetUser->id);

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
                             'id' => $targetUser->id,
                             'name' => '対象ユーザー',
                             'email' => 'target@example.com'
                         ]
                     ]
                 ]);
    }

    /** @test */
    public function admin_cannot_get_nonexistent_user()
    {
        $admin = User::factory()->create(['roles' => 'admin']);
        $adminToken = $admin->createToken('api-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $adminToken
        ])->getJson('/api/users/99999');

        $response->assertStatus(404);
    }

    /** @test */
    public function non_admin_cannot_get_specific_user()
    {
        $user = User::factory()->create(['roles' => 'user']);
        $token = $user->createToken('api-token')->plainTextToken;

        $targetUser = User::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson('/api/users/' . $targetUser->id);

        $response->assertStatus(403)
                 ->assertJsonStructure([
                     'success',
                     'errors',
                     'message'
                 ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_get_specific_user()
    {
        $targetUser = User::factory()->create();

        $response = $this->getJson('/api/users/' . $targetUser->id);

        $response->assertStatus(401);
    }

    /** @test */
    public function admin_can_search_users()
    {
        $admin = User::factory()->create(['roles' => 'admin']);
        $adminToken = $admin->createToken('api-token')->plainTextToken;

        User::factory()->create(['name' => '山田太郎', 'email' => 'yamada@example.com']);
        User::factory()->create(['name' => '佐藤花子', 'email' => 'sato@example.com']);
        User::factory()->create(['name' => '鈴木次郎', 'email' => 'suzuki@example.com']);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $adminToken
        ])->getJson('/api/users/search?q=山田');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'users',
                         'pagination' => [
                             'current_page',
                             'last_page',
                             'per_page',
                             'total'
                         ]
                     ],
                     'message'
                 ])
                 ->assertJson([
                     'success' => true,
                     'message' => 'ユーザーを検索しました'
                 ]);

        $responseData = $response->json();
        $this->assertCount(1, $responseData['data']['users']);
        $this->assertEquals('山田太郎', $responseData['data']['users'][0]['name']);
    }

    /** @test */
    public function admin_can_search_users_by_email()
    {
        $admin = User::factory()->create(['roles' => 'admin']);
        $adminToken = $admin->createToken('api-token')->plainTextToken;

        User::factory()->create(['name' => '山田太郎', 'email' => 'yamada@example.com']);
        User::factory()->create(['name' => '佐藤花子', 'email' => 'sato@example.com']);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $adminToken
        ])->getJson('/api/users/search?q=example.com');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'ユーザーを検索しました'
                 ]);

        $responseData = $response->json();
        $this->assertCount(2, $responseData['data']['users']);
    }

    /** @test */
    public function admin_search_returns_empty_when_no_matches()
    {
        $admin = User::factory()->create(['roles' => 'admin']);
        $adminToken = $admin->createToken('api-token')->plainTextToken;

        User::factory()->count(3)->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $adminToken
        ])->getJson('/api/users/search?q=nonexistent');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'ユーザーを検索しました'
                 ]);

        $responseData = $response->json();
        $this->assertCount(0, $responseData['data']['users']);
        $this->assertEquals(0, $responseData['data']['pagination']['total']);
    }

    /** @test */
    public function non_admin_cannot_search_users()
    {
        $user = User::factory()->create(['roles' => 'user']);
        $token = $user->createToken('api-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson('/api/users/search?q=test');

        $response->assertStatus(403)
                 ->assertJsonStructure([
                     'success',
                     'errors',
                     'message'
                 ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_search_users()
    {
        $response = $this->getJson('/api/users/search?q=test');

        $response->assertStatus(401);
    }
}