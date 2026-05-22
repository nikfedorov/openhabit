<!DOCTYPE html>
@php
    use App\Services\LocaleService;
    $currentLocale = app()->getLocale();
    $isRtl = LocaleService::isRtl($currentLocale);
    $appUrl = url('/app/track');
    $telegramUrl = $botUsername ? 'https://t.me/' . $botUsername : null;
    $primaryUrl = ($isLoggedIn || !$telegramUrl) ? $appUrl : $telegramUrl;
    $primaryLabel = ($isLoggedIn || !$telegramUrl) ? __('welcome.nav.cta_app') : __('welcome.nav.cta_telegram');
    $callbackUrl = url('/auth/telegram/callback');
    $newsUrl = 'https://t.me/openhabit_news';
@endphp
<html lang="{{ str_replace('_', '-', $currentLocale) }}" @if ($isRtl) dir="rtl" @endif>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ __('welcome.meta.title') }}</title>
    <meta name="description" content="{{ __('welcome.meta.description') }}">

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="manifest" href="/site.webmanifest">

    <meta property="og:title" content="{{ __('welcome.meta.title') }}">
    <meta property="og:description" content="{{ __('welcome.meta.og_description') }}">
    <meta property="og:type" content="website">

    <script>
        (function () {
            var theme = localStorage.getItem('theme');
            var isDark = theme === 'dark' || (theme !== 'light' && window.matchMedia('(prefers-color-scheme: dark)').matches);
            if (isDark) document.documentElement.classList.add('dark');
            document.documentElement.style.backgroundColor = isDark ? '#0c0d0c' : '#fafaf7';
        })();
    </script>

    @vite(['resources/css/app.css'])

    <style>
        /* Page surface tinted slightly toward the brand hue (warm green-leaning
           neutral). Avoids pure #fff / #000 — see impeccable color rules. */
        :root { color-scheme: light dark; }
        body.welcome { background-color: #fafaf7; }
        html.dark body.welcome { background-color: #0c0d0c; }

        /* Display numerals for "How it works". Tabular figures keep multi-digit
           numerals aligned; very tight tracking turns each glyph into a
           graphic mark rather than text. */
        .display-numeral {
            font-feature-settings: "tnum" 1, "lnum" 1;
            font-variant-numeric: tabular-nums lining-nums;
            letter-spacing: -0.05em;
            line-height: 0.85;
        }

        /* Hairline dividers used between editorial list rows. */
        .rule { border-color: rgb(0 0 0 / 0.08); }
        .dark .rule { border-color: rgb(255 255 255 / 0.08); }

        /* Soft ambient wash behind the hero. Restrained: a single radial
           highlight tinted toward green, low opacity. Not a "gradient feature." */
        .hero-wash {
            background:
                radial-gradient(60% 50% at 50% 0%,
                    oklch(0.94 0.05 150 / 0.55),
                    transparent 70%);
        }
        .dark .hero-wash {
            background:
                radial-gradient(60% 50% at 50% 0%,
                    oklch(0.30 0.05 150 / 0.45),
                    transparent 70%);
        }

        /* Subtle floating animation for the heatmap legend pulse. */
        @keyframes pulse-soft {
            0%, 100% { opacity: 0.75; transform: scale(1); }
            50%      { opacity: 1;    transform: scale(1.08); }
        }
        @media (prefers-reduced-motion: no-preference) {
            .pulse-soft { animation: pulse-soft 3.2s cubic-bezier(0.22, 1, 0.36, 1) infinite; }
        }

        /* FAQ accordion — height + padding animate together.
           The grid-template-rows trick lets us transition from 0fr → 1fr
           without knowing the exact pixel height of the content. */
        .faq-item > summary { list-style: none; }
        .faq-item > summary::-webkit-details-marker { display: none; }
        .faq-answer {
            display: grid;
            grid-template-rows: 0fr;
            padding-bottom: 0;
            transition: grid-template-rows 280ms cubic-bezier(0.22, 1, 0.36, 1),
                        padding-bottom    280ms cubic-bezier(0.22, 1, 0.36, 1);
        }
        .faq-answer > div { overflow: hidden; }
        .faq-chevron {
            flex-shrink: 0;
            transition: transform 280ms cubic-bezier(0.22, 1, 0.36, 1);
        }
        .faq-item[open] .faq-chevron { transform: rotate(180deg); }
    </style>
</head>
<body class="welcome antialiased text-neutral-900 dark:text-neutral-100">

{{-- Skip link for keyboard / screen-reader users --}}
<a href="#main"
   class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-50 focus:px-4 focus:py-2 focus:rounded-full focus:bg-neutral-900 focus:text-white focus:dark:bg-white focus:dark:text-neutral-900 focus:text-sm focus:font-medium focus:shadow-lg focus:outline-none">
    {{ __('welcome.a11y.skip') }}
</a>

{{-- Top navigation --}}
<header class="fixed inset-x-0 top-0 z-20 bg-white/95 dark:bg-neutral-900/95 backdrop-blur-sm sm:relative sm:bg-transparent sm:dark:bg-transparent sm:backdrop-blur-none">
    <nav aria-label="{{ __('welcome.a11y.primary_nav') }}" class="max-w-5xl mx-auto px-4 py-5 flex items-center justify-between gap-3">
        <a href="/" class="flex items-center gap-2 group flex-shrink-0">
            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-neutral-100 dark:bg-neutral-800/80 transition-transform duration-200 group-hover:scale-105">
                <svg viewBox="0 0 512 512" class="w-5 h-5" xmlns="http://www.w3.org/2000/svg">
                    <rect x="32"  y="272" width="208" height="208" rx="32" fill="#14532d"/>
                    <rect x="272" y="272" width="208" height="208" rx="32" fill="#15803d"/>
                    <rect x="32"  y="32"  width="208" height="208" rx="32" fill="#16a34a"/>
                    <rect x="272" y="32"  width="208" height="208" rx="32" fill="#22c55e"/>
                </svg>
            </span>
            <span class="font-semibold text-base tracking-tight">{{ config('app.name', 'OpenHabit') }}</span>
        </a>

        {{-- Desktop nav (sm+) --}}
        <div class="hidden sm:flex items-center gap-1 sm:gap-2">
            <a href="#features" class="inline-block px-3 py-1.5 text-sm font-medium text-neutral-500 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-white rounded-full transition-colors duration-200">
                {{ __('welcome.nav.features') }}
            </a>
            <a href="#compare" class="inline-block px-3 py-1.5 text-sm font-medium text-neutral-500 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-white rounded-full transition-colors duration-200">
                {{ __('welcome.nav.compare') }}
            </a>
            <a href="#faq" class="inline-block px-3 py-1.5 text-sm font-medium text-neutral-500 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-white rounded-full transition-colors duration-200">
                {{ __('welcome.nav.faq') }}
            </a>

            {{-- Language selector --}}
            <div class="relative" data-lang-menu>
                <button type="button"
                        data-lang-toggle
                        aria-haspopup="true"
                        aria-expanded="false"
                        aria-label="{{ __('welcome.nav.language') }}"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-neutral-500 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-white hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded-full transition-colors duration-200">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 010 20M12 2a15.3 15.3 0 000 20"/>
                    </svg>
                    <span>{{ LocaleService::label($currentLocale) }}</span>
                </button>
                <div data-lang-panel hidden
                     class="absolute end-0 mt-2 min-w-[10rem] py-1.5 rounded-xl border border-neutral-200/80 dark:border-neutral-700 bg-white dark:bg-neutral-800 shadow-lg z-20">
                    @foreach (LocaleService::all() as $code => $label)
                        <a href="{{ request()->fullUrlWithQuery(['lang' => $code]) }}"
                           class="flex items-center justify-between px-3 py-1.5 text-sm hover:bg-neutral-100 dark:hover:bg-neutral-700 transition-colors duration-150 {{ $code === $currentLocale ? 'font-medium text-neutral-900 dark:text-white' : 'text-neutral-600 dark:text-neutral-300' }}">
                            <span>{{ $label }}</span>
                            @if ($code === $currentLocale)
                                <svg class="w-3.5 h-3.5 text-green-600 dark:text-green-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Theme toggle --}}
            <button type="button"
                    data-theme-toggle
                    aria-label="{{ __('welcome.nav.theme_dark') }}"
                    class="inline-flex items-center justify-center w-8 h-8 rounded-full text-neutral-500 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-white hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors duration-200">
                <svg data-icon-sun class="w-4 h-4 hidden dark:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="12" cy="12" r="4"/>
                    <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/>
                </svg>
                <svg data-icon-moon class="w-4 h-4 block dark:hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
                </svg>
            </button>

            <a href="https://github.com/nikfedorov/openhabit" target="_blank" rel="noopener noreferrer"
               aria-label="GitHub"
               class="inline-flex items-center justify-center w-8 h-8 rounded-full text-neutral-500 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-white hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors duration-200">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"/>
                </svg>
            </a>

            <a href="{{ $primaryUrl }}" target="_blank" rel="noopener noreferrer"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-full bg-neutral-900 dark:bg-white text-white dark:text-neutral-900 hover:bg-neutral-700 dark:hover:bg-neutral-200 transition-colors duration-200">
                {{ $primaryLabel }}
                <svg class="w-3 h-3 rtl:scale-x-[-1]" viewBox="0 0 10 11" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M7.70833 6.95834V2.79167H3.54167M2.5 8L7.5 3.00001" stroke="currentColor" stroke-linecap="square"/>
                </svg>
            </a>
        </div>

        {{-- Mobile burger toggle (sm hidden) --}}
        <button type="button"
                data-menu-toggle
                aria-controls="mobile-menu"
                aria-expanded="false"
                aria-label="{{ __('welcome.nav.menu') }}"
                class="sm:hidden inline-flex items-center justify-center w-9 h-9 rounded-full text-neutral-500 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-white hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors duration-200">
            <svg data-icon-burger class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                <path d="M4 7h16M4 12h16M4 17h16"/>
            </svg>
            <svg data-icon-close class="w-5 h-5 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                <path d="M6 6l12 12M18 6L6 18"/>
            </svg>
        </button>
    </nav>

    {{-- Mobile menu panel --}}
    <div id="mobile-menu" data-menu-panel hidden
         class="sm:hidden border-t rule bg-white/95 dark:bg-neutral-900/95 backdrop-blur-sm">
        <div class="max-w-5xl mx-auto px-4 py-3 flex flex-col">
            {{-- Primary links --}}
            <a href="#features" data-menu-link
               class="flex items-center px-2 py-2.5 text-[15px] font-medium text-neutral-700 dark:text-neutral-200 hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded-lg transition-colors duration-150">
                {{ __('welcome.nav.features') }}
            </a>
            <a href="#compare" data-menu-link
               class="flex items-center px-2 py-2.5 text-[15px] font-medium text-neutral-700 dark:text-neutral-200 hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded-lg transition-colors duration-150">
                {{ __('welcome.nav.compare') }}
            </a>
            <a href="#faq" data-menu-link
               class="flex items-center px-2 py-2.5 text-[15px] font-medium text-neutral-700 dark:text-neutral-200 hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded-lg transition-colors duration-150">
                {{ __('welcome.nav.faq') }}
            </a>
            <a href="https://github.com/nikfedorov/openhabit" target="_blank" rel="noopener noreferrer"
               class="flex items-center gap-2 px-2 py-2.5 text-[15px] font-medium text-neutral-700 dark:text-neutral-200 hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded-lg transition-colors duration-150">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"/>
                </svg>
                GitHub
            </a>

            {{-- Language section --}}
            <div class="mt-2 pt-3 border-t rule">
                <p class="px-2 mb-1 text-[10px] font-medium uppercase tracking-[0.18em] text-neutral-400 dark:text-neutral-500">
                    {{ __('welcome.nav.language') }}
                </p>
                <div class="flex flex-wrap gap-1">
                    @foreach (LocaleService::all() as $code => $label)
                        <a href="{{ request()->fullUrlWithQuery(['lang' => $code]) }}"
                           class="inline-flex items-center gap-1 px-2.5 py-1.5 text-[13px] rounded-full transition-colors duration-150 {{ $code === $currentLocale ? 'bg-neutral-900 dark:bg-white text-white dark:text-neutral-900' : 'text-neutral-600 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800' }}">
                            <span>{{ $label }}</span>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Theme + CTA --}}
            <div class="mt-3 pt-3 border-t rule flex items-center justify-between gap-3">
                <button type="button"
                        data-theme-toggle
                        aria-label="{{ __('welcome.nav.theme_dark') }}"
                        class="inline-flex items-center gap-2 px-3 py-2 rounded-full text-sm font-medium text-neutral-600 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors duration-200">
                    <svg class="w-4 h-4 hidden dark:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="12" cy="12" r="4"/>
                        <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/>
                    </svg>
                    <svg class="w-4 h-4 block dark:hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
                    </svg>
                    <span class="dark:hidden">{{ __('welcome.nav.theme_dark') }}</span>
                    <span class="hidden dark:inline">{{ __('welcome.nav.theme_light') }}</span>
                </button>

                <a href="{{ $primaryUrl }}" target="_blank" rel="noopener noreferrer"
                   class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium rounded-full bg-neutral-900 dark:bg-white text-white dark:text-neutral-900 hover:bg-neutral-700 dark:hover:bg-neutral-200 transition-colors duration-200">
                    {{ $primaryLabel }}
                    <svg class="w-3 h-3 rtl:scale-x-[-1]" viewBox="0 0 10 11" fill="none" aria-hidden="true">
                        <path d="M7.70833 6.95834V2.79167H3.54167M2.5 8L7.5 3.00001" stroke="currentColor" stroke-linecap="square"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</header>

<main id="main" tabindex="-1" class="focus:outline-none">

{{-- ─────────────────────────────────────────────────────────────────────────
     Hero. Generous breathing room. Restrained color: tinted neutrals + a
     single green accent on the title word and the live dot. The preview
     surface beneath uses a single bordered card (no nested cards).
   ───────────────────────────────────────────────────────────────────────── --}}
<section aria-labelledby="hero-heading" class="relative">
    <div class="hero-wash absolute inset-x-0 top-0 h-[480px] pointer-events-none" aria-hidden="true"></div>

    <div class="relative max-w-3xl mx-auto px-4 pt-[calc(4rem+72px)] pb-16 sm:pt-24 sm:pb-24">
        <div class="flex flex-col items-start gap-7">
            <span class="inline-flex items-center gap-2.5 text-[11px] font-medium uppercase tracking-[0.18em] text-neutral-500 dark:text-neutral-400">
                <span class="relative flex h-1.5 w-1.5">
                    <span class="absolute inline-flex h-full w-full rounded-full bg-green-500 opacity-60 pulse-soft"></span>
                    <span class="relative inline-flex h-1.5 w-1.5 rounded-full bg-green-500"></span>
                </span>
                {{ __('welcome.hero.badge') }}
            </span>

            <h1 id="hero-heading" class="text-5xl sm:text-6xl lg:text-7xl font-semibold tracking-[-0.025em] leading-[1.02]">
                {{ __('welcome.hero.title_pre') }}
                <span class="text-green-600 dark:text-green-500">{{ __('welcome.hero.title_accent') }}</span>
            </h1>

            <p class="text-lg sm:text-xl text-neutral-500 dark:text-neutral-400 leading-relaxed max-w-[58ch]">
                {{ __('welcome.hero.subtitle') }}
            </p>

            <div class="flex flex-col sm:flex-row sm:items-center gap-3 mt-1">
                <a href="{{ $primaryUrl }}" target="_blank" rel="noopener noreferrer"
                   class="inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-medium rounded-full bg-neutral-900 dark:bg-white text-white dark:text-neutral-900 hover:bg-neutral-700 dark:hover:bg-neutral-200 transition-colors duration-200">
                    {{ $primaryLabel }}
                    <svg class="w-3.5 h-3.5 rtl:scale-x-[-1]" viewBox="0 0 10 11" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M7.70833 6.95834V2.79167H3.54167M2.5 8L7.5 3.00001" stroke="currentColor" stroke-linecap="square"/>
                    </svg>
                </a>

                @if ($botUsername && !$isLoggedIn)
                    {{-- Telegram Login Widget. Renders an iframe button that redirects --}}
                    {{-- the browser to the auth callback after the user confirms.    --}}
                    <div class="inline-flex items-center" aria-label="{{ __('welcome.hero.login_telegram') }}">
                        <script async
                                src="https://telegram.org/js/telegram-widget.js?22"
                                data-telegram-login="{{ $botUsername }}"
                                data-size="large"
                                data-radius="20"
                                data-userpic="false"
                                data-auth-url="{{ $callbackUrl }}"
                                data-request-access="write"></script>
                        <noscript>
                            <span class="text-sm text-neutral-500 dark:text-neutral-400">
                                {{ __('welcome.hero.login_telegram') }}
                            </span>
                        </noscript>
                    </div>
                @endif
            </div>
        </div>

        {{-- Preview surface. ONE card — not card-in-card. Hairline border,
             generous internal padding, the "Today" list and the heatmap share
             a single container, separated by a subtle rule. --}}
        <div role="region" aria-label="{{ __('welcome.a11y.preview') }}" class="mt-16 sm:mt-20 rounded-3xl border border-black/[0.06] dark:border-white/[0.08] bg-white/80 dark:bg-neutral-900/60 backdrop-blur-[2px] p-5 sm:p-7 shadow-[0_1px_0_rgba(255,255,255,0.6)_inset,0_24px_48px_-24px_rgba(15,23,42,0.16)] dark:shadow-[0_1px_0_rgba(255,255,255,0.04)_inset,0_24px_48px_-24px_rgba(0,0,0,0.7)]">
            <div class="flex items-baseline justify-between mb-5">
                <h2 class="text-sm font-semibold tracking-tight">{{ __('welcome.preview.today') }}</h2>
                <span class="text-[11px] font-medium uppercase tracking-[0.16em] text-neutral-400 dark:text-neutral-500">{{ __('welcome.preview.date') }}</span>
            </div>
            @php
                $habits = (array) __('welcome.preview.habits');
                $doneFlags = [true, true, false, false, false, false];
            @endphp
            <ul class="flex flex-col">
                @foreach ($habits as $i => $name)
                    @php $done = $doneFlags[$i] ?? false; @endphp
                    <li class="flex items-center gap-3 py-2 {{ $done ? 'opacity-55' : '' }}">
                        <span class="h-5 flex items-center flex-shrink-0">
                            <span class="w-4 h-4 rounded-md flex items-center justify-center {{ $done ? 'bg-green-500' : 'border border-neutral-300 dark:border-neutral-600' }}">
                                @if ($done)
                                    <svg class="w-3 h-3 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                                @endif
                            </span>
                        </span>
                        <span class="text-sm leading-5 {{ $done ? 'line-through' : '' }}">{{ $name }}</span>
                    </li>
                @endforeach
            </ul>

            <div class="mt-6 pt-6 border-t rule">
                @php
                    // 20 weeks × 7 days = 140 cells, column-first (week columns, day rows)
                    $intensities = [
                        0,0,0,1,0,0,0,
                        0,0,1,1,1,0,0,
                        0,1,1,2,1,1,0,
                        0,0,1,1,1,0,0,
                        0,0,1,2,2,1,0,
                        0,1,2,3,2,1,0,
                        1,1,2,3,3,2,1,
                        0,1,2,3,4,3,1,
                        0,1,1,2,2,1,0,
                        0,0,1,2,1,1,0,
                        1,2,3,4,4,3,2,
                        1,2,3,3,2,2,1,
                        0,1,2,2,3,2,1,
                        1,2,4,4,3,3,2,
                        2,3,3,4,4,3,1,
                        1,2,3,3,4,2,1,
                        2,3,4,4,3,2,1,
                        1,2,3,4,4,3,2,
                        2,3,4,4,4,3,2,
                        0,1,2,3,3,2,1,
                    ];
                    $heatShades = [
                        'bg-neutral-200 dark:bg-neutral-800',
                        'bg-green-200 dark:bg-green-900',
                        'bg-green-400 dark:bg-green-700',
                        'bg-green-500 dark:bg-green-600',
                        'bg-green-600 dark:bg-green-500',
                    ];
                @endphp
                <div class="flex justify-center mb-3" aria-hidden="true">
                    <div class="grid gap-1"
                         style="grid-template-rows: repeat(7, 14px); grid-auto-flow: column; grid-auto-columns: 14px;">
                        @foreach ($intensities as $i)
                            <span class="rounded-[3px] {{ $heatShades[$i] }} transition-transform duration-200 hover:scale-[1.6]"></span>
                        @endforeach
                    </div>
                </div>
                <div class="flex items-center justify-end gap-1" aria-hidden="true">
                    <span class="me-1 text-[10px] uppercase tracking-[0.16em] text-neutral-400 dark:text-neutral-500">{{ __('welcome.preview.less') }}</span>
                    @foreach ([0, 1, 2, 3, 4] as $level)
                        <span class="h-3 w-3 rounded-[3px] {{ $heatShades[$level] }}"></span>
                    @endforeach
                    <span class="ms-1 text-[10px] uppercase tracking-[0.16em] text-neutral-400 dark:text-neutral-500">{{ __('welcome.preview.more') }}</span>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ─────────────────────────────────────────────────────────────────────────
     Three lenses: Week / Year / Life. Single section heading, three flat
     tiles side-by-side on desktop. No nested cards: each tile is one
     bordered surface. Different visual densities deliberately vary the
     rhythm.
   ───────────────────────────────────────────────────────────────────────── --}}
<section aria-label="{{ __('welcome.a11y.lenses') }}" class="max-w-5xl mx-auto px-4 pb-20">
    @php
        // Mon=1 … Sun=7; Jan 6 2025 is a Monday (anchor for IntlDateFormatter)
        $weekDayLetters = [];
        if (class_exists(IntlDateFormatter::class)) {
            $fmt = new IntlDateFormatter(str_replace('_', '-', app()->getLocale()), IntlDateFormatter::NONE, IntlDateFormatter::NONE, null, null, 'EEEEE');
            for ($d = 6; $d <= 12; $d++) {
                $weekDayLetters[] = $fmt->format(mktime(0, 0, 0, 1, $d, 2025)); // Jan 6–12 2025 = Mon–Sun
            }
        } else {
            $weekDayLetters = ['M', 'T', 'W', 'T', 'F', 'S', 'S'];
        }
        $weekHabitNames = (array) __('welcome.preview.habits');
        $weekGrid = [
            // Mon,  Tue,  Wed,  Thu,  Fri,  Sat,  Sun
            [true,  true,  true,  true,  true,  true,  false],
            [true,  true,  true,  true,  true,  null,  null ],
            [true,  null,  true,  null,  false, true,  null ],
            [false, null,  true,  null,  true,  null,  null ],
            [true,  true,  true,  false, true,  true,  false],
            [null,  true,  null,  true,  null,  true,  null ],
        ];
        $yearLived = [2,1,3,4,2,3,1,4,3,2, 4,3,2,4,1,3,4,2,3,4, 1,3,4,2,4,3,1,2,4,3];
        $lifeCurrentAge = 28;
        $lifePattern    = [1,2,3,2,4,3,2,1,3,4, 2,3,4,2,3,1,4,3,2,4, 3,1,2,4,3,2,4,1,3,2, 4,3,1,2,3,4,2,1,4,3, 2,4,1,3,2,4,3,1,2,3, 4,2,3,1,4,2,3,4,1,2];
        $shades = [
            'bg-neutral-200 dark:bg-neutral-800',
            'bg-green-200 dark:bg-green-900',
            'bg-green-300 dark:bg-green-800',
            'bg-green-400 dark:bg-green-700',
            'bg-green-500 dark:bg-green-600',
        ];
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 sm:gap-4">
        {{-- Week --}}
        <div class="reveal rounded-2xl border border-black/[0.06] dark:border-white/[0.08] bg-white/70 dark:bg-neutral-900/40 p-5 flex flex-col">
            <p class="text-[11px] font-medium uppercase tracking-[0.18em] text-neutral-400 dark:text-neutral-500 mb-4">{{ __('welcome.views.week') }}</p>
            <div class="flex-1 overflow-x-auto">
                <div class="min-w-[240px]">
                    <div class="grid gap-1 mb-2" style="grid-template-columns: 1fr repeat(7, 1.5rem)">
                        <span></span>
                        @foreach ($weekDayLetters as $di => $letter)
                            <span class="flex items-center justify-center text-[10px] font-medium {{ $di === 6 ? 'text-neutral-300 dark:text-neutral-600' : 'text-neutral-400 dark:text-neutral-500' }} select-none">{{ $letter }}</span>
                        @endforeach
                    </div>
                    @foreach ($weekHabitNames as $hi => $habitName)
                        <div class="grid gap-1 mb-1" style="grid-template-columns: 1fr repeat(7, 1.5rem)">
                            <span class="text-[11px] text-neutral-600 dark:text-neutral-400 truncate leading-[1.5rem] pe-2">{{ $habitName }}</span>
                            @foreach ($weekGrid[$hi] ?? [] as $di => $cell)
                                @if ($cell === null)
                                    <span class="aspect-square flex items-center justify-center text-[10px] font-medium {{ $di === 6 ? 'text-neutral-200 dark:text-neutral-700' : 'text-neutral-300 dark:text-neutral-600' }} select-none leading-none">–</span>
                                @elseif ($cell)
                                    <span class="aspect-square rounded-[3px] {{ $di === 6 ? 'bg-green-400/60 dark:bg-green-700/60 ring-1 ring-inset ring-green-400/40 dark:ring-green-600/40' : 'bg-green-500 dark:bg-green-600' }}"></span>
                                @else
                                    <span class="aspect-square rounded-[3px] {{ $di === 6 ? 'border border-dashed border-neutral-300 dark:border-neutral-700' : 'bg-neutral-200 dark:bg-neutral-800' }}"></span>
                                @endif
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
            <p class="mt-4 pt-3 border-t rule text-[10px] text-neutral-400 dark:text-neutral-500">{{ __('welcome.views.week_desc') }}</p>
        </div>

        {{-- Year --}}
        <div class="reveal rounded-2xl border border-black/[0.06] dark:border-white/[0.08] bg-white/70 dark:bg-neutral-900/40 p-5 flex flex-col" data-reveal-delay="80">
            <p class="text-[11px] font-medium uppercase tracking-[0.18em] text-neutral-400 dark:text-neutral-500 mb-4">{{ __('welcome.views.year') }}</p>
            <div class="flex-1 flex items-center">
                <div class="grid gap-1 w-full" style="grid-template-columns: repeat(13, minmax(0, 1fr))">
                    @for ($w = 0; $w < 52; $w++)
                        <span class="aspect-square rounded-[3px] transition-transform duration-200 hover:scale-[1.4] {{ $w < 30 ? $shades[$yearLived[$w]] : 'border border-dashed border-neutral-300 dark:border-neutral-700' }}"
                              aria-label="{{ __('welcome.views.week_n', ['n' => $w + 1]) }}"></span>
                    @endfor
                </div>
            </div>
            <p class="mt-4 pt-3 border-t rule text-[10px] text-neutral-400 dark:text-neutral-500">{{ __('welcome.views.year_desc') }}</p>
        </div>

        {{-- Life --}}
        <div class="reveal rounded-2xl border border-black/[0.06] dark:border-white/[0.08] bg-white/70 dark:bg-neutral-900/40 p-5 flex flex-col" data-reveal-delay="160">
            <p class="text-[11px] font-medium uppercase tracking-[0.18em] text-neutral-400 dark:text-neutral-500 mb-4">{{ __('welcome.views.life') }}</p>
            <div class="flex-1 flex items-center">
                <div class="grid grid-cols-10 gap-1 w-full">
                    @for ($age = 0; $age < 80; $age++)
                        @if ($age < 21)
                            <span class="aspect-square rounded-[3px] relative bg-neutral-200 dark:bg-neutral-800 transition-transform duration-200 hover:scale-[1.4]"
                                  aria-label="{{ __('welcome.views.age', ['n' => $age]) }}">
                                @if ($age % 10 === 0)
                                    <span class="absolute inset-0 flex items-center justify-center text-[7px] font-semibold text-neutral-500 dark:text-neutral-400 select-none">{{ $age }}</span>
                                @endif
                            </span>
                        @elseif ($age < $lifeCurrentAge)
                            <span class="aspect-square rounded-[3px] relative {{ $shades[$lifePattern[$age % count($lifePattern)]] }} transition-transform duration-200 hover:scale-[1.4]"
                                  aria-label="{{ __('welcome.views.age', ['n' => $age]) }}">
                                @if ($age % 10 === 0)
                                    <span class="absolute inset-0 flex items-center justify-center text-[7px] font-semibold text-neutral-700 dark:text-neutral-200 select-none">{{ $age }}</span>
                                @endif
                            </span>
                        @elseif ($age === $lifeCurrentAge)
                            <span class="aspect-square rounded-[3px] relative bg-green-300 dark:bg-green-800 transition-transform duration-200 hover:scale-[1.4]"
                                  aria-label="{{ __('welcome.views.age', ['n' => $age]) }}"></span>
                        @else
                            <span class="aspect-square rounded-[3px] relative border border-dashed border-neutral-300 dark:border-neutral-700 transition-transform duration-200 hover:scale-[1.4]"
                                  aria-label="{{ __('welcome.views.age', ['n' => $age]) }}">
                                @if ($age % 10 === 0)
                                    <span class="absolute inset-0 flex items-center justify-center text-[7px] font-medium text-neutral-400 dark:text-neutral-500 select-none">{{ $age }}</span>
                                @endif
                            </span>
                        @endif
                    @endfor
                </div>
            </div>
            <p class="mt-4 pt-3 border-t rule text-[10px] text-neutral-400 dark:text-neutral-500">{{ __('welcome.views.life_desc') }}</p>
        </div>
    </div>
</section>

{{-- ─────────────────────────────────────────────────────────────────────────
     Features. Editorial 2-column list with hairline dividers — no card grid.
     Icon as a small accent, not a heavy badge. Tighter rhythm than the hero.
   ───────────────────────────────────────────────────────────────────────── --}}
<section id="features" aria-labelledby="features-heading" class="scroll-mt-20 max-w-3xl mx-auto px-4 py-20 sm:py-24">
    <div class="reveal mb-12 max-w-2xl">
        <p class="text-[11px] font-medium uppercase tracking-[0.18em] text-neutral-400 dark:text-neutral-500 mb-3">{{ __('welcome.features.eyebrow') }}</p>
        <h2 id="features-heading" class="text-3xl sm:text-4xl font-semibold tracking-[-0.02em] leading-[1.1]">{{ __('welcome.features.title') }}</h2>
    </div>

    <ul class="grid grid-cols-1 sm:grid-cols-2 sm:gap-x-10">
        @foreach ([
            ['key' => 'schedules', 'icon' => 'calendar'],
            ['key' => 'streaks',   'icon' => 'grid'],
            ['key' => 'reminders', 'icon' => 'bell'],
            ['key' => 'notes',     'icon' => 'note'],
            ['key' => 'ai',        'icon' => 'sparkles', 'premium' => true],
            ['key' => 'export',    'icon' => 'download', 'premium' => true],
        ] as $feature)
            <li class="reveal py-6 border-t rule first:border-t-0 sm:[&:nth-child(2)]:border-t-0"
                data-reveal-delay="{{ $loop->index * 60 }}">
                <div class="flex items-start gap-4">
                    <span class="flex-shrink-0 w-8 h-8 rounded-lg bg-neutral-100 dark:bg-neutral-800/80 flex items-center justify-center text-green-600 dark:text-green-500">
                        @switch($feature['icon'])
                            @case('calendar')
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                                @break
                            @case('grid')
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                                @break
                            @case('bell')
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 8a6 6 0 0112 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10 21a2 2 0 004 0"/></svg>
                                @break
                            @case('note')
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6M9 13h6M9 17h4"/></svg>
                                @break
                            @case('sparkles')
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3l1.9 4.6L18.5 9.5l-4.6 1.9L12 16l-1.9-4.6L5.5 9.5l4.6-1.9z"/><path d="M19 15l.8 1.9 1.9.8-1.9.8L19 20.4l-.8-1.9-1.9-.8 1.9-.8z"/></svg>
                                @break
                            @case('download')
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                @break
                        @endswitch
                    </span>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1.5">
                            <h3 class="text-[15px] font-semibold tracking-tight">{{ __('welcome.features.items.'.$feature['key'].'.title') }}</h3>
                            @if (! empty($feature['premium']))
                                <span class="text-[9px] font-medium uppercase tracking-[0.16em] px-1.5 py-0.5 rounded-full bg-green-100 dark:bg-green-950/60 text-green-700 dark:text-green-400 ring-1 ring-green-200/50 dark:ring-green-900/50">
                                    {{ __('welcome.features.premium') }}
                                </span>
                            @endif
                        </div>
                        <p class="text-sm leading-relaxed text-neutral-500 dark:text-neutral-400">{{ __('welcome.features.items.'.$feature['key'].'.body') }}</p>
                    </div>
                </div>
            </li>
        @endforeach
    </ul>
</section>

{{-- ─────────────────────────────────────────────────────────────────────────
     Comparison. A plain editorial table — no card wrapper, hairline row
     dividers, single bordered surface broken by rules. The OpenHabit
     column is the only one that earns the green accent (header tag,
     filled checks, mono pill text). Competitor columns stay restrained:
     muted outlined check icons and a thin dash for absent features.
     Variant labels ("MIT", "iOS only", "Premium") use the same mono
     uppercase voice as the open-source section.
   ───────────────────────────────────────────────────────────────────────── --}}
<section id="compare" aria-labelledby="compare-heading" class="scroll-mt-20 max-w-3xl mx-auto px-4 py-20 sm:py-24 border-t rule">
    <div class="reveal mb-12 max-w-2xl">
        <p class="text-[11px] font-medium uppercase tracking-[0.18em] text-neutral-400 dark:text-neutral-500 mb-3">{{ __('welcome.compare.eyebrow') }}</p>
        <h2 id="compare-heading" class="text-3xl sm:text-4xl font-semibold tracking-[-0.02em] leading-[1.1] mb-3">{{ __('welcome.compare.title') }}</h2>
        <p class="text-base text-neutral-500 dark:text-neutral-400 leading-relaxed max-w-[52ch]">{{ __('welcome.compare.subtitle') }}</p>
    </div>

    <div class="reveal -mx-4 sm:mx-0 overflow-x-auto">
        @php
            // Truthful, public-info comparison. Cell values:
            //   'yes' — full check (rendered the same for every column; fair)
            //   'no'  — small × mark
            //   ['label' => '…'] — qualifying tag (license, platform, tier, count)
            //
            // Columns: OpenHabit, Habitica, Streaks, Productive.
            // Productive (apploft) is the freemium pick: 3-habit free tier.
            $compareColumns = [
                ['name' => 'OpenHabit',  'us' => true],
                ['name' => 'Habitica',   'us' => false],
                ['name' => 'Streaks',    'us' => false],
                ['name' => 'Productive', 'us' => false],
            ];
            $compareRows = [
                ['key' => 'telegram',    'cells' => ['yes', 'no', 'no', 'no']],
                ['key' => 'open_source', 'cells' => [['label' => 'MIT'], ['label' => 'GPL'], 'no', 'no']],
                ['key' => 'free',        'cells' => ['yes', 'yes', ['label' => __('welcome.compare.cells.paid')], ['label' => __('welcome.compare.cells.freemium')]]],
                ['key' => 'free_habits', 'cells' => [['label' => '∞'], ['label' => '∞'], ['label' => '24'], ['label' => '3']]],
                ['key' => 'ai',          'cells' => [['label' => __('welcome.compare.cells.premium')], 'no', 'no', 'no']],
                ['key' => 'export',      'cells' => ['yes', 'yes', ['label' => __('welcome.compare.cells.limited')], ['label' => __('welcome.compare.cells.premium')]]],
                ['key' => 'cross',       'cells' => ['yes', 'yes', ['label' => __('welcome.compare.cells.ios_only')], 'yes']],
            ];
        @endphp

        <table class="w-full min-w-[600px] sm:min-w-0 text-sm border-collapse px-4 sm:px-0" role="table">
            <caption class="sr-only">{{ __('welcome.compare.caption') }}</caption>
            <thead>
                <tr class="text-left align-bottom [&>th:not(:first-child)]:text-center">
                    <th scope="col" class="pb-5 ps-4 sm:ps-0 pe-3 sm:pe-4 w-[34%]">
                        <span class="sr-only">{{ __('welcome.compare.criterion') }}</span>
                    </th>
                    @foreach ($compareColumns as $col)
                        <th scope="col" class="pb-5 px-3 sm:px-4 font-normal">
                            <span class="block text-[15px] font-semibold tracking-tight {{ $col['us'] ? 'text-neutral-900 dark:text-white' : 'text-neutral-500 dark:text-neutral-400' }}">
                                {{ $col['name'] }}
                            </span>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($compareRows as $row)
                    <tr class="border-t rule">
                        <th scope="row" class="py-4 ps-4 sm:ps-0 pe-3 sm:pe-4 text-left text-[14px] font-medium text-neutral-700 dark:text-neutral-200 leading-snug">
                            {{ __('welcome.compare.rows.'.$row['key']) }}
                        </th>
                        @foreach ($row['cells'] as $i => $cell)
                            @php $isUs = $compareColumns[$i]['us'] ?? false; @endphp
                            <td class="py-4 px-3 sm:px-4 align-middle text-center">
                                @if ($cell === 'yes')
                                    {{-- Single, identical green check for every column — fair by default. --}}
                                    <span class="inline-flex w-5 h-5 rounded-md items-center justify-center bg-green-500 text-white"
                                          aria-label="{{ __('welcome.compare.a11y.yes') }}">
                                        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                                    </span>
                                @elseif ($cell === 'no')
                                    {{-- Muted × — not red, not loud. Honest absence, no judgment. --}}
                                    <span class="inline-flex w-5 h-5 items-center justify-center text-neutral-300 dark:text-neutral-600"
                                          aria-label="{{ __('welcome.compare.a11y.no') }}">
                                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
                                    </span>
                                @elseif (is_array($cell))
                                    <span class="inline-flex items-center font-mono uppercase tabular-nums {{ $cell['label'] === '∞' ? 'text-[24px] leading-none' : 'text-[11px] tracking-[0.16em]' }} {{ $isUs ? 'text-green-600 dark:text-green-500' : 'text-neutral-500 dark:text-neutral-400' }}">
                                        {{ $cell['label'] }}
                                    </span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <p class="reveal mt-6 px-4 sm:px-0 text-[11px] text-neutral-400 dark:text-neutral-500 max-w-[60ch] leading-relaxed">
        {{ __('welcome.compare.footnote') }}
    </p>
</section>

{{-- ─────────────────────────────────────────────────────────────────────────
     How it works. Three steps shown as large display numerals — typography
     IS the ornament. No card backgrounds. Different rhythm again.
   ───────────────────────────────────────────────────────────────────────── --}}
<section id="how" aria-labelledby="how-heading" class="scroll-mt-20 max-w-3xl mx-auto px-4 py-20 sm:py-24 border-t rule">
    <div class="reveal mb-14 max-w-2xl">
        <p class="text-[11px] font-medium uppercase tracking-[0.18em] text-neutral-400 dark:text-neutral-500 mb-3">{{ __('welcome.how.eyebrow') }}</p>
        <h2 id="how-heading" class="text-3xl sm:text-4xl font-semibold tracking-[-0.02em] leading-[1.1]">{{ __('welcome.how.title') }}</h2>
    </div>

    @php $steps = (array) __('welcome.how.steps'); @endphp
    <ol class="flex flex-col gap-12 sm:gap-14">
        @foreach ($steps as $i => $step)
            <li class="reveal grid grid-cols-[auto,1fr] gap-6 sm:gap-10 items-baseline"
                data-reveal-delay="{{ $loop->index * 90 }}">
                <span class="display-numeral text-[64px] sm:text-[88px] font-semibold text-neutral-200 dark:text-neutral-800 select-none" aria-hidden="true">
                    {{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}
                </span>
                <div class="pt-2 sm:pt-4">
                    <h3 class="text-xl sm:text-2xl font-semibold tracking-tight mb-2">{{ $step['title'] }}</h3>
                    <p class="text-base text-neutral-500 dark:text-neutral-400 leading-relaxed max-w-[52ch]">{{ $step['body'] }}</p>
                </div>
            </li>
        @endforeach
    </ol>
</section>

{{-- ─────────────────────────────────────────────────────────────────────────
     Open source. ONE bordered surface that nods at a code/repo window: a
     mono header strip with the repo path and branch, then a calm split body —
     prose on the left, three quiet facts on the right. Different rhythm
     from the editorial features list above and the bare-typography CTA
     below. Restrained palette: tinted neutrals with a single green accent
     on the live dot and the mono labels.
   ───────────────────────────────────────────────────────────────────────── --}}
<section id="open-source" aria-labelledby="open-heading" class="scroll-mt-20 max-w-3xl mx-auto px-4 py-20 sm:py-24 border-t rule">
    <div class="reveal mb-10 max-w-2xl">
        <p class="text-[11px] font-medium uppercase tracking-[0.18em] text-neutral-400 dark:text-neutral-500 mb-3">
            {{ __('welcome.open.eyebrow') }}
        </p>
        <h2 id="open-heading" class="text-3xl sm:text-4xl font-semibold tracking-[-0.02em] leading-[1.1]">
            {{ __('welcome.open.title') }}
        </h2>
    </div>

    <div class="reveal rounded-2xl border border-black/[0.06] dark:border-white/[0.08] bg-white/70 dark:bg-neutral-900/40 overflow-hidden">
        {{-- Window-bar. Mono, hairline rule beneath. The pulsing dot reuses the hero motif. --}}
        <div class="flex items-center justify-between gap-3 px-5 py-3 border-b rule">
            <div class="flex items-center gap-2.5 font-mono text-[12px] text-neutral-500 dark:text-neutral-400 min-w-0">
                <span class="relative flex h-1.5 w-1.5 flex-shrink-0">
                    <span class="absolute inline-flex h-full w-full rounded-full bg-green-500 opacity-60 pulse-soft"></span>
                    <span class="relative inline-flex h-1.5 w-1.5 rounded-full bg-green-500"></span>
                </span>
                <span class="truncate">github.com/nikfedorov/openhabit</span>
            </div>
            <span class="hidden sm:inline-flex items-center gap-1.5 font-mono text-[11px] text-neutral-400 dark:text-neutral-500 flex-shrink-0">
                <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="6" cy="6" r="2.5"/>
                    <circle cx="6" cy="18" r="2.5"/>
                    <circle cx="18" cy="18" r="2.5"/>
                    <path d="M6 8.5v7"/>
                    <path d="M18 15.5V11a2 2 0 00-2-2H8.5"/>
                </svg>
                main
            </span>
        </div>

        {{-- Body: prose left, three facts right. Stacks on mobile with a hairline divider. --}}
        <div class="grid grid-cols-1 md:grid-cols-[1.05fr_1fr]">
            <div class="p-6 sm:p-8">
                <p class="text-[15px] sm:text-base leading-relaxed text-neutral-600 dark:text-neutral-300 max-w-[44ch] mb-6">
                    {{ __('welcome.open.body') }}
                </p>
                <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-5">
                    <a href="https://github.com/nikfedorov/openhabit" target="_blank" rel="noopener noreferrer"
                       class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-full bg-neutral-900 dark:bg-white text-white dark:text-neutral-900 hover:bg-neutral-700 dark:hover:bg-neutral-200 transition-colors duration-200">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"/>
                        </svg>
                        {{ __('welcome.open.cta') }}
                    </a>
                    <a href="https://github.com/nikfedorov/openhabit/blob/main/LICENSE" target="_blank" rel="noopener noreferrer"
                       class="inline-flex items-center gap-1 text-sm text-neutral-500 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-white transition-colors duration-200">
                        {{ __('welcome.open.license_link') }}
                        <svg class="w-3 h-3 rtl:scale-x-[-1]" viewBox="0 0 10 11" fill="none" aria-hidden="true">
                            <path d="M7.70833 6.95834V2.79167H3.54167M2.5 8L7.5 3.00001" stroke="currentColor" stroke-linecap="square"/>
                        </svg>
                    </a>
                </div>
            </div>

            <ul class="border-t md:border-t-0 md:border-s rule">
                @foreach ([
                    ['key' => 'license',  'label' => 'MIT'],
                    ['key' => 'selfhost', 'label' => 'docker'],
                    ['key' => 'audit',    'label' => '0 trackers'],
                ] as $fact)
                    <li class="flex items-baseline justify-between gap-4 px-5 sm:px-7 py-4 border-t rule first:border-t-0">
                        <div class="min-w-0">
                            <p class="text-sm font-medium tracking-tight text-neutral-900 dark:text-white">
                                {{ __('welcome.open.facts.'.$fact['key'].'.title') }}
                            </p>
                            <p class="text-[13px] text-neutral-500 dark:text-neutral-400 leading-snug mt-0.5">
                                {{ __('welcome.open.facts.'.$fact['key'].'.body') }}
                            </p>
                        </div>
                        <span class="font-mono text-[10px] uppercase tracking-[0.18em] text-green-600 dark:text-green-500 flex-shrink-0 whitespace-nowrap">
                            {{ $fact['label'] }}
                        </span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</section>

{{-- ─────────────────────────────────────────────────────────────────────────
     FAQ. Accordion with smooth height + padding animation.
   ───────────────────────────────────────────────────────────────────────── --}}
<section id="faq" aria-labelledby="faq-heading" class="scroll-mt-20 max-w-3xl mx-auto px-4 py-20 sm:py-24 border-t rule">
    <div class="reveal mb-12 max-w-2xl">
        <p class="text-[11px] font-medium uppercase tracking-[0.18em] text-neutral-400 dark:text-neutral-500 mb-3">{{ __('welcome.faq.eyebrow') }}</p>
        <h2 id="faq-heading" class="text-3xl sm:text-4xl font-semibold tracking-[-0.02em] leading-[1.1]">{{ __('welcome.faq.title') }}</h2>
    </div>

    <div class="flex flex-col">
        @foreach ((array) __('welcome.faq.items') as $faqItem)
            <details class="faq-item reveal border-t rule first:border-t-0" data-reveal-delay="{{ $loop->index * 60 }}">
                <summary class="flex items-center justify-between gap-4 py-5 cursor-pointer select-none group">
                    <span class="text-[15px] font-semibold tracking-tight text-neutral-900 dark:text-white group-hover:text-neutral-600 dark:group-hover:text-neutral-300 transition-colors duration-150">{{ $faqItem['q'] }}</span>
                    <svg class="faq-chevron w-4 h-4 text-neutral-400 dark:text-neutral-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <polyline points="6 9 12 15 18 9"/>
                    </svg>
                </summary>
                <div class="faq-answer">
                    <div>
                        <p class="text-[15px] leading-relaxed text-neutral-500 dark:text-neutral-400 max-w-[60ch]">{{ $faqItem['a'] }}</p>
                    </div>
                </div>
            </details>
        @endforeach
    </div>
</section>

{{-- ─────────────────────────────────────────────────────────────────────────
     Final CTA. Just typography on the page surface — no card background.
   ───────────────────────────────────────────────────────────────────────── --}}
<section aria-labelledby="cta-heading" class="max-w-3xl mx-auto px-4 py-24 sm:py-32 text-center border-t rule">
    <div class="reveal flex flex-col items-center gap-6">
        <h2 id="cta-heading" class="text-4xl sm:text-5xl font-semibold tracking-[-0.025em] leading-[1.05] max-w-[20ch]">{{ __('welcome.cta.title') }}</h2>
        <p class="text-base sm:text-lg text-neutral-500 dark:text-neutral-400 leading-relaxed max-w-[48ch]">
            {{ __('welcome.cta.subtitle') }}
        </p>
        <a href="{{ $primaryUrl }}" target="_blank" rel="noopener noreferrer"
           class="inline-flex items-center justify-center gap-2 px-6 py-3 mt-2 text-sm font-medium rounded-full bg-neutral-900 dark:bg-white text-white dark:text-neutral-900 hover:bg-neutral-700 dark:hover:bg-neutral-200 transition-colors duration-200">
            {{ $primaryLabel }}
            <svg class="w-3.5 h-3.5 rtl:scale-x-[-1]" viewBox="0 0 10 11" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M7.70833 6.95834V2.79167H3.54167M2.5 8L7.5 3.00001" stroke="currentColor" stroke-linecap="square"/>
            </svg>
        </a>
    </div>
</section>

</main>

{{-- Footer --}}
<footer class="border-t rule">
    <div class="max-w-5xl mx-auto px-4 py-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-neutral-400 dark:text-neutral-500">
        <span>{{ __('welcome.footer.tagline', ['year' => date('Y'), 'app' => config('app.name', 'OpenHabit')]) }}</span>
        <div class="flex items-center gap-4">
            @if ($telegramUrl)
                <a href="{{ $telegramUrl }}" target="_blank" rel="noopener noreferrer" class="hover:text-neutral-700 dark:hover:text-neutral-300 transition-colors duration-150">{{ __('welcome.footer.telegram') }}</a>
            @endif
            <a href="{{ $newsUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 hover:text-neutral-700 dark:hover:text-neutral-300 transition-colors duration-150">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M9.78 18.65l.28-4.23 7.68-6.92c.34-.31-.07-.46-.52-.19L7.74 13.24 3.64 11.95c-.88-.25-.89-.86.2-1.3l15.97-6.16c.73-.33 1.43.18 1.15 1.3l-2.72 12.81c-.19.91-.74 1.13-1.5.71L12.6 16.3l-1.99 1.93c-.23.23-.42.42-.83.42z"/>
                </svg>
                {{ __('welcome.footer.news') }}
            </a>
            <a href="https://github.com/nikfedorov/openhabit" target="_blank" rel="noopener noreferrer" aria-label="GitHub" class="hover:text-neutral-700 dark:hover:text-neutral-300 transition-colors duration-150">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"/>
                </svg>
            </a>
        </div>
    </div>
</footer>

@if ($trackingScripts)
    {!! $trackingScripts !!}
@endif

{{-- Theme toggle + language menu interactions --}}
<script>
    (function () {
        // Theme toggle (works for both desktop + mobile buttons)
        var themeButtons = document.querySelectorAll('[data-theme-toggle]');
        if (themeButtons.length) {
            var labelLight = @json(__('welcome.nav.theme_light'));
            var labelDark = @json(__('welcome.nav.theme_dark'));
            var sync = function () {
                var isDark = document.documentElement.classList.contains('dark');
                themeButtons.forEach(function (b) {
                    b.setAttribute('aria-label', isDark ? labelLight : labelDark);
                });
                document.documentElement.style.backgroundColor = isDark ? '#0c0d0c' : '#fafaf7';
            };
            sync();
            themeButtons.forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var isDark = document.documentElement.classList.toggle('dark');
                    try { localStorage.setItem('theme', isDark ? 'dark' : 'light'); } catch (e) {}
                    sync();
                });
            });
        }

        // Mobile burger menu
        var menuToggle = document.querySelector('[data-menu-toggle]');
        var menuPanel = document.querySelector('[data-menu-panel]');
        if (menuToggle && menuPanel) {
            var iconBurger = menuToggle.querySelector('[data-icon-burger]');
            var iconClose = menuToggle.querySelector('[data-icon-close]');
            var setMenu = function (open) {
                if (open) { menuPanel.removeAttribute('hidden'); } else { menuPanel.setAttribute('hidden', ''); }
                menuToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
                if (iconBurger && iconClose) {
                    iconBurger.classList.toggle('hidden', open);
                    iconClose.classList.toggle('hidden', !open);
                }
            };
            menuToggle.addEventListener('click', function () {
                setMenu(menuPanel.hasAttribute('hidden'));
            });
            // Close on in-page anchor click
            menuPanel.querySelectorAll('a[href^="#"]').forEach(function (a) {
                a.addEventListener('click', function () { setMenu(false); });
            });
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && !menuPanel.hasAttribute('hidden')) { setMenu(false); }
            });
        }

        // Language menu
        var menu = document.querySelector('[data-lang-menu]');
        if (menu) {
            var toggle = menu.querySelector('[data-lang-toggle]');
            var panel = menu.querySelector('[data-lang-panel]');
            var open = function (state) {
                if (state) { panel.removeAttribute('hidden'); } else { panel.setAttribute('hidden', ''); }
                toggle.setAttribute('aria-expanded', state ? 'true' : 'false');
            };
            toggle.addEventListener('click', function (e) {
                e.stopPropagation();
                open(panel.hasAttribute('hidden'));
            });
            document.addEventListener('click', function (e) {
                if (! menu.contains(e.target)) open(false);
            });
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') open(false);
            });
        }

        // Scroll-reveal via IntersectionObserver. Elements with .reveal start
        // hidden (via CSS) and transition in when they enter the viewport.
        // The transitionDelay is cleared afterwards so hover effects remain
        // instant.
        var reveals = document.querySelectorAll('.reveal');
        if (reveals.length && 'IntersectionObserver' in window) {
            var revealObserver = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (!entry.isIntersecting) { return; }
                    var el = entry.target;
                    var delay = parseInt(el.dataset.revealDelay || '0', 10);
                    if (delay) { el.style.transitionDelay = delay + 'ms'; }
                    el.classList.add('revealed');
                    el.addEventListener('transitionend', function () {
                        el.style.transitionDelay = '';
                    }, { once: true });
                    revealObserver.unobserve(el);
                });
            }, { threshold: 0.08, rootMargin: '0px 0px -30px 0px' });
            reveals.forEach(function (el) { revealObserver.observe(el); });
        } else {
            reveals.forEach(function (el) { el.classList.add('revealed'); });
        }

        // FAQ accordion — smooth height + padding animation.
        // Intercepts <summary> clicks to animate both grid-template-rows
        // (height proxy) and padding-bottom before toggling `open`.
        document.querySelectorAll('.faq-item').forEach(function (details) {
            var answer = details.querySelector('.faq-answer');
            var summary = details.querySelector('summary');
            if (!summary || !answer) { return; }

            summary.addEventListener('click', function (e) {
                e.preventDefault();
                if (details.open) {
                    // Animate to closed, then remove the `open` attribute.
                    answer.style.gridTemplateRows = '0fr';
                    answer.style.paddingBottom = '0';
                    answer.addEventListener('transitionend', function handler(ev) {
                        if (ev.propertyName !== 'grid-template-rows') { return; }
                        details.removeAttribute('open');
                        answer.removeEventListener('transitionend', handler);
                    });
                } else {
                    // Set `open` so the content is in the DOM, then on the
                    // next two frames animate from the closed state.
                    details.setAttribute('open', '');
                    requestAnimationFrame(function () {
                        requestAnimationFrame(function () {
                            answer.style.gridTemplateRows = '1fr';
                            answer.style.paddingBottom = '1.25rem';
                        });
                    });
                }
            });
        });
    })();
</script>

</body>
</html>
