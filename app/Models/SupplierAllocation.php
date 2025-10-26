<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierAllocation extends Model
{
    protected $fillable = [
        'menu_group_id',
        'ingredient_id',
        'supplier_id',
        'quantity',
        'unit',
        'notes',
        'status', // pending, approved, delivered
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
    ];

    public function menuGroup(): BelongsTo
    {
        return $this->belongsTo(MenuGroup::class);
    }

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}
