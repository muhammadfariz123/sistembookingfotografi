<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * 1. OTORISASI (Authorization Boundary)
     * Menentukan apakah user diizinkan memanggil request ini.
     * Mengembalikan 'true' karena form login terbuka untuk public.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * 2. ATURAN VALIDASI (Validation Rules)
     * Memastikan struktur data benar sebelum menyentuh Controller / Database.
     */
    public function rules(): array
    {
        return [
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * 3. PROSES PENGECETAKAN KREDENSIAL (Control & Entity Interaction)
     * Menghubungkan inputan User dengan Model/Database untuk verifikasi.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        // A. Cek apakah user sedang diblokir karena terlalu sering salah password
        $this->ensureIsNotRateLimited();

        // B. Mencocokkan email dan password ke Database (Auth::attempt)
        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            
            // Jika GAGAL: Catat 1x kegagalan (Hit Rate Limiter)
            RateLimiter::hit($this->throttleKey());

            // Lemparkan pesan error kembali ke View
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        // Jika BERHASIL: Hapus riwayat kegagalan (Clear Limit)
        RateLimiter::clear($this->throttleKey());
    }

    /**
     * 4. PROTEKSI KEAMANAN BRUTE-FORCE (TPS Security Feature)
     * Mencegah peretas mencoba menebak password terus-menerus.
     * Batas maksimal adalah 5x percobaan gagal.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        // Jika belum mencapai batas 5x gagal, izinkan lanjut
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        // Jika sudah lebih dari 5x, kunci sementara (Lockout)
        event(new Lockout($this));

        // Hitung sisa waktu tunggu untuk buka kunci
        $seconds = RateLimiter::availableIn($this->throttleKey());

        // Lemparkan pesan error throttle ke View
        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * 5. KUNCI UNIK RATE LIMITER
     * Membuat pengenal (identifier) unik berdasarkan Email dan IP Address.
     * Memastikan yang diblokir adalah IP/Email penyerang saja.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}