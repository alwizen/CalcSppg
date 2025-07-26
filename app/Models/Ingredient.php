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

    public function menus(): BelongsTo
    {
        return $this->belongsTo(RecipeIngredient::class);
    }
}
