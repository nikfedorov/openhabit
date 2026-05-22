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
        'title' => 'OpenHabit · Build habits that stick',
        'description' => 'A quiet habit tracker that lives inside Telegram. Track daily, weekly, and monthly habits, get gentle reminders, watch your streaks grow, and reflect with a daily AI digest.',
        'og_description' => 'A quiet habit tracker that lives inside Telegram. Mark your day, watch consistency compound.',
    ],
    'nav' => [
        'features' => 'Features',
        'compare' => 'Compare',
        'how' => 'How it works',
        'faq' => 'FAQ',
        'news' => 'News',
        'menu' => 'Menu',
        'cta_telegram' => 'Open in Telegram',
        'cta_app' => 'Open the app',
        'theme_light' => 'Switch to light theme',
        'theme_dark' => 'Switch to dark theme',
        'language' => 'Language',
    ],
    'hero' => [
        'badge' => 'Built for Telegram',
        'title_pre' => 'Build habits that',
        'title_accent' => 'stick.',
        'subtitle' => 'A calm, focused habit tracker that lives inside Telegram. No noisy notifications, no guilt about a broken streak. Just a quiet space to mark your day and watch the rest add up.',
        'login_telegram' => 'Log in with Telegram',
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
        'less' => 'Less',
        'more' => 'More',
    ],
    'features' => [
        'eyebrow' => 'Features',
        'title' => 'Only what you actually need',
        'premium' => 'Premium',
        'items' => [
            'schedules' => [
                'title' => 'Flexible schedules',
                'body' => 'Daily, weekly, monthly, or "the second Monday of the month." Pick the days, dates, or times that fit the habit, not the other way around.',
            ],
            'streaks' => [
                'title' => 'Streaks and a heatmap',
                'body' => 'See your consistency at a glance. The activity grid shows which days you marked and which you missed. No judgment, just the picture.',
            ],
            'reminders' => [
                'title' => 'Telegram reminders',
                'body' => 'Gentle nudges, sent right where you already chat. One per habit, or several if you want them. Nothing fires unless you ask it to.',
            ],
            'notes' => [
                'title' => 'Daily notes',
                'body' => 'A small, optional space for a sentence or two about how the day went. So your habits become more than checkmarks, they have a story.',
            ],
            'ai' => [
                'title' => 'AI digest',
                'body' => "A daily summary that picks up patterns you'd otherwise miss: what's working, what's slipping, and a short thought on why.",
            ],
            'export' => [
                'title' => 'Export anytime',
                'body' => 'Your data is yours. Download a clean CSV of every habit and check-in whenever you want, no questions asked.',
            ],
        ],
    ],
    'digest_examples' => [
        'eyebrow' => 'AI Digest',
        'title' => 'What a daily digest looks like',
        'subtitle' => 'Real examples of what users actually receive.',
        'items' => [
            [
                'date_machine' => '2026-05-20',
                'date' => '20 May 2026',
                'paragraph_1' => 'Today was real progress. You broke the social media relapse pattern and held the key habits: early rise, workout, meditation, deep work. The strong thing is how quickly you bounce back and learn from a rough day.',
                'paragraph_2' => 'Morning pages keep slipping when sleep runs late. That is the gap to close. Tonight, write one sentence about something good that happened today.',
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
                'paragraph_1' => 'Early rise and workout done, nutrition clean. The physical side held up. But a midday social media slip cascaded into missed deep work and a lost afternoon.',
                'paragraph_2' => 'Nutritional discipline holds even after a slip, which is a real asset. For digital limits, try adding friction: when the urge to scroll hits, do five squats first. That tiny pause is usually enough to reset.',
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
        'eyebrow' => 'How it works',
        'title' => 'Three small steps to get going.',
        'steps' => [
            [
                'title' => 'Open the bot in Telegram',
                'body' => 'Tap the button below. No signup, no email, no password to forget. If you have Telegram, you are already in.',
            ],
            [
                'title' => 'Add a habit or two',
                'body' => "Start small. Pick one thing you actually want to do most days, and add a reminder if it'll help.",
            ],
            [
                'title' => 'Check in, daily',
                'body' => 'One tap to mark the day. Watch your streak grow. Reflect when you feel like it, not because the app asked you to.',
            ],
        ],
    ],
    'cta' => [
        'title' => 'Start today. Future you will be grateful',
        'subtitle' => 'Free to use. Premium adds the AI digest, several reminders per habit, and CSV export.',
    ],
    'compare' => [
        'eyebrow' => 'Compared',
        'title' => 'Different where it counts',
        'subtitle' => 'How OpenHabit compares to the trackers people usually pick. The rows are not cherry-picked.',
        'caption' => 'Comparison of popular habit trackers',
        'criterion' => 'Criterion',
        'us_tag' => 'Our pick',
        'rows' => [
            'telegram' => 'Lives in Telegram',
            'open_source' => 'Open source',
            'free' => 'Free, no paywall',
            'free_habits' => 'Free habits limit',
            'ai' => 'AI daily digest',
            'export' => 'Plain-text export',
            'cross' => 'Works on every device',
        ],
        'cells' => [
            'paid' => 'Paid',
            'premium' => 'Premium',
            'freemium' => 'Freemium',
            'limited' => 'Limited',
            'ios_only' => 'iOS only',
        ],
        'a11y' => [
            'yes' => 'Supported',
            'no' => 'Not supported',
        ],
        'footnote' => 'Based on publicly available information. Product names are trademarks of their respective owners.',
    ],
    'open' => [
        'eyebrow' => 'Open source',
        'title' => 'Built in the open',
        'body' => 'Every line of OpenHabit is on GitHub under the MIT license. You can read it, fork it, or run it on your own server. There is no telemetry and no lock-in.',
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
                'title' => 'Nothing to hide',
                'body' => 'No third-party trackers and no analytics SDKs.',
            ],
        ],
    ],
    'views' => [
        'week' => 'Week',
        'year' => 'Year',
        'life' => 'Life',
        'week_desc' => 'Each row is one habit',
        'year_desc' => 'Each cell is a week of the year',
        'life_desc' => 'Each cell is a year',
        'week_n' => 'Week :n',
        'age' => 'Age :n',

    ],
    'footer' => [
        'tagline' => '© :year :app. Quiet by design.',
        'telegram' => 'Telegram',
        'news' => 'News channel',
        'app' => 'Open the app',
    ],
    'faq' => [
        'eyebrow' => 'FAQ',
        'title' => 'Questions worth asking',
        'items' => [
            [
                'q' => 'How much does OpenHabit cost?',
                'a' => 'The core features are completely free, with no time limit or habit cap. Premium adds an AI daily digest, multiple Telegram reminders per habit, and CSV export. You can start on the free plan and decide later.',
            ],
            [
                'q' => 'Do I need a Telegram account?',
                'a' => 'Yes. OpenHabit is built as a Telegram bot and mini app, so a Telegram account is all you need. No separate sign-up, no password to forget.',
            ],
            [
                'q' => 'What is the AI digest?',
                'a' => 'A short daily message that picks up patterns in your habits: what is trending up, what is slipping, and a brief thought on why. It looks at your recent check-ins and notes.',
            ],
            [
                'q' => 'Is my data private?',
                'a' => 'Your data is stored in our database with no third-party analytics or tracking SDKs. If you prefer complete control, you can self-host the entire app.',
            ],
            [
                'q' => 'Can I self-host OpenHabit?',
                'a' => 'Yes. The full source code is on GitHub under the MIT licence. One Docker command and the app runs on your own server. Your data never leaves your machine.',
            ],
        ],
    ],
];
