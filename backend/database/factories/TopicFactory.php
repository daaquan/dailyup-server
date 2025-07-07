<?php

namespace Database\Factories;

use App\Models\Topic;
use Illuminate\Database\Eloquent\Factories\Factory;

class TopicFactory extends Factory
{
    protected $model = Topic::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'url' => $this->faker->url,
            'category' => $this->faker->word,
            'published_at' => $this->faker->dateTime,
        ];
    }
}
