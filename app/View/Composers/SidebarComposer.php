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

        $companyName = 'Sistem Fotografi';

        if (Auth::check()) {
            $pendingBookingCount = Booking::where('user_id', Auth::id())
                ->where('status', 'Pending Bayar')
                ->count();

            $pendingPaymentCount = Booking::where('user_id', Auth::id())
                ->where('payment_status', 'Tunggu Konfirmasi')
                ->count();
                
            $setting = \App\Models\CompanySetting::where('user_id', Auth::id())->first();
            $companyName = !empty($setting?->company_name) ? $setting->company_name : 'Sistem Fotografi';
        }

        $view->with([
            'sidebarPendingBookingCount' => $pendingBookingCount,
            'sidebarPendingPaymentCount' => $pendingPaymentCount,
            'companyName' => $companyName,
        ]);
    }
}