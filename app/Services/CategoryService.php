<?php

namespace App\Services;

use App\Models\Category;

class CategoryService
{

    /**
     * Retorna todas as categorias
     *
     * @return Collection
     */
    public function findAllCategories()
    {
        return Category::all();
    }

    /**
     * Salva uma nova categoria
     *
     * @param Category $category
     * @return bool
     */
    public function saveCategory(Category $category): bool
    {
        return $category->save();
    }

    /**
     * Encontrar uma categoria
     *
     * @param string $name
     * @return Category|null
     */
    public function findCategory(string $name): ?Category
    {
        return Category::where("name", $name)->first();
    }
}
