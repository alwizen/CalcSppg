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
        'allocated_amount',
        'unit',
        'price_per_unit',
        'total_price',
        'status'
    ];

    protected static function booted(): void
    {
        static::saving(function (self $m) {
            if (!is_null($m->price_per_unit)) {
                $m->total_price = bcmul((string)$m->allocated_amount, (string)$m->price_per_unit, 2);
            }
        });
    }

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
