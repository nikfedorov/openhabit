<?php

declare(strict_types=1);

return [
    'a11y' => [
        'skip' => 'Saltar al contenido',
        'primary_nav' => 'Principal',
        'preview' => 'Vista previa de la app',
        'lenses' => 'Visualizaciones',
    ],
    'meta' => [
        'title' => 'OpenHabit · El rastreador de hábitos anti-rachas',
        'description' => 'Un rastreador de hábitos amable con las personas. Sin ansiedad por rachas, sin culpa, solo una forma más tranquila de volver.',
        'og_description' => 'Un rastreador de hábitos amable con las personas.',
    ],
    'nav' => [
        'why' => 'Por qué',
        'features' => 'Funciones',
        'menu' => 'Menú',
        'how' => 'Cómo funciona',
        'open_source' => 'Código abierto',
        'privacy' => 'Privacidad',
        'faq' => 'FAQ',
        'cta_telegram' => 'Abrir bot de Telegram',
        'cta_web' => 'Iniciar en la web',
        'cta_app' => 'Abrir rastreador web',
        'theme_light' => 'Cambiar a tema claro',
        'theme_dark' => 'Cambiar a tema oscuro',
        'language' => 'Idioma',
    ],
    'hero' => [
        'title_pre' => 'Un rastreador de hábitos',
        'title_accent' => 'amable con las personas',
        'subtitle' => 'OpenHabit no intenta convertirse en el rastreador de hábitos más potente. Está convirtiéndose en el que la gente por fin no abandona.',
        'login_telegram' => 'Entrar con Telegram',
        'web_login_note' => '¿Prefieres el navegador? Inicia sesión aquí con Telegram. Usa la misma cuenta y los mismos hábitos que el bot.',
        'web_login_js_required' => 'El inicio en el navegador necesita JavaScript. Sin él, usa el botón del bot de Telegram.',
        'login_fallback' => [
            'line_1' => 'El inicio con Telegram no se cargó.',
            'line_2' => 'Abre el bot de Telegram en su lugar.',
        ],
        'entry_note_telegram' => 'Bot o navegador. La misma cuenta de Telegram y los mismos hábitos.',
        'entry_note_app' => 'Ya iniciaste sesión. Esto abre tu rastreador web.',
        'entry_note_no_bot' => 'Telegram no está configurado aquí, así que se abrirá el rastreador web.',
    ],
    'preview' => [
        'today' => 'Hoy',
        'date' => '28 abr',
        'habits' => [
            'Meditación de la mañana',
            'Leer 30 min',
            'Salir a correr',
            'Entrenar',
            'Sin móvil antes de dormir',
            'Diario de la noche',
        ],
        'story_lines' => [
            'No es una racha.',
            'No es un calendario.',
            'Es la historia de cómo volviste.',
        ],
        'less' => 'Menos',
        'more' => 'Más',
    ],
    'features' => [
        'title' => 'Hecho para ayudarte a seguir',
        'premium' => 'Premium',
        'items' => [
            'schedules' => [
                'title' => 'Deja de empezar de cero',
                'body' => 'Un día perdido no reinicia tu historia. Vuelve mañana y continúa desde donde estás.',
            ],
            'streaks' => [
                'title' => 'Mira progreso, no perfección',
                'body' => 'La cuadrícula muestra la imagen completa: días fuertes, días tranquilos y el regreso gradual que más importa.',
            ],
            'reminders' => [
                'title' => 'Recordatorios suaves',
                'body' => 'Pequeños avisos en Telegram, solo cuando los pides. Un recordatorio debería invitarte a volver, no hacerte sentir atrasado.',
            ],
            'notes' => [
                'title' => 'Espacio para la vida real',
                'body' => 'Usa las notas diarias para registrar lo que pasó sin juzgarlo. El contexto imperfecto también forma parte de construir poco a poco.',
            ],
            'ai' => [
                'title' => 'Reflexión tranquila',
                'body' => 'Un resumen tranquilo que detecta patrones que quizá no verías y te ayuda a elegir un pequeño siguiente paso.',
            ],
            'export' => [
                'title' => 'Exportación CSV para tus registros',
                'body' => 'Premium incluye exportación CSV desde la app alojada, para descargar tus hábitos, notas y ajustes cuando lo necesites.',
            ],
        ],
    ],
    'problem' => [
        'title' => 'La cultura de productividad convierte un mal día en culpa',
        'body' => [
            'Empieza con motivación. Luego un día perdido. Luego culpa. Luego la app desaparece hasta el siguiente lunes.',
        ],
        'cycle' => [
            ['day' => 'Lunes', 'mood' => '🔥 Esta vez funcionará'],
            ['day' => 'Miércoles', 'mood' => '🙂 Todo bien'],
            ['day' => 'Viernes', 'mood' => '😕 Fallé un día'],
            ['day' => 'Sábado', 'mood' => '😞 Ya está arruinado'],
            ['day' => 'El mes siguiente', 'mood' => 'Nuevo tracker'],
        ],
        'closing' => 'Creamos OpenHabit para romper este ciclo.',
    ],
    'digest_examples' => [
        'eyebrow' => 'Reflexión',
        'title' => 'Una forma más tranquila de revisar tu día',
        'subtitle' => 'Sin puntuaciones. Sin regaños. Solo una mirada útil a lo que ayudó y a qué probar después.',
        'items' => [
            [
                'date_machine' => '2026-05-20',
                'date' => '20 may. 2026',
                'paragraph_1' => 'Hoy contó. Volviste a las costumbres que suelen sostenerte: levantarte temprano, entrenar, meditar y trabajar en profundidad. El patrón útil es lo rápido que regresaste después de un día difícil.',
                'paragraph_2' => 'Las páginas matutinas se pierden cuando duermes tarde. Esta noche haz la siguiente versión más pequeña: una sola frase sobre algo bueno que pasó hoy.',
                'habits' => [
                    ['name' => 'Despertar a las 8am', 'done' => true],
                    ['name' => 'Entrenamiento, 60 min', 'done' => true],
                    ['name' => 'Meditación 10–20 min', 'done' => true],
                    ['name' => 'Páginas matutinas', 'done' => false],
                    ['name' => 'Trabajo profundo 2–4h', 'done' => true],
                    ['name' => 'Sin redes sociales hasta las 18:00', 'done' => true],
                ],
            ],
            [
                'date_machine' => '2026-05-19',
                'date' => '19 may. 2026',
                'paragraph_1' => 'Madrugar y entrenar ocurrió. El trabajo profundo no. Eso no borra la mañana; solo muestra dónde se volvió pesado el día.',
                'paragraph_2' => 'Para los límites digitales, prueba una pausa pequeña: cuando aparezca el impulso de desplazarte, ponte de pie primero. Ese espacio puede hacer que volver sea más fácil.',
                'habits' => [
                    ['name' => 'Despertar a las 8am', 'done' => true],
                    ['name' => 'Entrenamiento, 60 min', 'done' => true],
                    ['name' => 'Meditación 10–20 min', 'done' => true],
                    ['name' => 'Páginas matutinas', 'done' => false],
                    ['name' => 'Trabajo profundo 2–4h', 'done' => false],
                    ['name' => 'Sin redes sociales hasta las 18:00', 'done' => false],
                ],
            ],
        ],
    ],
    'how' => [
        'title' => 'Tres pasos pequeños para seguir',
        'steps' => [
            [
                'title' => 'Elige Telegram o el inicio en el navegador',
                'body' => 'Abre el bot o usa el widget del navegador. Ambos caminos usan la misma cuenta de Telegram, así que no hay email ni contraseña que recordar.',
            ],
            [
                'title' => 'Conserva los hábitos que encajen',
                'body' => 'Tus hábitos quedan en la misma cuenta de cualquier forma. Conserva los que encajen, pausa el resto y ajusta los recordatorios después.',
            ],
            [
                'title' => 'Vuelve mañana',
                'body' => 'Un toque para marcar el día. Si ayer faltó, nada se borra. Hoy todavía cuenta.',
            ],
        ],
    ],
    'philosophy' => [
        'title' => 'The Anti-Streak Manifesto',
        'items' => [
            'No creemos que los hábitos deban construirse sobre la culpa.',
            'Creemos que una persona importa más que una racha.',
            'Creemos que un mal día no cancela un buen mes.',
            'Creemos que volver importa más que ser perfecto.',
            'Por eso hicimos OpenHabit.',
        ],
    ],
    'anti_streak' => [
        'title' => 'Por qué abandonamos las rachas',
        'body' => [
            'Casi todas las apps de hábitos se construyen alrededor de rachas. Funcionan, pero solo mientras todo es perfecto.',
            'Un mal día se convierte en culpa. La culpa en evitación. La evitación en otro reinicio.',
            'No quitamos las rachas porque sean malas. Las quitamos porque queremos ayudar a la gente a volver.',
        ],
    ],
    'cta' => [
        'title' => 'Prueba sin culpa',
        'subtitle' => 'Construye hábitos que de verdad puedas mantener, con herramientas que reducen la culpa en vez de añadir más.',
    ],
    'open' => [
        'title' => 'Código abierto',
        'body' => 'Cada línea de OpenHabit vive en GitHub bajo la licencia MIT. Puedes leerlo, forkearlo o ejecutarlo en tu propio servidor. El seguimiento básico sigue siendo gratuito; el control total siempre está disponible con self-hosting.',
        'cta' => 'Ver en GitHub',
        'license_link' => 'Leer la licencia',
        'facts' => [
            'license' => [
                'title' => 'Licencia permisiva',
                'body' => 'Con licencia MIT. Fórkalo, modifícalo y publica lo que quieras.',
            ],
            'selfhost' => [
                'title' => 'Autohospedable',
                'body' => 'Un comando de Docker y lo tienes en marcha tú mismo.',
            ],
            'audit' => [
                'title' => 'Límite de privacidad claro',
                'body' => 'El código de la app se entrega sin SDK de analítica. Un host puede añadir scripts a las páginas públicas, y esos scripts aparecen en el código fuente de la página.',
            ],
        ],
    ],
    'author' => [
        'title' => '¡Hola! Hice OpenHabit después de probar varios rastreadores de hábitos distintos',
        'body' => [
            'Siempre terminaba igual. Perdía un día, perdía la racha y dejaba de abrir la app.',
            'En algún momento entendí que el problema no era yo. Simplemente no quería usar un rastreador que castiga por un solo día perdido.',
            'Por eso hice OpenHabit: una app a la que está bien volver después de un día, una semana o incluso un mes, y simplemente seguir.',
        ],
        'name' => 'Nik Fedorov',
        'role' => 'Desarrollador independiente de OpenHabit',
        'photo_alt' => 'Nik Fedorov',
        'github' => 'GitHub',
        'twitter' => 'Twitter',
    ],
    'views' => [
        'week' => 'Semana',
        'year' => 'Año',
        'life' => 'Vida',
        'week_desc' => 'Perder un día no borra la semana',
        'year_desc' => 'El progreso también tiene temporadas tranquilas',
        'life_desc' => 'Hecho para años, no para semanas perfectas',
        'week_n' => 'Semana :n',
        'age' => ':n años',

    ],
    'footer' => [
        'tagline' => '© :year :app. Tranquilo a propósito.',
        'telegram' => 'Telegram',
        'news' => 'Canal de novedades',
        'app' => 'Abrir la app',
        'why' => 'Por qué existe OpenHabit',
        'roadmap' => 'Roadmap',
        'changelog' => 'Changelog',
        'github' => 'GitHub',
        'privacy' => 'Privacidad',
    ],
    'faq' => [
        'title' => 'Preguntas frecuentes',
        'items' => [
            [
                'q' => '¿Cuánto cuesta OpenHabit?',
                'a' => 'El seguimiento básico es gratuito, sin límite de tiempo ni de hábitos. Premium añade reflexión tranquila, varios recordatorios por hábito en Telegram y exportación CSV desde la app alojada. Las cuentas nuevas reciben :days días de prueba gratis automáticamente.',
            ],
            [
                'q' => '¿Tengo que empezar en el bot de Telegram?',
                'a' => 'No. Puedes abrir el bot o iniciar sesión con el widget del navegador. Ambos caminos usan tu cuenta de Telegram y los mismos hábitos, y te dejan continuar en cualquier dispositivo.',
            ],
            [
                'q' => '¿Puedo usar OpenHabit en el navegador?',
                'a' => 'Sí. Usa el widget de inicio con Telegram en la web y el rastreador completo se abrirá en tu navegador. Los mismos hábitos, el mismo historial, solo una pantalla más grande.',
            ],
            [
                'q' => '¿Qué es el resumen de IA?',
                'a' => 'Una breve reflexión diaria que detecta patrones en tus hábitos y notas. Está diseñada para ayudarte a seguir, no para calificar tu día.',
            ],
            [
                'q' => '¿Son privados mis datos?',
                'a' => 'Tus hábitos y notas se guardan en la base de datos de OpenHabit para tu cuenta. El código de la app no incluye SDK de analítica ni seguimiento de terceros. Los hosts pueden añadir scripts a páginas públicas, así que usa self-hosting si quieres controlar cada petición.',
            ],
            [
                'q' => '¿Puedo alojarla yo mismo (self-host)?',
                'a' => 'Sí. El código fuente completo está en GitHub bajo la licencia MIT. Un comando de Docker y la app funciona en tu servidor. Tus datos nunca salen de tu máquina.',
            ],
        ],
    ],
];
