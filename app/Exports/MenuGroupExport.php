<?php

namespace App\Exports;

use App\Models\MenuGroup;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MenuGroupExport implements FromArray, WithHeadings
{
    protected MenuGroup $menuGroup;

    public function __construct(MenuGroup $menuGroup)
    {
        // load relasi yang dibutuhkan
        $this->menuGroup = $menuGroup->load('recipes.recipe.recipeIngredients.ingredient');
    }

    public function array(): array
    {
        $data = [];
        $no = 1;

        // porsi global per menu group (fallback 1)
        $groupPortions = (int) ($this->menuGroup->requested_portions ?? 1);
        if ($groupPortions < 1) {
            $groupPortions = 1;
        }

        foreach ($this->menuGroup->recipes as $menuRecipe) {
            $recipe = $menuRecipe->recipe;

            // base_portions aman dari null/0
            $base = (float) ($recipe->base_portions ?? 1);
            if ($base <= 0) {
                $base = 1;
            }

            $multiplier = $groupPortions / $base;

            foreach ($recipe->recipeIngredients as $idx => $ri) {
                $ingredient = $ri->ingredient;
                $amount = round(((float) $ri->amount) * $multiplier, 2);

                $data[] = [
                    'no'     => $idx === 0 ? $no : '',
                    'menu'   => $idx === 0 ? $recipe->name : '',
                    'porsi'  => $idx === 0 ? $groupPortions : '',
                    'bahan'  => $ingredient->name ?? '-',
                    'jumlah' => $amount,
                    'satuan' => strtoupper($ingredient->unit ?? ''),
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
