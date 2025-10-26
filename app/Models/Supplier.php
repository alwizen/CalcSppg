<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Spatie\Permission\Traits\HasRoles;

class Supplier extends Authenticatable implements FilamentUser
{
    use HasRoles;

    protected $guard_name = 'suppliers';

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'is_active'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class, 'ingredient_suppliers');
    }

    // Untuk Filament authentication
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active;
    }

    public function allocations()
    {
        return $this->hasMany(SupplierAllocation::class);
    }
}
