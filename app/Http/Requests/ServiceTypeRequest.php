<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules.
     */
    public function rules(): array
    {
        return [

            'name' => 'required|string|max:255',

            'description' => 'nullable|string',

            'price' => 'nullable|numeric|min:0'

        ];
    }

    /**
     * Custom messages.
     */
    public function messages(): array
    {
        return [

            'name.required' => 'Nama layanan wajib diisi',

            'price.numeric' => 'Harga harus berupa angka',

        ];
    }
}