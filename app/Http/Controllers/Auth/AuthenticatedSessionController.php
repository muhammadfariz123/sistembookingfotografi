<?php
// app/Http/Controllers/Auth/AuthenticatedSessionController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Menampilkan halaman login untuk Admin.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Memproses Request Login yang dikirim dari form AJAX (login.blade.php).
     */
    public function store(LoginRequest $request)
    {
        // 1. Validasi & Cek kredensial email + password di database
        $request->authenticate();

        // 2. Mencegah serangan Session Fixation (Keamanan TPS)
        $request->session()->regenerate();

        // 3. Mengembalikan response dalam bentuk JSON (Sesuai ekspektasi fetch Javascript)
        return response()->json([
            'success' => true,
            'redirect' => route('dashboard'), // URL tujuan setelah sukses
        ]);
    }

    /**
     * Menghancurkan session (Proses Logout Admin).
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken(); // Reset token CSRF

        return redirect('/'); // Kembali ke halaman utama (login.blade.php)
    }
}