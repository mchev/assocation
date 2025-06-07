<?php

namespace Database\Factories;

use App\Enums\ReservationStatus;
use App\Models\Organization;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-1 month', '+2 months');
        $endDate = fake()->dateTimeBetween($startDate, '+3 months');
        $subtotal = fake()->numberBetween(1000, 100000); // 10€ à 1000€
        $hasDiscount = fake()->boolean(30); // 30% de chance d'avoir une remise
        $discountType = $hasDiscount ? fake()->randomElement(['percentage', 'amount']) : null;
        $discountValue = null;
        $discountAmount = 0;

        if ($hasDiscount) {
            if ($discountType === 'percentage') {
                $discountValue = fake()->numberBetween(5, 25); // 5% à 25%
                $discountAmount = round($subtotal * ($discountValue / 100));
            } else {
                $discountValue = fake()->numberBetween(500, 5000); // 5€ à 50€
                $discountAmount = $discountValue;
            }
        }

        return [
            'from_organization_id' => Organization::factory(),
            'to_organization_id' => Organization::factory(),
            'user_id' => User::factory(),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => fake()->randomElement(ReservationStatus::cases()),
            'notes' => fake()->optional()->paragraph(),
            'subtotal' => $subtotal,
            'discount_type' => $discountType,
            'discount_value' => $discountValue,
            'discount_amount' => $discountAmount,
            'discount_reason' => $hasDiscount ? fake()->sentence() : null,
            'total' => $subtotal - $discountAmount,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ReservationStatus::PENDING,
        ]);
    }

    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ReservationStatus::CONFIRMED,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ReservationStatus::COMPLETED,
        ]);
    }

    public function cancelled(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => ReservationStatus::CANCELLED,
        ]);
    }

    public function rejected(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => ReservationStatus::REJECTED,
        ]);
    }
} 