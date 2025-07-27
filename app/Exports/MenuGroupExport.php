<?php

namespace App\Exports;

use App\Models\MenuGroup;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MenuGroupExport implements FromArray, WithHeadings
{
    protected $menuGroup;

    public function __construct(MenuGroup $menuGroup)
    {
        $this->menuGroup = $menuGroup->load('recipes.recipe.recipeIngredients.ingredient');
    }

    public function array(): array
    {
        $data = [];
        $no = 1;

        foreach ($this->menuGroup->recipes as $menuRecipe) {
            $recipe = $menuRecipe->recipe;
            $multiplier = $menuRecipe->requested_portions / $recipe->base_portions;
            $ingredients = $recipe->recipeIngredients;

            foreach ($ingredients as $index => $ri) {
                $ingredient = $ri->ingredient;
                $amount = round($ri->amount * $multiplier, 2);

                $data[] = [
                    'no'          => $index === 0 ? $no : '',
                    'menu'        => $index === 0 ? $recipe->name : '',
                    'porsi'       => $index === 0 ? $menuRecipe->requested_portions : '',
                    'bahan'       => $ingredient->name,
                    'jumlah'      => $amount,
                    'satuan'      => $ingredient->unit,
                ];
            }

            $no++;
        }

        return $data;
    }

    public function headings(): array
    {
        return ['No', 'Nama Menu', 'Porsi', 'Bahan-bahan', 'Jumlah', 'Satuan'];
    }
}
