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
            'name' => 'required|string|max:255',
            'description' => 'required|string', // SEKARANG REQUIRED
            'price' => 'required|numeric|min:0', // SEKARANG REQUIRED
        ]);
        $validated['user_id'] = Auth::id();
        $service = ServiceType::create($validated);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Layanan berhasil ditambahkan!', 'data' => $service]);
        }
        return redirect()->route('service-types.index')->with('success', 'Layanan berhasil ditambahkan!');
    }

    public function update(Request $request, ServiceType $serviceType)
    {
        if ($serviceType->user_id !== Auth::id()) {
            abort(403);
        }
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string', // SEKARANG REQUIRED
            'price' => 'required|numeric|min:0', // SEKARANG REQUIRED
        ]);
        $serviceType->update($validated);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Layanan diperbarui!', 'data' => $serviceType]);
        }
        return redirect()->route('service-types.index')->with('success', 'Layanan diperbarui!');
    }

    public function destroy(ServiceType $serviceType)
    {
        if ($serviceType->user_id !== Auth::id()) {
            abort(403);
        }

        if ($serviceType->bookings()->exists()) {
            if (request()->wantsJson()) {
                return response()->json(['message' => 'Layanan sedang digunakan di booking, tidak bisa dihapus.'], 400);
            }
            return redirect()->back()->with('error', 'Layanan masih digunakan.');
        }

        $serviceType->delete();

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Layanan dihapus!']);
        }
        return redirect()->route('service-types.index')->with('success', 'Layanan berhasil dihapus!');
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


}