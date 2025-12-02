<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;



class PasswordUpdateRequest extends FormRequest
{
    

    public function authorize(): bool
    {
        return auth()->check();
    }

    

    public function rules(): array
    {
        return [
            'current_password' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (!Hash::check($value, auth()->user()->password)) {
                        $fail('Aktualne hasło jest nieprawidłowe.');
                    }
                },
            ],
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers(),
            ],
        ];
    }

    

    public function messages(): array
    {
        return [
            'current_password.required' => 'Aktualne hasło jest wymagane.',
            'password.required' => 'Nowe hasło jest wymagane.',
            'password.confirmed' => 'Hasła nie są takie same.',
            'password.min' => 'Hasło musi mieć co najmniej 8 znaków.',
        ];
    }
}
