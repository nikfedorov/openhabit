<?php

declare(strict_types=1);

return [
    'a11y' => [
        'skip' => '跳到内容',
        'primary_nav' => '主导航',
        'preview' => '应用预览',
        'lenses' => '可视化',
    ],
    'meta' => [
        'title' => 'OpenHabit · 给总是放弃习惯追踪器的人用的习惯追踪器',
        'description' => '一个安静的习惯追踪器，给那些厌倦了不断重新开始的人。没有连续记录焦虑，没有负罪感，只有可以明天继续回来的稳定进步。',
        'og_description' => '没有连续记录。没有负罪感。只有稳定进步。',
    ],
    'nav' => [
        'features' => '理念',
        'compare' => '对比',
        'news' => '新闻',
        'menu' => '菜单',
        'how' => '怎么用',
        'faq' => 'FAQ',
        'cta_telegram' => '试试另一种方式',
        'cta_app' => '试试另一种方式',
        'theme_light' => '切换到浅色主题',
        'theme_dark' => '切换到深色主题',
        'language' => '语言',
    ],
    'hero' => [
        'badge' => '进步胜过完美',
        'title_pre' => '给总是',
        'title_accent' => '放弃习惯追踪器的人。',
        'subtitle' => '没有连续记录。没有负罪感。只有稳定进步。OpenHabit 给你一个安全的地方，让你在不完美的日子之后也能继续。',
        'login_telegram' => '使用 Telegram 登录',
        'trial_note' => '轻轻开始。Premium :days 天体验，无需信用卡。',
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
        'eyebrow' => 'OpenHabit 有什么不同',
        'title' => '为帮助你继续而设计',
        'premium' => '高级版',
        'items' => [
            'schedules' => [
                'title' => '停止从头再来',
                'body' => '错过一天不会重置你的故事。明天回来，从你所在的地方继续。',
            ],
            'streaks' => [
                'title' => '看见进步，而不是完美',
                'body' => '活动网格展示完整画面：状态好的日子、比较安静的日子，以及最重要的慢慢回来。',
            ],
            'reminders' => [
                'title' => '温和提醒',
                'body' => '在 Telegram 里给你小小提示，只在你需要时出现。提醒应该邀请你回来，而不是让你觉得落后。',
            ],
            'notes' => [
                'title' => '给真实生活留空间',
                'body' => '用每日笔记记录发生了什么，不评判。混乱的背景也是慢慢建立的一部分。',
            ],
            'ai' => [
                'title' => '温和复盘',
                'body' => '一份安静的总结，发现你可能错过的规律，并帮你选择一个很小的下一步。',
            ],
            'export' => [
                'title' => '你的历史仍然属于你',
                'body' => '随时导出你的数据。长期坚持不应该依赖平台锁定。',
            ],
        ],
    ],
    'problem' => [
        'eyebrow' => '问题',
        'title' => '每个周一，你都答应自己要重新开始。',
        'body' => [
            '到了周四，你已经错过了一天。',
            '到了周日，你已经放弃了。',
            '也许问题不在你。也许问题在连续记录本身。',
        ],
    ],
    'digest_examples' => [
        'eyebrow' => '复盘',
        'title' => '用更平静的方式回看一天',
        'subtitle' => '不打分。不责备。只看看什么有帮助，以及下一步可以试什么。',
        'items' => [
            [
                'date_machine' => '2026-05-20',
                'date' => '2026年5月20日',
                'paragraph_1' => '今天算数。你回到了通常能支撑你的习惯：早起、锻炼、冥想和深度工作。值得注意的是，在困难的一天之后你很快回来了。',
                'paragraph_2' => '睡得晚时，晨间日记总会被挤掉。今晚把下一版变小一点：只写一句今天发生的好事。',
                'habits' => [
                    ['name' => '8点前起床', 'done' => true],
                    ['name' => '锻炼60分钟', 'done' => true],
                    ['name' => '冥想10-20分钟', 'done' => true],
                    ['name' => '晨间日记', 'done' => false],
                    ['name' => '深度工作2-4小时', 'done' => true],
                    ['name' => '18点前不刷社交媒体', 'done' => true],
                ],
            ],
            [
                'date_machine' => '2026-05-19',
                'date' => '2026年5月19日',
                'paragraph_1' => '早起和锻炼做到了。深度工作没有。这不会抹掉早晨，只是说明这一天在哪里变得吃力。',
                'paragraph_2' => '对于数字边界，试试一个小暂停：想刷屏时，先站起来。这个小空隙会让回来更容易。',
                'habits' => [
                    ['name' => '8点前起床', 'done' => true],
                    ['name' => '锻炼60分钟', 'done' => true],
                    ['name' => '冥想10-20分钟', 'done' => true],
                    ['name' => '晨间日记', 'done' => false],
                    ['name' => '深度工作2-4小时', 'done' => false],
                    ['name' => '18点前不刷社交媒体', 'done' => false],
                ],
            ],
        ],
    ],
    'how' => [
        'eyebrow' => '怎么用',
        'title' => '三小步，继续下去。',
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
                'title' => '明天再回来',
                'body' => '一下点击就标记今天。如果昨天错过了，什么都没有被抹掉。今天依然算数。',
            ],
        ],
    ],
    'philosophy' => [
        'eyebrow' => '我们的理念',
        'title' => '进步胜过完美。',
        'items' => [
            '持续胜过强度。',
            '错过一天很正常。',
            '我们要避免的是彻底放弃。',
        ],
    ],
    'cta' => [
        'title' => '不要因为错过一天就放弃。',
        'subtitle' => '建立你真的能保留下来的习惯，用减少负罪感而不是增加压力的工具。',
        'trial_note' => 'Premium :days 天免费试用，无需信用卡。',
    ],
    'compare' => [
        'eyebrow' => '对比',
        'title' => '传统习惯应用奖励完美。',
        'subtitle' => 'OpenHabit 鼓励你回来。差别不在于更多功能，而在于和进步建立更安心的关系。',
        'caption' => '传统习惯应用与 OpenHabit 对比',
        'criterion' => '行为',
        'us_tag' => '我们的选择',
        'rows' => [
            'miss_day' => '错过一天',
            'feeling' => '经历艰难的一周后',
            'restart' => '接下来会发生什么',
            'goal' => '你追求什么',
        ],
        'cells' => [
            'traditional' => '传统习惯应用',
            'openhabit' => 'OpenHabit',
            'lose_streak' => '失去连续记录',
            'keep_going' => '继续前进',
            'feel_guilty' => '感到内疚',
            'feel_safe' => '安心继续',
            'start_over' => '从头开始',
            'come_back' => '明天回来',
            'perfection' => '完美',
            'steady_progress' => '稳定进步',
        ],
        'a11y' => [
            'yes' => '支持',
            'no' => '不支持',
        ],
        'footnote' => '这是理念对比，不是功能清单。OpenHabit 围绕长期持续而建。',
    ],
    'open' => [
        'eyebrow' => '开源项目',
        'title' => '公开构建',
        'body' => 'OpenHabit 的每一行代码都托管在 GitHub，采用 MIT 许可证。随意阅读、fork，或在自己的服务器上运行。没有遥测，也没有锁定。',
        'cta' => '在 GitHub 上查看',
        'license_link' => '阅读许可证',
        'facts' => [
            'license' => [
                'title' => '宽松许可证',
                'body' => 'MIT 许可证。随意 fork、修改、重发布。',
            ],
            'selfhost' => [
                'title' => '可自托管',
                'body' => '一条 Docker 命令，自己部署上线。',
            ],
            'audit' => [
                'title' => '无需隐瞒',
                'body' => '没有第三方追踪器或分析 SDK。',
            ],
        ],
    ],
    'views' => [
        'week' => '一周',
        'year' => '一年',
        'life' => '一生',
        'week_desc' => '错过一天不会抹掉一周',
        'year_desc' => '进步也会有安静的季节',
        'life_desc' => '为多年而建，不为完美一周',
        'week_n' => '第 :n 周',
        'age' => ':n 岁',

    ],
    'footer' => [
        'tagline' => '© :year :app。安静，本来如此。',
        'telegram' => 'Telegram',
        'news' => '新闻频道',
        'app' => '打开应用',
    ],
    'faq' => [
        'eyebrow' => '常见问题',
        'title' => '值得一问的问题',
        'items' => [
            [
                'q' => 'OpenHabit 收费吗？',
                'a' => '核心功能完全免费，无时间限制，无习惯数量上限。Premium 增加温和复盘、每个习惯多个 Telegram 提醒和 CSV 导出。新账号会自动获得 :days 天免费试用。',
            ],
            [
                'q' => '我需要 Telegram 账号吗？',
                'a' => '是的。OpenHabit 是一个 Telegram 机器人和小程序，所以只需要一个 Telegram 账号即可。无需单独注册，也没有密码需要记忆。',
            ],
            [
                'q' => '我可以在浏览器中使用 OpenHabit 吗？',
                'a' => '可以。在网页上用 Telegram 账号登录一次，完整的应用就会在浏览器里打开。同样的习惯，同样的记录——只是屏幕更大。',
            ],
            [
                'q' => 'AI 摘要是什么？',
                'a' => '每日一段简短复盘，发现你习惯和笔记里的规律。它是为了帮你继续，而不是给你的一天打分。',
            ],
            [
                'q' => '我的数据安全吗？',
                'a' => '你的数据存储在我们的数据库中，不包含任何第三方分析 SDK。如果想完全掌控，也可以在自己的服务器上部署整个应用。',
            ],
            [
                'q' => '我可以自己部署吗？',
                'a' => '可以。完整源代码在 GitHub 上，采用 MIT 许可证。一条 Docker 命令即可在你自己的服务器上运行，数据永远不离开你的机器。',
            ],
        ],
    ],
];
