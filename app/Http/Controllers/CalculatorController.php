<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Http\Request;

class CalculatorController extends Controller
{
    public function index()
    {
        $recipes = Recipe::where('is_active', true)
            ->with('ingredients')
            ->orderBy('name')
            ->get();

        return view('calculator.index', compact('recipes'));
    }

    public function calculate(Request $request)
    {
        $request->validate([
            'recipe_id' => 'required|exists:recipes,id',
            'portions' => 'required|numeric|min:1'
        ]);

        $recipe = Recipe::with('recipeIngredients.ingredient')->findOrFail($request->recipe_id);
        $multiplier = $request->portions / $recipe->base_portions;

        $calculatedIngredients = $recipe->recipeIngredients->map(function ($ri) use ($multiplier) {
            return [
                'name' => $ri->ingredient->name,
                'original_amount' => $ri->amount,
                'calculated_amount' => round($ri->amount * $multiplier, 2),
                'unit' => $ri->ingredient->unit,
            ];
        });

        return response()->json([
            'recipe_name' => $recipe->name,
            'requested_portions' => (int) $request->portions,
            'base_portions' => $recipe->base_portions,
            'multiplier' => round($multiplier, 2),
            'ingredients' => $calculatedIngredients
        ]);
    }

    public function calculateMultiple(Request $request)
    {
        $request->validate([
            'recipe_ids' => 'required|array|min:1',
            'recipe_ids.*' => 'exists:recipes,id',
            'portions' => 'required|numeric|min:1'
        ]);

        $recipeIds = $request->recipe_ids;
        $requestedPortions = $request->portions;

        // Get all selected recipes with their ingredients
        $recipes = Recipe::with('recipeIngredients.ingredient')
            ->whereIn('id', $recipeIds)
            ->get();

        // Array to store combined ingredients
        $combinedIngredients = [];
        $recipeDetails = [];

        foreach ($recipes as $recipe) {
            // Calculate multiplier for each recipe
            $multiplier = $requestedPortions / $recipe->base_portions;

            // Store recipe details
            $recipeDetails[] = [
                'name' => $recipe->name,
                'base_portions' => $recipe->base_portions,
                'multiplier' => round($multiplier, 2)
            ];

            // Process each ingredient in the recipe
            foreach ($recipe->recipeIngredients as $recipeIngredient) {
                $ingredient = $recipeIngredient->ingredient;
                $calculatedAmount = $recipeIngredient->amount * $multiplier;

                // Check if ingredient already exists in combined array
                $existingKey = null;
                foreach ($combinedIngredients as $key => $existingIngredient) {
                    if (
                        $existingIngredient['name'] === $ingredient->name &&
                        $existingIngredient['unit'] === $ingredient->unit
                    ) {
                        $existingKey = $key;
                        break;
                    }
                }

                if ($existingKey !== null) {
                    // Add to existing ingredient
                    $combinedIngredients[$existingKey]['calculated_amount'] += $calculatedAmount;
                    $combinedIngredients[$existingKey]['calculated_amount'] = round($combinedIngredients[$existingKey]['calculated_amount'], 2);

                    // Add recipe source info
                    $combinedIngredients[$existingKey]['from_recipes'][] = $recipe->name;
                } else {
                    // Add new ingredient
                    $combinedIngredients[] = [
                        'name' => $ingredient->name,
                        'calculated_amount' => round($calculatedAmount, 2),
                        'unit' => $ingredient->unit,
                        'from_recipes' => [$recipe->name]
                    ];
                }
            }
        }

        // Sort ingredients by name
        usort($combinedIngredients, function ($a, $b) {
            return strcmp($a['name'], $b['name']);
        });

        return response()->json([
            'ingredients' => $combinedIngredients,
            'total_recipes' => count($recipes),
            'requested_portions' => (int) $requestedPortions,
            'recipe_details' => $recipeDetails,
            'message' => 'Perhitungan berhasil untuk ' . count($recipes) . ' menu'
        ]);
    }
}
