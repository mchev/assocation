<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Equipment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'name',
        'description',
        'category_id',
        'condition',
        'purchase_price',
        'rental_price',
        'deposit_amount',
        'is_available',
        'requires_deposit',
        'is_rentable',
        'specifications',
        'images',
        'qr_code',
        'last_maintenance_date',
        'next_maintenance_date',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'requires_deposit' => 'boolean',
        'is_rentable' => 'boolean',
        'specifications' => 'array',
        'images' => 'array',
        'purchase_price' => 'decimal:2',
        'rental_price' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'last_maintenance_date' => 'date',
        'next_maintenance_date' => 'date',
    ];

    protected $table = 'equipments';

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function contracts()
    {
        return $this->hasManyThrough(Contract::class, Reservation::class);
    }

    public function depot()
    {
        return $this->belongsTo(Depot::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function availabilities(): HasMany
    {
        return $this->hasMany(Availability::class);
    }
}
