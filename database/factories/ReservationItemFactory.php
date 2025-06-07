<?php

namespace Database\Factories;

use App\Enums\ReservationItemStatus;
use App\Models\Depot;
use App\Models\Equipment;
use App\Models\Reservation;
use App\Models\ReservationItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReservationItemFactory extends Factory
{
    protected $model = ReservationItem::class;

    public function definition(): array
    {
        return [
            'reservation_id' => Reservation::factory(),
            'equipment_id' => Equipment::factory(),
            'depot_id' => Depot::factory(),
            'quantity' => fake()->numberBetween(1, 5),
            'price' => fake()->numberBetween(1000, 50000),
            'status' => fake()->randomElement(ReservationItemStatus::cases()),
            'notes' => fake()->optional()->paragraph(),
            'picked_up_at' => fake()->optional()->dateTimeBetween('-1 month', 'now'),
            'returned_at' => fake()->optional()->dateTimeBetween('-1 month', 'now'),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ReservationItemStatus::PENDING,
            'picked_up_at' => null,
            'returned_at' => null,
        ]);
    }

    public function pickedUp(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ReservationItemStatus::PICKED_UP,
            'picked_up_at' => fake()->dateTimeBetween('-1 month', 'now'),
            'returned_at' => null,
        ]);
    }

    public function returned(): static
    {
        $pickedUpAt = fake()->dateTimeBetween('-1 month', '-1 week');
        
        return $this->state(fn (array $attributes) => [
            'status' => ReservationItemStatus::RETURNED,
            'picked_up_at' => $pickedUpAt,
            'returned_at' => fake()->dateTimeBetween($pickedUpAt, 'now'),
        ]);
    }
} 