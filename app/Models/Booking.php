<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'service_type_id',
        'client_name',
        'client_contact',
        'client_address',
        'booking_date',
        'start_date',
        'end_date',
        'booking_time',
        'status',
        'payment_status',
        'quantity',
        'unit_price',
        'discount_percent',
        'paid_amount',
        'subtotal',
        'discount_amount',
        'total',
        'remaining',
        'notes',
    ];

    protected $casts = [
        'booking_date'     => 'date',
        'start_date'       => 'date',
        'end_date'         => 'date',
        'quantity'         => 'integer',
        'unit_price'       => 'integer',
        'discount_percent' => 'decimal:2',
        'paid_amount'      => 'integer',
        'subtotal'         => 'integer',
        'discount_amount'  => 'integer',
        'total'            => 'integer',
        'remaining'        => 'integer',
    ];

    // ── Relasi ──────────────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class);
    }

    // ── TPS Processing — Rumus 3.1 s/d 3.5 ─────────────────────
    /**
     * Jalankan semua kalkulasi TPS dan kembalikan array hasil.
     * Dipanggil di Controller sebelum simpan ke DB.
     *
     * Rumus:
     *   (3.1) Subtotal = P × Q
     *   (3.2) Nd       = Subtotal × D/100
     *   (3.3) Total    = Subtotal - Nd
     *   (3.4) Sisa     = Total - Db
     *   (3.5) Status   = kondisional berdasarkan Db vs Total
     */
    public static function calculateTps(
        int $unitPrice,
        int $quantity,
        float $discountPercent,
        int $paidAmount
    ): array {
        // (3.1) Subtotal = P × Q
        $subtotal = $unitPrice * $quantity;

        // (3.2) Nd = Subtotal × D/100
        $discountAmount = (int) round($subtotal * ($discountPercent / 100));

        // (3.3) Total = Subtotal - Nd
        $total = max($subtotal - $discountAmount, 0);

        // (3.4) Sisa = Total - Db
        $remaining = max($total - $paidAmount, 0);

        // (3.5) Status pembayaran — otomatis
        if ($paidAmount <= 0) {
            $paymentStatus = 'Belum Bayar';
        } elseif ($paidAmount >= $total) {
            $paymentStatus = 'Lunas';
        } else {
            $paymentStatus = 'Down Payment';
        }

        return [
            'subtotal'        => $subtotal,
            'discount_amount' => $discountAmount,
            'total'           => $total,
            'remaining'       => $remaining,
            'payment_status'  => $paymentStatus,
        ];
    }
}