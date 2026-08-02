<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            // Aturan validasi baru
            'photo_limit' => 'nullable|integer|min:0' 
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama layanan wajib diisi',
            'price.numeric' => 'Harga harus berupa angka',
            'photo_limit.integer' => 'Batas pilih foto harus berupa angka',
        ];
    }
}