<?php

namespace App\Models;

use App\Enums\ReservationItemStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReservationItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reservation_id',
        'equipment_id',
        'depot_id',
        'quantity',
        'notes',
        'status',
        'picked_up_at',
        'returned_at',
        'price',
        'total_price',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'integer',
        'status' => ReservationItemStatus::class,
        'picked_up_at' => 'datetime',
        'returned_at' => 'datetime',
        'total_price' => 'integer',
    ];

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    public function depot(): BelongsTo
    {
        return $this->belongsTo(Depot::class);
    }

    public function isPending(): bool
    {
        return $this->status === ReservationItemStatus::PENDING;
    }

    public function isPickedUp(): bool
    {
        return $this->status === ReservationItemStatus::PICKED_UP;
    }

    public function isReturned(): bool
    {
        return $this->status === ReservationItemStatus::RETURNED;
    }

    public function canBePickedUp(): bool
    {
        return $this->status === ReservationItemStatus::PENDING &&
            $this->reservation->isConfirmed();
    }

    public function canBeReturned(): bool
    {
        return $this->status === ReservationItemStatus::PICKED_UP;
    }

    public function getTotalPrice(): int
    {
        return $this->price * $this->quantity;
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            // Calculer le prix total à partir du prix unitaire et de la quantité
            if ($item->price && $item->quantity) {
                $item->total_price = $item->price * $item->quantity;
            }
        });

        static::saved(function ($item) {
            // Recalculer les totaux de la réservation
            $item->reservation->calculateTotals();
        });
    }

    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price / 100, 2, ',', ' ').' €';
    }

    public function getFormattedTotalPriceAttribute(): string
    {
        return number_format($this->total_price / 100, 2, ',', ' ').' €';
    }
}
