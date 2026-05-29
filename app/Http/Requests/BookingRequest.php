<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class BookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_name' => 'required|string|max:255',
            'client_contact' => 'nullable|string|max:255',
            'client_address' => 'nullable|string|max:500',
            'service_type_id' => 'required|exists:service_types,id',
            'booking_date' => 'nullable|date',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'booking_time' => 'nullable|date_format:H:i',
            'status' => 'required|in:Dijadwalkan,Selesai,Dibatalkan',
            'unit_price' => 'required|integer|min:0',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'paid_amount' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'client_name.required' => 'Nama klien wajib diisi.',
            'service_type_id.required' => 'Jenis layanan wajib dipilih.',
            'service_type_id.exists' => 'Jenis layanan tidak valid.',
            'booking_date.date' => 'Format tanggal tidak valid.',
            'end_date.after_or_equal' => 'Tanggal selesai harus setelah tanggal mulai.',
            'unit_price.min' => 'Harga tidak boleh negatif.',
            'discount_percent.max' => 'Diskon maksimal 100%.',
        ];
    }
}