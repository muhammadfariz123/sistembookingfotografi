<?php

namespace App\Http\Controllers;

use App\Models\ServiceType;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index()
    {
        // Hanya melempar data service untuk filter/dropdown jika dibutuhkan di UI
        $services = ServiceType::where('user_id', Auth::id())->orderBy('name')->get();
        return view('transactions.index', compact('services'));
    }
}