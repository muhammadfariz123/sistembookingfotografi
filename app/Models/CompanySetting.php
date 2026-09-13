<?php
// app/Models/CompanySetting.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    protected $fillable = [
        'user_id',
        'company_name',
        'company_address',
        'company_phone',
        'company_email',
        'company_logo',
        'payment_method', // Tambahkan ini
        'qris_image',
        'bank_name',
        'bank_account',
        'bank_holder',
        'payment_instruction',
        'bank_name_2',
        'bank_account_2',
        'bank_holder_2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getWhatsappNumberAttribute()
    {
        $phone = preg_replace('/[^0-9]/', '', $this->company_phone ?? '');
        
        // Ubah awalan 0 menjadi 62
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }
        
        return $phone;
    }
}