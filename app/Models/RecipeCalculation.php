<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RecipeCalculation extends Model
{
    protected $fillable = [
        'recipe_id',
        'requested_portions'
    ];

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    public function calculatedIngredients(): HasMany
    {
        return $this->hasMany(RecipeCalculationIngredient::class);
    }
}
