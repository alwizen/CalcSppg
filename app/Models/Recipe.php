<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Recipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'base_portions',
        'is_active'
    ];

    protected $casts = ['base_portions' => 'integer', 'is_active' => 'boolean'];

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

    public function menuGroupRecipes(): HasMany
    {
        return $this->hasMany(MenuGroupRecipe::class);
    }

    // ✅ Tambahkan relasi ke MenuGroup (many-to-many melalui pivot)
    public function menuGroups(): BelongsToMany
    {
        return $this->belongsToMany(MenuGroup::class, 'menu_group_recipes')
            ->withTimestamps();
    }
}
