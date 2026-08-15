<?php

namespace App\Http\Controllers;

use App\Models\ServiceType;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceTypeController extends Controller
{
    public function index()
    {
        $services = ServiceType::with('category')->where('user_id', Auth::id())->latest()->get();
        return view('service-types.index', compact('services'));
    }

    public function create()
    {
        $categories = ServiceCategory::where('user_id', Auth::id())->orderBy('name')->get();
        return view('service-types.form', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_category_id' => 'required|exists:service_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'duration' => 'nullable|integer|min:0', // Durasi dalam satuan Jam
            'photo_limit' => 'nullable|integer|min:0',
        ]);
        
        $validated['user_id'] = Auth::id();
        ServiceType::create($validated);

        return redirect()->route('service-types.index')->with('success', 'Paket Layanan berhasil ditambahkan!');
    }

    public function edit(ServiceType $serviceType)
    {
        if ($serviceType->user_id !== Auth::id()) abort(403);
        
        $categories = ServiceCategory::where('user_id', Auth::id())->orderBy('name')->get();
        
        return view('service-types.form', [
            'service' => $serviceType,
            'categories' => $categories
        ]);
    }

    public function update(Request $request, ServiceType $serviceType)
    {
        if ($serviceType->user_id !== Auth::id()) abort(403);

        $validated = $request->validate([
            'service_category_id' => 'required|exists:service_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'duration' => 'nullable|integer|min:0', // Durasi dalam satuan Jam
            'photo_limit' => 'nullable|integer|min:0',
        ]);
        
        $serviceType->update($validated);

        return redirect()->route('service-types.index')->with('success', 'Paket Layanan diperbarui!');
    }

    public function destroy(ServiceType $serviceType)
    {
        if ($serviceType->user_id !== Auth::id()) abort(403);
        if ($serviceType->bookings()->exists()) {
            return redirect()->back()->with('error', 'Layanan masih digunakan dalam booking.');
        }

        $serviceType->delete();

        return redirect()->route('service-types.index')->with('success', 'Paket Layanan berhasil dihapus!');
    }
}