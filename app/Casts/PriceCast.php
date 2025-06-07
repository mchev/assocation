<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class PriceCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  mixed  $value
     * @return float
     */
    public function get($model, string $key, $value, array $attributes)
    {
        // Convert from cents to decimal
        return $value ? number_format($value / 100, 2, ',', '') : '0,00';
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  mixed  $value
     * @return int
     */
    public function set($model, string $key, $value, array $attributes)
    {
        if ($value === null) {
            return 0;
        }

        // Standardize decimal separator
        $value = str_replace(',', '.', $value);

        // Convert to cents
        return (int) round((float) $value * 100);
    }
}
