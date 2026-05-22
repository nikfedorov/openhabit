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
        'title' => 'OpenHabit · Hábitos que se quedan',
        'description' => 'Un rastreador de hábitos tranquilo, dentro de Telegram. Sigue tus hábitos diarios, semanales y mensuales, recibe recordatorios suaves, observa tus rachas y reflexiona con un resumen diario de IA.',
        'og_description' => 'Un rastreador de hábitos tranquilo, dentro de Telegram. Marca el día. Lo demás llega solo.',
    ],
    'nav' => [
        'features' => 'Funciones',
        'compare' => 'Comparar',
        'news' => 'Novedades',
        'menu' => 'Menú',
        'how' => 'Cómo funciona',
        'faq' => 'FAQ',
        'cta_telegram' => 'Abrir en Telegram',
        'cta_app' => 'Abrir la app',
        'theme_light' => 'Cambiar a tema claro',
        'theme_dark' => 'Cambiar a tema oscuro',
        'language' => 'Idioma',
    ],
    'hero' => [
        'badge' => 'Hecho para Telegram',
        'title_pre' => 'Hábitos que',
        'title_accent' => 'se quedan.',
        'subtitle' => 'Un rastreador de hábitos tranquilo y enfocado, que vive dentro de Telegram. Sin notificaciones que griten ni culpa por romper una racha. Solo un espacio sereno donde marcar tu día, y lo demás llega solo.',
        'login_telegram' => 'Entrar con Telegram',
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
        'eyebrow' => 'Funciones',
        'title' => 'Solo lo que de verdad necesitas',
        'premium' => 'Premium',
        'items' => [
            'schedules' => [
                'title' => 'Horarios flexibles',
                'body' => 'Diario, semanal, mensual o «el segundo lunes del mes». Eliges los días, las fechas o las veces al día que mejor encajen con el hábito, no al revés.',
            ],
            'streaks' => [
                'title' => 'Rachas y mapa de calor',
                'body' => 'Tu constancia, de un vistazo. La cuadrícula muestra los días en los que apareciste y los que no. Sin juicios, solo la imagen.',
            ],
            'reminders' => [
                'title' => 'Recordatorios en Telegram',
                'body' => 'Avisos suaves, justo donde ya conversas. Uno por hábito, o varios si quieres. Nada salta a menos que tú lo pidas.',
            ],
            'notes' => [
                'title' => 'Notas del día',
                'body' => 'Un pequeño espacio opcional para anotar en una o dos frases cómo fue el día. Para que tus hábitos sean algo más que casillas marcadas: tengan historia.',
            ],
            'ai' => [
                'title' => 'Resumen con IA',
                'body' => 'Un resumen diario que detecta patrones que se te escaparían: qué está funcionando, qué empieza a aflojar y una breve idea de por qué.',
            ],
            'export' => [
                'title' => 'Exporta cuando quieras',
                'body' => 'Tus datos son tuyos. Descarga un CSV limpio con cada hábito y cada marca cuando te apetezca, sin tener que pedir permiso.',
            ],
        ],
    ],
    'digest_examples' => [
        'eyebrow' => 'AI Digest',
        'title' => 'Así es el resumen diario',
        'subtitle' => 'Ejemplos reales de lo que reciben los usuarios.',
        'items' => [
            [
                'date_machine' => '2026-05-20',
                'date' => '20 may. 2026',
                'paragraph_1' => 'Hoy hubo un progreso real. Rompiste el patrón de recaída en redes sociales y mantuviste los hábitos clave: madrugar, entrenar, meditar y trabajar en profundidad. Lo fuerte es la rapidez con la que te recuperas y aprendes de un día flojo.',
                'paragraph_2' => 'Las páginas matutinas se pierden cuando duermes tarde. Ese es el punto a cerrar. Esta noche escribe una oración sobre algo bueno que pasó hoy.',
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
                'paragraph_1' => 'Madrugar y entrenar completados, alimentación limpia. El lado físico aguantó. Pero la caída en redes sociales al mediodía desencadenó pérdida de enfoque y trabajo profundo perdido.',
                'paragraph_2' => 'La disciplina nutricional se mantiene incluso después de caídas, lo cual es un activo real. Para los límites digitales, añade fricción: cuando sientas el impulso de desplazarte, haz cinco sentadillas primero.',
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
        'title' => 'Tres pasos pequeños para empezar.',
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
                'title' => 'Marca el día, cada día',
                'body' => 'Un toque y listo. La racha crece sola. Reflexiona cuando te apetezca, no porque te lo pida la app.',
            ],
        ],
    ],
    'cta' => [
        'title' => 'Empieza hoy. Tu yo de mañana lo agradecerá',
        'subtitle' => 'Gratis para usar. Premium suma el resumen con IA, varios recordatorios por hábito y exportación CSV.',
    ],
    'compare' => [
        'eyebrow' => 'Comparativa',
        'title' => 'Diferente donde importa',
        'subtitle' => 'Cómo se compara OpenHabit con los rastreadores que la gente suele elegir. Las filas no están elegidas a dedo.',
        'caption' => 'Comparativa de rastreadores populares de hábitos',
        'criterion' => 'Criterio',
        'us_tag' => 'Nuestra elección',
        'rows' => [
            'telegram' => 'Vive en Telegram',
            'open_source' => 'Código abierto',
            'free' => 'Gratis, sin paywall',
            'free_habits' => 'Hábitos gratuitos',
            'ai' => 'Resumen diario con IA',
            'export' => 'Exportación en texto plano',
            'cross' => 'Funciona en todos los dispositivos',
        ],
        'cells' => [
            'paid' => 'De pago',
            'premium' => 'Premium',
            'freemium' => 'Freemium',
            'limited' => 'Limitada',
            'ios_only' => 'Solo iOS',
        ],
        'a11y' => [
            'yes' => 'Sí',
            'no' => 'No',
        ],
        'footnote' => 'Según información pública. Los nombres de productos pertenecen a sus respectivos dueños.',
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
        'week_desc' => 'Cada fila es un hábito',
        'year_desc' => 'Cada celda es una semana del año',
        'life_desc' => 'Cada celda es un año',
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
                'a' => 'Las funciones principales son completamente gratuitas, sin límite de tiempo ni de hábitos. Premium añade el resumen diario de IA, varios recordatorios por hábito en Telegram y exportación en CSV. Empieza gratis y decide después.',
            ],
            [
                'q' => '¿Necesito una cuenta de Telegram?',
                'a' => 'Sí. OpenHabit es un bot y una mini app de Telegram, así que solo necesitas una cuenta de Telegram. Sin registro adicional, sin contraseña que olvidar.',
            ],
            [
                'q' => '¿Qué es el resumen de IA?',
                'a' => 'Un breve mensaje diario que detecta patrones en tus hábitos: qué va bien, qué está decayendo y por qué. Analiza tus registros recientes y notas.',
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
