<?php

declare(strict_types=1);

use App\Models\Setting;
use App\Services\LocaleService;
use Illuminate\Support\Facades\Lang;

it('renders the distilled welcome page path', function (): void {
    Setting::setValue('telegram_bot_username', 'openhabit_test_bot');

    $response = $this->get('/')
        ->assertOk()
        ->assertSee('Open Telegram bot')
        ->assertDontSee('Sign in on web')
        ->assertSee('href="#problem-heading"', false)
        ->assertSee('href="#open-source"', false)
        ->assertDontSee('href="#features"', false)
        ->assertDontSee('href="#web-sign-in"', false)
        ->assertSee('whitespace-nowrap', false)
        ->assertSee('flex w-full flex-col items-center gap-0 mt-1 text-center', false)
        ->assertSee('mt-2 flex flex-col items-center justify-center sm:flex-row', false)
        ->assertSee('telegram-login-collapse flex items-center min-h-10 max-w-[238px] max-h-10 mt-3 sm:mt-0 sm:ms-3', false)
        ->assertSee('telegram-login-collapse mt-2 text-[13px] text-neutral-500 dark:text-neutral-400 max-w-[48ch] max-h-12 leading-relaxed text-pretty', false)
        ->assertSee('mt-2 flex flex-wrap items-center justify-center gap-x-3 gap-y-1 text-sm font-medium leading-relaxed', false)
        ->assertDontSee('data-web-sign-in-toggle', false)
        ->assertDontSee('aria-controls="web-sign-in"', false)
        ->assertDontSee('id="web-sign-in"', false)
        ->assertDontSee('data-web-sign-in-panel', false)
        ->assertDontSee('class="web-sign-in-panel mt-2 scroll-mt-24"', false)
        ->assertDontSee('html.js .web-sign-in-panel', false)
        ->assertDontSee('openWebSignIn', false)
        ->assertDontSee("webSignInPanel.setAttribute('aria-hidden', 'false')", false)
        ->assertSee('Bot or browser. Same Telegram account, same habits.')
        ->assertDontSee('Add your first habit')
        ->assertDontSee('Premium trial: 14 days, no card needed.')
        ->assertDontSee('Prefer the browser? Sign in here with Telegram. It uses the same account and habits as the bot.')
        ->assertDontSee('telegram-widget.js')
        ->assertSee('https://oauth.telegram.org/embed/openhabit_test_bot', false)
        ->assertSee('lang=en', false)
        ->assertSee('auth\/telegram\/callback', false)
        ->assertSee('telegram-login-collapse', false)
        ->assertSee('is-hidden', false)
        ->assertSee('data-telegram-login-container', false)
        ->assertSee('data-telegram-login-note-target="hero-entry-note"', false)
        ->assertSee('telegramLoginUnavailableTimer', false)
        ->assertSee('hideTelegramLoginElement', false)
        ->assertSee('getBoundingClientRect()', false)
        ->assertSee('window.requestAnimationFrame', false)
        ->assertSee('width 220ms cubic-bezier', false)
        ->assertSee('height 220ms cubic-bezier', false)
        ->assertSee('padding 220ms cubic-bezier', false)
        ->assertSee('hideTelegramLoginContainer(frame)', false)
        ->assertSee('hideTelegramLoginNote(frame)', false)
        ->assertDontSee('Open the Telegram bot. Same Telegram account, same habits.')
        ->assertDontSee('data-telegram-login-unavailable-note', false)
        ->assertDontSee('entry_note_telegram_bot_only', false)
        ->assertDontSee('Browser sign-in needs JavaScript. Without it, use the Telegram bot button.')
        ->assertDontSee('Telegram sign-in did not load.')
        ->assertDontSee('Open the Telegram bot instead.')
        ->assertDontSee('id="telegram-login-hero-fallback"', false)
        ->assertDontSee('id="telegram-login-cta-fallback"', false)
        ->assertDontSee('data-telegram-login-fallback', false)
        ->assertSee('markTelegramLoginReady(frame)', false)
        ->assertSee('html:not(.js) .faq-item[open] .faq-answer', false)
        ->assertSee("document.documentElement.classList.add('js')", false)
        ->assertSee('details._faqCloseTimer', false)
        ->assertSee('try { theme = localStorage.getItem', false)
        ->assertSee('try { prefersDark = window.matchMedia', false)
        ->assertSee('font-sans', false)
        ->assertSee('CSV export for your records')
        ->assertSee('Premium includes CSV export from the hosted app, so you can download your habits, notes, and settings when you need them.')
        ->assertSee('Core tracking stays free; full control is always available through self-hosting.')
        ->assertSee('Clear privacy boundary')
        ->assertSee('The app code ships without analytics SDKs. A host can add scripts to public pages, and those scripts appear in the page source.')
        ->assertSee('Core tracking is free, with no time limit or habit cap. Premium adds gentle reflection, multiple Telegram reminders per habit, and CSV export from the hosted app. New accounts get a 14-day free trial automatically.')
        ->assertSee('Your habits and notes are stored in the OpenHabit database for your account. The app code does not include third-party analytics or tracking SDKs. Hosts can add scripts to public pages, so self-host if you want full control over every request.')
        ->assertSee('href="#privacy"', false)
        ->assertSee('id="privacy"', false)
        ->assertSee('openFaqFromHash', false)
        ->assertSee("window.addEventListener('hashchange', openFaqFromHash)", false)
        ->assertSee('Three small steps to keep going')
        ->assertSee('Choose Telegram or browser sign-in')
        ->assertSee('Open the bot or use the browser widget. Both paths use the same Telegram account, so there is no email or password to remember.')
        ->assertSee('Keep the habits that fit')
        ->assertSee('Your habits stay with the same account either way. Keep what fits, pause the rest, and adjust reminders later.')
        ->assertDontSee('Add a habit or two')
        ->assertSee('Come back tomorrow')
        ->assertSee('Open source')
        ->assertSee('FAQ')
        ->assertSee('Do I have to start in the Telegram bot?')
        ->assertSee('No. You can open the bot or sign in with the browser widget. Both paths use your Telegram account, the same habits, and let you continue on any device.')
        ->assertSee('font-kerning: normal', false)
        ->assertSee('text-balance', false)
        ->assertSee('text-pretty', false)
        ->assertSee('tabular-nums', false)
        ->assertDontSee('pulse-soft')
        ->assertDontSee('backdrop-blur-[2px]')
        ->assertDontSee('shadow-[0_1px_0')
        ->assertSee('A calmer way to look back at today')
        ->assertSee('20 May 2026')
        ->assertSee('Missing one day does not erase the week')
        ->assertSee('Progress has quiet seasons too')
        ->assertSee('Built for years, not perfect weeks')
        ->assertSee('The Anti-Streak Manifesto')
        ->assertSee('Why we do not use streaks')
        ->assertDontSee('The loop')
        ->assertDontSee('Made for coming back')
        ->assertDontSee('How OpenHabit is different')
        ->assertDontSee('Built by one developer')
        ->assertDontSee('Traditional habit apps make perfection the goal.');

    expect((string) $response->getContent())->toMatch('/href="#how".*href="#open-source".*href="#faq"/s');

    expect(mb_substr_count((string) $response->getContent(), 'https://oauth.telegram.org/embed/openhabit_test_bot'))->toBe(2)
        ->and(mb_substr_count((string) $response->getContent(), '<div data-telegram-login-container'))->toBe(2)
        ->and(mb_substr_count((string) $response->getContent(), ' data-telegram-login-unavailable-note='))->toBe(0);
});

it('does not render telegram login fallback copy in any locale', function (): void {
    Setting::setValue('telegram_bot_username', 'openhabit_test_bot');

    foreach (array_keys(LocaleService::all()) as $locale) {
        $fallback = Lang::get('welcome.hero.login_fallback', [], $locale);

        $this->get('/?lang='.$locale)
            ->assertOk()
            ->assertDontSee((string) $fallback['line_1'])
            ->assertDontSee((string) $fallback['line_2']);
    }
});

it('explains the web fallback when telegram is not configured', function (): void {
    Setting::setValue('telegram_bot_username', null);

    $this->get('/')
        ->assertOk()
        ->assertSee('Open web tracker')
        ->assertSee('Telegram is not configured here, so this opens the web tracker.')
        ->assertDontSee('Sign in on web')
        ->assertDontSee('href="#web-sign-in"', false)
        ->assertDontSee('aria-controls="web-sign-in"', false)
        ->assertDontSee('id="web-sign-in" data-web-sign-in-panel', false)
        ->assertDontSee('telegram-widget.js')
        ->assertDontSee('You are signed in. This opens your web tracker.');
});

it('keeps trust copy accurate when landing scripts are configured', function (): void {
    Setting::setValue('telegram_bot_username', 'openhabit_test_bot');
    Setting::setValue('tracking_scripts', '<script>window.openHabitTracking = true;</script>');

    $this->get('/')
        ->assertOk()
        ->assertSee('window.openHabitTracking = true;', false)
        ->assertSee('The app code ships without analytics SDKs. A host can add scripts to public pages, and those scripts appear in the page source.')
        ->assertSee('Hosts can add scripts to public pages, so self-host if you want full control over every request.')
        ->assertDontSee('No third-party trackers and no analytics SDKs.')
        ->assertDontSee('The app itself has no telemetry and no lock-in.');
});
