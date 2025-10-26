<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IngredientSupplier extends Model
{

    protected $table = 'ingredient_suppliers';

    protected $fillable = [
        'supplier_id',
        'ingredient_id',
    ];
}
