<?php

declare(strict_types = 1);

namespace Database\Factories\Api;

use App\Models\Api\Post;
use App\Models\Api\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Api\Post
 * >
 */
class PostFactory extends Factory
{
    protected $model = Post::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'content' => fake()->paragraph(),
            'user_id' => User::factory(),
            'tags' => ['Laravel, PHP, API'],
        ];
    }
}
