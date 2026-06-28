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
        'title' => 'OpenHabit · El rastreador de hábitos para quienes siempre abandonan los rastreadores de hábitos',
        'description' => 'Un rastreador de hábitos tranquilo para quienes están cansados de empezar de cero. Sin ansiedad por rachas, sin culpa, solo progreso constante al que puedes volver mañana.',
        'og_description' => 'Sin rachas. Sin culpa. Solo progreso constante.',
    ],
    'nav' => [
        'features' => 'Filosofía',
        'compare' => 'Comparar',
        'news' => 'Novedades',
        'menu' => 'Menú',
        'how' => 'Cómo funciona',
        'faq' => 'FAQ',
        'cta_telegram' => 'Prueba otra forma',
        'cta_app' => 'Prueba otra forma',
        'theme_light' => 'Cambiar a tema claro',
        'theme_dark' => 'Cambiar a tema oscuro',
        'language' => 'Idioma',
    ],
    'hero' => [
        'badge' => 'Progreso sobre perfección',
        'title_pre' => 'El rastreador de hábitos para quienes',
        'title_accent' => 'siempre abandonan los rastreadores de hábitos.',
        'subtitle' => 'Sin rachas. Sin culpa. Solo progreso constante. OpenHabit te da un lugar seguro para seguir, incluso después de días imperfectos.',
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
        'eyebrow' => 'El problema',
        'title' => 'Cada lunes prometes que vas a empezar otra vez.',
        'body' => [
            'Para el jueves ya has fallado un día.',
            'Para el domingo ya lo dejaste.',
            'Quizá el problema no eres tú. Quizá es la racha.',
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
        'eyebrow' => 'Nuestra filosofía',
        'title' => 'El progreso vence a la perfección.',
        'items' => [
            'La constancia vence a la intensidad.',
            'Perder un día es normal.',
            'Abandonar es lo que queremos evitar.',
        ],
    ],
    'cta' => [
        'title' => 'No abandones solo porque perdiste un día.',
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
