<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;



class AddressRequest extends FormRequest
{
    

    public function authorize(): bool
    {
        return auth()->check();
    }

    

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'street_address' => 'required|string|max:255',
            'apartment' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'country' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'is_default' => 'nullable|boolean',
            'type' => 'nullable|in:shipping,billing',
        ];
    }

    

    public function messages(): array
    {
        return [
            'first_name.required' => 'Imię jest wymagane.',
            'last_name.required' => 'Nazwisko jest wymagane.',
            'street_address.required' => 'Adres jest wymagany.',
            'city.required' => 'Miasto jest wymagane.',
            'postal_code.required' => 'Kod pocztowy jest wymagany.',
            'country.required' => 'Kraj jest wymagany.',
        ];
    }
}

