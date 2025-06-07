<?php

namespace App\Http\Requests\Organization;

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
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'postal_code' => ['required', 'string', 'max:10'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'logo' => ['nullable', 'image', 'max:2048'], // 2MB max
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom de l\'organisation est requis.',
            'name.max' => 'Le nom ne peut pas dépasser 255 caractères.',
            'description.required' => 'La description est requise.',
            'address.required' => 'L\'adresse est requise.',
            'city.required' => 'La ville est requise.',
            'postal_code.required' => 'Le code postal est requis.',
            'phone.required' => 'Le numéro de téléphone est requis.',
            'email.required' => 'L\'adresse email est requise.',
            'email.email' => 'L\'adresse email doit être valide.',
            'website.url' => 'L\'URL du site web doit être valide.',
            'logo.image' => 'Le fichier doit être une image.',
            'logo.max' => 'L\'image ne peut pas dépasser 2MB.',
        ];
    }
}
