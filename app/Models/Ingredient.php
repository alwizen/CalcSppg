<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ingredient extends Model
{
    protected $fillable = [
        'name',
        'unit'
    ];

    public function recipeIngredients()
    {
        return $this->hasMany(RecipeIngredient::class);
    }

    public function recipes()
    {
        return $this->belongsToMany(Recipe::class, 'recipe_ingredients')
            ->withPivot('amount')
            ->withTimestamps();
    }
}
