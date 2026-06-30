<?php

declare(strict_types=1);

return [
    'a11y' => [
        'skip' => 'Ir para o conteúdo',
        'primary_nav' => 'Principal',
        'preview' => 'Pré-visualização do app',
        'lenses' => 'Visualizações',
    ],
    'meta' => [
        'title' => 'OpenHabit · O app de hábitos anti-sequências',
        'description' => 'Um app de hábitos gentil com pessoas. Sem ansiedade por sequências, sem culpa, só um jeito mais calmo de voltar.',
        'og_description' => 'Um app de hábitos gentil com pessoas.',
    ],
    'nav' => [
        'features' => 'Manifesto',
        'compare' => 'Comparar',
        'news' => 'Novidades',
        'menu' => 'Menu',
        'how' => 'Como funciona',
        'faq' => 'FAQ',
        'cta_telegram' => 'Tente sem culpa',
        'cta_app' => 'Tente sem culpa',
        'theme_light' => 'Mudar para tema claro',
        'theme_dark' => 'Mudar para tema escuro',
        'language' => 'Idioma',
    ],
    'hero' => [
        'badge' => 'Feito para voltar',
        'title_pre' => 'Um app de hábitos',
        'title_accent' => 'gentil com pessoas.',
        'subtitle' => 'O OpenHabit não tenta virar o app de hábitos mais poderoso. Ele está virando o app que as pessoas finalmente não abandonam.',
        'login_telegram' => 'Entrar com o Telegram',
        'trial_note' => 'Comece com calma. :days dias de Premium, sem cartão.',
    ],
    'preview' => [
        'today' => 'Hoje',
        'date' => '28 abr',
        'habits' => [
            'Meditação da manhã',
            'Ler 30 min',
            'Corrida da manhã',
            'Treino',
            'Sem celular antes de dormir',
            'Diário da noite',
        ],
        'story_lines' => [
            'Não é uma sequência.',
            'Não é um calendário.',
            'É a história de como você voltou.',
        ],
        'less' => 'Menos',
        'more' => 'Mais',
    ],
    'features' => [
        'eyebrow' => 'Como o OpenHabit é diferente',
        'title' => 'Feito para ajudar você a continuar',
        'premium' => 'Premium',
        'items' => [
            'schedules' => [
                'title' => 'Pare de recomeçar',
                'body' => 'Um dia perdido não reinicia a sua história. Volte amanhã e continue de onde você está.',
            ],
            'streaks' => [
                'title' => 'Veja progresso, não perfeição',
                'body' => 'A grade mostra a imagem completa: dias fortes, dias mais parados e o retorno gradual que mais importa.',
            ],
            'reminders' => [
                'title' => 'Lembretes gentis',
                'body' => 'Pequenos toques no Telegram, só quando você pedir. Um lembrete deve convidar você de volta, não fazer você se sentir atrasado.',
            ],
            'notes' => [
                'title' => 'Espaço para a vida real',
                'body' => 'Use notas diárias para registrar o que aconteceu sem julgamento. O contexto bagunçado também faz parte de construir aos poucos.',
            ],
            'ai' => [
                'title' => 'Reflexão leve',
                'body' => 'Um resumo calmo que percebe padrões que talvez passassem batido e ajuda você a escolher um pequeno próximo passo.',
            ],
            'export' => [
                'title' => 'Sua história continua sua',
                'body' => 'Exporte seus dados quando quiser. Constância de longo prazo não deve depender de ficar preso a uma plataforma.',
            ],
        ],
    ],
    'problem' => [
        'eyebrow' => 'O ciclo',
        'title' => 'A cultura da produtividade transforma um dia ruim em culpa.',
        'body' => [
            'Começa com motivação. Depois vem um dia perdido. Depois culpa. Depois o app desaparece até a próxima segunda.',
        ],
        'cycle' => [
            ['day' => 'Segunda', 'mood' => '🔥 Agora vai'],
            ['day' => 'Quarta', 'mood' => '🙂 Tudo certo'],
            ['day' => 'Sexta', 'mood' => '😕 Perdi um dia'],
            ['day' => 'Sábado', 'mood' => '😞 Agora já era'],
            ['day' => 'Mês seguinte', 'mood' => 'Novo tracker'],
        ],
        'closing' => 'Criamos o OpenHabit para quebrar esse ciclo.',
        'cta' => 'Tente sem culpa',
    ],
    'proof' => [
        'eyebrow' => 'Fatos honestos',
        'items' => [
            ['value' => '1', 'label' => 'desenvolvedor construindo em público'],
            ['value' => 'MIT', 'label' => 'licença e self-hosting'],
            ['value' => '0', 'label' => 'SDKs de análise no app'],
        ],
    ],
    'digest_examples' => [
        'eyebrow' => 'Reflexão',
        'title' => 'Um jeito mais calmo de revisar o dia',
        'subtitle' => 'Sem nota. Sem bronca. Só uma visão útil do que ajudou e do que tentar depois.',
        'items' => [
            [
                'date_machine' => '2026-05-20',
                'date' => '20 mai. 2026',
                'paragraph_1' => 'Hoje contou. Você voltou aos hábitos que costumam sustentar você: acordar cedo, treinar, meditar e trabalho profundo. O padrão útil é a rapidez com que você voltou depois de um dia difícil.',
                'paragraph_2' => 'As páginas matinais escapam quando o sono atrasa. Hoje à noite, deixe a próxima versão menor: uma frase sobre algo bom que aconteceu hoje.',
                'habits' => [
                    ['name' => 'Acordar às 8h', 'done' => true],
                    ['name' => 'Treino, 60 min', 'done' => true],
                    ['name' => 'Meditação 10–20 min', 'done' => true],
                    ['name' => 'Páginas matinais', 'done' => false],
                    ['name' => 'Trabalho profundo 2–4h', 'done' => true],
                    ['name' => 'Sem redes sociais até 18:00', 'done' => true],
                ],
            ],
            [
                'date_machine' => '2026-05-19',
                'date' => '19 mai. 2026',
                'paragraph_1' => 'Acordar cedo e treinar aconteceu. Trabalho profundo não. Isso não apaga a manhã; só mostra onde o dia ficou pesado.',
                'paragraph_2' => 'Para limites digitais, experimente uma pausa pequena: quando vier a vontade de rolar a tela, levante primeiro. Esse pequeno espaço pode facilitar a volta.',
                'habits' => [
                    ['name' => 'Acordar às 8h', 'done' => true],
                    ['name' => 'Treino, 60 min', 'done' => true],
                    ['name' => 'Meditação 10–20 min', 'done' => true],
                    ['name' => 'Páginas matinais', 'done' => false],
                    ['name' => 'Trabalho profundo 2–4h', 'done' => false],
                    ['name' => 'Sem redes sociais até 18:00', 'done' => false],
                ],
            ],
        ],
    ],
    'how' => [
        'eyebrow' => 'Como funciona',
        'title' => 'Três passinhos para continuar.',
        'steps' => [
            [
                'title' => 'Abra o bot no Telegram',
                'body' => 'Toque no botão abaixo. Não tem cadastro, e-mail ou senha pra esquecer. Se você tem Telegram, já está dentro.',
            ],
            [
                'title' => 'Adicione um hábito ou dois',
                'body' => 'Comece pequeno. Algo que você realmente queira fazer quase todo dia. Se ajudar, coloque um lembrete.',
            ],
            [
                'title' => 'Volte amanhã',
                'body' => 'Um toque para marcar o dia. Se ontem ficou em branco, nada foi apagado. Hoje ainda conta.',
            ],
        ],
    ],
    'philosophy' => [
        'eyebrow' => 'Manifesto',
        'title' => 'The Anti-Streak Manifesto',
        'items' => [
            'Não acreditamos que hábitos devam ser construídos com culpa.',
            'Acreditamos que uma pessoa importa mais que uma sequência.',
            'Acreditamos que um dia ruim não cancela um bom mês.',
            'Acreditamos que voltar importa mais que ser impecável.',
            'Por isso fizemos o OpenHabit.',
        ],
    ],
    'anti_streak' => [
        'eyebrow' => 'Por que sem sequências',
        'title' => 'Por que abrimos mão das sequências',
        'body' => [
            'Quase todo app de hábitos é construído em torno de sequências. Elas funcionam, mas só enquanto tudo está perfeito.',
            'Um dia ruim vira culpa. Culpa vira evitação. Evitação vira mais um recomeço.',
            'Não removemos sequências porque elas são ruins. Removemos porque queremos ajudar as pessoas a voltar.',
        ],
    ],
    'cta' => [
        'title' => 'Tente sem culpa.',
        'subtitle' => 'Construa hábitos que você realmente vai manter, com ferramentas que reduzem a culpa em vez de aumentar.',
        'trial_note' => ':days dias de Premium grátis. Sem cartão.',
    ],
    'compare' => [
        'eyebrow' => 'Comparação',
        'title' => 'Apps tradicionais de hábito recompensam perfeição.',
        'subtitle' => 'O OpenHabit valoriza a volta. A diferença não é ter mais recursos. É uma relação mais leve com o progresso.',
        'caption' => 'Comparação entre apps tradicionais de hábitos e OpenHabit',
        'criterion' => 'Comportamento',
        'us_tag' => 'Nossa escolha',
        'rows' => [
            'miss_day' => 'Perde um dia',
            'feeling' => 'Depois de uma semana difícil',
            'restart' => 'O que acontece depois',
            'goal' => 'O que você persegue',
        ],
        'cells' => [
            'traditional' => 'Apps tradicionais',
            'openhabit' => 'OpenHabit',
            'lose_streak' => 'Perde a sequência',
            'keep_going' => 'Continua',
            'feel_guilty' => 'Sente culpa',
            'feel_safe' => 'Sente segurança para continuar',
            'start_over' => 'Recomeça',
            'come_back' => 'Volta amanhã',
            'perfection' => 'Perfeição',
            'steady_progress' => 'Progresso constante',
        ],
        'a11y' => [
            'yes' => 'Sim',
            'no' => 'Não',
        ],
        'footnote' => 'Uma comparação de filosofia, não uma lista de recursos. O OpenHabit foi feito para constância de longo prazo.',
    ],
    'open' => [
        'eyebrow' => 'Código aberto',
        'title' => 'Feito às claras',
        'body' => 'Cada linha do OpenHabit vive no GitHub sob a licença MIT. Você pode ler, fazer fork ou rodar no seu próprio servidor. Não tem telemetria nem nada prendendo você aqui.',
        'cta' => 'Ver no GitHub',
        'license_link' => 'Ler a licença',
        'facts' => [
            'license' => [
                'title' => 'Licença permissiva',
                'body' => 'Licença MIT. Faça fork, modifique e publique o que quiser.',
            ],
            'selfhost' => [
                'title' => 'Auto-hospedável',
                'body' => 'Um comando Docker e você está rodando por conta própria.',
            ],
            'audit' => [
                'title' => 'Nada a esconder',
                'body' => 'Sem rastreadores de terceiros e sem SDKs de análise.',
            ],
        ],
    ],
    'author' => [
        'eyebrow' => 'Feito por um desenvolvedor',
        'title' => 'Oi. Eu fiz o OpenHabit porque eu também abandonava apps de hábitos.',
        'body' => [
            'Por vários anos tentei usar diferentes apps de hábitos. Toda vez acontecia a mesma coisa.',
            'Um dia perdido. Uma sequência perdida. Culpa. Uma nova segunda-feira.',
            'Por isso fiz o OpenHabit: um lugar mais calmo para marcar progresso e voltar sem vergonha.',
        ],
        'name' => 'Nik Fedorov',
        'role' => 'Desenvolvedor independente do OpenHabit',
        'photo_alt' => 'Nik Fedorov',
        'github' => 'GitHub',
        'twitter' => 'Twitter',
    ],
    'views' => [
        'week' => 'Semana',
        'year' => 'Ano',
        'life' => 'Vida',
        'week_desc' => 'Perder um dia não apaga a semana',
        'year_desc' => 'O progresso também tem fases mais calmas',
        'life_desc' => 'Feito para anos, não semanas perfeitas',
        'week_n' => 'Semana :n',
        'age' => ':n anos',

    ],
    'footer' => [
        'tagline' => '© :year :app. Silencioso de propósito.',
        'telegram' => 'Telegram',
        'news' => 'Canal de novidades',
        'app' => 'Abrir o app',
        'why' => 'Por que o OpenHabit existe',
        'roadmap' => 'Roadmap',
        'changelog' => 'Changelog',
        'github' => 'GitHub',
        'privacy' => 'Privacidade',
    ],
    'faq' => [
        'eyebrow' => 'Perguntas frequentes',
        'title' => 'Perguntas que vale a pena fazer',
        'items' => [
            [
                'q' => 'Quanto custa o OpenHabit?',
                'a' => 'As funções principais são completamente gratuitas, sem limite de tempo ou de hábitos. O Premium adiciona reflexão leve, vários lembretes por hábito no Telegram e exportação em CSV. Novas contas recebem automaticamente :days dias de teste grátis.',
            ],
            [
                'q' => 'Preciso de uma conta no Telegram?',
                'a' => 'Sim. O OpenHabit funciona como bot e mini app do Telegram, por isso basta ter uma conta no Telegram. Sem cadastro adicional, sem senha para esquecer.',
            ],
            [
                'q' => 'Posso usar o OpenHabit no navegador?',
                'a' => 'Sim. Faça login com sua conta do Telegram na web e o app completo abre no navegador. Os mesmos hábitos, o mesmo histórico — só numa tela maior.',
            ],
            [
                'q' => 'O que é o resumo de IA?',
                'a' => 'Uma breve reflexão diária que percebe padrões nos seus hábitos e notas. Ela existe para ajudar você a continuar, não para dar nota ao seu dia.',
            ],
            [
                'q' => 'Meus dados são privados?',
                'a' => 'Seus dados ficam no nosso banco de dados sem SDKs de análise de terceiros. Se quiser controle total, você pode hospedar o app no seu próprio servidor.',
            ],
            [
                'q' => 'Posso hospedar por conta própria?',
                'a' => 'Sim. O código completo está no GitHub sob a licença MIT. Um comando Docker e o app roda no seu servidor. Seus dados nunca saem da sua máquina.',
            ],
        ],
    ],
];
