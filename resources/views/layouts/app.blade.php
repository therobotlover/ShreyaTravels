<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('ui.brand_name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600&family=Noto+Serif+Bengali:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        gold: '#B08D57',
                        ink: '#0F172A',
                    },
                    fontFamily: {
                        display: ['Noto Serif Bengali', 'serif'],
                        body: ['Hind Siliguri', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Hind Siliguri', sans-serif;
            background: radial-gradient(1200px 600px at 10% -10%, rgba(176, 141, 87, 0.18), transparent 60%),
                        radial-gradient(900px 500px at 110% 10%, rgba(15, 23, 42, 0.08), transparent 60%),
                        #f8f5f0;
            color: #0f172a;
        }
        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
        }
    </style>
</head>
<body class="min-h-screen">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <header class="py-6 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-full border border-gold/50 flex items-center justify-center text-gold">
                    ST
                </div>
                <div>
                    <p class="font-display text-xl tracking-wide">{{ __('ui.brand_name') }}</p>
                    <p class="text-xs uppercase tracking-[0.3em] text-slate-500">{{ __('ui.brand_tagline') }}</p>
                </div>
            </div>
            <div class="flex items-center gap-4 text-sm">
                @php
                    $locale = app()->getLocale();
                    $toggleLocale = $locale === 'bn' ? 'en' : 'bn';
                    $toggleLabel = $locale === 'bn' ? __('ui.switch_to_en') : __('ui.switch_to_bn');
                @endphp
                <a href="{{ url("/lang/{$toggleLocale}") }}" class="px-3 py-1.5 rounded-full border border-slate-300 text-xs uppercase tracking-[0.2em] text-slate-600 hover:text-ink">
                    {{ $toggleLabel }}
                </a>
                @auth
                    <a href="{{ route('dashboard') }}" class="text-slate-700 hover:text-ink">{{ __('ui.nav_dashboard') }}</a>
                    @php
                        $adminEmails = collect(explode(',', (string) config('app.admin_emails', '')))
                            ->map(fn ($email) => strtolower(trim($email)))
                            ->filter();
                    @endphp
                    @if ($adminEmails->contains(strtolower(auth()->user()->email)))
                        <a href="{{ route('admin.index') }}" class="text-slate-700 hover:text-ink">{{ __('ui.nav_admin') }}</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="text-slate-700 hover:text-ink">{{ __('ui.nav_logout') }}</button>
                    </form>
                @else
                    <button data-open-login class="text-slate-700 hover:text-ink">{{ __('ui.nav_login') }}</button>
                @endauth
            </div>
        </header>

        @yield('content')

        <footer class="py-10 text-xs text-slate-500">
            {{ __('ui.footer_tagline') }}
        </footer>
    </div>

    @yield('scripts')
</body>
</html>
