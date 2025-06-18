<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Organization extends Model
{
    use HasFactory, Notifiable, SoftDeletes;

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
        'owner_id',
        'helloasso_id',
        'helloasso_name',
        'helloasso_slug',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'subscription_ends_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::created(function ($organization) {
            Depot::create([
                'organization_id' => $organization->id,
                'name' => 'Dépôt principal',
                'address' => $organization->address,
                'city' => $organization->city,
                'postal_code' => $organization->postal_code,
                'is_active' => true,
            ]);
        });
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    public function roles(): HasMany
    {
        return $this->hasMany(Role::class);
    }

    public function permissions(): HasMany
    {
        return $this->hasMany(Permission::class);
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(OrganizationInvitation::class);
    }

    public function equipments(): HasMany
    {
        return $this->hasMany(Equipment::class);
    }

    /**
     * Get the depots for the organization.
     */
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
