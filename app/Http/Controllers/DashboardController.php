<?php

namespace App\Http\Controllers;

use App\Models\Booking;

class DashboardController extends Controller
{
    public function index()
    {
        $bookings = Booking::query()
            ->where('user_id', auth()->id())
            ->with('tour')
            ->latest()
            ->paginate(10);

        return view('dashboard', [
            'bookings' => $bookings,
            'bkashEnabled' => config('bkash.enabled'),
        ]);
    }
}

