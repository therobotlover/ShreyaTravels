@extends('layouts.app')

@section('content')
    <section class="grid lg:grid-cols-2 gap-10 items-center py-10">
        <div>
            <p class="text-gold uppercase tracking-[0.35em] text-xs">{{ __('ui.hero_label') }}</p>
            <h1 class="font-display text-4xl sm:text-5xl leading-tight mt-3">{{ __('ui.hero_title') }}</h1>
            <p class="text-slate-600 mt-4 max-w-lg">{{ __('ui.hero_subtitle') }}</p>
            <div class="flex flex-wrap items-center gap-4 mt-6">
                <button data-open-login class="px-5 py-3 rounded-full bg-ink text-white text-sm tracking-wide">{{ __('ui.hero_cta_start_otp') }}</button>
                <span class="text-sm text-slate-500">{{ __('ui.hero_cta_pay_or_hold') }}</span>
            </div>
        </div>
        <div class="glass rounded-3xl p-6 border border-white/60 shadow-lg">
            <div class="grid gap-4">
                <div class="flex items-center justify-between">
                    <span class="text-xs uppercase tracking-[0.25em] text-slate-500">{{ __('ui.ongoing_trips') }}</span>
                    <span class="text-gold text-xs">{{ __('ui.handpicked') }}</span>
                </div>
                <div class="grid gap-3">
                    @foreach($featured as $tour)
                        <div class="flex items-center justify-between border border-slate-200/60 rounded-2xl px-4 py-3">
                            <div>
                                <p class="font-medium text-slate-800">{{ $tour->title }}</p>
                                <p class="text-xs text-slate-500">{{ $tour->location }} - {{ __('ui.starts') }} {{ optional($tour->next_start_date)->locale(app()->getLocale())->translatedFormat('d M, Y') }}</p>
                            </div>
                            <button data-tour='@json($tour)' class="text-xs uppercase tracking-[0.25em] text-gold">{{ __('ui.book_now') }}</button>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="py-6">
        <div class="flex items-center justify-between">
            <h2 class="font-display text-2xl">{{ __('ui.why_title') }}</h2>
            <span class="text-xs uppercase tracking-[0.3em] text-slate-400">{{ __('ui.why_label') }}</span>
        </div>
        <div class="grid md:grid-cols-3 gap-6 mt-6">
            <div class="glass border border-white/60 rounded-3xl p-5">
                <p class="text-xs uppercase tracking-[0.3em] text-slate-500">{{ __('ui.why_safety_label') }}</p>
                <h3 class="font-display text-xl mt-2">{{ __('ui.why_safety_title') }}</h3>
                <p class="text-sm text-slate-600 mt-3">{{ __('ui.why_safety_body') }}</p>
            </div>
            <div class="glass border border-white/60 rounded-3xl p-5">
                <p class="text-xs uppercase tracking-[0.3em] text-slate-500">{{ __('ui.why_premium_label') }}</p>
                <h3 class="font-display text-xl mt-2">{{ __('ui.why_premium_title') }}</h3>
                <p class="text-sm text-slate-600 mt-3">{{ __('ui.why_premium_body') }}</p>
            </div>
            <div class="glass border border-white/60 rounded-3xl p-5">
                <p class="text-xs uppercase tracking-[0.3em] text-slate-500">{{ __('ui.why_trust_label') }}</p>
                <h3 class="font-display text-xl mt-2">{{ __('ui.why_trust_title') }}</h3>
                <p class="text-sm text-slate-600 mt-3">{{ __('ui.why_trust_body') }}</p>
            </div>
        </div>
    </section>

    <section class="py-6">
        <div class="flex items-center justify-between">
            <h2 class="font-display text-2xl">{{ __('ui.all_tours') }}</h2>
            <span class="text-xs uppercase tracking-[0.3em] text-slate-400">{{ __('ui.curated') }}</span>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
            @foreach($tours as $tour)
                <div class="glass border border-white/60 rounded-3xl p-5 flex flex-col">
                    <div class="h-40 rounded-2xl bg-cover bg-center" style="background-image: url('{{ $tour->hero_image_url }}');"></div>
                    <div class="mt-4">
                        <p class="text-xs uppercase tracking-[0.3em] text-slate-500">{{ $tour->location }}</p>
                        <h3 class="font-display text-xl mt-1">{{ $tour->title }}</h3>
                        <p class="text-sm text-slate-600 mt-2">{{ trans_choice('ui.days', $tour->duration_days, ['count' => $tour->duration_days]) }} - BDT {{ number_format($tour->base_price_bdt) }} {{ __('ui.per_traveler') }}</p>
                    </div>
                    <div class="mt-4 flex items-center justify-between">
                        <span class="text-xs text-slate-500">{{ __('ui.next_start') }} {{ optional($tour->next_start_date)->locale(app()->getLocale())->translatedFormat('d M, Y') }}</span>
                        <button data-tour='@json($tour)' class="px-4 py-2 rounded-full border border-gold text-gold text-xs uppercase tracking-[0.2em]">{{ __('ui.book_now') }}</button>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <div id="booking-modal" class="fixed inset-0 hidden items-center justify-center bg-black/40 z-40">
        <div class="bg-white rounded-3xl w-[95%] max-w-2xl p-6 relative">
            <button class="absolute top-4 right-4 text-slate-400" data-close-modal>{{ __('ui.modal_close') }}</button>
            <div class="flex items-center justify-between mb-6">
                <div>
                    <p class="text-xs uppercase tracking-[0.3em] text-slate-500">{{ __('ui.modal_secure_booking') }}</p>
                    <h3 class="font-display text-2xl">{{ __('ui.modal_stepper_title') }}</h3>
                </div>
                <span class="text-xs text-gold">{{ __('ui.modal_otp_verified_only') }}</span>
            </div>

            <div id="step-1" class="space-y-4">
                <h4 class="font-medium">{{ __('ui.step1_title') }}</h4>
                <input id="otp-email" type="email" placeholder="{{ __('ui.email_placeholder') }}" class="w-full border border-slate-200 rounded-xl px-4 py-3">
                <button id="send-otp" class="w-full py-3 rounded-xl bg-ink text-white">{{ __('ui.send_otp') }}</button>
                <p id="otp-request-msg" class="text-sm text-slate-500"></p>
            </div>

            <div id="step-2" class="space-y-4 hidden">
                <h4 class="font-medium">{{ __('ui.step2_title') }}</h4>
                <div class="grid grid-cols-6 gap-2">
                    @for($i = 0; $i < 6; $i++)
                        <input maxlength="1" class="otp-input text-center border border-slate-200 rounded-xl py-3 text-lg">
                    @endfor
                </div>
                <button id="verify-otp" class="w-full py-3 rounded-xl bg-ink text-white">{{ __('ui.verify_otp') }}</button>
                <p id="otp-verify-msg" class="text-sm text-slate-500"></p>
            </div>

            <div id="step-3" class="space-y-4 hidden">
                <h4 class="font-medium">{{ __('ui.step3_title') }}</h4>
                <div class="grid gap-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <p id="selected-tour" class="font-medium"></p>
                            <p class="text-xs text-slate-500">{{ __('ui.total_auto') }}</p>
                        </div>
                        <span id="base-rate" class="text-sm text-gold"></span>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-3">
                        <input id="travel-date" type="date" class="w-full border border-slate-200 rounded-xl px-4 py-3">
                        <input id="travelers" type="number" min="1" max="20" value="2" class="w-full border border-slate-200 rounded-xl px-4 py-3">
                    </div>
                    <textarea id="note" placeholder="{{ __('ui.note_placeholder') }}" class="w-full border border-slate-200 rounded-xl px-4 py-3"></textarea>
                    <input id="discount-code" placeholder="{{ __('ui.discount_placeholder') }}" class="w-full border border-slate-200 rounded-xl px-4 py-3">
                    <div class="border border-slate-200 rounded-2xl p-4 space-y-1">
                        <div class="flex justify-between text-sm">
                            <span>{{ __('ui.price_base') }}</span>
                            <span id="base-amount">BDT 0</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span>{{ __('ui.price_discount') }}</span>
                            <span id="discount-amount">BDT 0</span>
                        </div>
                        <div class="flex justify-between font-medium">
                            <span>{{ __('ui.price_total') }}</span>
                            <span id="total-amount">BDT 0</span>
                        </div>
                    </div>
                    @if (! $bkashEnabled)
                        <p class="text-xs text-amber-600">{{ __('messages.bkash_not_configured') }}</p>
                    @endif
                    <div class="grid sm:grid-cols-2 gap-3">
                        <button id="pay-now" class="py-3 rounded-xl bg-ink text-white {{ $bkashEnabled ? '' : 'opacity-50 cursor-not-allowed' }}" @if(! $bkashEnabled) disabled @endif>{{ __('ui.pay_with_bkash') }}</button>
                        <button id="hold-now" class="py-3 rounded-xl border border-slate-300 text-slate-700">{{ __('ui.hold_pay_later') }}</button>
                    </div>
                    <p id="checkout-msg" class="text-sm text-slate-500"></p>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    const modal = document.getElementById('booking-modal');
    const step1 = document.getElementById('step-1');
    const step2 = document.getElementById('step-2');
    const step3 = document.getElementById('step-3');
    const otpEmail = document.getElementById('otp-email');
    const sendOtpBtn = document.getElementById('send-otp');
    const verifyOtpBtn = document.getElementById('verify-otp');
    const otpInputs = Array.from(document.querySelectorAll('.otp-input'));
    const selectedTourEl = document.getElementById('selected-tour');
    const baseRateEl = document.getElementById('base-rate');
    const baseAmountEl = document.getElementById('base-amount');
    const discountAmountEl = document.getElementById('discount-amount');
    const totalAmountEl = document.getElementById('total-amount');
    const travelDateEl = document.getElementById('travel-date');
    const travelersEl = document.getElementById('travelers');
    const noteEl = document.getElementById('note');
    const discountCodeEl = document.getElementById('discount-code');
    const payNowBtn = document.getElementById('pay-now');
    const holdNowBtn = document.getElementById('hold-now');
    const otpRequestMsg = document.getElementById('otp-request-msg');
    const otpVerifyMsg = document.getElementById('otp-verify-msg');
    const checkoutMsg = document.getElementById('checkout-msg');

    const discountTokens = @json($discountTokens);
    const bkashEnabled = @json($bkashEnabled);
    const i18n = {
        otpSending: @json(__('ui.otp_sending')),
        otpSent: @json(__('ui.otp_sent')),
        otpSendFailed: @json(__('ui.otp_send_failed')),
        otpVerifying: @json(__('ui.otp_verifying')),
        otpVerified: @json(__('ui.otp_verified')),
        otpVerifyFailed: @json(__('ui.otp_verify_failed')),
        selectedTourPrompt: @json(__('ui.selected_tour_prompt')),
        selectTourFirst: @json(__('ui.select_tour_first')),
        submitting: @json(__('ui.submitting')),
        sessionExpired: @json(__('ui.session_expired')),
        checkoutFailed: @json(__('ui.checkout_failed')),
        perTraveler: @json(__('ui.per_traveler')),
    };
    let selectedTour = null;

    const today = new Date().toISOString().split('T')[0];
    if (travelDateEl) {
        travelDateEl.min = today;
        if (!travelDateEl.value) {
            travelDateEl.value = today;
        }
    }

    const showStep = (step) => {
        step1.classList.add('hidden');
        step2.classList.add('hidden');
        step3.classList.add('hidden');
        step.classList.remove('hidden');
    };

    const openModal = () => {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    };

    const closeModal = () => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    };

    document.querySelectorAll('[data-open-login]').forEach(btn => {
        btn.addEventListener('click', () => {
            selectedTour = null;
            populateTour();
            openModal();
            showStep(step1);
        });
    });

    document.querySelectorAll('[data-tour]').forEach(btn => {
        btn.addEventListener('click', () => {
            selectedTour = JSON.parse(btn.getAttribute('data-tour'));
            populateTour();
            openModal();
            @if(auth()->check())
                showStep(step3);
            @else
                showStep(step1);
            @endif
        });
    });

    document.querySelector('[data-close-modal]')?.addEventListener('click', closeModal);

    otpInputs.forEach((input, index) => {
        input.addEventListener('input', (e) => {
            if (e.target.value && index < otpInputs.length - 1) {
                otpInputs[index + 1].focus();
            }
        });
    });

    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const readJson = async (response) => {
        try {
            return await response.json();
        } catch (err) {
            return null;
        }
    };

    const responseIsJson = (response) =>
        response.headers.get('content-type')?.includes('application/json');

    sendOtpBtn?.addEventListener('click', async () => {
        otpRequestMsg.textContent = i18n.otpSending;
        const res = await fetch('/auth/request-otp', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json',
            },
            credentials: 'same-origin',
            body: JSON.stringify({ email: otpEmail.value }),
        });
        const data = await readJson(res);
        otpRequestMsg.textContent = data?.message || (res.ok ? i18n.otpSent : i18n.otpSendFailed);
        if (res.ok) {
            showStep(step2);
        }
    });

    verifyOtpBtn?.addEventListener('click', async () => {
        otpVerifyMsg.textContent = i18n.otpVerifying;
        const otp = otpInputs.map(input => input.value).join('');
        const res = await fetch('/auth/verify-otp', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json',
            },
            credentials: 'same-origin',
            body: JSON.stringify({ email: otpEmail.value, otp }),
        });
        const data = await readJson(res);
        otpVerifyMsg.textContent = data?.message || (res.ok ? i18n.otpVerified : i18n.otpVerifyFailed);
        if (res.ok) {
            showStep(step3);
        }
    });

    const normalizeTokens = () => {
        const map = {};
        if (Array.isArray(discountTokens)) {
            discountTokens.forEach(token => {
                const key = (token.code || '').toString().trim().toLowerCase();
                if (key) {
                    map[key] = token;
                }
            });
        }
        return map;
    };

    const tokenMap = normalizeTokens();

    const updateTotals = () => {
        if (!selectedTour) return;
        const travelers = parseInt(travelersEl.value || '1', 10);
        const base = selectedTour.base_price_bdt * travelers;
        let discount = 0;
        const code = (discountCodeEl.value || '').trim().toLowerCase();
        const token = tokenMap[code];
        if (token && token.active) {
            if (token.type === 'percent') {
                discount = Math.round(base * (Number(token.value) / 100));
            } else if (token.type === 'fixed') {
                discount = Number(token.value || 0);
            }
        }
        discount = Math.max(0, Math.min(discount, base - 1));
        const total = Math.max(1, base - discount);
        baseAmountEl.textContent = `BDT ${base.toLocaleString()}`;
        discountAmountEl.textContent = `BDT ${discount.toLocaleString()}`;
        totalAmountEl.textContent = `BDT ${total.toLocaleString()}`;
    };

    const populateTour = () => {
        if (!selectedTour) {
            selectedTourEl.textContent = i18n.selectedTourPrompt;
            baseRateEl.textContent = '';
            baseAmountEl.textContent = 'BDT 0';
            discountAmountEl.textContent = 'BDT 0';
            totalAmountEl.textContent = 'BDT 0';
            payNowBtn.disabled = true;
            holdNowBtn.disabled = true;
            return;
        }
        selectedTourEl.textContent = `${selectedTour.title} - ${selectedTour.location}`;
        baseRateEl.textContent = `BDT ${Number(selectedTour.base_price_bdt).toLocaleString()} ${i18n.perTraveler}`;
        payNowBtn.disabled = !bkashEnabled;
        holdNowBtn.disabled = false;
        updateTotals();
    };

    travelersEl?.addEventListener('input', updateTotals);
    discountCodeEl?.addEventListener('input', updateTotals);

    const submitCheckout = async (intent) => {
        if (!selectedTour) {
            checkoutMsg.textContent = i18n.selectTourFirst;
            return;
        }
        checkoutMsg.textContent = i18n.submitting;
        const payload = {
            tour_id: selectedTour.id,
            travel_date: travelDateEl.value,
            travelers: travelersEl.value || 2,
            note: noteEl.value,
            discount_code: discountCodeEl.value,
            intent,
        };
        const res = await fetch('/checkout', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json',
            },
            credentials: 'same-origin',
            body: JSON.stringify(payload),
        });
        const data = responseIsJson(res) ? await readJson(res) : null;
        if (res.redirected || res.status === 401 || res.status === 419) {
            checkoutMsg.textContent = i18n.sessionExpired;
            showStep(step1);
            return;
        }
        if (res.ok && data?.redirect_url) {
            window.location.href = data.redirect_url;
            return;
        }
        const errorMessage = data?.message
            || (data?.errors ? Object.values(data.errors).flat().join(' ') : null)
            || `${i18n.checkoutFailed} (${res.status}).`;
        checkoutMsg.textContent = errorMessage;
    };

    payNowBtn?.addEventListener('click', () => submitCheckout('pay'));
    holdNowBtn?.addEventListener('click', () => submitCheckout('hold'));

    populateTour();
</script>
@endsection
