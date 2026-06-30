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
        'features' => 'Manifiesto',
        'compare' => 'Comparar',
        'news' => 'Novedades',
        'menu' => 'Menú',
        'how' => 'Cómo funciona',
        'faq' => 'FAQ',
        'cta_telegram' => 'Prueba sin culpa',
        'cta_app' => 'Prueba sin culpa',
        'theme_light' => 'Cambiar a tema claro',
        'theme_dark' => 'Cambiar a tema oscuro',
        'language' => 'Idioma',
    ],
    'hero' => [
        'badge' => 'Hecho para volver',
        'title_pre' => 'Un rastreador de hábitos',
        'title_accent' => 'amable con las personas.',
        'subtitle' => 'OpenHabit no intenta convertirse en el rastreador de hábitos más potente. Está convirtiéndose en el que la gente por fin no abandona.',
        'login_telegram' => 'Entrar con Telegram',
        'trial_note' => 'Empieza con calma. :days días de Premium, sin tarjeta.',
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
        'eyebrow' => 'Por qué OpenHabit es diferente',
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
                'title' => 'Tu historia sigue siendo tuya',
                'body' => 'Exporta tus datos cuando quieras. La constancia a largo plazo no debería depender de quedarte atado a una plataforma.',
            ],
        ],
    ],
    'problem' => [
        'eyebrow' => 'El bucle',
        'title' => 'La cultura de productividad convierte un mal día en culpa.',
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
        'cta' => 'Prueba sin culpa',
    ],
    'proof' => [
        'eyebrow' => 'Hechos honestos',
        'items' => [
            ['value' => '1', 'label' => 'desarrollador construyendo en público'],
            ['value' => 'MIT', 'label' => 'licencia y self-hosting'],
            ['value' => '0', 'label' => 'SDKs de analítica en la app'],
        ],
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
        'eyebrow' => 'Cómo funciona',
        'title' => 'Tres pasos pequeños para seguir.',
        'steps' => [
            [
                'title' => 'Abre el bot en Telegram',
                'body' => 'Toca el botón de abajo. No hace falta cuenta nueva, ni email, ni contraseña que olvidar. Si tienes Telegram, ya estás dentro.',
            ],
            [
                'title' => 'Añade un hábito o dos',
                'body' => 'Empieza pequeño. Algo que de verdad quieras hacer casi todos los días. Y, si te ayuda, ponle un recordatorio.',
            ],
            [
                'title' => 'Vuelve mañana',
                'body' => 'Un toque para marcar el día. Si ayer faltó, nada se borra. Hoy todavía cuenta.',
            ],
        ],
    ],
    'philosophy' => [
        'eyebrow' => 'Manifiesto',
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
        'eyebrow' => 'Por qué sin rachas',
        'title' => 'Por qué abandonamos las rachas',
        'body' => [
            'Casi todas las apps de hábitos se construyen alrededor de rachas. Funcionan, pero solo mientras todo es perfecto.',
            'Un mal día se convierte en culpa. La culpa en evitación. La evitación en otro reinicio.',
            'No quitamos las rachas porque sean malas. Las quitamos porque queremos ayudar a la gente a volver.',
        ],
    ],
    'cta' => [
        'title' => 'Prueba sin culpa.',
        'subtitle' => 'Construye hábitos que de verdad puedas mantener, con herramientas que reducen la culpa en vez de añadir más.',
        'trial_note' => ':days días de prueba de Premium, sin tarjeta.',
    ],
    'compare' => [
        'eyebrow' => 'Comparación',
        'title' => 'Los rastreadores tradicionales premian la perfección.',
        'subtitle' => 'OpenHabit valora que vuelvas. La diferencia no son más funciones. Es una relación más amable con el progreso.',
        'caption' => 'Comparación entre apps tradicionales de hábitos y OpenHabit',
        'criterion' => 'Comportamiento',
        'us_tag' => 'Nuestra elección',
        'rows' => [
            'miss_day' => 'Pierdes un día',
            'feeling' => 'Después de una semana difícil',
            'restart' => 'Qué pasa después',
            'goal' => 'Qué persigues',
        ],
        'cells' => [
            'traditional' => 'Apps tradicionales',
            'openhabit' => 'OpenHabit',
            'lose_streak' => 'Pierdes la racha',
            'keep_going' => 'Sigues adelante',
            'feel_guilty' => 'Sientes culpa',
            'feel_safe' => 'Te sientes seguro para continuar',
            'start_over' => 'Empiezas de cero',
            'come_back' => 'Vuelves mañana',
            'perfection' => 'Perfección',
            'steady_progress' => 'Progreso constante',
        ],
        'a11y' => [
            'yes' => 'Sí',
            'no' => 'No',
        ],
        'footnote' => 'Una comparación de filosofía, no una lista de funciones. OpenHabit está construido alrededor de la constancia a largo plazo.',
    ],
    'open' => [
        'eyebrow' => 'Código abierto',
        'title' => 'Desarrollado en abierto',
        'body' => 'Cada línea de OpenHabit vive en GitHub bajo la licencia MIT. Puedes leerlo, forkearlo o ejecutarlo en tu propio servidor. No hay telemetría ni bloqueo de proveedor.',
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
                'title' => 'Nada que esconder',
                'body' => 'Sin rastreadores de terceros ni SDK de analítica.',
            ],
        ],
    ],
    'author' => [
        'eyebrow' => 'Hecho por un desarrollador',
        'title' => 'Hola. Hice OpenHabit porque yo también abandonaba los rastreadores de hábitos.',
        'body' => [
            'Durante varios años intenté usar distintos rastreadores de hábitos. Siempre pasaba lo mismo.',
            'Un día perdido. Una racha perdida. Culpa. Un nuevo lunes.',
            'Por eso hice OpenHabit: un lugar más tranquilo para marcar progreso y volver sin vergüenza.',
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
        'eyebrow' => 'Preguntas frecuentes',
        'title' => 'Preguntas que merece la pena hacer',
        'items' => [
            [
                'q' => '¿Cuánto cuesta OpenHabit?',
                'a' => 'Las funciones principales son completamente gratuitas, sin límite de tiempo ni de hábitos. Premium añade reflexión tranquila, varios recordatorios por hábito en Telegram y exportación en CSV. Las cuentas nuevas reciben :days días de prueba gratis automáticamente.',
            ],
            [
                'q' => '¿Necesito una cuenta de Telegram?',
                'a' => 'Sí. OpenHabit es un bot y una mini app de Telegram, así que solo necesitas una cuenta de Telegram. Sin registro adicional, sin contraseña que olvidar.',
            ],
            [
                'q' => '¿Puedo usar OpenHabit en el navegador?',
                'a' => 'Sí. Inicia sesión con tu cuenta de Telegram en la web y la app completa se abre en el navegador. Los mismos hábitos, el mismo historial — solo una pantalla más grande.',
            ],
            [
                'q' => '¿Qué es el resumen de IA?',
                'a' => 'Una breve reflexión diaria que detecta patrones en tus hábitos y notas. Está diseñada para ayudarte a seguir, no para calificar tu día.',
            ],
            [
                'q' => '¿Son privados mis datos?',
                'a' => 'Tus datos se almacenan en nuestra base de datos sin SDK de analítica de terceros. Si quieres control total, puedes alojar la app en tu propio servidor.',
            ],
            [
                'q' => '¿Puedo alojarla yo mismo (self-host)?',
                'a' => 'Sí. El código fuente completo está en GitHub bajo la licencia MIT. Un comando de Docker y la app funciona en tu servidor. Tus datos nunca salen de tu máquina.',
            ],
        ],
    ],
];
