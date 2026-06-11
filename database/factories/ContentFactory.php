<?php

namespace Database\Factories;

use App\Content\Models\Content;
use App\Models\Platform;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContentFactory extends Factory
{
    protected $model = Content::class;

    public function definition(): array
    {
        return [
            'platform_id' => Platform::factory(),
            'theme' => fake()->sentence(3),
            'content_code' => strtoupper(fake()->unique()->bothify('???-####')),
            'status' => 'draft',
            'priority' => 'medium',
            'version' => 1,
        ];
    }
}
