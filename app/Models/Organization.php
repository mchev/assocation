<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'address',
        'city',
        'postal_code',
        'phone',
        'email',
        'website',
        'logo_path',
        'is_active',
        'subscription_type',
        'subscription_ends_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'subscription_ends_at' => 'datetime',
    ];

    public function equipments(): HasMany
    {
        return $this->hasMany(Equipment::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    public function depots(): HasMany
    {
        return $this->hasMany(Depot::class);
    }

    /**
     * Get reservations where this organization is lending equipment
     */
    public function lentReservations(): HasMany
    {
        return $this->hasMany(Reservation::class, 'from_organization_id');
    }

    /**
     * Get reservations where this organization is borrowing equipment
     */
    public function borrowedReservations(): HasMany
    {
        return $this->hasMany(Reservation::class, 'to_organization_id');
    }

    /**
     * Alias for backward compatibility
     *
     * @deprecated Use lentReservations() instead
     */
    public function outgoingReservations(): HasMany
    {
        return $this->lentReservations();
    }

    /**
     * Alias for backward compatibility
     *
     * @deprecated Use borrowedReservations() instead
     */
    public function incomingReservations(): HasMany
    {
        return $this->borrowedReservations();
    }

    /**
     * Get all reservations where this organization is involved (either lending or borrowing)
     */
    public function allReservations()
    {
        return Reservation::where(function ($query) {
            $query->where('from_organization_id', $this->id)
                ->orWhere('to_organization_id', $this->id);
        });
    }
}
