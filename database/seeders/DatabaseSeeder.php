<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //User::factory(4)->create();
        Category::factory(4)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test1@example.com',
        // ]);
    }
}
