<?php
// app/Http/Controllers/CompanySettingController.php

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
        $request->validate([
            'company_name'        => 'required|string|max:255',
            'company_address'     => 'required|string',
            'company_phone'       => 'required|string|max:50',
            'company_email'       => 'required|email|max:255',
            'company_logo'        => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:5120',
            'bank_name'           => 'nullable|string|max:100',
            'bank_account'        => 'nullable|string|max:50',
            'bank_holder'         => 'nullable|string|max:255',
            'payment_instruction' => 'nullable|string',
            'bank_name_2'         => 'nullable|string|max:100',
            'bank_account_2'      => 'nullable|string|max:50',
            'bank_holder_2'       => 'nullable|string|max:255',
        ]);

        $setting = CompanySetting::firstOrNew(['user_id' => Auth::id()]);

        // Handle upload logo
        if ($request->hasFile('company_logo')) {
            // Hapus logo lama jika ada
            if ($setting->company_logo && Storage::disk('public')->exists($setting->company_logo)) {
                Storage::disk('public')->delete($setting->company_logo);
            }
            $path = $request->file('company_logo')->store('company-logos', 'public');
            $setting->company_logo = $path;
        }

        $setting->fill([
            'user_id'             => Auth::id(),
            'company_name'        => $request->company_name,
            'company_address'     => $request->company_address,
            'company_phone'       => $request->company_phone,
            'company_email'       => $request->company_email,
            'bank_name'           => $request->bank_name,
            'bank_account'        => $request->bank_account,
            'bank_holder'         => $request->bank_holder,
            'payment_instruction' => $request->payment_instruction,
            'bank_name_2'         => $request->bank_name_2,
            'bank_account_2'      => $request->bank_account_2,
            'bank_holder_2'       => $request->bank_holder_2,
        ]);

        $setting->save();

        // Redirect kembali ke halaman form dengan pesan sukses
        return redirect()->route('company-setting.edit')->with('success', 'Pengaturan perusahaan berhasil disimpan.');
    }
}