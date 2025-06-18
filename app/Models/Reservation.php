<?php

namespace App\Models;

use App\Casts\PriceCast;
use App\Enums\ReservationStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'from_organization_id',
        'to_organization_id',
        'created_by_user_id',
        'start_date',
        'end_date',
        'status',
        'notes',
        'subtotal',
        'discount_amount',
        'discount_type',
        'discount_value',
        'discount_reason',
        'total',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'status' => ReservationStatus::class,
        'subtotal' => PriceCast::class,
        'discount_amount' => PriceCast::class,
        'discount_value' => 'integer',
        'total' => PriceCast::class,
    ];

    // Discount Types
    const DISCOUNT_TYPE_PERCENTAGE = 'percentage';

    const DISCOUNT_TYPE_FIXED = 'fixed';

    public static function discountTypes(): array
    {
        return [
            self::DISCOUNT_TYPE_PERCENTAGE => 'Pourcentage',
            self::DISCOUNT_TYPE_FIXED => 'Montant fixe',
        ];
    }

    /**
     * Get the organization that lends the equipment (from_organization)
     */
    public function lenderOrganization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'from_organization_id');
    }

    /**
     * Get the organization that borrows the equipment (to_organization)
     */
    public function borrowerOrganization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'to_organization_id');
    }

    /**
     * Alias for backward compatibility
     *
     * @deprecated Use lenderOrganization() instead
     */
    public function fromOrganization(): BelongsTo
    {
        return $this->lenderOrganization();
    }

    /**
     * Alias for backward compatibility
     *
     * @deprecated Use borrowerOrganization() instead
     */
    public function toOrganization(): BelongsTo
    {
        return $this->borrowerOrganization();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ReservationItem::class);
    }

    public function getDurationAttribute(): int
    {
        return abs($this->start_date->diffInDays($this->end_date)) + 1;
    }

    public function getDeadlineAttribute(): Carbon
    {
        // Since we're casting to 'date', we need to ensure we're working with dates properly
        $startDate = $this->start_date->startOfDay();
        $now = now()->startOfDay();
        $daysUntilStart = abs($startDate->diffInDays($now, false));

        // If the reservation starts in less than 3 days, deadline is 24 hours before start
        if ($daysUntilStart < 3) {
            return $this->start_date->copy()->subHours(24);
        }

        // If the reservation starts in less than 7 days, deadline is 48 hours before start
        if ($daysUntilStart < 7) {
            return $this->start_date->copy()->subHours(48);
        }

        // If the reservation starts in less than 14 days, deadline is 1 week before start
        if ($daysUntilStart < 14) {
            return $this->start_date->copy()->subDays(5);
        }

        // If the reservation starts in less than 20 days, deadline is 1 week before start
        if ($daysUntilStart < 20) {
            return $this->start_date->copy()->subDays(7);
        }

        // Otherwise, deadline is 2 weeks before the start date
        return $this->start_date->copy()->subDays(14);
    }

    public function getDeadlineForHumanAttribute(): string
    {
        $deadline = $this->deadline;
        $now = now();

        if ($now->isAfter($deadline)) {
            return 'Délai expiré';
        }

        $totalHoursUntilDeadline = (int) $now->diffInHours($deadline, false);
        $daysUntilDeadline = (int) floor($totalHoursUntilDeadline / 24);
        $remainingHours = $totalHoursUntilDeadline % 24;

        // If less than 1 day, show in hours
        if ($daysUntilDeadline < 1) {
            return $totalHoursUntilDeadline.' heure'.($totalHoursUntilDeadline > 1 ? 's' : '');
        }

        // Show days and remaining hours
        $daysText = $daysUntilDeadline.' jour'.($daysUntilDeadline > 1 ? 's' : '');
        $hoursText = $remainingHours > 0 ? ' et '.$remainingHours.' heure'.($remainingHours > 1 ? 's' : '') : '';

        return $daysText.$hoursText;
    }

    public function isPending(): bool
    {
        return $this->status === ReservationStatus::PENDING;
    }

    public function isConfirmed(): bool
    {
        return $this->status === ReservationStatus::CONFIRMED;
    }

    public function isRejected(): bool
    {
        return $this->status === ReservationStatus::REJECTED;
    }

    public function isCancelled(): bool
    {
        return $this->status === ReservationStatus::CANCELLED;
    }

    public function isCompleted(): bool
    {
        return $this->status === ReservationStatus::COMPLETED;
    }

    public function canBeConfirmed(): bool
    {
        return $this->status === ReservationStatus::PENDING;
    }

    public function canBeRejected(): bool
    {
        return $this->status === ReservationStatus::PENDING;
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, [
            ReservationStatus::PENDING,
            ReservationStatus::CONFIRMED,
        ]);
    }

    public function canBeCompleted(): bool
    {
        // return $this->status === ReservationStatus::CONFIRMED &&
        //     $this->items->every(fn ($item) => $item->isReturned());
        return $this->status === ReservationStatus::CONFIRMED;
    }

    public function calculateTotals(): void
    {
        // Calculer le sous-total
        $this->subtotal = $this->items->sum('total_price');

        // Calculer la remise
        if ($this->discount_type === self::DISCOUNT_TYPE_PERCENTAGE) {
            $this->discount_amount = (int) round($this->subtotal * ($this->discount_value / 100));
        } elseif ($this->discount_type === self::DISCOUNT_TYPE_FIXED) {
            $this->discount_amount = $this->discount_value;
        } else {
            $this->discount_amount = 0;
        }

        // Calculer le total final
        $this->total = max(0, $this->subtotal - $this->discount_amount);

        $this->save();
    }

    public function applyDiscount(string $type, int $value, ?string $reason = null): void
    {
        $this->discount_type = $type;
        $this->discount_value = $value;
        $this->discount_reason = $reason;
        $this->calculateTotals();
    }

    public function removeDiscount(): void
    {
        $this->discount_type = null;
        $this->discount_value = null;
        $this->discount_reason = null;
        $this->discount_amount = 0;
        $this->calculateTotals();
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return number_format($this->subtotal / 100, 2, ',', ' ').' €';
    }

    public function getFormattedDiscountAmountAttribute(): string
    {
        return number_format($this->discount_amount / 100, 2, ',', ' ').' €';
    }

    public function getFormattedTotalAttribute(): string
    {
        return number_format($this->total / 100, 2, ',', ' ').' €';
    }

    public function getDiscountTextAttribute(): string
    {
        if (! $this->discount_type) {
            return 'Aucune remise';
        }

        if ($this->discount_type === self::DISCOUNT_TYPE_PERCENTAGE) {
            return $this->discount_value.'% ('.$this->formatted_discount_amount.')';
        }

        return $this->formatted_discount_amount;
    }

    public function confirm(): void
    {
        if (! $this->canBeConfirmed()) {
            throw new \Exception('Cette réservation ne peut pas être confirmée.');
        }

        $this->update(['status' => ReservationStatus::CONFIRMED]);
    }

    public function reject(): void
    {
        if (! $this->canBeRejected()) {
            throw new \Exception('Cette réservation ne peut pas être refusée.');
        }

        $this->update(['status' => ReservationStatus::REJECTED]);
    }

    public function cancel(string $reason): void
    {
        if (! $this->canBeCancelled()) {
            throw new \Exception('Cette réservation ne peut pas être annulée.');
        }

        $this->update([
            'status' => ReservationStatus::CANCELLED,
            'cancellation_reason' => $reason,
        ]);
    }
}
