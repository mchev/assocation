<?php

namespace App\Enums;

enum ReservationItemStatus: string
{
    case PENDING = 'pending';
    case PICKED_UP = 'picked_up';
    case RETURNED = 'returned';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'En attente',
            self::PICKED_UP => 'Récupéré',
            self::RETURNED => 'Retourné',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'bg-yellow-100 text-yellow-800',
            self::PICKED_UP => 'bg-green-100 text-green-800',
            self::RETURNED => 'bg-blue-100 text-blue-800',
        };
    }
}
