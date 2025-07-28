<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuGroup extends Model
{
    protected $fillable = ['date', 'name', 'sppg_id'];

    protected $casts = [
        'date' => 'datetime',
    ];


    public function recipes(): HasMany
    {
        return $this->hasMany(MenuGroupRecipe::class);
    }

    public function sppg(): BelongsTo
    {
        return $this->belongsTo(Sppg::class);
    }
}
