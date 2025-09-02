<?php

namespace App\Http\Requests\Reservation;

use Carbon\Carbon;
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
            'equipment_id' => ['required', 'exists:equipments,id'],
            'start_date' => [
                'required',
                'date',
                'after_or_equal:'.Carbon::today()->format('Y-m-d'),
            ],
            'end_date' => [
                'required',
                'date',
                'after_or_equal:start_date',
            ],
            'purpose' => ['required', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'equipment_id.required' => 'L\'équipement est requis.',
            'equipment_id.exists' => 'L\'équipement sélectionné n\'existe pas.',
            'start_date.required' => 'La date de début est requise.',
            'start_date.date' => 'La date de début doit être une date valide.',
            'start_date.after_or_equal' => 'La date de début doit être aujourd\'hui ou une date ultérieure.',
            'end_date.required' => 'La date de fin est requise.',
            'end_date.date' => 'La date de fin doit être une date valide.',
            'end_date.after_or_equal' => 'La date de fin doit être postérieure ou égale à la date de début.',
            'purpose.required' => 'Le motif de la réservation est requis.',
            'purpose.max' => 'Le motif ne peut pas dépasser 1000 caractères.',
            'notes.max' => 'Les notes ne peuvent pas dépasser 1000 caractères.',
        ];
    }
}
