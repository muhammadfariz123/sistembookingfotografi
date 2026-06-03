<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceType extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'price',
    ];

    /**
     * Relasi ke User pemilik layanan ini
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke tabel Bookings (Satu layanan bisa dipakai banyak booking)
     * INI ADALAH FUNGSI YANG KURANG
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}