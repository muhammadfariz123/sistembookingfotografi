<?php

namespace App\Http\Controllers;

use App\Models\ServiceType;
use App\Http\Requests\ServiceTypeRequest;
use Illuminate\Support\Facades\Auth;

class ServiceTypeController extends Controller
{
    public function index()
    {
        $services = ServiceType::where('user_id', Auth::id())
            ->orderBy('name')
            ->get();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $services,
            ]);
        }

        return view('service-types.index', compact('services'));
    }

    public function store(ServiceTypeRequest $request)
    {
        $validated = $request->validated();

        $price = isset($validated['price'])
            ? (int) preg_replace('/[^0-9]/', '', (string) $validated['price'])
            : 0;

        $service = ServiceType::create([
            'user_id' => Auth::id(),
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price' => $price,
        ]);

        // AJAX request → return JSON
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Layanan berhasil ditambahkan.',
                'data' => $service,
            ], 201);
        }

        // Form biasa → redirect
        return redirect()
            ->route('service-types.index')
            ->with('success', 'Layanan berhasil ditambahkan.');
    }

    public function update(ServiceTypeRequest $request, ServiceType $serviceType)
    {
        if ($serviceType->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validated();

        $price = isset($validated['price'])
            ? (int) preg_replace('/[^0-9]/', '', (string) $validated['price'])
            : 0;

        $serviceType->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price' => $price,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Layanan berhasil diperbarui.',
                'data' => $serviceType->fresh(),
            ]);
        }

        return redirect()
            ->route('service-types.index')
            ->with('success', 'Layanan berhasil diperbarui.');
    }

    public function destroy(ServiceType $serviceType)
    {
        if ($serviceType->user_id !== Auth::id()) {
            abort(403);
        }

        try {
            $serviceType->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            // Foreign key constraint — layanan masih dipakai booking
            return response()->json([
                'success' => false,
                'message' => "Layanan \"{$serviceType->name}\" tidak bisa dihapus karena masih digunakan oleh data booking.",
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => "Layanan \"{$serviceType->name}\" berhasil dihapus.",
        ]);
    }
}