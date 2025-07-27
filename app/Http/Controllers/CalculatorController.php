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
}
