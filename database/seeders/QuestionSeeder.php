<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Question;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{

    public function run(): void
    {
        $category = Category::firstOrCreate(["name" => "Torrance Spencer"]);

        Question::create([
            "question" => "What is the capital of France?",
            "answer" => "Paris",
            "category_id" => $category->id,
        ]);

        Question::create([
            "question" => "What is the largest planet in our solar system?",
            "answer" => "Jupiter",
            "category_id" => $category->id,
        ]);

        $category2 = Category::firstOrCreate(['name' => 'IHC']);
        Question::create([
            'question' => 'Who wrote "Romeo and Juliet"?',
            'answer' => 'William Shakespeare',
            'category_id' => $category2->id,
        ]);

        Question::create([
            'question' => 'What is the chemical symbol for water?',
            'answer' => 'H2O',
            'category_id' => $category2->id,
        ]);
    }
}
