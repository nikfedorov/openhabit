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
        'features' => 'Manifesto',
        'compare' => 'Compare',
        'how' => 'How it works',
        'faq' => 'FAQ',
        'news' => 'News',
        'menu' => 'Menu',
        'cta_telegram' => 'Start gently',
        'cta_app' => 'Start gently',
        'theme_light' => 'Switch to light theme',
        'theme_dark' => 'Switch to dark theme',
        'language' => 'Language',
    ],
    'hero' => [
        'badge' => 'Made for coming back',
        'title_pre' => 'A habit tracker that',
        'title_accent' => 'does not push.',
        'subtitle' => 'OpenHabit helps you notice progress, miss a day, and return without starting over.',
        'login_telegram' => 'Log in with Telegram',
        'trial_note' => 'Start gently. :days days of Premium, no card needed.',
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
        'eyebrow' => 'How OpenHabit is different',
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
                'title' => 'Your history stays yours',
                'body' => 'Export your data anytime. Long-term consistency should not depend on being stuck in one app.',
            ],
        ],
    ],
    'problem' => [
        'eyebrow' => 'The loop',
        'title' => 'A bad day should not become a full reset.',
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
        'cta' => 'Start gently',
    ],
    'proof' => [
        'eyebrow' => 'Proof of intent',
        'items' => [
            ['value' => '1', 'label' => 'developer building in public'],
            ['value' => 'MIT', 'label' => 'licensed and self-hostable'],
            ['value' => '0', 'label' => 'analytics SDKs in the app'],
        ],
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
        'eyebrow' => 'How it works',
        'title' => 'Three small steps to keep going.',
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
                'title' => 'Come back tomorrow',
                'body' => 'One tap to mark the day. If yesterday was missed, nothing is erased. Today still counts.',
            ],
        ],
    ],
    'philosophy' => [
        'eyebrow' => 'Manifesto',
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
        'eyebrow' => 'Why no streaks',
        'title' => 'Why we do not use streaks',
        'body' => [
            'Most habit apps put streaks at the center. They can help, but only while everything goes perfectly.',
            'One bad day becomes guilt. Guilt becomes avoidance. Avoidance becomes another restart.',
            'We left streaks out because OpenHabit is about returning, not protecting a number.',
        ],
    ],
    'cta' => [
        'title' => 'Build habits you can come back to.',
        'subtitle' => 'Use a tracker that keeps the door open after a missed day.',
        'trial_note' => ':days-day free trial on Premium, no card needed.',
    ],
    'compare' => [
        'eyebrow' => 'Comparison',
        'title' => 'Traditional habit apps make perfection the goal.',
        'subtitle' => 'OpenHabit makes it easier to come back. The difference is not more features, but a calmer relationship with progress.',
        'caption' => 'Comparison of traditional habit apps and OpenHabit',
        'criterion' => 'Behavior',
        'us_tag' => 'Our pick',
        'rows' => [
            'miss_day' => 'Miss a day',
            'feeling' => 'After a hard week',
            'restart' => 'What happens next',
            'goal' => 'What you chase',
        ],
        'cells' => [
            'traditional' => 'Traditional habit apps',
            'openhabit' => 'OpenHabit',
            'lose_streak' => 'Lose streak',
            'keep_going' => 'Keep going',
            'feel_guilty' => 'Feel guilty',
            'feel_safe' => 'Feel safe to continue',
            'start_over' => 'Start over',
            'come_back' => 'Come back tomorrow',
            'perfection' => 'Perfection',
            'steady_progress' => 'Steady progress',
        ],
        'a11y' => [
            'yes' => 'Supported',
            'no' => 'Not supported',
        ],
        'footnote' => 'A philosophy comparison, not a feature checklist. OpenHabit is built around long-term consistency.',
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
    'author' => [
        'eyebrow' => 'Built by one developer',
        'title' => 'Hi. I made OpenHabit because I kept quitting habit trackers too.',
        'body' => [
            'For several years I tried to use different habit trackers. Every time the same thing happened.',
            'One missed day. A lost streak. Guilt. A new Monday.',
            'So I built OpenHabit: a calmer place to mark progress and come back without shame.',
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
        'eyebrow' => 'FAQ',
        'title' => 'Questions worth asking',
        'items' => [
            [
                'q' => 'How much does OpenHabit cost?',
                'a' => 'The core features are completely free, with no time limit or habit cap. Premium adds gentle reflection, multiple Telegram reminders per habit, and CSV export. New accounts get a :days-day free trial automatically.',
            ],
            [
                'q' => 'Do I need a Telegram account?',
                'a' => 'Yes. OpenHabit is built as a Telegram bot and mini app, so a Telegram account is all you need. No separate sign-up, no password to forget.',
            ],
            [
                'q' => 'Can I use OpenHabit in a browser?',
                'a' => 'Yes. Log in once with your Telegram account on the web and the full app opens in your browser. Same habits, same history — just a bigger screen.',
            ],
            [
                'q' => 'What is the AI digest?',
                'a' => 'A short daily reflection that notices patterns in your habits and notes. It is designed to help you keep going, not grade your day.',
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
