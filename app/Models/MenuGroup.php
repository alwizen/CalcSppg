<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuGroup extends Model
{
    protected $fillable = ['date'];

    protected $casts = [
        'date' => 'datetime',
    ];


    public function recipes(): HasMany
    {
        return $this->hasMany(MenuGroupRecipe::class);
    }
}
