<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Recipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'base_portions',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($recipe) {
            $recipe->slug = Str::slug($recipe->name);
        });
    }

    public function recipeIngredients(): HasMany
    {
        return $this->hasMany(RecipeIngredient::class);
    }

    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class, 'recipe_ingredients')
            ->withPivot('amount')
            ->withTimestamps();
    }
}
