<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sppg extends Model
{
    protected $fillable = [
        'name',
        'district',
        'regency',
        'province'
    ];
}
