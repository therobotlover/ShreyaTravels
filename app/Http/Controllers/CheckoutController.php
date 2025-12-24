<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Booking;
use App\Models\Tour;
use App\Services\BkashService;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function checkout(CheckoutRequest $request, BkashService $bkashService)
    {
        $data = $request->validated();
        $tour = Tour::query()->where('is_active', true)->findOrFail($data['tour_id']);

        $baseAmount = $tour->base_price_bdt * (int) $data['travelers'];
        $discount = $this->calculateDiscount($baseAmount, $data['discount_code'] ?? null);
        $total = max(1, $baseAmount - $discount['amount']);

        $booking = Booking::create([
            'reference' => 'TEMP-'.Str::uuid(),
            'user_id' => $request->user()->id,
            'user_email' => $request->user()->email,
            'tour_id' => $tour->id,
            'travel_date' => $data['travel_date'],
            'travelers' => $data['travelers'],
            'note' => $data['note'] ?? null,
            'base_amount' => $baseAmount,
            'discount_amount' => $discount['amount'],
            'total_amount' => $total,
            'discount_code' => $discount['code'],
            'status' => Booking::STATUS_PENDING,
        ]);

        $booking->reference = 'ST-'.now()->format('Y').'-'.str_pad((string) $booking->id, 6, '0', STR_PAD_LEFT);
        $booking->save();

        logger()->info('checkout.created', [
            'booking_id' => $booking->id,
            'tour_id' => $tour->id,
            'user_id' => $request->user()->id,
            'intent' => $data['intent'],
            'total_amount' => $total,
        ]);

        if ($data['intent'] === 'hold') {
            return response()->json([
                'message' => __('messages.booking_held'),
                'redirect_url' => route('dashboard'),
                'booking_id' => $booking->id,
            ]);
        }

        if (! config('bkash.enabled')) {
            return response()->json(['message' => __('messages.bkash_not_configured')], 400);
        }

        try {
            $redirectUrl = $bkashService->createPayment($booking);
        } catch (\Throwable $e) {
            logger()->error('bkash.checkout.failed', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => __('messages.bkash_start_failed'),
            ], 502);
        }

        return response()->json([
            'message' => __('messages.bkash_redirecting'),
            'redirect_url' => $redirectUrl,
            'booking_id' => $booking->id,
        ]);
    }

    private function calculateDiscount(int $baseAmount, ?string $code): array
    {
        $normalized = strtolower(trim((string) $code));
        if ($normalized === '') {
            return ['amount' => 0, 'code' => null];
        }

        $tokens = json_decode((string) env('DISCOUNT_TOKENS', '[]'), true);
        if (! is_array($tokens)) {
            return ['amount' => 0, 'code' => null];
        }

        $token = collect($tokens)
            ->mapWithKeys(fn ($item) => [strtolower(trim((string) ($item['code'] ?? ''))) => $item])
            ->get($normalized);

        if (! $token || empty($token['active'])) {
            return ['amount' => 0, 'code' => null];
        }

        $amount = 0;
        if (($token['type'] ?? '') === 'percent') {
            $amount = (int) round($baseAmount * ((float) ($token['value'] ?? 0) / 100));
        } elseif (($token['type'] ?? '') === 'fixed') {
            $amount = (int) ($token['value'] ?? 0);
        }

        $amount = max(0, min($amount, $baseAmount - 1));

        return ['amount' => $amount, 'code' => $normalized];
    }
}
