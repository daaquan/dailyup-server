<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_model_uses_soft_deletes_trait()
    {
        $user = new User();

        $this->assertContains('Illuminate\Database\Eloquent\SoftDeletes', class_uses_recursive($user));
    }

    /** @test */
    public function user_model_uses_has_api_tokens_trait()
    {
        $user = new User();

        $this->assertContains('Laravel\Sanctum\HasApiTokens', class_uses_recursive($user));
    }

    /** @test */
    public function user_model_uses_has_factory_trait()
    {
        $user = new User();

        $this->assertContains('Illuminate\Database\Eloquent\Factories\HasFactory', class_uses_recursive($user));
    }

    /** @test */
    public function user_model_uses_notifiable_trait()
    {
        $user = new User();

        $this->assertContains('Illuminate\Notifications\Notifiable', class_uses_recursive($user));
    }

    /** @test */
    public function user_has_fillable_attributes()
    {
        $user = new User();
        $expectedFillable = ['name', 'email', 'password', 'roles'];

        $this->assertEquals($expectedFillable, $user->getFillable());
    }

    /** @test */
    public function user_has_hidden_attributes()
    {
        $user = new User();
        $expectedHidden = ['password', 'remember_token'];

        $this->assertEquals($expectedHidden, $user->getHidden());
    }

    /** @test */
    public function user_has_correct_casts()
    {
        $user = new User();
        $expectedCasts = [
            'id' => 'int',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'deleted_at' => 'datetime',
        ];

        $this->assertEquals($expectedCasts, $user->getCasts());
    }

    /** @test */
    public function is_admin_returns_true_when_roles_is_admin()
    {
        $admin = new User(['roles' => 'admin']);

        $this->assertTrue($admin->isAdmin());
    }

    /** @test */
    public function is_admin_returns_false_when_roles_is_not_admin()
    {
        $user = new User(['roles' => 'user']);
        $userWithoutRole = new User();

        $this->assertFalse($user->isAdmin());
        $this->assertFalse($userWithoutRole->isAdmin());
    }

    /** @test */
    public function user_can_be_created_with_factory()
    {
        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);
        $this->assertNotNull($user->name);
        $this->assertNotNull($user->email);
        $this->assertNotNull($user->password);
    }

    /** @test */
    public function user_can_be_soft_deleted()
    {
        $user = User::factory()->create();

        $user->delete();

        $this->assertSoftDeleted($user);
        $this->assertNotNull($user->deleted_at);
    }

    /** @test */
    public function soft_deleted_user_can_be_restored()
    {
        $user = User::factory()->create();

        $user->delete();
        $user->restore();

        $this->assertNotSoftDeleted($user);
        $this->assertNull($user->deleted_at);
    }

    /** @test */
    public function user_can_create_api_tokens()
    {
        $user = User::factory()->create();

        $token = $user->createToken('test-token');

        $this->assertNotNull($token);
        $this->assertNotEmpty($token->plainTextToken);
    }

    /** @test */
    public function user_can_have_multiple_api_tokens()
    {
        $user = User::factory()->create();

        $token1 = $user->createToken('token1');
        $token2 = $user->createToken('token2');

        $this->assertNotEquals($token1->plainTextToken, $token2->plainTextToken);
        $this->assertEquals(2, $user->tokens()->count());
    }
}