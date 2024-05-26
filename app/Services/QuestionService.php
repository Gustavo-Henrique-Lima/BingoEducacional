<?php

namespace App\Services;

use App\Models\Question;

class QuestionService
{
    /**
     * Salvar uma nova questão
     *
     * @param Question $question
     * @return bool
     */
    public function saveQuestion(Question $question): bool
    {
        return $question->save();
    }

    /**
     * Retornar todas as perguntas filtradas pelo category_id
     *
     * @param int $categoryId
     * @return Collection
     */
    public function getQuestionsByCategoryId(int $categoryId)
    {
        return Question::where('category_id', $categoryId)->get();
    }
}
