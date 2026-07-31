<?php

namespace App\View\Composers;

use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SidebarComposer
{
    public function compose(View $view)
    {
        $pendingBookingCount = 0;
        $pendingPaymentCount = 0;

        if (Auth::check()) {
            $pendingBookingCount = Booking::where('user_id', Auth::id())
                ->where('status', 'Pending Bayar')
                ->count();

            $pendingPaymentCount = Booking::where('user_id', Auth::id())
                ->where('payment_status', 'Tunggu Konfirmasi')
                ->count();
        }

        $view->with([
            'sidebarPendingBookingCount' => $pendingBookingCount,
            'sidebarPendingPaymentCount' => $pendingPaymentCount,
        ]);
    }
}