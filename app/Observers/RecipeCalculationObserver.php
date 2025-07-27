<?php

namespace App\Observers;

use App\Models\Recipe;
use App\Models\RecipeCalculation;
use App\Models\RecipeCalculationIngredient;

class RecipeCalculationObserver
{
    public function created(RecipeCalculation $recipeCalculation): void
    {
        \Log::info('=== OBSERVER CREATED CALLED ===');
        \Log::info('Record ID: ' . $recipeCalculation->id);
        \Log::info('Recipe ID: ' . $recipeCalculation->recipe_id);

        // Load recipe dengan relasi yang diperlukan
        $recipe = Recipe::with('recipeIngredients.ingredient')->find($recipeCalculation->recipe_id);

        if (!$recipe) {
            \Log::error('Recipe not found: ' . $recipeCalculation->recipe_id);
            return;
        }

        \Log::info('Recipe found: ' . $recipe->name);
        \Log::info('Base portions: ' . $recipe->base_portions);
        \Log::info('Recipe ingredients count: ' . $recipe->recipeIngredients->count());

        if ($recipe->base_portions == 0) {
            \Log::error('Recipe base_portions is 0 for recipe: ' . $recipe->name);
            return;
        }

        if ($recipe->recipeIngredients->count() == 0) {
            \Log::error('No ingredients found for recipe: ' . $recipe->name);
            return;
        }

        $multiplier = $recipeCalculation->requested_portions / $recipe->base_portions;
        \Log::info('Multiplier: ' . $multiplier);

        foreach ($recipe->recipeIngredients as $recipeIngredient) {
            $ingredient = $recipeIngredient->ingredient;

            if (!$ingredient) {
                \Log::error('Ingredient not found for recipe ingredient: ' . $recipeIngredient->id);
                continue;
            }

            $calculatedAmount = round($recipeIngredient->amount * $multiplier, 2);

            \Log::info("Processing: {$ingredient->name} - Original: {$recipeIngredient->amount}, Calculated: {$calculatedAmount}");

            try {
                $calculatedIngredient = RecipeCalculationIngredient::create([
                    'recipe_calculation_id' => $recipeCalculation->id,
                    'ingredient_id' => $ingredient->id,
                    'calculated_amount' => $calculatedAmount,
                    'unit' => $ingredient->unit,
                ]);

                \Log::info("Successfully created calculated ingredient ID: " . $calculatedIngredient->id);
            } catch (\Exception $e) {
                \Log::error("Error creating calculated ingredient: " . $e->getMessage());
            }
        }

        \Log::info('=== OBSERVER FINISHED ===');
    }
}
