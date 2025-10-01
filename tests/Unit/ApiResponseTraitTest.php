<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Tests\TestCase;

class ApiResponseTraitTest extends TestCase
{
    use ApiResponseTrait;

    /** @test */
    public function success_response_returns_correct_structure()
    {
        $data = ['user' => ['id' => 1, 'name' => 'テストユーザー']];
        $message = '成功メッセージ';
        $status = 201;

        $response = $this->successResponse($data, $message, $status);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($status, $response->getStatusCode());

        $responseData = $response->getData(true);
        $this->assertEquals(true, $responseData['success']);
        $this->assertEquals($data, $responseData['data']);
        $this->assertEquals($message, $responseData['message']);
    }

    /** @test */
    public function success_response_works_with_null_data()
    {
        $message = '成功メッセージ';

        $response = $this->successResponse(null, $message);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $responseData = $response->getData(true);
        $this->assertEquals(true, $responseData['success']);
        $this->assertNull($responseData['data']);
        $this->assertEquals($message, $responseData['message']);
    }

    /** @test */
    public function success_response_works_with_empty_message()
    {
        $data = ['key' => 'value'];

        $response = $this->successResponse($data, '');

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $responseData = $response->getData(true);
        $this->assertEquals(true, $responseData['success']);
        $this->assertEquals($data, $responseData['data']);
        $this->assertEquals('', $responseData['message']);
    }

    /** @test */
    public function success_response_defaults_to_200_status()
    {
        $data = ['test' => 'data'];
        $message = 'テストメッセージ';

        $response = $this->successResponse($data, $message);

        $this->assertEquals(200, $response->getStatusCode());
    }

    /** @test */
    public function error_response_returns_correct_structure()
    {
        $errors = ['email' => ['メールアドレスは必須です'], 'password' => ['パスワードは必須です']];
        $message = '入力エラーが発生しました';
        $status = 422;

        $response = $this->errorResponse($errors, $message, $status);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($status, $response->getStatusCode());

        $responseData = $response->getData(true);
        $this->assertEquals(false, $responseData['success']);
        $this->assertEquals($errors, $responseData['errors']);
        $this->assertEquals($message, $responseData['message']);
    }

    /** @test */
    public function error_response_works_with_empty_errors()
    {
        $errors = [];
        $message = '一般的なエラーメッセージ';

        $response = $this->errorResponse($errors, $message);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(400, $response->getStatusCode());

        $responseData = $response->getData(true);
        $this->assertEquals(false, $responseData['success']);
        $this->assertEquals($errors, $responseData['errors']);
        $this->assertEquals($message, $responseData['message']);
    }

    /** @test */
    public function error_response_works_with_empty_message()
    {
        $errors = ['field' => ['エラーメッセージ']];

        $response = $this->errorResponse($errors, '');

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(400, $response->getStatusCode());

        $responseData = $response->getData(true);
        $this->assertEquals(false, $responseData['success']);
        $this->assertEquals($errors, $responseData['errors']);
        $this->assertEquals('', $responseData['message']);
    }

    /** @test */
    public function error_response_defaults_to_400_status()
    {
        $errors = ['test' => ['error']];
        $message = 'テストエラー';

        $response = $this->errorResponse($errors, $message);

        $this->assertEquals(400, $response->getStatusCode());
    }

    /** @test */
    public function success_response_preserves_data_types()
    {
        $data = [
            'string' => 'テキスト',
            'integer' => 42,
            'boolean' => true,
            'array' => ['item1', 'item2'],
            'object' => ['nested' => 'value']
        ];

        $response = $this->successResponse($data, 'テスト');

        $responseData = $response->getData(true);
        $this->assertEquals($data, $responseData['data']);
        $this->assertIsString($responseData['data']['string']);
        $this->assertIsInt($responseData['data']['integer']);
        $this->assertIsBool($responseData['data']['boolean']);
        $this->assertIsArray($responseData['data']['array']);
        $this->assertIsArray($responseData['data']['object']);
    }

    /** @test */
    public function error_response_preserves_error_structure()
    {
        $errors = [
            'email' => ['メールアドレスは必須です', '有効なメールアドレスを入力してください'],
            'password' => ['パスワードは必須です'],
            'name' => ['名前は必須です']
        ];

        $response = $this->errorResponse($errors, 'バリデーションエラー');

        $responseData = $response->getData(true);
        $this->assertEquals($errors, $responseData['errors']);
        $this->assertCount(3, $responseData['errors']);
        $this->assertCount(2, $responseData['errors']['email']);
        $this->assertCount(1, $responseData['errors']['password']);
        $this->assertCount(1, $responseData['errors']['name']);
    }
}