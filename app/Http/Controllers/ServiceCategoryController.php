<?php

namespace App\Http\Controllers;

use App\Models\ServiceCategory;
use App\Models\ServiceGallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ServiceCategoryController extends Controller
{
    public function index()
    {
        $categories = ServiceCategory::with('galleries')->where('user_id', Auth::id())->latest()->get();
        return view('service-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('service-categories.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'galleries.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240', // Maks 10MB per foto
        ]);

        $category = ServiceCategory::create([
            'user_id' => Auth::id(),
            'name' => $request->name
        ]);

        // =========================================================
        // UPLOAD FOTO KE CLOUDINARY (PERMANEN & ANTI HILANG)
        // =========================================================
        if ($request->hasFile('galleries')) {
            foreach ($request->file('galleries') as $file) {
                // Menitipkan file ke Cloudinary di dalam folder "service_galleries"
                $path = $file->storeOnCloudinary('service_galleries')->getSecurePath();
                $category->galleries()->create(['image_path' => $path]);
            }
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'category' => $category]);
        }

        return redirect()->route('service-categories.index')->with('success', 'Kategori & Portofolio berhasil ditambahkan!');
    }

    public function edit(ServiceCategory $serviceCategory)
    {
        if ($serviceCategory->user_id !== Auth::id()) abort(403);
        $serviceCategory->load('galleries');
        return view('service-categories.form', ['category' => $serviceCategory]);
    }

    public function update(Request $request, ServiceCategory $serviceCategory)
    {
        if ($serviceCategory->user_id !== Auth::id()) abort(403);

        $request->validate([
            'name' => 'required|string|max:255',
            'galleries.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        $serviceCategory->update(['name' => $request->name]);

        // =========================================================
        // UPLOAD FOTO TAMBAHAN KE CLOUDINARY
        // =========================================================
        if ($request->hasFile('galleries')) {
            foreach ($request->file('galleries') as $file) {
                $path = $file->storeOnCloudinary('service_galleries')->getSecurePath();
                $serviceCategory->galleries()->create(['image_path' => $path]);
            }
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'category' => $serviceCategory]);
        }

        return redirect()->route('service-categories.index')->with('success', 'Kategori berhasil diperbarui!');
    }

    public function destroy(Request $request, ServiceCategory $serviceCategory)
    {
        if ($serviceCategory->user_id !== Auth::id()) abort(403);

        foreach ($serviceCategory->galleries as $gallery) {
            // Pengaman: Jika fotonya masih pakai link lokal lama, hapus dari folder lokal
            // Jika foto baru (dimulai dengan http/Cloudinary), biarkan di Cloud (kuota sangat aman)
            if (!str_starts_with($gallery->image_path, 'http')) {
                Storage::disk('public')->delete($gallery->image_path);
            }
        }
        $serviceCategory->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('service-categories.index')->with('success', 'Kategori & foto berhasil dihapus!');
    }

    public function destroyGallery(ServiceGallery $gallery)
    {
        if ($gallery->category->user_id !== Auth::id()) abort(403);

        // Pengaman penghapusan untuk URL Cloudinary vs Lokal
        if (!str_starts_with($gallery->image_path, 'http')) {
            Storage::disk('public')->delete($gallery->image_path);
        }
        $gallery->delete();

        return response()->json(['message' => 'Foto dihapus!']);
    }
}