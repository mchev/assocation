<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reservation_id',
        'contract_number',
        'terms_and_conditions',
        'equipment_condition_before',
        'equipment_condition_after',
        'borrower_signature',
        'lender_signature',
        'signed_at',
        'status',
    ];

    protected $casts = [
        'equipment_condition_before' => 'array',
        'equipment_condition_after' => 'array',
        'signed_at' => 'datetime',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function equipment()
    {
        return $this->hasOneThrough(Equipment::class, Reservation::class);
    }
}
