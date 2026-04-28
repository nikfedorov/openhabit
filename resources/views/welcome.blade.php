<!DOCTYPE html>
@php
    use App\Services\LocaleService;
    $currentLocale = app()->getLocale();
    $isRtl = LocaleService::isRtl($currentLocale);
    $appUrl = url('/app/track');
    $telegramUrl = $botUsername ? 'https://t.me/' . $botUsername : null;
    $primaryUrl = $telegramUrl ?? $appUrl;
    $primaryLabel = $telegramUrl ? __('welcome.nav.cta_telegram') : __('welcome.nav.cta_app');
    $callbackUrl = url('/auth/telegram/callback');
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
            document.documentElement.style.backgroundColor = isDark ? '#171717' : '#ffffff';
        })();
    </script>

    @vite(['resources/css/app.css'])
</head>
<body class="antialiased bg-white dark:bg-neutral-900 text-neutral-900 dark:text-white">

{{-- Top navigation --}}
<header class="border-b border-neutral-200 dark:border-neutral-800">
    <nav class="max-w-5xl mx-auto px-4 py-4 flex items-center justify-between gap-3">
        <a href="/" class="flex items-center gap-2 group flex-shrink-0">
            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-neutral-100 dark:bg-neutral-800 transition-all duration-150 group-hover:scale-105">
                <svg viewBox="0 0 512 512" class="w-5 h-5" xmlns="http://www.w3.org/2000/svg">
                    <rect x="32"  y="272" width="208" height="208" rx="32" fill="#14532d"/>
                    <rect x="272" y="272" width="208" height="208" rx="32" fill="#15803d"/>
                    <rect x="32"  y="32"  width="208" height="208" rx="32" fill="#16a34a"/>
                    <rect x="272" y="32"  width="208" height="208" rx="32" fill="#22c55e"/>
                </svg>
            </span>
            <span class="font-semibold text-base tracking-tight">{{ config('app.name', 'OpenHabit') }}</span>
        </a>

        <div class="flex items-center gap-1 sm:gap-2">
            <a href="#features" class="hidden sm:inline-block px-3 py-1.5 text-sm font-medium text-neutral-500 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-white rounded-full transition-all duration-150">
                {{ __('welcome.nav.features') }}
            </a>
            <a href="#how" class="hidden sm:inline-block px-3 py-1.5 text-sm font-medium text-neutral-500 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-white rounded-full transition-all duration-150">
                {{ __('welcome.nav.how') }}
            </a>

            {{-- Language selector --}}
            <div class="relative" data-lang-menu>
                <button type="button"
                        data-lang-toggle
                        aria-haspopup="true"
                        aria-expanded="false"
                        aria-label="{{ __('welcome.nav.language') }}"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-neutral-500 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-white hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded-full transition-all duration-150">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 010 20M12 2a15.3 15.3 0 000 20"/>
                    </svg>
                    <span>{{ LocaleService::label($currentLocale) }}</span>
                </button>
                <div data-lang-panel hidden
                     class="absolute end-0 mt-2 min-w-[10rem] py-1.5 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 shadow-lg z-20">
                    @foreach (LocaleService::all() as $code => $label)
                        <a href="{{ request()->fullUrlWithQuery(['lang' => $code]) }}"
                           class="flex items-center justify-between px-3 py-1.5 text-sm hover:bg-neutral-100 dark:hover:bg-neutral-700 transition-all duration-150 {{ $code === $currentLocale ? 'font-medium text-neutral-900 dark:text-white' : 'text-neutral-600 dark:text-neutral-300' }}">
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
                    class="inline-flex items-center justify-center w-8 h-8 rounded-full text-neutral-500 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-white hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-all duration-150">
                <svg data-icon-sun class="w-4 h-4 hidden dark:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="12" cy="12" r="4"/>
                    <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/>
                </svg>
                <svg data-icon-moon class="w-4 h-4 block dark:hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
                </svg>
            </button>

            <a href="{{ $primaryUrl }}" target="_blank" rel="noopener noreferrer"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-full bg-neutral-900 dark:bg-white text-white dark:text-neutral-900 hover:bg-neutral-700 dark:hover:bg-neutral-200 transition-all duration-150">
                {{ $primaryLabel }}
                <svg class="w-3 h-3 rtl:scale-x-[-1]" viewBox="0 0 10 11" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M7.70833 6.95834V2.79167H3.54167M2.5 8L7.5 3.00001" stroke="currentColor" stroke-linecap="square"/>
                </svg>
            </a>
        </div>
    </nav>
</header>

{{-- Hero --}}
<section class="max-w-3xl mx-auto px-4 pt-16 pb-12 sm:pt-24 sm:pb-16">
    <div class="flex flex-col items-start gap-6">
        <span class="animate-fade-in-up inline-flex items-center gap-2 px-3 py-1 text-xs font-medium rounded-full bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-400">
            <span class="inline-block w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
            {{ __('welcome.hero.badge') }}
        </span>
        <h1 class="animate-fade-in-up text-4xl sm:text-5xl font-bold tracking-tight leading-tight" style="animation-delay:80ms">
            {{ __('welcome.hero.title_pre') }}
            <span class="text-green-600 dark:text-green-500">{{ __('welcome.hero.title_accent') }}</span>
        </h1>
        <p class="animate-fade-in-up text-lg text-neutral-500 dark:text-neutral-400 leading-relaxed max-w-2xl" style="animation-delay:160ms">
            {{ __('welcome.hero.subtitle') }}
        </p>

        <div class="animate-fade-in-up flex flex-col sm:flex-row sm:items-center gap-3 mt-2" style="animation-delay:240ms">
            <a href="{{ $primaryUrl }}" target="_blank" rel="noopener noreferrer"
               class="inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-medium rounded-full bg-neutral-900 dark:bg-white text-white dark:text-neutral-900 hover:bg-neutral-700 dark:hover:bg-neutral-200 transition-all duration-150">
                {{ $primaryLabel }}
                <svg class="w-3.5 h-3.5 rtl:scale-x-[-1]" viewBox="0 0 10 11" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M7.70833 6.95834V2.79167H3.54167M2.5 8L7.5 3.00001" stroke="currentColor" stroke-linecap="square"/>
                </svg>
            </a>

            @if ($botUsername)
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

    {{-- Visual: tracker preview card --}}
    <div class="animate-fade-in-up mt-14 bg-neutral-100 dark:bg-neutral-800 rounded-2xl p-4 sm:p-6" style="animation-delay:340ms">
        <div class="bg-white dark:bg-neutral-900 rounded-xl px-4 py-4 shadow-sm">
            <div class="flex items-baseline justify-between mb-4">
                <h3 class="text-sm font-semibold">{{ __('welcome.preview.today') }}</h3>
                <span class="text-xs text-neutral-400 dark:text-neutral-500">{{ __('welcome.preview.date') }}</span>
            </div>
            @php
                $habits = (array) __('welcome.preview.habits');
                $doneFlags = [true, true, false, false, false, false];
            @endphp
            <ul class="flex flex-col gap-1">
                @foreach ($habits as $i => $name)
                    @php $done = $doneFlags[$i] ?? false; @endphp
                    <li class="flex items-center gap-3 px-2 py-2 rounded-lg hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-all duration-150 {{ $done ? 'opacity-60' : '' }}">
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

            <div class="mt-5 pt-4 border-t border-neutral-200 dark:border-neutral-700">
                @php
                    // 20 weeks × 7 days = 140 cells, column-first (week columns, day rows)
                    $intensities = [
                        0,0,0,1,0,0,0, // week 1
                        0,0,1,1,1,0,0, // week 2
                        0,1,1,2,1,1,0, // week 3
                        0,0,1,2,2,1,0, // week 4
                        0,1,2,3,2,1,0, // week 5
                        1,1,2,3,3,2,1, // week 6
                        0,1,2,3,4,3,1, // week 7
                        1,2,3,4,4,3,2, // week 8
                        2,3,4,4,3,2,1, // week 9
                        1,2,3,4,4,3,2, // week 10
                        2,3,4,4,4,3,2, // week 11
                        1,2,4,4,3,3,2, // week 12
                        2,3,3,4,4,3,1, // week 13
                        1,2,3,3,4,2,1, // week 14
                        0,1,2,3,3,2,1, // week 15
                        1,2,3,3,2,2,1, // week 16
                        0,1,2,2,3,2,1, // week 17
                        0,1,1,2,2,1,0, // week 18
                        0,0,1,2,1,1,0, // week 19
                        0,0,1,1,1,0,0, // week 20
                    ];
                    $heatShades = [
                        'bg-neutral-200 dark:bg-neutral-700',
                        'bg-green-200 dark:bg-green-900',
                        'bg-green-400 dark:bg-green-700',
                        'bg-green-500 dark:bg-green-600',
                        'bg-green-600 dark:bg-green-500',
                    ];
                @endphp
                {{-- 20 columns (weeks) × 7 rows (days), column-first — matches the app's heatmap --}}
                <div class="flex justify-center mb-2">
                    <div class="grid gap-1"
                         style="grid-template-rows: repeat(7, 12px); grid-auto-flow: column; grid-auto-columns: 12px;">
                        @foreach ($intensities as $i)
                            <span class="rounded-sm {{ $heatShades[$i] }} transition-all duration-150 hover:scale-150"></span>
                        @endforeach
                    </div>
                </div>
                {{-- Legend: Less [■■■■■] More --}}
                <div class="flex items-center justify-end gap-1">
                    <span class="me-1 text-[10px] text-neutral-500 dark:text-neutral-400">{{ __('welcome.preview.less') }}</span>
                    @foreach ([0, 1, 2, 3, 4] as $level)
                        <span class="h-3 w-3 rounded-sm {{ $heatShades[$level] }}"></span>
                    @endforeach
                    <span class="ms-1 text-[10px] text-neutral-500 dark:text-neutral-400">{{ __('welcome.preview.more') }}</span>
                </div>
            </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Views showcase --}}
<section class="max-w-3xl mx-auto px-4 pb-16 flex flex-col gap-4">
    @php
        $weekDayLetters = ['M', 'T', 'W', 'T', 'F', 'S', 'S'];
        $weekHabitNames = (array) __('welcome.preview.habits');
        $weekGrid = [
            // Mon,  Tue,  Wed,  Thu,  Fri,  Sat,  Sun
            [true,  true,  true,  true,  true,  true,  false], // meditation — every day
            [true,  true,  true,  true,  true,  null,  null ], // read — Mon–Fri
            [true,  null,  true,  null,  false, true,  null ], // run — Mon/Wed/Fri/Sat
            [false, null,  true,  null,  true,  null,  null ], // workout — Mon/Wed/Fri
            [true,  true,  true,  false, true,  true,  true ], // no phone — every day
            [null,  true,  null,  true,  null,  true,  true ], // journal — Tue/Thu/Sat/Sun
        ];
        $yearLived = [4,3,4,4,3,3,4,4,3,4, 4,4,3,4,4,3,3,4,4,4, 3,4,4,3,4,3,3,4,3,4];
        $lifeCurrentAge = 28;
        $lifePattern    = [4,3,4,4,3,4,3,4,4,3, 4,3,3,4,4,3,4,4,3,3, 4,4,3,4,3,4,4,3];
        $shades = [
            'bg-neutral-200 dark:bg-neutral-700',
            'bg-green-200 dark:bg-green-900',
            'bg-green-300 dark:bg-green-800',
            'bg-green-400 dark:bg-green-700',
            'bg-green-500 dark:bg-green-600',
        ];
    @endphp

    {{-- Week view --}}
    <div class="reveal bg-neutral-100 dark:bg-neutral-800 rounded-2xl p-4 sm:p-6">
        <p class="text-xs font-medium uppercase tracking-wider text-neutral-400 dark:text-neutral-500 mb-3">{{ __('welcome.views.week') }}</p>
        <div class="bg-white dark:bg-neutral-900 rounded-xl px-4 py-4">
            <div class="overflow-x-auto">
                <div style="min-width: 260px">
                    <div class="grid gap-1 mb-2" style="grid-template-columns: 1fr repeat(7, 1.75rem)">
                        <span></span>
                        @foreach ($weekDayLetters as $letter)
                            <span class="flex items-center justify-center text-[10px] font-medium text-neutral-400 dark:text-neutral-500 select-none">{{ $letter }}</span>
                        @endforeach
                    </div>
                    @foreach ($weekHabitNames as $hi => $habitName)
                        <div class="grid gap-1 mb-1.5" style="grid-template-columns: 1fr repeat(7, 1.75rem)">
                            <span class="text-xs text-neutral-600 dark:text-neutral-400 truncate leading-[1.75rem] pr-2">{{ $habitName }}</span>
                            @foreach ($weekGrid[$hi] ?? [] as $cell)
                                @if ($cell === null)
                                    <span class="aspect-square flex items-center justify-center text-[11px] leading-none text-neutral-400 dark:text-neutral-600 select-none">–</span>
                                @else
                                    <span class="aspect-square rounded-sm {{ $cell ? 'bg-green-500 dark:bg-green-600' : 'bg-neutral-200 dark:bg-neutral-700' }}"></span>
                                @endif
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
            <p class="mt-3 text-[10px] text-center text-neutral-400 dark:text-neutral-500">{{ __('welcome.views.week_desc') }}</p>
        </div>
    </div>

    {{-- Year view: 52 weeks in 13 × 4 --}}
    <div class="reveal bg-neutral-100 dark:bg-neutral-800 rounded-2xl p-4 sm:p-6">
        <p class="text-xs font-medium uppercase tracking-wider text-neutral-400 dark:text-neutral-500 mb-3">{{ __('welcome.views.year') }}</p>
        <div class="bg-white dark:bg-neutral-900 rounded-xl px-4 py-4">
            <div class="grid gap-1" style="grid-template-columns: repeat(13, minmax(0, 1fr))">
                @for ($w = 0; $w < 52; $w++)
                    <span class="aspect-square rounded-sm transition-all duration-150 hover:scale-125 {{ $w < 30 ? $shades[$yearLived[$w]] : 'border border-dashed border-neutral-300 dark:border-neutral-600' }}"
                          aria-label="{{ __('welcome.views.week_n', ['n' => $w + 1]) }}"></span>
                @endfor
            </div>
            <p class="mt-3 text-[10px] text-center text-neutral-400 dark:text-neutral-500">{{ __('welcome.views.year_desc') }}</p>
        </div>
    </div>

    {{-- Life view: 80 years in 10 × 8 --}}
    <div class="reveal bg-neutral-100 dark:bg-neutral-800 rounded-2xl p-4 sm:p-6">
        <p class="text-xs font-medium uppercase tracking-wider text-neutral-400 dark:text-neutral-500 mb-3">{{ __('welcome.views.life') }}</p>
        <div class="bg-white dark:bg-neutral-900 rounded-xl px-4 py-4">
            <div class="grid grid-cols-10 gap-1.5">
                @for ($age = 0; $age < 80; $age++)
                    @if ($age < $lifeCurrentAge)
                        <span class="aspect-square rounded-sm relative {{ $shades[$lifePattern[$age % count($lifePattern)]] }} transition-all duration-150 hover:scale-125"
                              aria-label="{{ __('welcome.views.age', ['n' => $age]) }}">
                            @if ($age % 10 === 0)
                                <span class="absolute inset-0 flex items-center justify-center text-[7px] font-semibold text-neutral-600 dark:text-neutral-300 select-none">{{ $age }}</span>
                            @endif
                        </span>
                    @elseif ($age === $lifeCurrentAge)
                        <span class="aspect-square rounded-sm relative ring-2 ring-green-500 dark:ring-green-400 bg-green-300 dark:bg-green-800 transition-all duration-150 hover:scale-125"
                              aria-label="{{ __('welcome.views.age', ['n' => $age]) }}"></span>
                    @else
                        <span class="aspect-square rounded-sm relative border border-dashed border-neutral-300 dark:border-neutral-600 transition-all duration-150 hover:scale-125"
                              aria-label="{{ __('welcome.views.age', ['n' => $age]) }}">
                            @if ($age % 10 === 0)
                                <span class="absolute inset-0 flex items-center justify-center text-[7px] font-medium text-neutral-400 dark:text-neutral-500 select-none">{{ $age }}</span>
                            @endif
                        </span>
                    @endif
                @endfor
            </div>
            <div class="mt-4 pt-4 border-t border-neutral-200 dark:border-neutral-700 grid grid-cols-3 gap-2 text-center text-[10px] text-neutral-400 dark:text-neutral-500">
                <span>{{ __('welcome.views.age', ['n' => $lifeCurrentAge]) }}</span>
                <span>{{ __('welcome.views.weeks_lived', ['n' => 1461]) }}</span>
                <span>{{ __('welcome.views.years_left', ['n' => 52]) }}</span>
            </div>
            <p class="mt-3 text-[10px] text-center text-neutral-400 dark:text-neutral-500">{{ __('welcome.views.life_desc') }}</p>
        </div>
    </div>
</section>

{{-- Features --}}
<section id="features" class="scroll-mt-20 max-w-3xl mx-auto px-4 py-16">
    <div class="mb-10 reveal">
        <p class="text-xs font-medium uppercase tracking-wider text-neutral-400 dark:text-neutral-500 mb-2">{{ __('welcome.features.eyebrow') }}</p>
        <h2 class="text-2xl sm:text-3xl font-bold tracking-tight">{{ __('welcome.features.title') }}</h2>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        @foreach ([
            ['key' => 'schedules', 'icon' => 'calendar'],
            ['key' => 'streaks',   'icon' => 'grid'],
            ['key' => 'reminders', 'icon' => 'bell'],
            ['key' => 'notes',     'icon' => 'note'],
            ['key' => 'ai',        'icon' => 'sparkles', 'premium' => true],
            ['key' => 'export',    'icon' => 'download', 'premium' => true],
        ] as $feature)
            <div class="reveal group bg-neutral-100 dark:bg-neutral-800 rounded-xl px-4 py-4 transition-all duration-150 hover:bg-neutral-200/60 dark:hover:bg-neutral-700/60 hover:-translate-y-0.5"
                 data-reveal-delay="{{ $loop->index * 60 }}">
                <div class="flex items-start justify-between gap-3 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-white dark:bg-neutral-900 flex items-center justify-center text-green-600 dark:text-green-500 flex-shrink-0 transition-transform duration-150 group-hover:scale-110">
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
                    </div>
                    @if (! empty($feature['premium']))
                        <span class="text-[10px] font-medium uppercase tracking-wider px-2 py-0.5 rounded-full bg-green-200 dark:bg-green-900 text-green-800 dark:text-green-200">
                            {{ __('welcome.features.premium') }}
                        </span>
                    @endif
                </div>
                <h3 class="text-sm font-semibold mb-1">{{ __('welcome.features.items.'.$feature['key'].'.title') }}</h3>
                <p class="text-sm leading-5 text-neutral-500 dark:text-neutral-400">{{ __('welcome.features.items.'.$feature['key'].'.body') }}</p>
            </div>
        @endforeach
    </div>
</section>

{{-- How it works --}}
<section id="how" class="scroll-mt-20 max-w-3xl mx-auto px-4 py-16">
    <div class="mb-10 reveal">
        <p class="text-xs font-medium uppercase tracking-wider text-neutral-400 dark:text-neutral-500 mb-2">{{ __('welcome.how.eyebrow') }}</p>
        <h2 class="text-2xl sm:text-3xl font-bold tracking-tight">{{ __('welcome.how.title') }}</h2>
    </div>

    @php $steps = (array) __('welcome.how.steps'); @endphp
    <ol class="flex flex-col gap-3">
        @foreach ($steps as $i => $step)
            <li class="reveal bg-neutral-100 dark:bg-neutral-800 rounded-xl px-4 py-4 flex items-start gap-4"
                data-reveal-delay="{{ $loop->index * 80 }}">
                <span class="flex-shrink-0 w-8 h-8 rounded-lg bg-white dark:bg-neutral-900 flex items-center justify-center text-sm font-semibold text-neutral-500 dark:text-neutral-400">
                    {{ $i + 1 }}
                </span>
                <div class="flex-1">
                    <h3 class="text-sm font-semibold mb-1">{{ $step['title'] }}</h3>
                    <p class="text-sm leading-5 text-neutral-500 dark:text-neutral-400">{{ $step['body'] }}</p>
                </div>
            </li>
        @endforeach
    </ol>
</section>

{{-- Final CTA --}}
<section class="max-w-3xl mx-auto px-4 py-16">
    <div class="reveal bg-neutral-100 dark:bg-neutral-800 rounded-2xl px-6 py-10 sm:px-10 sm:py-14 text-center">
        <h2 class="text-2xl sm:text-3xl font-bold tracking-tight mb-3">{{ __('welcome.cta.title') }}</h2>
        <p class="text-sm text-neutral-500 dark:text-neutral-400 mb-6 max-w-md mx-auto leading-relaxed">
            {{ __('welcome.cta.subtitle') }}
        </p>
        <a href="{{ $primaryUrl }}" target="_blank" rel="noopener noreferrer"
           class="inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-medium rounded-full bg-neutral-900 dark:bg-white text-white dark:text-neutral-900 hover:bg-neutral-700 dark:hover:bg-neutral-200 transition-all duration-150">
            {{ $primaryLabel }}
            <svg class="w-3.5 h-3.5 rtl:scale-x-[-1]" viewBox="0 0 10 11" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M7.70833 6.95834V2.79167H3.54167M2.5 8L7.5 3.00001" stroke="currentColor" stroke-linecap="square"/>
            </svg>
        </a>
    </div>
</section>

{{-- Footer --}}
<footer class="border-t border-neutral-200 dark:border-neutral-800 mt-8">
    <div class="max-w-5xl mx-auto px-4 py-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-neutral-400 dark:text-neutral-500">
        <span>{{ __('welcome.footer.tagline', ['year' => date('Y'), 'app' => config('app.name', 'OpenHabit')]) }}</span>
        <div class="flex items-center gap-4">
            @if ($telegramUrl)
                <a href="{{ $telegramUrl }}" target="_blank" rel="noopener noreferrer" class="hover:text-neutral-700 dark:hover:text-neutral-300 transition-all duration-150">{{ __('welcome.footer.telegram') }}</a>
            @endif
            <a href="{{ $appUrl }}" target="_blank" rel="noopener noreferrer" class="hover:text-neutral-700 dark:hover:text-neutral-300 transition-all duration-150">{{ __('welcome.footer.app') }}</a>
        </div>
    </div>
</footer>

@if ($trackingScripts)
    {!! $trackingScripts !!}
@endif

{{-- Theme toggle + language menu interactions --}}
<script>
    (function () {
        // Theme toggle
        var btn = document.querySelector('[data-theme-toggle]');
        if (btn) {
            var labelLight = @json(__('welcome.nav.theme_light'));
            var labelDark = @json(__('welcome.nav.theme_dark'));
            var sync = function () {
                var isDark = document.documentElement.classList.contains('dark');
                btn.setAttribute('aria-label', isDark ? labelLight : labelDark);
                document.documentElement.style.backgroundColor = isDark ? '#171717' : '#ffffff';
            };
            sync();
            btn.addEventListener('click', function () {
                var isDark = document.documentElement.classList.toggle('dark');
                try { localStorage.setItem('theme', isDark ? 'dark' : 'light'); } catch (e) {}
                sync();
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

        // Scroll-reveal via IntersectionObserver
        // Elements with .reveal start hidden (via CSS) and transition in when
        // they enter the viewport. The transitionDelay is cleaned up afterwards
        // so hover effects remain instant.
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
            // Fallback: reveal immediately (no IntersectionObserver support)
            reveals.forEach(function (el) { el.classList.add('revealed'); });
        }
    })();
</script>

</body>
</html>
