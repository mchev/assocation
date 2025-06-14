<?php

namespace App\Models;

use App\Casts\PriceCast;
use App\Enums\ReservationStatus;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Equipment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'reference',
        'brand',
        'model',
        'quantity',
        'is_available',
        'is_rentable',
        'requires_deposit',
        'purchase_price',
        'rental_price',
        'deposit_amount',
        'condition',
        'specifications',
        'images',
        'is_active',
        'organization_id',
        'depot_id',
        'category_id',
        'last_maintenance_date',
        'next_maintenance_date',
    ];

    protected $casts = [
        'purchase_price' => PriceCast::class,
        'rental_price' => PriceCast::class,
        'deposit_amount' => PriceCast::class,
        'is_available' => 'boolean',
        'requires_deposit' => 'boolean',
        'is_rentable' => 'boolean',
        'is_active' => 'boolean',
        'specifications' => 'array',
        'images' => 'array',
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
        return $this->hasManyThrough(Reservation::class, ReservationItem::class, 'equipment_id', 'id', 'id');
    }

    public function contracts()
    {
        return $this->hasManyThrough(Contract::class, Reservation::class);
    }

    public function depot()
    {
        return $this->belongsTo(Depot::class)->withTrashed();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(EquipmentImage::class)->orderBy('order');
    }

    public function availabilities(): HasMany
    {
        return $this->hasMany(Availability::class);
    }

    public function availableQuantity($startDate, $endDate)
    {
        // Check the quantity available between startDate and endDate
        $formattedStartDate = Carbon::parse($startDate)->format('Y-m-d');
        $formattedEndDate = Carbon::parse($endDate)->format('Y-m-d');

        $reservedQuantity = $this->reservations()
            ->whereHas('items', function ($query) {
                $query->where('equipment_id', $this->id);
            })
            ->whereIn('reservations.status', [ReservationStatus::CONFIRMED, ReservationStatus::PENDING])
            ->where('reservations.start_date', '<=', $formattedEndDate)
            ->where('reservations.end_date', '>=', $formattedStartDate)
            ->sum('reservation_items.quantity');

        $availableQuantity = $this->quantity - $reservedQuantity;

        return (int) $availableQuantity;
    }

    public function reservationsDatesByMonth($date)
    {
        // Returns : [
        //     'equipment_id' => 1,
        //     'date' => '2025-01-01', // Each date where the equipment is reserved (between startDate and endDate)
        //     'status' => 'confirmed',
        // ]

        $reservations = $this->reservations()
            ->whereIn('reservations.status', [ReservationStatus::CONFIRMED, ReservationStatus::PENDING])
            ->where('start_date', '<=', Carbon::parse($date)->startOfMonth()->format('Y-m-d'))
            ->where('end_date', '>=', Carbon::parse($date)->endOfMonth()->format('Y-m-d'))
            ->get();

        // For each reservation, get the dates between startDate and endDate
        $dates = [];
        $reservations->each(function ($reservation) use (&$dates) {
            $period = CarbonPeriod::create($reservation->start_date, $reservation->end_date);
            foreach ($period as $date) {
                $dates[] = [
                    'equipment_id' => $reservation->equipment_id,
                    'date' => $date->format('Y-m-d'),
                    'status' => $reservation->status,
                ];
            }
        });

        return $dates;

    }
}
