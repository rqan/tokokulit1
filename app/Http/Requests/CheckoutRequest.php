<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Autentikasi/Blacklist diurus di controller/middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'shipping_name' => 'required|string|min:3|max:255',
            'shipping_email' => 'required|email|max:255',
            'shipping_phone' => ['required', 'string', 'regex:/^(^\+62|62|0)8[1-9][0-9]{6,10}$/'],
            'shipping_address' => 'required|string|min:10',
            'notes' => 'nullable|string',
            'selected_items' => 'required|array',
            'province_id' => 'nullable|integer',
            'city_id' => 'nullable|integer',
            'fax_number' => 'nullable|string',
            'cf-turnstile-response' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'shipping_phone.regex' => 'Format nomor WhatsApp tidak valid (Gunakan 08x atau +628x)'
        ];
    }
}
