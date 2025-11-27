<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request for checkout validation.
 */
class CheckoutRequest extends FormRequest
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
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'street_address' => 'required|string|max:255',
            'apartment' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'country' => 'required|string|max:255',
            'shipping_method' => 'required|in:standard,express,pickup',
            'payment_method' => 'required|in:cash_on_delivery,bank_transfer,payu',
            'customer_notes' => 'nullable|string|max:1000',
            'coupon_code' => 'nullable|string|max:50',
            'terms_accepted' => 'required|accepted',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'Imię jest wymagane.',
            'last_name.required' => 'Nazwisko jest wymagane.',
            'email.required' => 'Adres email jest wymagany.',
            'email.email' => 'Podaj prawidłowy adres email.',
            'phone.required' => 'Numer telefonu jest wymagany.',
            'street_address.required' => 'Adres jest wymagany.',
            'city.required' => 'Miasto jest wymagane.',
            'postal_code.required' => 'Kod pocztowy jest wymagany.',
            'country.required' => 'Kraj jest wymagany.',
            'shipping_method.required' => 'Wybierz metodę dostawy.',
            'shipping_method.in' => 'Nieprawidłowa metoda dostawy.',
            'payment_method.required' => 'Wybierz metodę płatności.',
            'payment_method.in' => 'Nieprawidłowa metoda płatności.',
            'terms_accepted.required' => 'Musisz zaakceptować regulamin.',
            'terms_accepted.accepted' => 'Musisz zaakceptować regulamin.',
        ];
    }
}
 