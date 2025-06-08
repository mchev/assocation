<?php

namespace App\Http\Requests\Equipment;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'category' => ['sometimes', 'required', 'string', 'max:255'],
            'condition' => ['sometimes', 'required', 'string', 'in:new,good,fair,poor'],
            'purchase_price' => ['required', 'numeric'],
            'rental_price' => ['required', 'numeric'],
            'deposit_amount' => ['required', 'numeric'],
            'is_available' => ['sometimes', 'boolean'],
            'requires_deposit' => ['sometimes', 'boolean'],
            'is_rentable' => ['sometimes', 'boolean'],
            'specifications' => ['nullable', 'array'],
            'specifications.*.key' => ['required_with:specifications', 'string'],
            'specifications.*.value' => ['required_with:specifications', 'string'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'max:2048'], // 2MB max per image
            'last_maintenance_date' => ['nullable', 'date'],
            'next_maintenance_date' => ['nullable', 'date', 'after_or_equal:last_maintenance_date'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom de l\'équipement est requis.',
            'name.max' => 'Le nom ne peut pas dépasser 255 caractères.',
            'description.required' => 'La description est requise.',
            'category.required' => 'La catégorie est requise.',
            'condition.required' => 'L\'état est requis.',
            'condition.in' => 'L\'état doit être nouveau, bon, moyen ou mauvais.',
            'purchase_price.numeric' => 'Le prix d\'achat doit être un nombre.',
            'purchase_price.min' => 'Le prix d\'achat ne peut pas être négatif.',
            'rental_price.numeric' => 'Le prix de location doit être un nombre.',
            'rental_price.min' => 'Le prix de location ne peut pas être négatif.',
            'deposit_amount.numeric' => 'Le montant de la caution doit être un nombre.',
            'deposit_amount.min' => 'Le montant de la caution ne peut pas être négatif.',
            'specifications.*.key.required_with' => 'La clé de la spécification est requise.',
            'specifications.*.value.required_with' => 'La valeur de la spécification est requise.',
            'images.*.image' => 'Le fichier doit être une image.',
            'images.*.max' => 'L\'image ne peut pas dépasser 2MB.',
            'next_maintenance_date.after_or_equal' => 'La date de maintenance suivante doit être postérieure ou égale à la dernière maintenance.',
        ];
    }
}
