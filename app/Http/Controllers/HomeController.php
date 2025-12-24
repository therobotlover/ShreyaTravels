<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $featured = Tour::query()
            ->where('is_active', true)
            ->where('is_featured_ongoing', true)
            ->orderBy('next_start_date')
            ->take(4)
            ->get();

        $tours = Tour::query()
            ->where('is_active', true)
            ->orderBy('title')
            ->get();

        return view('home', [
            'featured' => $featured,
            'tours' => $tours,
            'bkashEnabled' => config('bkash.enabled'),
            'discountTokens' => json_decode((string) env('DISCOUNT_TOKENS', '[]'), true) ?: [],
        ]);
    }
}

