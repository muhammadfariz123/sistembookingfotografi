<?php

namespace App\Http\Controllers;

use App\Models\ServiceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceTypeController extends Controller
{
    public function index()
    {
        $services = ServiceType::where('user_id', Auth::id())->latest()->get();
        return view('service-types.index', compact('services'));
    }

    // FUNGSI BARU UNTUK HALAMAN CREATE
    public function create()
    {
        return view('service-types.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'nullable|numeric|min:0',
        ]);

        $validated['user_id'] = Auth::id();

        ServiceType::create($validated);

        // Ubah jadi Redirect dengan flash message
        return redirect()->route('service-types.index')->with('success', 'Layanan berhasil ditambahkan!');
    }

    // FUNGSI BARU UNTUK HALAMAN EDIT
    public function edit(ServiceType $serviceType)
    {
        // Pastikan hanya pemilik yang bisa edit
        if ($serviceType->user_id !== Auth::id()) {
            abort(403);
        }

        return view('service-types.form', ['service' => $serviceType]);
    }

    public function update(Request $request, ServiceType $serviceType)
    {
        if ($serviceType->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'nullable|numeric|min:0',
        ]);

        $serviceType->update($validated);

        return redirect()->route('service-types.index')->with('success', 'Layanan berhasil diperbarui!');
    }

    public function destroy(ServiceType $serviceType)
    {
        if ($serviceType->user_id !== Auth::id()) {
            abort(403);
        }

        // Cek jika layanan dipakai di booking
        if ($serviceType->bookings()->exists()) {
            return redirect()->back()->with('error', 'Layanan tidak bisa dihapus karena masih digunakan di Booking.');
        }

        $serviceType->delete();

        return redirect()->route('service-types.index')->with('success', 'Layanan berhasil dihapus!');
    }
}