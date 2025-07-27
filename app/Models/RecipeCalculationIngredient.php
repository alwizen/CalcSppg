<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecipeCalculationIngredient extends Model
{
    protected $fillable = [
        'recipe_calculation_id',
        'ingredient_id',
        'calculated_amount',
        'unit'
    ];

    public function recipeCalculation(): BelongsTo
    {
        return $this->belongsTo(RecipeCalculation::class);
    }

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }
}
