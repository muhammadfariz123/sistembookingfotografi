<?php

namespace App\Http\Controllers;

use App\Models\CompanySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CompanySettingController extends Controller
{
    // ── GET: Tampilkan Halaman Form Pengaturan ──────────────
    public function edit()
    {
        $setting = CompanySetting::where('user_id', Auth::id())->first();

        // Jika belum ada data, buat instance kosong agar form tidak error
        if (!$setting) {
            $setting = new CompanySetting();
        }

        return view('company-settings.form', compact('setting'));
    }

    // ── POST: Simpan atau Update Pengaturan ─────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'company_address' => 'required|string',
            'company_phone' => 'required|string|max:50',
            'company_email' => 'required|email|max:255',
            'company_logo' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:5120',
            'payment_method' => 'required|in:bank_transfer,qris', // Validasi tipe bayar
            'qris_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120', // Validasi foto QRIS
            'bank_name' => 'nullable|string|max:100',
            'bank_account' => 'nullable|string|max:50',
            'bank_holder' => 'nullable|string|max:255',
            'payment_instruction' => 'nullable|string',
            'bank_name_2' => 'nullable|string|max:100',
            'bank_account_2' => 'nullable|string|max:50',
            'bank_holder_2' => 'nullable|string|max:255',
        ]);

        $setting = CompanySetting::firstOrNew(['user_id' => Auth::id()]);

        // Handle upload logo
        if ($request->hasFile('company_logo')) {
            if ($setting->company_logo && Storage::disk('public')->exists($setting->company_logo)) {
                Storage::disk('public')->delete($setting->company_logo);
            }
            $validated['company_logo'] = $request->file('company_logo')->store('company-logos', 'public');
        }

        // Handle upload QRIS
        if ($request->hasFile('qris_image')) {
            if ($setting->qris_image && Storage::disk('public')->exists($setting->qris_image)) {
                Storage::disk('public')->delete($setting->qris_image);
            }
            $validated['qris_image'] = $request->file('qris_image')->store('qris-images', 'public');
        }

        // Simpan semua data yang sudah tervalidasi
        $validated['user_id'] = Auth::id();
        $setting->fill($validated);
        $setting->save();

        // Redirect kembali ke halaman form dengan pesan sukses
        return redirect()->route('company-setting.edit')->with('success', 'Pengaturan perusahaan berhasil disimpan.');
    }
}