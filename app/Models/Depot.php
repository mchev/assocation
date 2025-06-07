<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Depot extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'name',
        'address',
        'city',
        'postal_code',
        'country',
        'phone',
        'email',
    ];

    protected $casts = [
        'opening_hours' => 'array',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function equipments()
    {
        return $this->hasMany(Equipment::class);
    }
}
