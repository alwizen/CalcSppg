<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RequiredIngredient extends Model
{
    protected $fillable = [
        'menu_group_id',
        'ingredient_id',
        'required_amount',
        'unit'
    ];

    public function menuGroup(): BelongsTo
    {
        return $this->belongsTo(MenuGroup::class);
    }
    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(SupplierAllocation::class, 'ingredient_id', 'ingredient_id')
            ->where('menu_group_id', $this->menu_group_id);
    }

    public function getAllocatedTotalAttribute(): float
    {
        return (float) $this->allocations()->sum('allocated_amount');
    }

    public function getIsFullyAllocatedAttribute(): bool
    {
        return bccomp((string)$this->allocated_total, (string)$this->required_amount, 3) === 0;
    }
}
