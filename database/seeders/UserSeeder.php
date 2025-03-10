<?php

declare(strict_types = 1);

namespace Database\Seeders;

use App\Models\Api\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory(10)->create();
    }
}
