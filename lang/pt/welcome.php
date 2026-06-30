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
        'why' => 'Por quê',
        'features' => 'Recursos',
        'menu' => 'Menu',
        'how' => 'Como funciona',
        'open_source' => 'Código aberto',
        'privacy' => 'Privacidade',
        'faq' => 'FAQ',
        'cta_telegram' => 'Abrir bot do Telegram',
        'cta_web' => 'Entrar na web',
        'cta_app' => 'Abrir rastreador web',
        'theme_light' => 'Mudar para tema claro',
        'theme_dark' => 'Mudar para tema escuro',
        'language' => 'Idioma',
    ],
    'hero' => [
        'title_pre' => 'Um app de hábitos',
        'title_accent' => 'gentil com pessoas',
        'subtitle' => 'O OpenHabit não tenta virar o app de hábitos mais poderoso. Ele está virando o app que as pessoas finalmente não abandonam.',
        'login_telegram' => 'Entrar com o Telegram',
        'web_login_note' => 'Prefere o navegador? Entre aqui com o Telegram. Ele usa a mesma conta e os mesmos hábitos do bot.',
        'web_login_js_required' => 'O login no navegador precisa de JavaScript. Sem ele, use o botão do bot do Telegram.',
        'login_fallback' => [
            'line_1' => 'O login com Telegram não carregou.',
            'line_2' => 'Abra o bot do Telegram em vez disso.',
        ],
        'entry_note_telegram' => 'Bot ou navegador. A mesma conta do Telegram e os mesmos hábitos.',
        'entry_note_app' => 'Você já entrou. Isto abre seu rastreador web.',
        'entry_note_no_bot' => 'Telegram não está configurado aqui, então o rastreador web será aberto.',
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
                'title' => 'Exportação CSV para seus registros',
                'body' => 'O Premium inclui exportação CSV no app hospedado, para baixar hábitos, notas e ajustes quando você precisar.',
            ],
        ],
    ],
    'problem' => [
        'title' => 'A cultura da produtividade transforma um dia ruim em culpa',
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
        'title' => 'Três passinhos para continuar',
        'steps' => [
            [
                'title' => 'Escolha Telegram ou login no navegador',
                'body' => 'Abra o bot ou use o widget no navegador. Os dois caminhos usam a mesma conta do Telegram, então não há email nem senha para lembrar.',
            ],
            [
                'title' => 'Mantenha os hábitos que servirem',
                'body' => 'Seus hábitos ficam na mesma conta de qualquer forma. Mantenha o que servir, pause o resto e ajuste lembretes depois.',
            ],
            [
                'title' => 'Volte amanhã',
                'body' => 'Um toque para marcar o dia. Se ontem ficou em branco, nada foi apagado. Hoje ainda conta.',
            ],
        ],
    ],
    'philosophy' => [
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
        'title' => 'Por que abrimos mão das sequências',
        'body' => [
            'Quase todo app de hábitos é construído em torno de sequências. Elas funcionam, mas só enquanto tudo está perfeito.',
            'Um dia ruim vira culpa. Culpa vira evitação. Evitação vira mais um recomeço.',
            'Não removemos sequências porque elas são ruins. Removemos porque queremos ajudar as pessoas a voltar.',
        ],
    ],
    'cta' => [
        'title' => 'Tente sem culpa',
        'subtitle' => 'Construa hábitos que você realmente vai manter, com ferramentas que reduzem a culpa em vez de aumentar.',
    ],
    'open' => [
        'title' => 'Código aberto',
        'body' => 'Cada linha do OpenHabit vive no GitHub sob a licença MIT. Você pode ler, fazer fork ou rodar no seu próprio servidor. O acompanhamento básico continua gratuito; controle total sempre está disponível com self-hosting.',
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
                'title' => 'Limite de privacidade claro',
                'body' => 'O código do app vem sem SDKs de análise. Um host pode adicionar scripts às páginas públicas, e esses scripts aparecem no código-fonte da página.',
            ],
        ],
    ],
    'author' => [
        'title' => 'Oi! Eu fiz o OpenHabit depois de tentar usar vários apps de hábitos diferentes',
        'body' => [
            'Sempre acabava do mesmo jeito. Eu perdia um dia, perdia a sequência e simplesmente parava de abrir o app.',
            'Em algum momento percebi que o problema não era eu. Eu só não queria usar um app que te pune por perder um único dia.',
            'Por isso fiz o OpenHabit — um app para o qual é normal voltar depois de um dia, uma semana ou até um mês, e simplesmente continuar.',
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
        'title' => 'Perguntas frequentes',
        'items' => [
            [
                'q' => 'Quanto custa o OpenHabit?',
                'a' => 'O acompanhamento básico é gratuito, sem limite de tempo ou de hábitos. O Premium adiciona reflexão leve, vários lembretes por hábito no Telegram e exportação CSV no app hospedado. Novas contas recebem automaticamente :days dias de teste grátis.',
            ],
            [
                'q' => 'Preciso começar pelo bot do Telegram?',
                'a' => 'Não. Você pode abrir o bot ou entrar pelo widget no navegador. Os dois caminhos usam sua conta do Telegram e os mesmos hábitos, e deixam você continuar em qualquer dispositivo.',
            ],
            [
                'q' => 'Posso usar o OpenHabit no navegador?',
                'a' => 'Sim. Use o widget de login com Telegram na web e o rastreador completo abrirá no navegador. Os mesmos hábitos, o mesmo histórico, só numa tela maior.',
            ],
            [
                'q' => 'O que é o resumo de IA?',
                'a' => 'Uma breve reflexão diária que percebe padrões nos seus hábitos e notas. Ela existe para ajudar você a continuar, não para dar nota ao seu dia.',
            ],
            [
                'q' => 'Meus dados são privados?',
                'a' => 'Seus hábitos e notas ficam no banco de dados do OpenHabit para sua conta. O código do app não inclui SDKs de análise ou rastreamento de terceiros. Hosts podem adicionar scripts a páginas públicas, então use self-hosting se quiser controlar cada requisição.',
            ],
            [
                'q' => 'Posso hospedar por conta própria?',
                'a' => 'Sim. O código completo está no GitHub sob a licença MIT. Um comando Docker e o app roda no seu servidor. Seus dados nunca saem da sua máquina.',
            ],
        ],
    ],
];
