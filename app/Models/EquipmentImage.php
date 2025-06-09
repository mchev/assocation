<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class EquipmentImage extends Model
{
    protected $fillable = [
        'equipment_id',
        'path',
        'original_name',
        'mime_type',
        'size',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
        'size' => 'integer',
    ];

    protected $appends = ['url'];

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    public function url(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Storage::url($this->path),
        );
    }

    protected static function booted(): void
    {
        static::creating(function ($image) {
            if (is_null($image->order)) {
                $image->order = static::where('equipment_id', $image->equipment_id)->max('order') + 1;
            }
        });

        static::deleting(function ($image) {
            Storage::delete($image->path);
        });
    }
}
