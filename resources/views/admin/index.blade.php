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
                <h2 class="font-display text-3xl">{{ __('ui.admin_overview_title') }}</h2>
                <p class="text-sm text-slate-500">{{ __('ui.admin_overview_subtitle') }}</p>
            </div>
            <a href="{{ route('home') }}" class="text-sm text-gold">{{ __('ui.back_to_site') }}</a>
        </div>

        <div class="grid sm:grid-cols-4 gap-4 mt-6">
            <div class="glass rounded-2xl p-4">
                <p class="text-xs uppercase tracking-[0.3em] text-slate-500">{{ __('ui.total_label_short') }}</p>
                <p class="text-2xl font-display">{{ $counts['total'] }}</p>
            </div>
            <div class="glass rounded-2xl p-4">
                <p class="text-xs uppercase tracking-[0.3em] text-slate-500">{{ __('ui.pending_label_short') }}</p>
                <p class="text-2xl font-display">{{ $counts['pending'] }}</p>
            </div>
            <div class="glass rounded-2xl p-4">
                <p class="text-xs uppercase tracking-[0.3em] text-slate-500">{{ __('ui.paid_label_short') }}</p>
                <p class="text-2xl font-display">{{ $counts['paid'] }}</p>
            </div>
            <div class="glass rounded-2xl p-4">
                <p class="text-xs uppercase tracking-[0.3em] text-slate-500">{{ __('ui.failed_label_short') }}</p>
                <p class="text-2xl font-display">{{ $counts['failed'] }}</p>
            </div>
        </div>

        <form method="GET" class="mt-6 flex flex-wrap gap-3 items-end">
            <div>
                <label class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ __('ui.status_label') }}</label>
                <select name="status" class="border border-slate-200 rounded-xl px-3 py-2 text-sm">
                    <option value="">{{ __('ui.all_label') }}</option>
                    @foreach([\App\Models\Booking::STATUS_PENDING, \App\Models\Booking::STATUS_PAID, \App\Models\Booking::STATUS_FAILED, \App\Models\Booking::STATUS_CANCELLED] as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ $statusLabels[$status] ?? $status }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ __('ui.search_label') }}</label>
                <input name="q" value="{{ request('q') }}" placeholder="{{ __('ui.search_placeholder') }}" class="border border-slate-200 rounded-xl px-3 py-2 text-sm">
            </div>
            <button class="px-4 py-2 rounded-full bg-ink text-white text-xs uppercase tracking-[0.2em]">{{ __('ui.filter_label') }}</button>
        </form>

        <div class="mt-6 space-y-3">
            @foreach($bookings as $booking)
                <div class="glass border border-white/60 rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <p class="text-xs uppercase tracking-[0.3em] text-slate-500">{{ $booking->reference }}</p>
                        <p class="font-medium">{{ $booking->user_email }}</p>
                        <p class="text-sm text-slate-500">{{ __('ui.tour_label') }} #{{ $booking->tour_id }} - {{ $booking->travel_date->locale(app()->getLocale())->translatedFormat('d M, Y') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-slate-500">{{ __('ui.status_label') }}</p>
                        <p class="font-medium">{{ $statusLabels[$booking->status] ?? $booking->status }}</p>
                        <a href="{{ route('admin.bookings.show', $booking) }}" class="text-xs uppercase tracking-[0.2em] text-gold">{{ __('ui.view_label') }}</a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $bookings->links() }}
        </div>
    </section>
@endsection
