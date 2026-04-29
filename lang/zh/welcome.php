<?php

declare(strict_types=1);

return [
    'meta' => [
        'title' => 'OpenHabit · 让习惯真正留下来',
        'description' => '一个安静的习惯追踪器，住在你每天都会打开的 Telegram 里。记录每日、每周、每月的习惯，收到温柔的提醒，看着连续记录慢慢长出来，再用每天一份的 AI 摘要回顾自己。',
        'og_description' => '一个安静的习惯追踪器，住在 Telegram 里。打个卡，慢慢就有了。',
    ],
    'nav' => [
        'features' => '功能',
        'how' => '怎么用',
        'cta_telegram' => '在 Telegram 中打开',
        'cta_app' => '打开应用',
        'theme_light' => '切换到浅色主题',
        'theme_dark' => '切换到深色主题',
        'language' => '语言',
    ],
    'hero' => [
        'badge' => '为 Telegram 而生',
        'title_pre' => '让习惯真正',
        'title_accent' => '留下来。',
        'subtitle' => '一个安静、专注的习惯追踪器，住在你常用的 Telegram 里。没有屌耳的通知，也没有连续断了的负罪感。只是一个平静的小角落，打个卡，其他的慢慢来就好。',
        'login_telegram' => '使用 Telegram 登录',
    ],
    'preview' => [
        'today' => '今天',
        'date' => '4 月 28 日',
        'habits' => [
            '早晨冥想',
            '阅读 30 分钟',
            '晨跑',
            '锻炼',
            '睡前不碰手机',
            '晚间日记',
        ],
        'less' => '少',
        'more' => '多',
    ],
    'features' => [
        'eyebrow' => '功能',
        'title' => '需要的，刚刚好。',
        'premium' => '高级版',
        'items' => [
            'schedules' => [
                'title' => '灵活的日程',
                'body' => '每天、每周、每月，或者「每月第二个周一」。按天、按日期、按一天几次都行，让节奏迁就习惯，而不是反过来。',
            ],
            'streaks' => [
                'title' => '连续与热力图',
                'body' => '一眼就能看清自己的坚持。安静的活动网格显示你哪些日子来了，哪些没来。不评判，只是把画面摆在那里。',
            ],
            'reminders' => [
                'title' => 'Telegram 提醒',
                'body' => '温和的提示出现在你本来就会打开的聊天里。一个习惯一条提醒，或者多条都可以。不打扰，不焦虑，不会让手机白白震一下。',
            ],
            'notes' => [
                'title' => '每日笔记',
                'body' => '一个小小的、可有可无的角落，写一两句今天怎么样。让习惯不止是打勾，还有它的故事。',
            ],
            'ai' => [
                'title' => 'AI 摘要',
                'body' => '每天一份摘要，安静地把你可能错过的规律拎出来：哪些在起作用，哪些开始松动，又是为什么。',
            ],
            'export' => [
                'title' => '随时导出',
                'body' => '数据是你的。任何时候都可以下载一份干净的 CSV，每条习惯、每次打卡都在里面，不用解释什么。',
            ],
        ],
    ],
    'how' => [
        'eyebrow' => '怎么用',
        'title' => '三小步，没有阻力。',
        'steps' => [
            [
                'title' => '在 Telegram 中打开机器人',
                'body' => '点下面的按钮。不用账号、邮箱，也没什么密码要记。你已经在里面了。',
            ],
            [
                'title' => '加一两个习惯',
                'body' => '从小处开始。一件你确实想几乎每天做的事，需要的话，再顺手加个提醒。',
            ],
            [
                'title' => '每天打个卡',
                'body' => '一下点击就标记今天。连续记录会自己长出来。想回头看看的时候再看，不是因为应用催你。',
            ],
        ],
    ],
    'cta' => [
        'title' => '今天就开始。未来的你，会替自己说一声谢谢。',
        'subtitle' => '免费使用。高级版增加 AI 摘要、每个习惯多个提醒，以及 CSV 导出。',
    ],
    'open' => [
        'eyebrow' => '开源项目',
        'title' => '公开构建，随时可读。',
        'body' => 'OpenHabit 的每一行代码都托管在 GitHub，采用 MIT 许可证。随意阅读、审计、fork，或在自己的服务器上运行。没有遥测，没有锁定，没有惊喜。',
        'cta' => '在 GitHub 上查看',
        'license_link' => '阅读许可证',
        'facts' => [
            'license' => [
                'title' => '宽松许可证',
                'body' => 'MIT。随意 fork、发布、修改。',
            ],
            'selfhost' => [
                'title' => '可自托管',
                'body' => '一条 Docker 命令，自己部署上线。',
            ],
            'audit' => [
                'title' => '无需隐瞒',
                'body' => '无追踪器，无分析 SDK，无隐藏角落。',
            ],
        ],
    ],
    'views' => [
        'week' => '一周',
        'year' => '一年',
        'life' => '一生',
        'week_desc' => '每行代表一个习惯',
        'year_desc' => '每格代表一年中的一周',
        'life_desc' => '每格代表一年',
        'week_n' => '第 :n 周',
        'age' => ':n 岁',

    ],
    'footer' => [
        'tagline' => '© :year :app。安静，是有意为之。',
        'telegram' => 'Telegram',
        'app' => '打开应用',
    ],
];
