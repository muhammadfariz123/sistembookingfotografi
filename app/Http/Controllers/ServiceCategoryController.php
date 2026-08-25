<?php

namespace App\Http\Controllers;

use App\Models\ServiceCategory;
use App\Models\ServiceGallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

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

    private function uploadToCloudinaryDirect($file)
    {
        $cloudinaryUrl = env('CLOUDINARY_URL');
        
        // Parse CLOUDINARY_URL format: cloudinary://API_KEY:API_SECRET@CLOUD_NAME
        if (preg_match('/cloudinary:\/\/([0-9]+):(.[^@]+)@(.+)/', $cloudinaryUrl, $matches)) {
            $apiKey = $matches[1];
            $apiSecret = $matches[2];
            $cloudName = $matches[3];

            $timestamp = time();
            $folder = 'service_galleries';
            
            // Generate signature untuk keamanan upload Cloudinary v3
            $signatureString = "folder={$folder}&timestamp={$timestamp}{$apiSecret}";
            $signature = sha1($signatureString);

            $response = Http::attach(
                'file', file_get_contents($file->getRealPath()), $file->getClientOriginalName()
            )->post("https://api.cloudinary.com/v1_1/{$cloudName}/image/upload", [
                'api_key' => $apiKey,
                'timestamp' => $timestamp,
                'folder' => $folder,
                'signature' => $signature,
            ]);

            if ($response->successful()) {
                return $response->json()['secure_url'];
            }
        }

        // Fallback jika upload cloud gagal, simpan ke lokal sementara agar tidak 500 error
        return $file->store('service_galleries', 'public');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'galleries.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        $category = ServiceCategory::create([
            'user_id' => Auth::id(),
            'name' => $request->name
        ]);

        if ($request->hasFile('galleries')) {
            foreach ($request->file('galleries') as $file) {
                $secureUrl = $this->uploadToCloudinaryDirect($file);
                $category->galleries()->create(['image_path' => $secureUrl]);
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

        if ($request->hasFile('galleries')) {
            foreach ($request->file('galleries') as $file) {
                $secureUrl = $this->uploadToCloudinaryDirect($file);
                $serviceCategory->galleries()->create(['image_path' => $secureUrl]);
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

        if (!str_starts_with($gallery->image_path, 'http')) {
            Storage::disk('public')->delete($gallery->image_path);
        }
        $gallery->delete();

        return response()->json(['message' => 'Foto dihapus!']);
    }
}