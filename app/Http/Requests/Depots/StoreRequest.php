<?php

namespace App\Http\Requests\Depots;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'postal_code' => ['required', 'string', 'max:20'],
            'country' => ['required', 'string', 'size:2'],
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom est requis',
            'name.string' => 'Le nom doit être une chaîne de caractères',
            'name.max' => 'Le nom ne doit pas dépasser 255 caractères',
            'description.string' => 'La description doit être une chaîne de caractères',
            'address.required' => 'L\'adresse est requise',
            'address.string' => 'L\'adresse doit être une chaîne de caractères',
            'address.max' => 'L\'adresse ne doit pas dépasser 255 caractères',
            'city.required' => 'La ville est requise',
        ];
    }
}
