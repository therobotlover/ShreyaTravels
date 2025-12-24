@extends('layouts.app')

@section('content')
    @php
        $statusLabels = [
            \App\Models\Booking::STATUS_PENDING => __('ui.status_pending'),
            \App\Models\Booking::STATUS_PAID => __('ui.status_paid'),
            \App\Models\Booking::STATUS_FAILED => __('ui.status_failed'),
            \App\Models\Booking::STATUS_CANCELLED => __('ui.status_cancelled'),
        ];
        $paymentStatusLabels = [
            \App\Models\Payment::STATUS_INITIATED => __('ui.payment_status_initiated'),
            \App\Models\Payment::STATUS_SUCCESS => __('ui.payment_status_success'),
            \App\Models\Payment::STATUS_FAILED => __('ui.payment_status_failed'),
        ];
    @endphp
    <section class="py-8">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-display text-3xl">{{ __('ui.booking_title', ['reference' => $booking->reference]) }}</h2>
                <p class="text-sm text-slate-500">{{ __('ui.booking_detail_subtitle') }}</p>
            </div>
            <a href="{{ route('admin.index') }}" class="text-sm text-gold">{{ __('ui.back_to_admin') }}</a>
        </div>

        @if(session('status'))
            <div class="mt-4 p-3 rounded-xl bg-amber-50 text-amber-700 text-sm">
                {{ session('status') }}
            </div>
        @endif

        <div class="mt-6 grid lg:grid-cols-2 gap-6">
            <div class="glass rounded-2xl p-5 space-y-2">
                <p class="text-xs uppercase tracking-[0.3em] text-slate-500">{{ __('ui.booking_section_title') }}</p>
                <p><span class="text-slate-500">{{ __('ui.user_label') }}</span> {{ $booking->user_email }}</p>
                <p><span class="text-slate-500">{{ __('ui.tour_label') }}</span> {{ $booking->tour->title ?? __('ui.tour_fallback') }}</p>
                <p><span class="text-slate-500">{{ __('ui.travel_date_label') }}</span> {{ $booking->travel_date->locale(app()->getLocale())->translatedFormat('d M, Y') }}</p>
                <p><span class="text-slate-500">{{ __('ui.travelers_label') }}</span> {{ $booking->travelers }}</p>
                <p><span class="text-slate-500">{{ __('ui.status_label') }}</span> {{ $statusLabels[$booking->status] ?? $booking->status }}</p>
                <p><span class="text-slate-500">{{ __('ui.total_label') }}</span> BDT {{ number_format($booking->total_amount) }}</p>
                @if($booking->note)
                    <p><span class="text-slate-500">{{ __('ui.note_label') }}</span> {{ $booking->note }}</p>
                @endif
            </div>
            <div class="glass rounded-2xl p-5 space-y-2">
                <p class="text-xs uppercase tracking-[0.3em] text-slate-500">{{ __('ui.payments_section_title') }}</p>
                @forelse($booking->payments as $payment)
                    <div class="border border-slate-200 rounded-xl p-3">
                        <p><span class="text-slate-500">{{ __('ui.provider_label') }}</span> {{ $payment->provider }}</p>
                        <p><span class="text-slate-500">{{ __('ui.status_label') }}</span> {{ $paymentStatusLabels[$payment->status] ?? $payment->status }}</p>
                        <p><span class="text-slate-500">{{ __('ui.amount_label') }}</span> BDT {{ number_format($payment->amount) }}</p>
                        <details class="mt-2 text-xs">
                            <summary class="cursor-pointer text-slate-500">{{ __('ui.raw_payload') }}</summary>
                            <pre class="whitespace-pre-wrap">{{ json_encode($payment->raw_payload, JSON_PRETTY_PRINT) }}</pre>
                        </details>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">{{ __('ui.no_payments') }}</p>
                @endforelse
            </div>
        </div>

        @if($booking->status !== \App\Models\Booking::STATUS_PAID)
            <form method="POST" action="{{ route('admin.bookings.cancel', $booking) }}" class="mt-6">
                @csrf
                <button class="px-4 py-2 rounded-full border border-slate-300 text-slate-700 text-xs uppercase tracking-[0.2em]">{{ __('ui.cancel_booking') }}</button>
            </form>
        @endif
    </section>
@endsection
