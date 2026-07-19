<?php

namespace App\Http\Controllers;

use App\Models\ServiceType;
use App\Models\ServiceGallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ServiceTypeController extends Controller
{
    public function index()
    {
        // Load beserta galleries agar bisa dihitung jumlah fotonya
        $services = ServiceType::with('galleries')->where('user_id', Auth::id())->latest()->get();
        return view('service-types.index', compact('services'));
    }

    public function create()
    {
        return view('service-types.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'galleries.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072', // Maksimal 3MB per foto
        ]);
        
        $validated['user_id'] = Auth::id();
        $service = ServiceType::create($validated);

        // Upload Foto ke Database
        if ($request->hasFile('galleries')) {
            foreach ($request->file('galleries') as $file) {
                $path = $file->store('service_galleries', 'public');
                $service->galleries()->create(['image_path' => $path]);
            }
        }

        return redirect()->route('service-types.index')->with('success', 'Layanan & foto berhasil ditambahkan!');
    }

    public function edit(ServiceType $serviceType)
    {
        if ($serviceType->user_id !== Auth::id()) abort(403);
        
        $serviceType->load('galleries'); // Load foto lama
        return view('service-types.form', ['service' => $serviceType]);
    }

    public function update(Request $request, ServiceType $serviceType)
    {
        if ($serviceType->user_id !== Auth::id()) abort(403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'galleries.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);
        
        $serviceType->update($validated);

        // Upload Foto Tambahan (Jika ada)
        if ($request->hasFile('galleries')) {
            foreach ($request->file('galleries') as $file) {
                $path = $file->store('service_galleries', 'public');
                $serviceType->galleries()->create(['image_path' => $path]);
            }
        }

        return redirect()->route('service-types.index')->with('success', 'Layanan diperbarui!');
    }

    public function destroy(ServiceType $serviceType)
    {
        if ($serviceType->user_id !== Auth::id()) abort(403);
        if ($serviceType->bookings()->exists()) {
            return redirect()->back()->with('error', 'Layanan masih digunakan dalam booking.');
        }

        // Hapus file fisik dari storage
        foreach ($serviceType->galleries as $gallery) {
            Storage::disk('public')->delete($gallery->image_path);
        }
        $serviceType->delete();

        return redirect()->route('service-types.index')->with('success', 'Layanan berhasil dihapus!');
    }

    // FUNGSI BARU UNTUK MENGHAPUS FOTO SATUAN (Saat Edit)
    public function destroyGallery(ServiceGallery $gallery)
    {
        $service = $gallery->serviceType;
        if ($service->user_id !== Auth::id()) abort(403);

        // Hapus fisik foto
        Storage::disk('public')->delete($gallery->image_path);
        // Hapus dari database
        $gallery->delete();

        return response()->json(['message' => 'Foto dihapus!']);
    }
}