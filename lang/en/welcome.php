<?php

declare(strict_types=1);

return [
    'a11y' => [
        'skip' => 'Skip to content',
        'primary_nav' => 'Primary',
        'preview' => 'App preview',
        'lenses' => 'Visualizations',
    ],
    'meta' => [
        'title' => 'OpenHabit · The anti-streak habit tracker',
        'description' => 'A gentler habit tracker for people who miss days. No streak anxiety, no shame, just an easier way to come back.',
        'og_description' => 'A gentler habit tracker for people who miss days.',
    ],
    'nav' => [
        'why' => 'Why',
        'features' => 'Features',
        'how' => 'How it works',
        'open_source' => 'Open source',
        'privacy' => 'Privacy',
        'faq' => 'FAQ',
        'menu' => 'Menu',
        'cta_telegram' => 'Open Telegram bot',
        'cta_web' => 'Sign in on web',
        'cta_app' => 'Open web tracker',
        'theme_light' => 'Switch to light theme',
        'theme_dark' => 'Switch to dark theme',
        'language' => 'Language',
    ],
    'hero' => [
        'title_pre' => 'A habit tracker that',
        'title_accent' => 'does not push',
        'subtitle' => 'OpenHabit helps you notice progress, miss a day, and return without starting over.',
        'login_telegram' => 'Sign in with Telegram',
        'web_login_note' => 'Prefer the browser? Sign in here with Telegram. It uses the same account and habits as the bot.',
        'web_login_js_required' => 'Browser sign-in needs JavaScript. Without it, use the Telegram bot button.',
        'login_fallback' => [
            'line_1' => 'Telegram sign-in did not load.',
            'line_2' => 'Open the Telegram bot instead.',
        ],
        'entry_note_telegram' => 'Bot or browser. Same Telegram account, same habits.',
        'entry_note_app' => 'You are signed in. This opens your web tracker.',
        'entry_note_no_bot' => 'Telegram is not configured here, so this opens the web tracker.',
    ],
    'preview' => [
        'today' => 'Today',
        'date' => 'Apr 28',
        'habits' => [
            'Morning meditation',
            'Read 30 min',
            'Daily run',
            'Workout',
            'No phone before bed',
            'Evening journal',
        ],
        'story_lines' => [
            'Not a streak.',
            'Not a calendar.',
            'The story of how you returned.',
        ],
        'less' => 'Less',
        'more' => 'More',
    ],
    'features' => [
        'title' => 'Built to help you keep going',
        'premium' => 'Premium',
        'items' => [
            'schedules' => [
                'title' => 'No more full resets',
                'body' => 'A missed day stays a missed day, not the end of the whole habit. Come back tomorrow and continue from where you are.',
            ],
            'streaks' => [
                'title' => 'See progress, not perfection',
                'body' => 'The activity grid shows the whole picture: strong days, quiet days, and the small returns that keep the habit alive.',
            ],
            'reminders' => [
                'title' => 'Gentle reminders',
                'body' => 'Small nudges in Telegram, only when you ask for them. A reminder should invite you back, not make you feel late.',
            ],
            'notes' => [
                'title' => 'Room for real life',
                'body' => 'Use daily notes to write down what actually happened. The messy context is part of building something that lasts.',
            ],
            'ai' => [
                'title' => 'Gentle reflection',
                'body' => 'A quiet summary that notices patterns you might miss and helps you choose one small next step.',
            ],
            'export' => [
                'title' => 'CSV export for your records',
                'body' => 'Premium includes CSV export from the hosted app, so you can download your habits, notes, and settings when you need them.',
            ],
        ],
    ],
    'problem' => [
        'title' => 'A bad day should not become a full reset',
        'body' => [
            'You start motivated. You miss one day. Guilt builds. Then the app disappears until the next fresh Monday.',
        ],
        'cycle' => [
            ['day' => 'Monday', 'mood' => '🔥 This time will stick'],
            ['day' => 'Wednesday', 'mood' => '🙂 Still going'],
            ['day' => 'Friday', 'mood' => '😕 Missed a day'],
            ['day' => 'Saturday', 'mood' => '😞 It already feels ruined'],
            ['day' => 'Next month', 'mood' => 'New tracker'],
        ],
        'closing' => 'OpenHabit is built to make the return easier.',
    ],
    'digest_examples' => [
        'eyebrow' => 'Reflection',
        'title' => 'A calmer way to look back at today',
        'subtitle' => 'No scores. No scolding. Just a useful look at what helped and what to try next.',
        'items' => [
            [
                'date_machine' => '2026-05-20',
                'date' => '20 May 2026',
                'paragraph_1' => 'Today counted. You returned to the habits that usually anchor you: early rise, workout, meditation, deep work. The useful pattern is how quickly you came back after a rough day.',
                'paragraph_2' => 'Morning pages keep slipping when sleep runs late. Tonight, make the next version smaller: one sentence about something good that happened today.',
                'habits' => [
                    ['name' => 'Wake up by 8am', 'done' => true],
                    ['name' => 'Workout, 60 min', 'done' => true],
                    ['name' => 'Meditation 10–20 min', 'done' => true],
                    ['name' => 'Morning pages', 'done' => false],
                    ['name' => 'Deep work 2–4h', 'done' => true],
                    ['name' => 'No social media until 18:00', 'done' => true],
                ],
            ],
            [
                'date_machine' => '2026-05-19',
                'date' => '19 May 2026',
                'paragraph_1' => 'Early rise and workout happened. Deep work did not. That does not erase the morning; it just shows where the day got heavy.',
                'paragraph_2' => 'For digital limits, try adding a small pause: when the urge to scroll hits, stand up first. That tiny gap can make coming back easier.',
                'habits' => [
                    ['name' => 'Wake up by 8am', 'done' => true],
                    ['name' => 'Workout, 60 min', 'done' => true],
                    ['name' => 'Meditation 10–20 min', 'done' => true],
                    ['name' => 'Morning pages', 'done' => false],
                    ['name' => 'Deep work 2–4h', 'done' => false],
                    ['name' => 'No social media until 18:00', 'done' => false],
                ],
            ],
        ],
    ],
    'how' => [
        'title' => 'Three small steps to keep going',
        'steps' => [
            [
                'title' => 'Choose Telegram or browser sign-in',
                'body' => 'Open the bot or use the browser widget. Both paths use the same Telegram account, so there is no email or password to remember.',
            ],
            [
                'title' => 'Keep the habits that fit',
                'body' => 'Your habits stay with the same account either way. Keep what fits, pause the rest, and adjust reminders later.',
            ],
            [
                'title' => 'Come back tomorrow',
                'body' => 'One tap to mark the day. If yesterday was missed, nothing is erased. Today still counts.',
            ],
        ],
    ],
    'philosophy' => [
        'title' => 'The Anti-Streak Manifesto',
        'items' => [
            'We do not believe habits should be built on guilt.',
            'We believe a person matters more than a streak.',
            'We believe one bad day does not cancel a good month.',
            'We believe returning matters more than being flawless.',
            'That is why we made OpenHabit.',
        ],
    ],
    'anti_streak' => [
        'title' => 'Why we do not use streaks',
        'body' => [
            'Most habit apps put streaks at the center. They can help, but only while everything goes perfectly.',
            'One bad day becomes guilt. Guilt becomes avoidance. Avoidance becomes another restart.',
            'We left streaks out because OpenHabit is about returning, not protecting a number.',
        ],
    ],
    'cta' => [
        'title' => 'Build habits you can come back to',
        'subtitle' => 'Use a tracker that keeps the door open after a missed day.',
    ],
    'open' => [
        'title' => 'Open source',
        'body' => 'Every line of OpenHabit is on GitHub under the MIT license. You can read it, fork it, or run it on your own server. Core tracking stays free; full control is always available through self-hosting.',
        'cta' => 'View on GitHub',
        'license_link' => 'Read the license',
        'facts' => [
            'license' => [
                'title' => 'Permissive license',
                'body' => 'MIT licensed. Fork, modify, and ship whatever you want.',
            ],
            'selfhost' => [
                'title' => 'Self-hostable',
                'body' => 'One Docker command and you are running it yourself.',
            ],
            'audit' => [
                'title' => 'Clear privacy boundary',
                'body' => 'The app code ships without analytics SDKs. A host can add scripts to public pages, and those scripts appear in the page source.',
            ],
        ],
    ],
    'author' => [
        'title' => 'Hi! I made OpenHabit after trying a bunch of different habit trackers',
        'body' => [
            "It always ended the same way. I'd miss a day, lose my streak, and just stop opening the app.",
            "At some point I realized the problem wasn't me. I just didn't want to use a tracker that punishes you for one missed day.",
            "So I made OpenHabit — an app it's fine to come back to after a day, a week, or even a month, and just keep going.",
        ],
        'name' => 'Nik Fedorov',
        'role' => 'Independent developer of OpenHabit',
        'photo_alt' => 'Nik Fedorov',
        'github' => 'GitHub',
        'twitter' => 'Twitter',
    ],
    'views' => [
        'week' => 'Week',
        'year' => 'Year',
        'life' => 'Life',
        'week_desc' => 'Missing one day does not erase the week',
        'year_desc' => 'Progress has quiet seasons too',
        'life_desc' => 'Built for years, not perfect weeks',
        'week_n' => 'Week :n',
        'age' => 'Age :n',

    ],
    'footer' => [
        'tagline' => '© :year :app. Quiet by design.',
        'telegram' => 'Telegram',
        'news' => 'News channel',
        'app' => 'Open the app',
        'why' => 'Why OpenHabit exists',
        'roadmap' => 'Roadmap',
        'changelog' => 'Changelog',
        'github' => 'GitHub',
        'privacy' => 'Privacy',
    ],
    'faq' => [
        'title' => 'FAQ',
        'items' => [
            [
                'q' => 'How much does OpenHabit cost?',
                'a' => 'Core tracking is free, with no time limit or habit cap. Premium adds gentle reflection, multiple Telegram reminders per habit, and CSV export from the hosted app. New accounts get a :days-day free trial automatically.',
            ],
            [
                'q' => 'Do I have to start in the Telegram bot?',
                'a' => 'No. You can open the bot or sign in with the browser widget. Both paths use your Telegram account, the same habits, and let you continue on any device.',
            ],
            [
                'q' => 'Can I use OpenHabit in a browser?',
                'a' => 'Yes. Use the Telegram sign-in widget on the web and the full tracker opens in your browser. Same habits, same history, just a bigger screen.',
            ],
            [
                'q' => 'What is the AI digest?',
                'a' => 'A short daily reflection that notices patterns in your habits and notes. It is designed to help you keep going, not grade your day.',
            ],
            [
                'q' => 'Is my data private?',
                'a' => 'Your habits and notes are stored in the OpenHabit database for your account. The app code does not include third-party analytics or tracking SDKs. Hosts can add scripts to public pages, so self-host if you want full control over every request.',
            ],
            [
                'q' => 'Can I self-host OpenHabit?',
                'a' => 'Yes. The full source code is on GitHub under the MIT licence. One Docker command and the app runs on your own server. Your data never leaves your machine.',
            ],
        ],
    ],
];
