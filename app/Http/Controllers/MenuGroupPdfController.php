<?php

namespace App\Http\Controllers;

use App\Models\MenuGroup;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class MenuGroupPdfController extends Controller
{
    public function print(MenuGroup $menuGroup)
    {
        // Load relasi resep
        $menuGroup->load('recipes.recipe.recipeIngredients.ingredient');

        // Hitung kalkulasi bahan
        $ingredients = [];

        foreach ($menuGroup->recipes as $menuRecipe) {
            $recipe = $menuRecipe->recipe;
            $multiplier = $menuRecipe->requested_portions / $recipe->base_portions;

            foreach ($recipe->recipeIngredients as $ri) {
                $ingredient = $ri->ingredient;
                $key = $ingredient->name . '|' . $ingredient->unit;

                if (!isset($ingredients[$key])) {
                    $ingredients[$key] = 0;
                }

                $ingredients[$key] += $ri->amount * $multiplier;
            }
        }

        // Render PDF
        $pdf = Pdf::loadView('pdf.menu-group', [
            'menuGroup' => $menuGroup,
            'ingredients' => $ingredients,
        ]);

        return $pdf->stream('menu-group-' . $menuGroup->id . '.pdf');
    }
}
