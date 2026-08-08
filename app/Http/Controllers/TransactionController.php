<?php

namespace App\Http\Controllers;

use App\Models\ServiceType;
use App\Models\PaymentTransaction; // Pastikan model di-import
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index()
    {
        // Hanya melempar data service untuk filter/dropdown jika dibutuhkan di UI
        $services = ServiceType::where('user_id', Auth::id())->orderBy('name')->get();
        return view('transactions.index', compact('services'));
    }

    // Fungsi khusus untuk menghapus data Payment Transaction
    public function bulkDelete(Request $request)
    {
        // Validasi pastikan ID ada di tabel payment_transactions
        $request->validate([
            'ids' => 'required|array', 
            'ids.*' => 'exists:payment_transactions,id'
        ]);

        // Hapus data transaksi berdasarkan ID yang dikirim
        PaymentTransaction::whereIn('id', $request->ids)->delete();

        return response()->json([
            'success' => true,
            'message' => count($request->ids) . ' data transaksi berhasil dihapus.'
        ]);
    }
}