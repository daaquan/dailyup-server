<?php

namespace Tests;

use App\Models\Topic;
use Laravel\Lumen\Testing\DatabaseMigrations;

class TopicApiTest extends TestCase
{
    use DatabaseMigrations;

    public function test_topics_index_returns_paginated_list()
    {
        Topic::factory()->count(3)->create();

        $this->get('/api/v1/topics');
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'topics' => [
                '*' => ['id', 'title', 'url', 'published_at'],
            ],
            'meta' => ['current_page', 'per_page', 'total'],
        ]);
    }
}
