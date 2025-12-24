<?php

namespace App\Http\Controllers;

use App\Http\Requests\BkashCreateRequest;
use App\Models\Booking;
use App\Models\Payment;
use App\Services\BkashService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BkashController extends Controller
{
    public function create(BkashCreateRequest $request, BkashService $bkashService)
    {
        $booking = Booking::query()
            ->where('user_id', $request->user()->id)
            ->where('status', Booking::STATUS_PENDING)
            ->findOrFail($request->validated()['booking_id']);

        if (! config('bkash.enabled')) {
            return response()->json(['message' => __('messages.bkash_not_configured')], 400);
        }

        $window = config('limits.bkash_create.active_initiated_window_minutes');
        $already = $booking->payments()
            ->where('status', Payment::STATUS_INITIATED)
            ->where('created_at', '>=', now()->subMinutes($window))
            ->exists();

        if ($already) {
            return response()->json(['message' => __('messages.bkash_active_payment')], 429);
        }

        try {
            $redirectUrl = $bkashService->createPayment($booking);
        } catch (\Throwable $e) {
            Log::error('bkash.create.failed', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['message' => __('messages.bkash_create_failed')], 502);
        }

        return response()->json([
            'message' => __('messages.bkash_redirecting'),
            'redirect_url' => $redirectUrl,
        ]);
    }

    public function callback(Request $request, BkashService $bkashService)
    {
        $payload = $request->all();
        $paymentId = $request->input('paymentID') ?? $request->input('payment_id');
        $status = strtolower((string) $request->input('status'));

        if (! $paymentId) {
            Log::warning('bkash.callback.missing_payment_id', $payload);
            return redirect()->route('dashboard')->with('status', __('messages.bkash_callback_missing_payment_id'));
        }

        $payment = Payment::query()->where('provider_payment_id', $paymentId)->first();
        if (! $payment) {
            Log::warning('bkash.callback.payment_not_found', $payload);
            return redirect()->route('dashboard')->with('status', __('messages.bkash_payment_not_found'));
        }

        if ($status === 'success') {
            try {
                $response = $bkashService->executePayment($payment);
                $payment->status = Payment::STATUS_SUCCESS;
                $payment->trx_id = $response['trxID'] ?? $payment->trx_id;
                $payment->raw_payload = array_merge($payment->raw_payload ?? [], [
                    'callback_payload' => $payload,
                    'execute_response' => $response,
                ]);
                $payment->save();

                $payment->booking()->update(['status' => Booking::STATUS_PAID]);
            } catch (\Throwable $e) {
                Log::error('bkash.execute.failed', [
                    'payment_id' => $payment->id,
                    'error' => $e->getMessage(),
                ]);
                $payment->status = Payment::STATUS_FAILED;
                $payment->raw_payload = array_merge($payment->raw_payload ?? [], [
                    'callback_payload' => $payload,
                    'execute_error' => $e->getMessage(),
                ]);
                $payment->save();
                $payment->booking()->update(['status' => Booking::STATUS_FAILED]);
            }
        } elseif ($status === 'failed' || $status === 'cancelled') {
            $payment->status = Payment::STATUS_FAILED;
            $payment->raw_payload = $payload;
            $payment->save();

            $payment->booking()->update(['status' => Booking::STATUS_FAILED]);
        } else {
            Log::info('bkash.callback.unhandled_status', $payload);
            $payment->raw_payload = $payload;
            $payment->save();
        }

        return redirect()->route('dashboard')->with('status', __('messages.bkash_processed'));
    }
}
