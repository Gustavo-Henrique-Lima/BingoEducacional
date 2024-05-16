<?php

namespace App\Services;

use App\Models\Category;

class CategoryService{

    /**
     * Retorna todas as categorias
     *
     * @return Collection
     */
    public function findAllCategories()
    {
        return Category::all();
    }
}