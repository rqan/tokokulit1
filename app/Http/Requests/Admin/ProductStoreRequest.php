<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ProductStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->session()->get('role') === 'superadmin' || optional(Auth::user())->role === 'superadmin';
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
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'category' => ['nullable', 'string', 'max:100'],
            'new_category' => ['nullable', 'string', 'max:100'],
            'gender' => ['nullable', 'string', 'in:Pria,Wanita,Unisex'],
            'stock' => ['required', 'integer', 'min:0'],
            'image_url' => ['nullable', 'string'],
            'sizes' => ['nullable', 'array'],
            'sizes.*' => ['string'],
            'colors' => ['nullable', 'array'],
            'colors.*' => ['string'],
            'variants' => ['nullable', 'array'],
            'variants.*.size' => ['nullable', 'string'],
            'variants.*.color' => ['nullable', 'string'],
            'variants.*.stock' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
