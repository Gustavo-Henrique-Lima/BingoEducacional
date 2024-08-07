<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Question;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(2)->create();
        Category::factory(4)->create();
        $this->call(QuestionSeeder::class);
    }
}
