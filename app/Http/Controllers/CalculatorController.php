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

        $recipe = Recipe::with('ingredients')->findOrFail($request->recipe_id);
        $multiplier = $request->portions / $recipe->base_portions;

        $calculatedIngredients = $recipe->ingredients->map(function ($ingredient) use ($multiplier) {
            return [
                'name' => $ingredient->name,
                'original_amount' => $ingredient->amount,
                'calculated_amount' => round($ingredient->amount * $multiplier, 2),
                'unit' => $ingredient->unit
            ];
        });

        return response()->json([
            'recipe_name' => $recipe->name,
            'requested_portions' => $request->portions,
            'base_portions' => $recipe->base_portions,
            'multiplier' => round($multiplier, 2),
            'ingredients' => $calculatedIngredients
        ]);
    }
}
