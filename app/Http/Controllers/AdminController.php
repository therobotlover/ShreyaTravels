<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::query()->latest();

        if ($request->filled('status')) {
            $query->where('status', (string) $request->input('status'));
        }

        if ($request->filled('q')) {
            $term = (string) $request->input('q');
            $query->where(function ($q) use ($term) {
                $q->where('reference', 'like', "%{$term}%")
                    ->orWhere('user_email', 'like', "%{$term}%");
            });
        }

        $bookings = $query->paginate(15)->withQueryString();

        return view('admin.index', [
            'bookings' => $bookings,
            'counts' => [
                'total' => Booking::count(),
                'pending' => Booking::where('status', Booking::STATUS_PENDING)->count(),
                'paid' => Booking::where('status', Booking::STATUS_PAID)->count(),
                'failed' => Booking::where('status', Booking::STATUS_FAILED)->count(),
            ],
        ]);
    }

    public function show(Booking $booking)
    {
        $booking->load('tour', 'payments');

        return view('admin.show', [
            'booking' => $booking,
        ]);
    }

    public function cancel(Booking $booking)
    {
        if ($booking->status !== Booking::STATUS_PAID) {
            $booking->status = Booking::STATUS_CANCELLED;
            $booking->save();
        }

        return redirect()->route('admin.bookings.show', $booking)->with('status', __('messages.admin_booking_updated'));
    }
}
