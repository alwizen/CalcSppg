<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuGroup extends Model
{
    protected $fillable = ['date', 'name', 'sppg_id', 'created_by', 'requested_portions'];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function recipes(): HasMany
    {
        return $this->hasMany(MenuGroupRecipe::class);
    }

    public function sppg(): BelongsTo
    {
        return $this->belongsTo(Sppg::class);
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(SupplierAllocation::class);
    }

    // mendapatkan semua ingredients dari menu group
    public function getAllIngredients()
    {
        return Ingredient::whereHas('recipes.menuGroupRecipes', function ($query) {
            $query->where('menu_group_id', $this->id);
        })->with('suppliers')->get();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (auth()->check() && !$model->created_by) {
                $model->created_by = auth()->id();
            }
        });
    }

    /**
     * Ambil semua ingredient ID yang memang dipakai oleh menu group ini (berdasarkan relasi resep).
     */
    public function ingredientIdsUsed()
    {
        // Mirror cara kamu mem-filter di Resource (recipes.menuGroupRecipes)
        return \App\Models\Ingredient::query()
            ->whereHas('recipes.menuGroupRecipes', function ($q) {
                $q->where('menu_group_id', $this->id);
            })
            ->pluck('id');
    }

    /**
     * Total kebutuhan ingredient tertentu (hitung dari resep).
     */
    public function totalNeededForIngredient(int $ingredientId): float
    {
        $this->loadMissing('recipes.recipe.recipeIngredients');

        $requestedPortions = (float) ($this->requested_portions ?? 1);
        $total = 0.0;

        foreach ($this->recipes as $mgr) {
            $recipe = $mgr->recipe;
            $base   = (float) ($recipe->base_portions ?? 1);

            $ri = $recipe->recipeIngredients()
                ->where('ingredient_id', $ingredientId)
                ->first();

            if ($ri) {
                $total += ((float) $ri->amount / max($base, 1.0)) * $requestedPortions;
            }
        }

        return $total;
    }

    /**
     * Total alokasi yang sudah tercatat untuk ingredient tertentu.
     */
    public function allocatedForIngredient(int $ingredientId): float
    {
        return (float) \App\Models\SupplierAllocation::query()
            ->where('menu_group_id', $this->id)
            ->where('ingredient_id', $ingredientId)
            ->sum('quantity');
    }

    /**
     * Sisa kebutuhan (>= 0).
     */
    public function remainingForIngredient(int $ingredientId): float
    {
        $remaining = $this->totalNeededForIngredient($ingredientId) - $this->allocatedForIngredient($ingredientId);
        return $remaining > 0 ? $remaining : 0.0;
    }

    /**
     * Apakah masih ada minimal satu ingredient dengan remaining > 0?
     */
    public function hasRemainingAny(): bool
    {
        foreach ($this->ingredientIdsUsed() as $iid) {
            if ($this->remainingForIngredient((int) $iid) > 0) {
                return true;
            }
        }
        return false;
    }
}
