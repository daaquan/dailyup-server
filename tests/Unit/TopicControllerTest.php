<?php
namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Phalcon\Mvc\Micro;
use Phalcon\Di\DiInterface;

class TopicControllerTest extends TestCase
{
    private Micro $app;
    private DiInterface $di;

    protected function setUp(): void
    {
        $this->app = require __DIR__ . '/../bootstrap.php';
        $this->di = $this->app->getDi();
        require_once __DIR__ . "/../../database/migrations/001_create_topics.php";
        // run migration
        $migration = new \CreateTopics();
        $migration->up($this->di->get('db'));
        // insert sample
        $db = $this->di->get('db');
        $db->execute("INSERT INTO topics (title, url, category, published_at, created_at, updated_at) VALUES ('Test', 'http://example.com', 'news', '2024-01-01 00:00:00', datetime('now'), datetime('now'))");

        // routes
        require __DIR__ . '/../../app/routes.php';
    }

    public function testIndexSuccess()
    {
        $jwt = $this->di->get(\App\Services\JwtService::class);
        $token = $jwt->issue('1');
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/api/v1/topics?category=news&page=1&per_page=10';
        $_SERVER['HTTP_AUTHORIZATION'] = 'Bearer ' . $token;

        ob_start();
        $this->app->handle('/api/v1/topics');
        $content = ob_get_clean();
        $this->assertStringContainsString('topics', $content);
    }

    public function testUnauthorized()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/api/v1/topics?category=news&page=1&per_page=10';
        ob_start();
        $this->app->handle('/api/v1/topics');
        $content = ob_get_clean();
        $this->assertEmpty($content); // response 401 with no content
    }

    public function testValidationError()
    {
        $jwt = $this->di->get(\App\Services\JwtService::class);
        $token = $jwt->issue('1');
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/api/v1/topics?page=abc';
        $_SERVER['HTTP_AUTHORIZATION'] = 'Bearer ' . $token;

        ob_start();
        $this->app->handle('/api/v1/topics');
        $content = ob_get_clean();
        $this->assertStringContainsString('errors', $content);
    }
}
