<?php

namespace Tests\Unit;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserResourceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_resource_returns_correct_structure()
    {
        $user = User::factory()->create([
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $resource = new UserResource($user);
        $resourceArray = $resource->toArray(request());

        $expectedKeys = ['id', 'name', 'email', 'email_verified_at', 'created_at', 'updated_at'];

        foreach ($expectedKeys as $key) {
            $this->assertArrayHasKey($key, $resourceArray);
        }
    }

    /** @test */
    public function user_resource_returns_correct_data()
    {
        $user = User::factory()->create([
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
        ]);

        $resource = new UserResource($user);
        $resourceArray = $resource->toArray(request());

        $this->assertEquals($user->id, $resourceArray['id']);
        $this->assertEquals('テストユーザー', $resourceArray['name']);
        $this->assertEquals('test@example.com', $resourceArray['email']);
        $this->assertEquals($user->email_verified_at, $resourceArray['email_verified_at']);
        $this->assertEquals($user->created_at, $resourceArray['created_at']);
        $this->assertEquals($user->updated_at, $resourceArray['updated_at']);
    }

    /** @test */
    public function user_resource_excludes_sensitive_data()
    {
        $user = User::factory()->create([
            'password' => 'hashedpassword',
            'remember_token' => 'token123',
        ]);

        $resource = new UserResource($user);
        $resourceArray = $resource->toArray(request());

        $this->assertArrayNotHasKey('password', $resourceArray);
        $this->assertArrayNotHasKey('remember_token', $resourceArray);
        $this->assertArrayNotHasKey('deleted_at', $resourceArray);
    }

    /** @test */
    public function user_resource_handles_null_email_verified_at()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $resource = new UserResource($user);
        $resourceArray = $resource->toArray(request());

        $this->assertNull($resourceArray['email_verified_at']);
    }

    /** @test */
    public function user_resource_collection_works()
    {
        $users = User::factory()->count(3)->create();

        $resourceCollection = UserResource::collection($users);
        $resourceArray = $resourceCollection->toArray(request());

        $this->assertIsArray($resourceArray);
        $this->assertCount(3, $resourceArray);

        foreach ($resourceArray as $userData) {
            $this->assertArrayHasKey('id', $userData);
            $this->assertArrayHasKey('name', $userData);
            $this->assertArrayHasKey('email', $userData);
            $this->assertArrayHasKey('email_verified_at', $userData);
            $this->assertArrayHasKey('created_at', $userData);
            $this->assertArrayHasKey('updated_at', $userData);
        }
    }

    /** @test */
    public function user_resource_preserves_datetime_format()
    {
        $createdAt = now()->subDays(1);
        $updatedAt = now();
        $verifiedAt = now()->subHours(2);

        $user = User::factory()->create([
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
            'email_verified_at' => $verifiedAt,
        ]);

        $resource = new UserResource($user);
        $resourceArray = $resource->toArray(request());

        $this->assertEquals($createdAt->format('Y-m-d H:i:s'), $resourceArray['created_at']->format('Y-m-d H:i:s'));
        $this->assertEquals($updatedAt->format('Y-m-d H:i:s'), $resourceArray['updated_at']->format('Y-m-d H:i:s'));
        $this->assertEquals($verifiedAt->format('Y-m-d H:i:s'), $resourceArray['email_verified_at']->format('Y-m-d H:i:s'));
    }
}