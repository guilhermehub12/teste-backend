<?php

declare(strict_types = 1);

namespace Database\Seeders\Api;

use App\Models\Api\Post;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Post::factory(10)->create();
    }
}
