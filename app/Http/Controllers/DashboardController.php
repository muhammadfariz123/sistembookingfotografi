<?php

namespace App\Http\Controllers;

use App\Models\ServiceType;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $services = ServiceType::where('user_id', Auth::id())
            ->orderBy('name')
            ->get();

        return view('dashboard', compact('services'));
    }
}