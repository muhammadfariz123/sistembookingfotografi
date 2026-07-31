<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property int $service_type_id
 * @property string $client_name
 * @property string|null $client_contact
 * @property string|null $client_email
 * @property string|null $client_instagram
 * @property string|null $client_address
 * @property \Illuminate\Support\Carbon|null $booking_date
 * @property \Illuminate\Support\Carbon|null $start_date
 * @property \Illuminate\Support\Carbon|null $end_date
 * @property string|null $booking_time
 * @property string $status
 * @property string $payment_status
 * @property string|null $payment_type
 * @property string|null $payment_proof
 * @property int $unit_price
 * @property float $discount_percent
 * @property int $paid_amount
 * @property int $subtotal
 * @property int $discount_amount
 * @property int $total
 * @property int $remaining
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ServiceType|null $serviceType
 * @property-read \App\Models\User $user
 */
class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'service_type_id',
        'client_name',
        'client_contact',
        'client_email',
        'client_instagram',
        'client_address',
        'link_gmaps',     // ← [FIX] wajib ditambahkan, ini yang hilang
        'booking_date',
        'start_date',
        'end_date',
        'booking_time',
        'status',
        'payment_status',
        'payment_type',   // ← [FIX] wajib ditambahkan, ini yang hilang
        'payment_proof',
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
        'booking_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'unit_price' => 'integer',
        'discount_percent' => 'decimal:2',
        'paid_amount' => 'integer',
        'subtotal' => 'integer',
        'discount_amount' => 'integer',
        'total' => 'integer',
        'remaining' => 'integer',
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

    public function transactions()
    {
        return $this->hasMany(PaymentTransaction::class);
    }
    // ── TPS Processing — Rumus 3.1 s/d 3.5 ─────────────────────
    /**
     * Jalankan semua kalkulasi TPS dan kembalikan array hasil.
     * Dipanggil di Controller sebelum simpan ke DB.
     *
     * Rumus (Modifikasi tanpa Qty):
     * (3.1) Subtotal = P
     * (3.2) Nd       = Subtotal × D/100
     * (3.3) Total    = Subtotal - Nd
     * (3.4) Sisa     = Total - Db
     * (3.5) Status   = kondisional berdasarkan Db vs Total
     */
    public static function calculateTps(
        int $unitPrice,
        float $discountPercent,
        int $paidAmount
    ): array {
        // (3.1) Subtotal = P (Tanpa perlu dikalikan Quantity)
        $subtotal = $unitPrice;

        // (3.2) Nd = Subtotal × D/100
        $discountAmount = (int) round($subtotal * ($discountPercent / 100));

        // (3.3) Total = Subtotal - Nd
        $total = max($subtotal - $discountAmount, 0);

        // (3.4) Sisa = Total - Db
        $remaining = max($total - $paidAmount, 0);

        // (3.5) Status pembayaran — otomatis
        if ($paidAmount <= 0) {
            $paymentStatus = 'Pending';
        } elseif ($paidAmount >= $total) {
            $paymentStatus = 'Lunas';
        } else {
            $paymentStatus = 'Down Payment';
        }

        return [
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'total' => $total,
            'remaining' => $remaining,
            'payment_status' => $paymentStatus,
        ];
    }
}