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
}
