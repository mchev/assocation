<?php

namespace App\Http\Requests\Equipment;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            // Step 1: Informations générales
            'name' => ['required', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category_id' => ['required', 'exists:categories,id'],

            // Step 2: État et stockage
            'condition' => ['required', 'string', 'in:new,good,fair,poor'],
            'quantity' => ['required', 'integer', 'min:1'],
            'depot_id' => ['required', 'exists:depots,id'],

            // Step 3: Photos
            'images' => ['nullable', 'array', 'max:3'],
            'images.*' => ['image', 'mimes:jpeg,jpg,png', 'max:5120'], // 5MB max per image

            // Step 4: Tarification
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'rental_price' => ['required', 'numeric', 'min:0'],
            'deposit_amount' => ['required', 'numeric', 'min:0'],
            'is_available' => ['boolean'],
            'requires_deposit' => ['boolean'],
            'is_rentable' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            // Step 1
            'name.required' => 'Le nom de l\'équipement est requis.',
            'name.max' => 'Le nom ne peut pas dépasser 255 caractères.',
            'brand.max' => 'La marque ne peut pas dépasser 255 caractères.',
            'category_id.required' => 'La catégorie est requise.',
            'category_id.exists' => 'La catégorie sélectionnée n\'existe pas.',

            // Step 2
            'condition.required' => 'L\'état est requis.',
            'condition.in' => 'L\'état doit être neuf, bon, moyen ou mauvais.',
            'quantity.required' => 'La quantité est requise.',
            'quantity.min' => 'La quantité doit être d\'au moins 1.',
            'depot_id.required' => 'Le lieu de stockage est requis.',
            'depot_id.exists' => 'Le lieu de stockage sélectionné n\'existe pas.',

            // Step 3
            'images.max' => 'Vous ne pouvez pas ajouter plus de 3 images.',
            'images.*.image' => 'Le fichier doit être une image.',
            'images.*.mimes' => 'L\'image doit être au format JPG, JPEG ou PNG.',
            'images.*.max' => 'L\'image ne peut pas dépasser 5MB.',

            // Step 4
            'purchase_price.required' => 'Le prix d\'achat est requis.',
            'purchase_price.numeric' => 'Le prix d\'achat doit être un nombre.',
            'purchase_price.min' => 'Le prix d\'achat ne peut pas être négatif.',
            'rental_price.required' => 'Le prix de location est requis.',
            'rental_price.numeric' => 'Le prix de location doit être un nombre.',
            'rental_price.min' => 'Le prix de location ne peut pas être négatif.',
            'deposit_amount.required' => 'Le montant de la caution est requis.',
            'deposit_amount.numeric' => 'Le montant de la caution doit être un nombre.',
            'deposit_amount.min' => 'Le montant de la caution ne peut pas être négatif.',
        ];
    }
}
