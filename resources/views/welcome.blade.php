<!DOCTYPE html>
@php
    use App\Services\LocaleService;
    $currentLocale = app()->getLocale();
    $isRtl = LocaleService::isRtl($currentLocale);
    $appUrl = url('/app/track');
    $telegramUrl = $botUsername ? 'https://t.me/' . $botUsername : null;
    $primaryUrl = ($isLoggedIn || !$telegramUrl) ? $appUrl : $telegramUrl;
    $primaryLabel = ($isLoggedIn || !$telegramUrl) ? __('welcome.nav.cta_app') : __('welcome.nav.cta_telegram');
    $primaryNote = match (true) {
        $isLoggedIn => __('welcome.hero.entry_note_app'),
        (bool) $telegramUrl => __('welcome.hero.entry_note_telegram'),
        default => __('welcome.hero.entry_note_no_bot'),
    };
    $callbackUrl = url('/auth/telegram/callback');
    $telegramLoginUrl = $botUsername ? 'https://oauth.telegram.org/embed/' . rawurlencode($botUsername) . '?' . http_build_query([
        'origin' => url('/'),
        'return_to' => request()->fullUrl(),
        'size' => 'large',
        'userpic' => 'false',
        'request_access' => 'write',
        'radius' => '20',
        'lang' => $currentLocale,
    ]) : null;
    $newsUrl = 'https://t.me/openhabit_news';
    $githubUrl = 'https://github.com/nikfedorov/openhabit';
    $authorGithubUrl = 'https://github.com/nikfedorov';
    $authorTwitterUrl = 'https://x.com/n1kfedorov';
    $roadmapUrl = $githubUrl . '/issues';
    $changelogUrl = $githubUrl . '/releases';
    $privacyUrl = '#privacy';
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
            document.documentElement.classList.add('js');

            var theme = null;
            var prefersDark = false;
            try { theme = localStorage.getItem('theme'); } catch (e) {}
            try { prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches; } catch (e) {}
            var isDark = theme === 'dark' || (theme !== 'light' && prefersDark);
            if (isDark) document.documentElement.classList.add('dark');
            document.documentElement.style.backgroundColor = isDark ? '#0c0d0c' : '#fafaf7';
        })();
    </script>

    @vite(['resources/css/app.css'])

    <style>
        /* Page surface tinted slightly toward the brand hue (warm green-leaning
           neutral). Avoids pure #fff / #000 — see impeccable color rules. */
        :root { color-scheme: light dark; }
        body.welcome {
            background-color: #fafaf7;
            font-kerning: normal;
            text-rendering: optimizeLegibility;
        }
        html.dark body.welcome { background-color: #0c0d0c; }

        /* Hairline dividers used between editorial list rows. */
        .rule { border-color: color-mix(in oklch, CanvasText 8%, transparent); }

        /* Barely-there ambient wash to keep the surface from feeling sterile. */
        .hero-wash {
            background:
                radial-gradient(52% 42% at 50% 0%,
                    oklch(0.94 0.035 150 / 0.32),
                    transparent 70%);
        }
        .dark .hero-wash {
            background:
                radial-gradient(52% 42% at 50% 0%,
                    oklch(0.30 0.035 150 / 0.28),
                    transparent 70%);
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
        html:not(.js) .faq-item[open] .faq-answer {
            grid-template-rows: 1fr;
            padding-bottom: 1.25rem;
        }
        .faq-chevron {
            flex-shrink: 0;
            transition: transform 280ms cubic-bezier(0.22, 1, 0.36, 1);
        }
        .faq-item[open] .faq-chevron { transform: rotate(180deg); }

        .telegram-login-collapse {
            overflow: hidden;
            transition: opacity 180ms cubic-bezier(0.22, 1, 0.36, 1),
                        transform 180ms cubic-bezier(0.22, 1, 0.36, 1),
                        width 220ms cubic-bezier(0.22, 1, 0.36, 1),
                        height 220ms cubic-bezier(0.22, 1, 0.36, 1),
                        margin 220ms cubic-bezier(0.22, 1, 0.36, 1),
                        padding 220ms cubic-bezier(0.22, 1, 0.36, 1);
        }
        .telegram-login-collapse.is-hidden {
            opacity: 0;
            transform: translateY(-4px);
            width: 0 !important;
            height: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
            pointer-events: none;
        }
        @media (prefers-reduced-motion: reduce) {
            .telegram-login-collapse {
                transition: none;
                transform: translateY(0);
            }
        }

    </style>
</head>
<body class="welcome font-sans antialiased text-neutral-900 dark:text-neutral-100 [&_a:focus-visible]:outline-2 [&_a:focus-visible]:outline-offset-3 [&_a:focus-visible]:outline-green-600 dark:[&_a:focus-visible]:outline-green-500 [&_button:focus-visible]:outline-2 [&_button:focus-visible]:outline-offset-3 [&_button:focus-visible]:outline-green-600 dark:[&_button:focus-visible]:outline-green-500 [&_summary:focus-visible]:outline-2 [&_summary:focus-visible]:outline-offset-3 [&_summary:focus-visible]:outline-green-600 dark:[&_summary:focus-visible]:outline-green-500">

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

        {{-- Desktop nav (lg+) --}}
        <div class="hidden lg:flex items-center gap-1 sm:gap-2">
            <a href="#how" class="inline-block px-3 py-1.5 text-sm font-medium text-neutral-500 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-white rounded-full transition-colors duration-200">
                {{ __('welcome.nav.how') }}
            </a>
            <a href="#open-source" class="inline-block px-3 py-1.5 text-sm font-medium text-neutral-500 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-white rounded-full transition-colors duration-200">
                {{ __('welcome.nav.open_source') }}
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

            <a href="{{ $primaryUrl }}" target="_blank" rel="noopener noreferrer"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-full bg-neutral-900 dark:bg-white text-white dark:text-neutral-900 hover:bg-neutral-700 dark:hover:bg-neutral-200 transition-colors duration-200">
                {{ $primaryLabel }}
                <svg class="w-3 h-3 rtl:scale-x-[-1]" viewBox="0 0 10 11" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M7.70833 6.95834V2.79167H3.54167M2.5 8L7.5 3.00001" stroke="currentColor" stroke-linecap="square"/>
                </svg>
            </a>
        </div>

        {{-- Mobile burger toggle (lg hidden) --}}
        <button type="button"
                data-menu-toggle
                aria-controls="mobile-menu"
                aria-expanded="false"
                aria-label="{{ __('welcome.nav.menu') }}"
                class="lg:hidden inline-flex items-center justify-center w-9 h-9 rounded-full text-neutral-500 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-white hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors duration-200">
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
         class="lg:hidden border-t rule bg-white/95 dark:bg-neutral-900/95 backdrop-blur-sm">
        <div class="max-w-5xl mx-auto px-4 py-3 flex flex-col">
            {{-- Primary links --}}
            <a href="#how" data-menu-link
                class="flex items-center px-2 py-2.5 text-[15px] font-medium text-neutral-700 dark:text-neutral-200 hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded-lg transition-colors duration-150">
                {{ __('welcome.nav.how') }}
            </a>
            <a href="#open-source" data-menu-link
               class="flex items-center px-2 py-2.5 text-[15px] font-medium text-neutral-700 dark:text-neutral-200 hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded-lg transition-colors duration-150">
                {{ __('welcome.nav.open_source') }}
            </a>
            <a href="#faq" data-menu-link
               class="flex items-center px-2 py-2.5 text-[15px] font-medium text-neutral-700 dark:text-neutral-200 hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded-lg transition-colors duration-150">
                {{ __('welcome.nav.faq') }}
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
            <div class="mt-3 pt-3 border-t rule flex flex-col items-start gap-3 min-[360px]:flex-row min-[360px]:items-center min-[360px]:justify-between">
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
                   class="inline-flex items-center gap-1.5 whitespace-nowrap px-4 py-2 text-sm font-medium rounded-full bg-neutral-900 dark:bg-white text-white dark:text-neutral-900 hover:bg-neutral-700 dark:hover:bg-neutral-200 transition-colors duration-200">
                    {{ $primaryLabel }}
                    <svg class="w-3 h-3 rtl:scale-x-[-1]" viewBox="0 0 10 11" fill="none" aria-hidden="true">
                        <path d="M7.70833 6.95834V2.79167H3.54167M2.5 8L7.5 3.00001" stroke="currentColor" stroke-linecap="square"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>

    <noscript>
        <div class="lg:hidden border-t rule bg-white/95 dark:bg-neutral-900/95">
            <div class="max-w-5xl mx-auto px-4 py-3 flex flex-col gap-1">
                <a href="#how" class="flex items-center px-2 py-2.5 text-[15px] font-medium text-neutral-700 dark:text-neutral-200 rounded-lg">
                    {{ __('welcome.nav.how') }}
                </a>
                <a href="#open-source" class="flex items-center px-2 py-2.5 text-[15px] font-medium text-neutral-700 dark:text-neutral-200 rounded-lg">
                    {{ __('welcome.nav.open_source') }}
                </a>
                <a href="#faq" class="flex items-center px-2 py-2.5 text-[15px] font-medium text-neutral-700 dark:text-neutral-200 rounded-lg">
                    {{ __('welcome.nav.faq') }}
                </a>
                <a href="{{ $primaryUrl }}" target="_blank" rel="noopener noreferrer" class="mt-2 inline-flex items-center justify-center gap-1.5 whitespace-nowrap px-4 py-2 text-sm font-medium rounded-full bg-neutral-900 dark:bg-white text-white dark:text-neutral-900">
                    {{ $primaryLabel }}
                </a>
            </div>
        </div>
    </noscript>
</header>

<main id="main" tabindex="-1">

{{-- ─────────────────────────────────────────────────────────────────────────
     Hero. Generous breathing room. Restrained color: tinted neutrals + a
     single green accent on the title word and the live dot. The preview
     surface beneath uses a single bordered card (no nested cards).
   ───────────────────────────────────────────────────────────────────────── --}}
<section aria-labelledby="hero-heading" class="relative overflow-hidden">
    <div class="hero-wash absolute inset-x-0 top-0 h-[520px] pointer-events-none" aria-hidden="true"></div>

    <div class="relative max-w-3xl mx-auto px-4 pt-[calc(4rem+72px)] pb-16 sm:pt-24 sm:pb-24">
        <div class="flex flex-col items-start gap-7">
            <h1 id="hero-heading" class="text-4xl sm:text-5xl lg:text-6xl font-semibold tracking-[-0.022em] leading-[1.04] text-balance">
                {{ __('welcome.hero.title_pre') }}
                <span class="text-green-600 dark:text-green-500">{{ __('welcome.hero.title_accent') }}</span>
            </h1>

            <p class="text-lg sm:text-xl text-neutral-500 dark:text-neutral-400 leading-relaxed text-pretty max-w-[58ch]">
                {{ __('welcome.hero.subtitle') }}
            </p>

            <div class="flex w-full flex-col items-center gap-0 mt-1 text-center">
                <div class="mt-2 flex flex-col items-center justify-center sm:flex-row">
                    <a href="{{ $primaryUrl }}" target="_blank" rel="noopener noreferrer"
                       class="inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-medium rounded-full bg-neutral-900 dark:bg-white text-white dark:text-neutral-900 hover:bg-neutral-700 dark:hover:bg-neutral-200 transition-colors duration-200">
                        {{ $primaryLabel }}
                        <svg class="w-3.5 h-3.5 rtl:scale-x-[-1]" viewBox="0 0 10 11" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M7.70833 6.95834V2.79167H3.54167M2.5 8L7.5 3.00001" stroke="currentColor" stroke-linecap="square"/>
                        </svg>
                    </a>
                    @if ($botUsername && !$isLoggedIn)
                        <div data-telegram-login-container class="telegram-login-collapse flex items-center min-h-10 max-w-[238px] max-h-10 mt-3 sm:mt-0 sm:ms-3">
                            <iframe
                                id="telegram-login-hero"
                                data-telegram-login-frame
                                data-telegram-login-note-target="hero-entry-note"
                                title="{{ __('welcome.hero.login_telegram') }}"
                                src="{{ $telegramLoginUrl }}"
                                width="238"
                                height="40"
                                class="overflow-hidden border-0 bg-transparent"
                                scrolling="no"
                                frameborder="0"
                            ></iframe>
                        </div>
                    @endif
                </div>
                <p id="hero-entry-note" class="telegram-login-collapse mt-2 text-[13px] text-neutral-500 dark:text-neutral-400 max-w-[48ch] max-h-12 leading-relaxed text-pretty">
                    {{ $primaryNote }}
                </p>
                <div class="mt-2 flex flex-wrap items-center justify-center gap-x-3 gap-y-1 text-sm font-medium leading-relaxed">
                    <a href="#open-source" class="text-neutral-600 dark:text-neutral-300 hover:text-neutral-900 dark:hover:text-white underline underline-offset-4 decoration-neutral-300 dark:decoration-neutral-600 transition-colors duration-150">
                        {{ __('welcome.nav.open_source') }}
                    </a>
                    <a href="{{ $privacyUrl }}" class="text-neutral-600 dark:text-neutral-300 hover:text-neutral-900 dark:hover:text-white underline underline-offset-4 decoration-neutral-300 dark:decoration-neutral-600 transition-colors duration-150">
                        {{ __('welcome.nav.privacy') }}
                    </a>
                </div>
            </div>
        </div>

        {{-- Preview surface. ONE card — not card-in-card. Hairline border,
             generous internal padding, the "Today" list and the heatmap share
             a single container, separated by a subtle rule. --}}
        <div role="region" aria-label="{{ __('welcome.a11y.preview') }}" class="mt-14 sm:mt-20 rounded-2xl border border-black/[0.06] dark:border-white/[0.08] bg-white/70 dark:bg-neutral-900/45 p-5 sm:p-7">
            @php
                $previewWeekDayLetters = [];
                if (class_exists(IntlDateFormatter::class)) {
                    $previewWeekDayFormatter = new IntlDateFormatter(str_replace('_', '-', app()->getLocale()), IntlDateFormatter::NONE, IntlDateFormatter::NONE, null, null, 'EEEEE');
                    for ($day = 6; $day <= 12; $day++) {
                        $previewWeekDayLetters[] = $previewWeekDayFormatter->format(mktime(0, 0, 0, 1, $day, 2025));
                    }
                } else {
                    $previewWeekDayLetters = ['M', 'T', 'W', 'T', 'F', 'S', 'S'];
                }

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

            <div class="mb-6 pb-6 border-b rule">
                <div class="overflow-x-auto pb-1 text-center" aria-hidden="true">
                    <div class="mx-auto inline-flex items-start justify-center gap-2 min-w-max">
                        <div class="grid gap-1" style="grid-template-rows: repeat(7, 14px);">
                            @foreach ($previewWeekDayLetters as $letter)
                                <span class="flex h-[14px] w-3 items-center justify-center text-[9px] font-medium uppercase text-neutral-400 dark:text-neutral-500 select-none">
                                    {{ mb_substr((string) $letter, 0, 1) }}
                                </span>
                            @endforeach
                        </div>
                        <div class="grid gap-1" style="grid-template-rows: repeat(7, 14px); grid-auto-flow: column; grid-auto-columns: 14px;">
                            @foreach ($intensities as $i)
                                <span class="rounded-[3px] {{ $heatShades[$i] }} transition-transform duration-150 hover:scale-125"></span>
                            @endforeach
                        </div>
                    </div>
                    <div class="mt-3 flex items-center justify-center gap-1">
                        <span class="me-1 text-[10px] uppercase tracking-[0.16em] text-neutral-400 dark:text-neutral-500">{{ __('welcome.preview.less') }}</span>
                        @foreach ([0, 1, 2, 3, 4] as $level)
                            <span class="h-3 w-3 rounded-[3px] {{ $heatShades[$level] }}"></span>
                        @endforeach
                        <span class="ms-1 text-[10px] uppercase tracking-[0.16em] text-neutral-400 dark:text-neutral-500">{{ __('welcome.preview.more') }}</span>
                    </div>
                </div>
                <div class="mt-4 space-y-1 text-center">
                    @foreach ((array) __('welcome.preview.story_lines') as $line)
                        <p class="text-[15px] sm:text-base font-medium tracking-tight {{ $loop->last ? 'text-neutral-900 dark:text-white' : 'text-neutral-500 dark:text-neutral-400' }}">
                            {{ $line }}
                        </p>
                    @endforeach
                </div>
            </div>

            <div class="flex items-baseline justify-between mb-5">
                <h2 class="text-sm font-semibold tracking-tight">{{ __('welcome.preview.today') }}</h2>
                <span class="text-[11px] font-medium uppercase tracking-[0.16em] tabular-nums text-neutral-400 dark:text-neutral-500">{{ __('welcome.preview.date') }}</span>
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
        </div>

    </div>
</section>

{{-- ─────────────────────────────────────────────────────────────────────────
     Problem setup. Names the user's loop after the promise is clear, so the
     pain reads as evidence for the product instead of the first impression.
   ───────────────────────────────────────────────────────────────────────── --}}
<section aria-labelledby="problem-heading" class="max-w-5xl mx-auto px-4 py-16 sm:py-20 border-t rule">
    <div class="reveal rounded-[1.5rem] border border-black/[0.06] dark:border-white/[0.08] bg-white/70 dark:bg-neutral-900/45 p-6 sm:p-8">
        <div class="grid grid-cols-1 lg:grid-cols-[1.05fr_0.95fr] gap-5">
            <div class="flex flex-col justify-between gap-8">
                <div>
                    <div class="space-y-4">
                        <h2 id="problem-heading" class="scroll-mt-24 text-3xl sm:text-4xl lg:text-5xl font-semibold tracking-[-0.03em] leading-[1.06] text-balance">
                            {{ __('welcome.problem.title') }}
                        </h2>
                        @foreach ((array) __('welcome.problem.body') as $line)
                            <p class="text-base sm:text-lg leading-relaxed text-pretty text-neutral-500 dark:text-neutral-400 max-w-[48ch]">{{ $line }}</p>
                        @endforeach
                    </div>
                </div>

                {{-- On desktop this sits at the bottom of the left column, level with
                     the infographic. On mobile it's hidden here and shown again below
                     the grid, so it doesn't read as a caption for the infographic. --}}
                <p class="hidden lg:block max-w-[42ch] text-sm font-medium leading-relaxed text-green-700 dark:text-green-400">
                    {{ __('welcome.problem.closing') }}
                </p>
            </div>

            <div class="rounded-2xl bg-neutral-100/80 dark:bg-neutral-800/60 p-4 sm:p-5">
                <ol class="flex flex-col">
                    @foreach ((array) __('welcome.problem.cycle') as $step)
                        <li class="relative flex gap-3 pb-4 last:pb-0">
                            @if (! $loop->last)
                                <span class="absolute start-[0.5625rem] top-7 bottom-0 w-px bg-neutral-300 dark:bg-neutral-700" aria-hidden="true"></span>
                            @endif
                            <span class="relative z-10 mt-1 h-5 w-5 rounded-full bg-white dark:bg-neutral-900 ring-1 ring-neutral-300 dark:ring-neutral-700 flex-shrink-0"></span>
                            <span class="min-w-0">
                                <span class="block text-[11px] font-medium uppercase tracking-[0.16em] tabular-nums text-neutral-400 dark:text-neutral-500">{{ $step['day'] }}</span>
                                <span class="block mt-0.5 text-[15px] font-semibold tracking-tight text-neutral-900 dark:text-white">{{ $step['mood'] }}</span>
                            </span>
                        </li>
                    @endforeach
                </ol>
            </div>
        </div>

        <p class="lg:hidden mt-6 text-sm font-medium leading-relaxed text-green-700 dark:text-green-400">
            {{ __('welcome.problem.closing') }}
        </p>
    </div>
</section>

@php $digestItems = (array) __('welcome.digest_examples.items'); @endphp
@if (count($digestItems) > 0)
<section aria-labelledby="digest-heading" class="scroll-mt-20 max-w-3xl mx-auto px-4 py-20 sm:py-24">
    <div class="reveal mb-12 max-w-2xl">
        <h2 id="digest-heading" class="text-3xl sm:text-4xl font-semibold tracking-[-0.02em] leading-[1.1] text-balance mb-3">{{ __('welcome.digest_examples.title') }}</h2>
        <p class="text-base text-neutral-500 dark:text-neutral-400 leading-relaxed text-pretty max-w-[52ch]">{{ __('welcome.digest_examples.subtitle') }}</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
        @foreach ($digestItems as $i => $digest)
            <article class="reveal rounded-2xl border border-black/[0.06] dark:border-white/[0.08] bg-white/70 dark:bg-neutral-900/40 flex flex-col overflow-hidden"
                     data-reveal-delay="{{ $i * 80 }}">
                <div class="flex items-center justify-between gap-3 px-5 py-3 border-b rule">
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-md bg-green-100 dark:bg-green-950/60 text-green-600 dark:text-green-500 flex-shrink-0" aria-hidden="true">
                        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 3l1.9 4.6L18.5 9.5l-4.6 1.9L12 16l-1.9-4.6L5.5 9.5l4.6-1.9z"/>
                            <path d="M19 15l.8 1.9 1.9.8-1.9.8L19 20.4l-.8-1.9-1.9-.8 1.9-.8z"/>
                        </svg>
                    </span>
                    <time class="text-[11px] font-medium tabular-nums text-neutral-400 dark:text-neutral-500 flex-shrink-0" datetime="{{ $digest['date_machine'] }}">
                        {{ $digest['date'] }}
                    </time>
                </div>

                <div class="px-5 py-4 flex-1 space-y-2">
                    <p class="text-[13px] leading-relaxed text-pretty text-neutral-600 dark:text-neutral-300">{{ $digest['paragraph_1'] }}</p>
                    <p class="text-[13px] leading-relaxed text-pretty text-neutral-600 dark:text-neutral-300">{{ $digest['paragraph_2'] }}</p>
                </div>

                @if (! empty($digest['habits']))
                    <div class="px-5 pb-4 pt-1 border-t rule flex flex-wrap gap-1.5">
                        @foreach ($digest['habits'] as $habit)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium
                                         {{ $habit['done'] ? 'bg-green-100 dark:bg-green-950/60 text-green-700 dark:text-green-400' : 'bg-neutral-100 dark:bg-neutral-800 text-neutral-500 dark:text-neutral-400' }}">
                                @if ($habit['done'])
                                    <svg class="w-2.5 h-2.5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                                @endif
                                <span class="{{ $habit['done'] ? 'line-through opacity-70' : '' }}">{{ $habit['name'] }}</span>
                            </span>
                        @endforeach
                    </div>
                @endif
            </article>
        @endforeach
    </div>
</section>
@endif

<section aria-label="{{ __('welcome.a11y.lenses') }}" class="max-w-5xl mx-auto px-4 pb-20">
    @php
        $weekDayLetters = [];
        if (class_exists(IntlDateFormatter::class)) {
            $fmt = new IntlDateFormatter(str_replace('_', '-', app()->getLocale()), IntlDateFormatter::NONE, IntlDateFormatter::NONE, null, null, 'EEEEE');
            for ($d = 6; $d <= 12; $d++) {
                $weekDayLetters[] = $fmt->format(mktime(0, 0, 0, 1, $d, 2025));
            }
        } else {
            $weekDayLetters = ['M', 'T', 'W', 'T', 'F', 'S', 'S'];
        }
        $weekHabitNames = (array) __('welcome.preview.habits');
        $weekGrid = [
            [true,  true,  true,  true,  true,  true,  false],
            [true,  true,  true,  true,  true,  null,  null],
            [true,  null,  true,  null,  false, true,  null],
            [false, null,  true,  null,  true,  null,  null],
            [true,  true,  true,  false, true,  true,  false],
            [null,  true,  null,  true,  null,  true,  null],
        ];
        $yearLived = [2,1,3,4,2,3,1,4,3,2, 4,3,2,4,1,3,4,2,3,4, 1,3,4,2,4,3,1,2,4,3];
        $lifeCurrentAge = 28;
        $lifePattern = [1,2,3,2,4,3,2,1,3,4, 2,3,4,2,3,1,4,3,2,4, 3,1,2,4,3,2,4,1,3,2, 4,3,1,2,3,4,2,1,4,3, 2,4,1,3,2,4,3,1,2,3, 4,2,3,1,4,2,3,4,1,2];
        $shades = [
            'bg-neutral-200 dark:bg-neutral-800',
            'bg-green-200 dark:bg-green-900',
            'bg-green-300 dark:bg-green-800',
            'bg-green-400 dark:bg-green-700',
            'bg-green-500 dark:bg-green-600',
        ];
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 sm:gap-4">
        <div class="reveal rounded-2xl border border-black/[0.06] dark:border-white/[0.08] bg-white/70 dark:bg-neutral-900/40 p-5 flex flex-col">
            <h3 class="text-sm font-semibold tracking-tight text-neutral-900 dark:text-white mb-4">{{ __('welcome.views.week') }}</h3>
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
                                    <span class="aspect-square flex items-center justify-center text-[10px] font-medium {{ $di === 6 ? 'text-neutral-200 dark:text-neutral-700' : 'text-neutral-300 dark:text-neutral-600' }} select-none leading-none">-</span>
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

        <div class="reveal rounded-2xl border border-black/[0.06] dark:border-white/[0.08] bg-white/70 dark:bg-neutral-900/40 p-5 flex flex-col" data-reveal-delay="80">
            <h3 class="text-sm font-semibold tracking-tight text-neutral-900 dark:text-white mb-4">{{ __('welcome.views.year') }}</h3>
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

        <div class="reveal rounded-2xl border border-black/[0.06] dark:border-white/[0.08] bg-white/70 dark:bg-neutral-900/40 p-5 flex flex-col" data-reveal-delay="160">
            <h3 class="text-sm font-semibold tracking-tight text-neutral-900 dark:text-white mb-4">{{ __('welcome.views.life') }}</h3>
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

<section id="how" aria-labelledby="how-heading" class="scroll-mt-20 max-w-3xl mx-auto px-4 py-20 sm:py-24 border-t rule">
    <div class="reveal mb-10 max-w-2xl">
        <h2 id="how-heading" class="text-3xl sm:text-4xl font-semibold tracking-[-0.02em] leading-[1.1] text-balance">{{ __('welcome.how.title') }}</h2>
    </div>

    <ol class="flex flex-col border-y rule">
        @foreach ((array) __('welcome.how.steps') as $step)
            <li class="reveal grid grid-cols-[2rem_1fr] gap-4 py-5 border-t rule first:border-t-0" data-reveal-delay="{{ $loop->index * 60 }}">
                <span class="mt-0.5 text-sm font-semibold text-green-600 dark:text-green-500" aria-hidden="true">{{ $loop->iteration }}</span>
                <div class="min-w-0">
                    <h3 class="text-[15px] font-semibold tracking-tight text-neutral-900 dark:text-white">{{ $step['title'] }}</h3>
                    <p class="mt-1.5 text-sm leading-relaxed text-pretty text-neutral-500 dark:text-neutral-400 max-w-[60ch]">{{ $step['body'] }}</p>
                </div>
            </li>
        @endforeach
    </ol>
</section>

{{-- ─────────────────────────────────────────────────────────────────────────
     Philosophy. Editorial 2-column list with hairline dividers — no card grid.
     Icon as a small accent, not a heavy badge. Tighter rhythm than the hero.
   ───────────────────────────────────────────────────────────────────────── --}}
<section id="features" aria-labelledby="features-heading" class="scroll-mt-20 max-w-3xl mx-auto px-4 py-20 sm:py-24">
    <div class="reveal mb-12 max-w-2xl">
        <h2 id="features-heading" class="text-3xl sm:text-4xl font-semibold tracking-[-0.02em] leading-[1.1] text-balance">{{ __('welcome.features.title') }}</h2>
    </div>

    <ul class="grid grid-cols-1 sm:grid-cols-2 sm:gap-x-10">
        @foreach ([
            ['key' => 'schedules', 'icon' => 'calendar'],
            ['key' => 'streaks',   'icon' => 'grid'],
            ['key' => 'reminders', 'icon' => 'bell'],
            ['key' => 'notes',     'icon' => 'note'],
            ['key' => 'ai',        'icon' => 'reflection', 'premium' => true],
            ['key' => 'export',    'icon' => 'download', 'premium' => true],
        ] as $feature)
            <li class="reveal py-6 border-t rule first:border-t-0 sm:[&:nth-child(2)]:border-t-0"
                data-reveal-delay="{{ $loop->index * 60 }}">
                <div class="flex items-start gap-4">
                    <span class="flex-shrink-0 w-8 h-8 rounded-lg bg-neutral-100 dark:bg-neutral-800/80 flex items-center justify-center text-green-700 dark:text-green-400">
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
                            @case('reflection')
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"/></svg>
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
                                <span class="text-[9px] font-medium uppercase tracking-[0.16em] px-1.5 py-0.5 rounded-full bg-neutral-100 dark:bg-neutral-800 text-neutral-500 dark:text-neutral-400 ring-1 ring-neutral-200/70 dark:ring-neutral-700/70">
                                    {{ __('welcome.features.premium') }}
                                </span>
                            @endif
                        </div>
                        <p class="text-sm leading-relaxed text-pretty text-neutral-500 dark:text-neutral-400">{{ __('welcome.features.items.'.$feature['key'].'.body') }}</p>
                    </div>
                </div>
            </li>
        @endforeach
    </ul>
</section>

<section aria-labelledby="philosophy-heading" class="max-w-3xl mx-auto px-4 py-20 sm:py-24 border-t rule">
    <div class="reveal rounded-2xl border border-black/[0.06] dark:border-white/[0.08] bg-white/70 dark:bg-neutral-900/40 p-6 sm:p-8">
        <h2 id="philosophy-heading" class="text-3xl sm:text-4xl font-semibold tracking-[-0.02em] leading-[1.1] text-balance">{{ __('welcome.philosophy.title') }}</h2>

        <ul class="mt-8 grid gap-4">
            @foreach ((array) __('welcome.philosophy.items') as $item)
                <li class="reveal flex items-start gap-3" data-reveal-delay="{{ $loop->index * 60 }}">
                    <span class="mt-2 h-2 w-2 rounded-full bg-green-500 flex-shrink-0" aria-hidden="true"></span>
                    <span class="text-lg sm:text-xl font-medium tracking-[-0.015em] leading-snug text-neutral-800 dark:text-neutral-100">{{ $item }}</span>
                </li>
            @endforeach
        </ul>
    </div>
</section>

<section aria-labelledby="anti-streak-heading" class="max-w-3xl mx-auto px-4 py-20 sm:py-24 border-t rule">
    <div class="reveal rounded-2xl bg-neutral-100/80 dark:bg-neutral-800/60 p-6 sm:p-8">
        <h2 id="anti-streak-heading" class="text-3xl sm:text-4xl font-semibold tracking-[-0.02em] leading-[1.1] text-balance max-w-[16ch]">{{ __('welcome.anti_streak.title') }}</h2>
        <div class="mt-8 space-y-4 text-[15px] sm:text-base leading-relaxed text-pretty text-neutral-600 dark:text-neutral-300 max-w-[58ch]">
            @foreach ((array) __('welcome.anti_streak.body') as $paragraph)
                <p>{{ $paragraph }}</p>
            @endforeach
        </div>
    </div>
</section>

{{-- ─────────────────────────────────────────────────────────────────────────
     Open source proof. Trust without a staged repo-window metaphor.
   ───────────────────────────────────────────────────────────────────────── --}}
<section id="open-source" aria-labelledby="open-heading" class="scroll-mt-20 max-w-3xl mx-auto px-4 py-20 sm:py-24 border-t rule">
    <div class="reveal mb-8 max-w-2xl">
        <h2 id="open-heading" class="text-3xl sm:text-4xl font-semibold tracking-[-0.02em] leading-[1.1] text-balance">
            {{ __('welcome.open.title') }}
        </h2>
        <p class="mt-4 text-[15px] sm:text-base leading-relaxed text-pretty text-neutral-600 dark:text-neutral-300 max-w-[44ch]">
            {{ __('welcome.open.body') }}
        </p>
    </div>

    <div class="reveal flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-5 mb-8">
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

    <ul class="reveal rule">
        @foreach (['license', 'selfhost', 'audit'] as $key)
            <li class="py-4 rule">
                <p class="text-sm font-medium tracking-tight text-neutral-900 dark:text-white">
                    {{ __('welcome.open.facts.'.$key.'.title') }}
                </p>
                <p class="text-[13px] text-neutral-500 dark:text-neutral-400 leading-snug text-pretty mt-0.5 max-w-[56ch]">
                    {{ __('welcome.open.facts.'.$key.'.body') }}
                </p>
            </li>
        @endforeach
    </ul>
</section>

<section aria-labelledby="author-heading" class="max-w-3xl mx-auto px-4 py-20 sm:py-24 border-t rule">
    <div class="reveal grid grid-cols-1 sm:grid-cols-[auto_1fr] gap-6 sm:gap-8 items-start rounded-2xl border border-black/[0.06] dark:border-white/[0.08] bg-white/70 dark:bg-neutral-900/40 p-6 sm:p-8">
        <img src="https://github.com/nikfedorov.png" alt="{{ __('welcome.author.photo_alt') }}" class="w-20 h-20 rounded-2xl bg-neutral-100 dark:bg-neutral-800 object-cover" loading="lazy">

        <div class="min-w-0">
            <h2 id="author-heading" class="text-2xl sm:text-3xl font-semibold tracking-[-0.02em] leading-[1.12] text-balance">{{ __('welcome.author.title') }}</h2>

            <div class="mt-5 space-y-3 text-[15px] leading-relaxed text-pretty text-neutral-600 dark:text-neutral-300">
                @foreach ((array) __('welcome.author.body') as $paragraph)
                    <p>{{ $paragraph }}</p>
                @endforeach
            </div>

            <div class="mt-6 flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-5">
                <div>
                    <p class="text-sm font-semibold text-neutral-900 dark:text-white">{{ __('welcome.author.name') }}</p>
                    <p class="text-[12px] text-neutral-500 dark:text-neutral-400">{{ __('welcome.author.role') }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3 mt-3 text-sm font-medium">
                <a href="{{ $authorGithubUrl }}" target="_blank" rel="noopener noreferrer" class="text-neutral-500 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-white transition-colors duration-150">{{ __('welcome.author.github') }}</a>
                <a href="{{ $authorTwitterUrl }}" target="_blank" rel="noopener noreferrer" class="text-neutral-500 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-white transition-colors duration-150">{{ __('welcome.author.twitter') }}</a>
            </div>
        </div>
    </div>
</section>

{{-- ─────────────────────────────────────────────────────────────────────────
     FAQ. Accordion with smooth height + padding animation.
   ───────────────────────────────────────────────────────────────────────── --}}
<section id="faq" aria-labelledby="faq-heading" class="scroll-mt-20 max-w-3xl mx-auto px-4 py-20 sm:py-24 border-t rule">
    <div class="reveal mb-12 max-w-2xl">
        <h2 id="faq-heading" class="text-3xl sm:text-4xl font-semibold tracking-[-0.02em] leading-[1.1] text-balance">{{ __('welcome.faq.title') }}</h2>
    </div>

    <div class="flex flex-col">
        @foreach ((array) __('welcome.faq.items') as $faqItem)
            <details @if ($loop->index === 4) id="privacy" @endif class="faq-item reveal border-t rule first:border-t-0 scroll-mt-24" data-reveal-delay="{{ $loop->index * 60 }}">
                <summary class="flex items-center justify-between gap-4 py-5 cursor-pointer select-none group">
                    <span class="text-[15px] font-semibold tracking-tight text-neutral-900 dark:text-white group-hover:text-neutral-600 dark:group-hover:text-neutral-300 transition-colors duration-150">{{ $faqItem['q'] }}</span>
                    <svg class="faq-chevron w-4 h-4 text-neutral-400 dark:text-neutral-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <polyline points="6 9 12 15 18 9"/>
                    </svg>
                </summary>
                <div class="faq-answer">
                    <div>
                        <p class="text-[15px] leading-relaxed text-pretty text-neutral-500 dark:text-neutral-400 max-w-[60ch]">{{ __('welcome.faq.items.' . $loop->index . '.a', ['days' => $trialPeriodDays]) }}</p>
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
        <h2 id="cta-heading" class="text-4xl sm:text-5xl font-semibold tracking-[-0.025em] leading-[1.05] text-balance max-w-[20ch]">{{ __('welcome.cta.title') }}</h2>
        <p class="text-base sm:text-lg text-neutral-500 dark:text-neutral-400 leading-relaxed text-pretty max-w-[48ch]">
            {{ __('welcome.cta.subtitle') }}
        </p>
        <div class="mt-2 flex flex-col items-center justify-center sm:flex-row">
            <a href="{{ $primaryUrl }}" target="_blank" rel="noopener noreferrer"
               class="inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-medium rounded-full bg-neutral-900 dark:bg-white text-white dark:text-neutral-900 hover:bg-neutral-700 dark:hover:bg-neutral-200 transition-colors duration-200">
                {{ $primaryLabel }}
                <svg class="w-3.5 h-3.5 rtl:scale-x-[-1]" viewBox="0 0 10 11" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M7.70833 6.95834V2.79167H3.54167M2.5 8L7.5 3.00001" stroke="currentColor" stroke-linecap="square"/>
                </svg>
            </a>

            @if ($botUsername && !$isLoggedIn)
                <div data-telegram-login-container class="telegram-login-collapse max-w-[238px] max-h-10 mt-3 sm:mt-0 sm:ms-3">
                    <iframe
                        id="telegram-login-cta"
                        data-telegram-login-frame
                        title="{{ __('welcome.hero.login_telegram') }}"
                        src="{{ $telegramLoginUrl }}"
                        width="238"
                        height="40"
                        class="overflow-hidden border-0 bg-transparent"
                        scrolling="no"
                        frameborder="0"
                    ></iframe>
                </div>
            @endif
        </div>
        <div class="flex flex-wrap items-center justify-center gap-x-4 gap-y-1 text-sm font-medium leading-relaxed">
            <a href="#open-source" class="text-neutral-500 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-white underline underline-offset-4 decoration-neutral-300 dark:decoration-neutral-600 transition-colors duration-150">
                {{ __('welcome.nav.open_source') }}
            </a>
            <a href="{{ $privacyUrl }}" class="text-neutral-500 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-white underline underline-offset-4 decoration-neutral-300 dark:decoration-neutral-600 transition-colors duration-150">
                {{ __('welcome.nav.privacy') }}
            </a>
        </div>
    </div>
</section>

</main>

{{-- Footer --}}
<footer class="border-t rule">
    <div class="max-w-5xl mx-auto px-4 py-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-5 text-xs text-neutral-400 dark:text-neutral-500">
        <div class="space-y-2">
            <span class="block">{{ __('welcome.footer.tagline', ['year' => date('Y'), 'app' => config('app.name', 'OpenHabit')]) }}</span>
            <a href="#problem-heading" class="inline-flex text-neutral-500 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-white transition-colors duration-150">
                {{ __('welcome.footer.why') }}
            </a>
        </div>
        <div class="flex flex-wrap items-center gap-x-4 gap-y-2">
            @if ($telegramUrl)
                <a href="{{ $telegramUrl }}" target="_blank" rel="noopener noreferrer" class="hover:text-neutral-700 dark:hover:text-neutral-300 transition-colors duration-150">{{ __('welcome.footer.telegram') }}</a>
            @endif
            <a href="{{ $roadmapUrl }}" target="_blank" rel="noopener noreferrer" class="hover:text-neutral-700 dark:hover:text-neutral-300 transition-colors duration-150">{{ __('welcome.footer.roadmap') }}</a>
            <a href="{{ $changelogUrl }}" target="_blank" rel="noopener noreferrer" class="hover:text-neutral-700 dark:hover:text-neutral-300 transition-colors duration-150">{{ __('welcome.footer.changelog') }}</a>
            <a href="{{ $newsUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 hover:text-neutral-700 dark:hover:text-neutral-300 transition-colors duration-150">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M9.78 18.65l.28-4.23 7.68-6.92c.34-.31-.07-.46-.52-.19L7.74 13.24 3.64 11.95c-.88-.25-.89-.86.2-1.3l15.97-6.16c.73-.33 1.43.18 1.15 1.3l-2.72 12.81c-.19.91-.74 1.13-1.5.71L12.6 16.3l-1.99 1.93c-.23.23-.42.42-.83.42z"/>
                </svg>
                {{ __('welcome.footer.news') }}
            </a>
            <a href="{{ $privacyUrl }}" class="hover:text-neutral-700 dark:hover:text-neutral-300 transition-colors duration-150">{{ __('welcome.footer.privacy') }}</a>
            <a href="{{ $githubUrl }}" target="_blank" rel="noopener noreferrer" aria-label="{{ __('welcome.footer.github') }}" class="hover:text-neutral-700 dark:hover:text-neutral-300 transition-colors duration-150">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"/>
                </svg>
            </a>
            <a href="{{ $primaryUrl }}" target="_blank" rel="noopener noreferrer" class="hover:text-neutral-700 dark:hover:text-neutral-300 transition-colors duration-150">{{ __('welcome.footer.app') }}</a>
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

        // Telegram's legacy script gives every login iframe the same id for a
        // bot username, so only one widget can render. These independent
        // iframes keep both login widgets working and preserve the same
        // callback contract as data-auth-url.
        var telegramLoginFrames = Array.prototype.slice.call(document.querySelectorAll('[data-telegram-login-frame]'));
        if (telegramLoginFrames.length) {
            var telegramAuthCallbackUrl = @json($callbackUrl);
            var telegramAuthRedirected = false;
            var hideTelegramLoginElement = function (element) {
                if (!element || element.getAttribute('data-telegram-login-hiding') === 'true') { return; }

                var rect = element.getBoundingClientRect();
                var styles = window.getComputedStyle(element);

                element.setAttribute('data-telegram-login-hiding', 'true');
                element.style.width = rect.width + 'px';
                element.style.height = rect.height + 'px';
                element.style.marginTop = styles.marginTop;
                element.style.marginRight = styles.marginRight;
                element.style.marginBottom = styles.marginBottom;
                element.style.marginLeft = styles.marginLeft;
                element.style.paddingTop = styles.paddingTop;
                element.style.paddingRight = styles.paddingRight;
                element.style.paddingBottom = styles.paddingBottom;
                element.style.paddingLeft = styles.paddingLeft;

                window.requestAnimationFrame(function () {
                    element.classList.add('is-hidden');
                });

                window.setTimeout(function () {
                    element.hidden = true;
                }, 220);
            };
            var hideTelegramLoginContainer = function (frame) {
                hideTelegramLoginElement(frame.closest('[data-telegram-login-container]'));
            };
            var hideTelegramLoginNote = function (frame) {
                var noteTargetId = frame.getAttribute('data-telegram-login-note-target');
                var noteTarget = noteTargetId ? document.getElementById(noteTargetId) : null;
                hideTelegramLoginElement(noteTarget);
            };
            var telegramLoginUnavailableTimer = window.setTimeout(function () {
                telegramLoginFrames.forEach(function (frame) {
                    if (frame.getAttribute('data-telegram-login-ready') === 'true') { return; }

                    hideTelegramLoginContainer(frame);
                    hideTelegramLoginNote(frame);
                });
            }, 3500);
            var markTelegramLoginReady = function (frame) {
                frame.setAttribute('data-telegram-login-ready', 'true');

                if (telegramLoginFrames.every(function (item) { return item.getAttribute('data-telegram-login-ready') === 'true'; })) {
                    window.clearTimeout(telegramLoginUnavailableTimer);
                }
            };
            var redirectToTelegramAuth = function (authData) {
                if (telegramAuthRedirected || !authData || typeof authData !== 'object') { return; }
                telegramAuthRedirected = true;

                var authUrl = new URL(telegramAuthCallbackUrl, window.location.href);
                Object.keys(authData || {}).forEach(function (key) {
                    authUrl.searchParams.set(key, authData[key]);
                });
                window.location.href = authUrl.toString();
            };
            var handleTelegramAuthResult = function () {
                var locationHash = window.location.hash.toString();
                var match = locationHash.match(/[#?&]tgAuthResult=([A-Za-z0-9\-_=]*)$/);
                if (!match) { return; }

                try {
                    window.location.hash = locationHash.replace(/[#?&]tgAuthResult=([A-Za-z0-9\-_=]*)$/, '');
                    var data = match[1].replace(/-/g, '+').replace(/_/g, '/');
                    var pad = data.length % 4;
                    if (pad > 1) { data += new Array(5 - pad).join('='); }
                    redirectToTelegramAuth(JSON.parse(window.atob(data)));
                } catch (e) {}
            };

            handleTelegramAuthResult();
            window.addEventListener('message', function (e) {
                if (e.origin !== 'https://oauth.telegram.org') { return; }
                if (!telegramLoginFrames.some(function (frame) { return frame.contentWindow === e.source; })) { return; }

                try {
                    var data = JSON.parse(e.data);
                    if (data.event === 'resize') {
                        telegramLoginFrames.forEach(function (frame) {
                            if (frame.contentWindow !== e.source) { return; }
                            markTelegramLoginReady(frame);
                            if (data.width) { frame.style.width = data.width + 'px'; }
                            if (data.height) { frame.style.height = data.height + 'px'; }
                        });
                    }
                    if (data.event === 'auth_user' && data.auth_data && !data.init) {
                        redirectToTelegramAuth(data.auth_data);
                    }
                } catch (error) {}
            });
        }

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
                if (details._faqCloseTimer) {
                    window.clearTimeout(details._faqCloseTimer);
                    details._faqCloseTimer = null;
                }
                if (details.open) {
                    // Animate to closed, then remove the `open` attribute.
                    answer.style.gridTemplateRows = '0fr';
                    answer.style.paddingBottom = '0';
                    var finishClose = function () {
                        details.removeAttribute('open');
                        details._faqCloseTimer = null;
                    };
                    var closeOnTransitionEnd = function (ev) {
                        if (ev.propertyName !== 'grid-template-rows') { return; }
                        window.clearTimeout(details._faqCloseTimer);
                        answer.removeEventListener('transitionend', closeOnTransitionEnd);
                        finishClose();
                    };
                    answer.addEventListener('transitionend', closeOnTransitionEnd);
                    details._faqCloseTimer = window.setTimeout(function () {
                        answer.removeEventListener('transitionend', closeOnTransitionEnd);
                        finishClose();
                    }, 360);
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

        var openFaqFromHash = function () {
            if (!window.location.hash) { return; }

            var target = document.getElementById(window.location.hash.slice(1));
            if (!target || !target.classList.contains('faq-item')) { return; }

            var answer = target.querySelector('.faq-answer');
            if (!answer) { return; }

            target.setAttribute('open', '');
            answer.style.gridTemplateRows = '1fr';
            answer.style.paddingBottom = '1.25rem';
        };
        openFaqFromHash();
        window.addEventListener('hashchange', openFaqFromHash);
    })();
</script>

</body>
</html>
