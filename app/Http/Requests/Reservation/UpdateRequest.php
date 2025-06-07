<?php

namespace App\Http\Requests\Reservation;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

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
            'start_date' => [
                'sometimes',
                'required',
                'date',
                'after_or_equal:' . Carbon::today()->format('Y-m-d'),
            ],
            'end_date' => [
                'sometimes',
                'required',
                'date',
                'after_or_equal:start_date',
            ],
            'purpose' => ['sometimes', 'required', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'status' => ['sometimes', 'required', 'string', 'in:pending,approved,rejected,cancelled'],
            'cancellation_reason' => ['required_if:status,cancelled', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'start_date.required' => 'La date de début est requise.',
            'start_date.date' => 'La date de début doit être une date valide.',
            'start_date.after_or_equal' => 'La date de début doit être aujourd\'hui ou une date ultérieure.',
            'end_date.required' => 'La date de fin est requise.',
            'end_date.date' => 'La date de fin doit être une date valide.',
            'end_date.after_or_equal' => 'La date de fin doit être postérieure ou égale à la date de début.',
            'purpose.required' => 'Le motif de la réservation est requis.',
            'purpose.max' => 'Le motif ne peut pas dépasser 1000 caractères.',
            'notes.max' => 'Les notes ne peuvent pas dépasser 1000 caractères.',
            'status.required' => 'Le statut est requis.',
            'status.in' => 'Le statut doit être en attente, approuvé, rejeté ou annulé.',
            'cancellation_reason.required_if' => 'La raison de l\'annulation est requise.',
            'cancellation_reason.max' => 'La raison de l\'annulation ne peut pas dépasser 1000 caractères.',
        ];
    }
}
