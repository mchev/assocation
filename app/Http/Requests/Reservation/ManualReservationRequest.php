<?php

namespace App\Http\Requests\Reservation;

use Illuminate\Foundation\Http\FormRequest;

class ManualReservationRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'notes' => ['nullable', 'string'],
            'recipient' => ['nullable', 'string', 'max:255'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.equipment_id' => ['required', 'exists:equipments,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.depot_id' => ['nullable', 'exists:depots,id'],
            'items.*.notes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Le titre de la réservation est requis.',
            'title.max' => 'Le titre ne peut pas dépasser 255 caractères.',
            'start_date.required' => 'La date de début est requise.',
            'start_date.after_or_equal' => 'La date de début doit être aujourd\'hui ou dans le futur.',
            'end_date.required' => 'La date de fin est requise.',
            'end_date.after_or_equal' => 'La date de fin doit être après la date de début.',
            'recipient.max' => 'Le destinataire ne peut pas dépasser 255 caractères.',
            'items.required' => 'Au moins un équipement doit être sélectionné.',
            'items.min' => 'Au moins un équipement doit être sélectionné.',
            'items.*.equipment_id.required' => 'L\'équipement est requis.',
            'items.*.equipment_id.exists' => 'L\'équipement sélectionné n\'existe pas.',
            'items.*.quantity.required' => 'La quantité est requise.',
            'items.*.quantity.min' => 'La quantité doit être d\'au moins 1.',
            'items.*.depot_id.exists' => 'Le lieu de stockage sélectionné n\'existe pas.',
        ];
    }
}
