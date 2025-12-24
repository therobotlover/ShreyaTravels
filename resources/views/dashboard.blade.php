@extends('layouts.app')

@section('content')
    @php
        $statusLabels = [
            \App\Models\Booking::STATUS_PENDING => __('ui.status_pending'),
            \App\Models\Booking::STATUS_PAID => __('ui.status_paid'),
            \App\Models\Booking::STATUS_FAILED => __('ui.status_failed'),
            \App\Models\Booking::STATUS_CANCELLED => __('ui.status_cancelled'),
        ];
    @endphp
    <section class="py-8">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-display text-3xl">{{ __('ui.dashboard_title') }}</h2>
                <p class="text-sm text-slate-500">{{ __('ui.dashboard_subtitle') }}</p>
            </div>
            <a href="{{ route('home') }}" class="text-sm text-gold">{{ __('ui.explore_tours') }}</a>
        </div>

        @if(session('status'))
            <div class="mt-4 p-3 rounded-xl bg-amber-50 text-amber-700 text-sm">
                {{ session('status') }}
            </div>
        @endif

        <div class="mt-6 space-y-4">
            @forelse($bookings as $booking)
                <div class="glass border border-white/60 rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <p class="text-xs uppercase tracking-[0.3em] text-slate-500">{{ $booking->reference }}</p>
                        <p class="font-medium">{{ $booking->tour->title ?? __('ui.tour_fallback') }}</p>
                        <p class="text-sm text-slate-500">{{ __('ui.travel_date_label') }} {{ $booking->travel_date->locale(app()->getLocale())->translatedFormat('d M, Y') }} - {{ __('ui.travelers_label') }} {{ $booking->travelers }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-slate-500">{{ __('ui.status_label') }}</p>
                        <p class="font-medium">{{ $statusLabels[$booking->status] ?? $booking->status }}</p>
                        <p class="text-sm text-slate-500">{{ __('ui.total_label') }} BDT {{ number_format($booking->total_amount) }}</p>
                        @if($bkashEnabled && $booking->status === \App\Models\Booking::STATUS_PENDING)
                            <button data-booking-id="{{ $booking->id }}" class="mt-2 px-4 py-2 rounded-full border border-gold text-gold text-xs uppercase tracking-[0.2em]">{{ __('ui.pay_with_bkash_short') }}</button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="glass border border-white/60 rounded-2xl p-6 text-center text-slate-500">
                    {{ __('ui.no_bookings') }}
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $bookings->links() }}
        </div>
    </section>
@endsection

@section('scripts')
<script>
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const readJson = async (response) => {
        try {
            return await response.json();
        } catch (err) {
            return null;
        }
    };
    document.querySelectorAll('[data-booking-id]').forEach(btn => {
        btn.addEventListener('click', async () => {
            const bookingId = btn.getAttribute('data-booking-id');
            const res = await fetch('/pay/bkash/create', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                },
                credentials: 'same-origin',
                body: JSON.stringify({ booking_id: bookingId }),
            });
            const data = await readJson(res);
            if (res.ok && data?.redirect_url) {
                window.location.href = data.redirect_url;
            } else {
                alert(data?.message || `${@json(__('ui.payment_start_failed'))} (${res.status}).`);
            }
        });
    });
</script>
@endsection
