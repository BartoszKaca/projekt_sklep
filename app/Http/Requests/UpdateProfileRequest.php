<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;



class UpdateProfileRequest extends FormRequest
{
    

    public function authorize(): bool
    {
        return auth()->check();
    }

    

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore(auth()->id()),
            ],
            'phone' => 'nullable|string|max:20',
        ];
    }

    

    public function messages(): array
    {
        return [
            'name.required' => 'Imię i nazwisko jest wymagane.',
            'email.required' => 'Adres email jest wymagany.',
            'email.email' => 'Podaj prawidłowy adres email.',
            'email.unique' => 'Ten adres email jest już zajęty.',
        ];
    }
}
