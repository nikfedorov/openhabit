<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CategoryTemplate;
use App\Models\HabitTemplate;
use App\Services\RRuleService;
use Illuminate\Database\Seeder;

final class HabitTemplateSeeder extends Seeder
{
    /**
     * @var array<int, array{name: array<string, string>, description: array<string, string>}>
     */
    private const array VIRTUES = [
        [
            'name' => [
                'en' => 'Temperance', 'ru' => 'Умеренность', 'es' => 'Templanza',
                'zh' => '节制', 'hi' => 'संयम', 'bn' => 'সংযম', 'pt' => 'Temperança', 'ar' => 'الاعتدال',
            ],
            'description' => [
                'en' => 'Eat not to dullness; drink not to elevation.',
                'ru' => 'Ешь не до отупения; пей не до возвышения.',
                'es' => 'No comas hasta el hartazgo; no bebas hasta la embriaguez.',
                'zh' => '食不过饱，饮不过量。',
                'hi' => 'जड़ता तक न खाओ; उन्माद तक न पियो।',
                'bn' => 'জড়তা পর্যন্ত খাবেন না; উন্মত্ততা পর্যন্ত পান করবেন না।',
                'pt' => 'Não coma até a letargia; não beba até a embriaguez.', 'ar' => 'لا تأكل حتى البلادة؛ ولا تشرب حتى السُّكر.',
            ],
        ],
        [
            'name' => [
                'en' => 'Silence', 'ru' => 'Молчание', 'es' => 'Silencio',
                'zh' => '沉默', 'hi' => 'मौन', 'bn' => 'নীরবতা', 'pt' => 'Silêncio', 'ar' => 'الصمت',
            ],
            'description' => [
                'en' => 'Speak not but what may benefit others or yourself; avoid trifling conversation.',
                'ru' => 'Говори лишь то, что может принести пользу другим или тебе; избегай пустых разговоров.',
                'es' => 'No hables sino lo que pueda beneficiar a otros o a ti mismo; evita conversaciones triviales.',
                'zh' => '只说有益于他人或自己的话；避免无聊的谈话。',
                'hi' => 'केवल वही बोलो जो दूसरों या तुम्हारे लिए लाभकारी हो; तुच्छ बातचीत से बचो।',
                'bn' => 'শুধু সেটাই বলুন যা অন্যদের বা আপনার উপকারে আসতে পারে; তুচ্ছ কথোপকথন এড়িয়ে চলুন।',
                'pt' => 'Fale apenas o que pode beneficiar outros ou você mesmo; evite conversas triviais.', 'ar' => 'لا تتحدث إلا بما قد يفيد الآخرين أو نفسك؛ تجنب الأحاديث التافهة.',
            ],
        ],
        [
            'name' => [
                'en' => 'Order', 'ru' => 'Порядок', 'es' => 'Orden',
                'zh' => '秩序', 'hi' => 'व्यवस्था', 'bn' => 'শৃঙ্খলা', 'pt' => 'Ordem', 'ar' => 'النظام',
            ],
            'description' => [
                'en' => 'Let all your things have their places; let each part of your business have its time.',
                'ru' => 'Пусть у каждой твоей вещи будет свое место; пусть каждая часть дела имеет свое время.',
                'es' => 'Que cada cosa tenga su lugar; que cada parte de tu trabajo tenga su tiempo.',
                'zh' => '让所有东西各有其位；让每项事务各有其时。',
                'hi' => 'सब चीजों को उनके स्थान पर रखो; प्रत्येक काम को उसका समय दो।',
                'bn' => 'সব কিছুর জন্য নির্দিষ্ট জায়গা রাখুন; প্রতিটি কাজের জন্য নির্দিষ্ট সময় রাখুন।',
                'pt' => 'Que tudo tenha seu lugar; que cada parte do seu trabalho tenha seu tempo.', 'ar' => 'دع كل أشيائك في أماكنها؛ ودع كل جزء من عملك له وقته.',
            ],
        ],
        [
            'name' => [
                'en' => 'Resolution', 'ru' => 'Решимость', 'es' => 'Resolución',
                'zh' => '决心', 'hi' => 'संकल्प', 'bn' => 'দৃঢ়তা', 'pt' => 'Resolução', 'ar' => 'العزيمة',
            ],
            'description' => [
                'en' => 'Resolve to perform what you ought; perform without fail what you resolve.',
                'ru' => 'Реши делать то, что должен; неуклонно выполняй то, что решил.',
                'es' => 'Resuelve realizar lo que debes; cumple sin falta lo que resuelvas.',
                'zh' => '下定决心做你应做之事；坚决执行你所决定的。',
                'hi' => 'जो करना चाहिए उसे करने का संकल्प करो; जो संकल्प किया उसे बिना चूक पूरा करो।',
                'bn' => 'যা করা উচিত তা করার সংকল্প করুন; যা সংকল্প করেছেন তা অবশ্যই পালন করুন।',
                'pt' => 'Resolva realizar o que deve; cumpra sem falta o que resolver.', 'ar' => 'اعقد العزم على فعل ما يجب عليك؛ ونفّذ بلا تردد ما عزمت عليه.',
            ],
        ],
        [
            'name' => [
                'en' => 'Frugality', 'ru' => 'Бережливость', 'es' => 'Frugalidad',
                'zh' => '节俭', 'hi' => 'मितव्ययिता', 'bn' => 'মিতব্যয়িতা', 'pt' => 'Frugalidade', 'ar' => 'التوفير',
            ],
            'description' => [
                'en' => 'Make no expense but to do good to others or yourself; waste nothing.',
                'ru' => 'Не трать ни на что, кроме добра другим или себе; ничего не расточай.',
                'es' => 'No hagas gastos sino para hacer bien a otros o a ti mismo; no desperdicies nada.',
                'zh' => '除了行善，不要花钱；不要浪费任何东西。',
                'hi' => 'दूसरों या स्वयं के लिए अच्छा करने के अलावा कोई खर्च मत करो; कुछ भी व्यर्थ मत करो।',
                'bn' => 'অন্যদের বা নিজের ভালোর জন্য ছাড়া কোনো খরচ করবেন না; কিছুই নষ্ট করবেন না।',
                'pt' => 'Não gaste senão para fazer bem aos outros ou a si mesmo; não desperdice nada.', 'ar' => 'لا تنفق إلا فيما يعود بالخير على الآخرين أو على نفسك؛ ولا تبذّر شيئًا.',
            ],
        ],
        [
            'name' => [
                'en' => 'Industry', 'ru' => 'Трудолюбие', 'es' => 'Industria',
                'zh' => '勤劳', 'hi' => 'उद्यमशीलता', 'bn' => 'পরিশ্রম', 'pt' => 'Diligência', 'ar' => 'الاجتهاد',
            ],
            'description' => [
                'en' => 'Lose no time; be always employed in something useful; cut off all unnecessary actions.',
                'ru' => 'Не теряй времени; всегда занимайся чем-то полезным; откажись от всех ненужных действий.',
                'es' => 'No pierdas el tiempo; ocúpate siempre en algo útil; elimina todas las acciones innecesarias.',
                'zh' => '不要浪费时间；总是做有用的事；切断不必要的行为。',
                'hi' => 'समय मत खोओ; हमेशा किसी उपयोगी काम में लगे रहो; सभी अनावश्यक कार्यों को काटो।',
                'bn' => 'সময় নষ্ট করবেন না; সর্বদা কিছু উপকারী কাজে নিয়োজিত থাকুন; অপ্রয়োজনীয় কাজ বন্ধ করুন।',
                'pt' => 'Não perca tempo; esteja sempre empregado em algo útil; elimine ações desnecessárias.', 'ar' => 'لا تضيّع الوقت؛ كن دائمًا منشغلًا بشيء مفيد؛ واقطع كل فعل غير ضروري.',
            ],
        ],
        [
            'name' => [
                'en' => 'Sincerity', 'ru' => 'Искренность', 'es' => 'Sinceridad',
                'zh' => '真诚', 'hi' => 'ईमानदारी', 'bn' => 'আন্তরিকতা', 'pt' => 'Sinceridade', 'ar' => 'الصدق',
            ],
            'description' => [
                'en' => 'Use no hurtful deceit; think innocently and justly, and if you speak, speak accordingly.',
                'ru' => 'Не используй обман, причиняющий вред; думай невинно и справедливо, и если говоришь — говори соответственно.',
                'es' => 'No uses engaños dañinos; piensa con inocencia y justicia, y si hablas, habla en consecuencia.',
                'zh' => '不使用有害的欺骗；以纯真和公正去思考，如果说话，就照此说。',
                'hi' => 'हानिकारक छल का प्रयोग मत करो; निर्दोष और न्यायपूर्ण सोचो, और यदि बोलो तो उसी के अनुसार बोलो।',
                'bn' => 'ক্ষতিকর প্রতারণা ব্যবহার করবেন না; নির্দোষভাবে এবং ন্যায়সঙ্গতভাবে চিন্তা করুন, এবং যদি কথা বলেন, সেই অনুযায়ী বলুন।',
                'pt' => 'Não use enganos prejudiciais; pense com inocência e justiça, e se falar, fale conforme.', 'ar' => 'لا تستخدم الخداع المؤذي؛ فكّر ببراءة وعدل، وإن تحدثت فتحدث بما يوافق ذلك.',
            ],
        ],
        [
            'name' => [
                'en' => 'Justice', 'ru' => 'Справедливость', 'es' => 'Justicia',
                'zh' => '正义', 'hi' => 'न्याय', 'bn' => 'ন্যায়বিচার', 'pt' => 'Justiça', 'ar' => 'العدل',
            ],
            'description' => [
                'en' => 'Wrong none by doing injuries, or omitting the benefits that are your duty.',
                'ru' => 'Не причиняй вреда никому и не упускай возможности делать добро, которое является твоим долгом.',
                'es' => 'No perjudiques a nadie causando daños u omitiendo los beneficios que son tu deber.',
                'zh' => '不要通过伤害或忽略应尽的责任来冤枉任何人。',
                'hi' => 'किसी को चोट पहुँचाकर या अपने कर्तव्य के लाभ देने से चूककर अन्याय मत करो।',
                'bn' => 'কাউকে আঘাত করে বা আপনার কর্তব্যের সুবিধা দিতে ব্যর্থ হয়ে কারো ক্ষতি করবেন না।',
                'pt' => 'Não prejudique ninguém causando danos ou omitindo os benefícios que são seu dever.', 'ar' => 'لا تظلم أحدًا بإلحاق الأذى أو بإهمال الخير الذي هو واجبك.',
            ],
        ],
        [
            'name' => [
                'en' => 'Moderation', 'ru' => 'Умеренность', 'es' => 'Moderación',
                'zh' => '适度', 'hi' => 'संतुलन', 'bn' => 'সংযত', 'pt' => 'Moderação', 'ar' => 'الاعتدال في الأمور',
            ],
            'description' => [
                'en' => 'Avoid extremes; forbear resenting injuries so much as you think they deserve.',
                'ru' => 'Избегай крайностей; воздерживайся от обиды на оскорбления.',
                'es' => 'Evita los extremos; abstente de resentir las ofensas tanto como crees que merecen.',
                'zh' => '避免极端；不要对伤害耿耿于怀。',
                'hi' => 'अतिवाद से बचो; चोटों का उतना बुरा मत मानो जितना तुम सोचते हो कि वे योग्य हैं।',
                'bn' => 'চরমপন্থা এড়িয়ে চলুন; আঘাতের প্রতি ততটা ক্ষোভ করবেন না যতটা আপনি মনে করেন তারা প্রাপ্য।',
                'pt' => 'Evite extremos; abstenha-se de ressentir ofensas tanto quanto achar que merecem.', 'ar' => 'تجنب التطرف؛ وامتنع عن الحقد على الإساءات بقدر ما تظن أنها تستحق.',
            ],
        ],
        [
            'name' => [
                'en' => 'Cleanliness', 'ru' => 'Чистота', 'es' => 'Limpieza',
                'zh' => '清洁', 'hi' => 'स्वच्छता', 'bn' => 'পরিচ্ছন্নতা', 'pt' => 'Limpeza', 'ar' => 'النظافة',
            ],
            'description' => [
                'en' => 'Tolerate no uncleanliness in body, clothes, or habitation.',
                'ru' => 'Не допускай нечистоты тела, одежды или жилища.',
                'es' => 'No toleres la suciedad en el cuerpo, la ropa o la vivienda.',
                'zh' => '不容忍身体、衣物或住所的不洁。',
                'hi' => 'शरीर, कपड़ों या निवास में अस्वच्छता सहन मत करो।',
                'bn' => 'শরীর, পোশাক বা বাসস্থানে অপরিচ্ছন্নতা সহ্য করবেন না।',
                'pt' => 'Não tolere sujeira no corpo, nas roupas ou na moradia.', 'ar' => 'لا تتسامح مع عدم النظافة في الجسد أو الملابس أو المسكن.',
            ],
        ],
        [
            'name' => [
                'en' => 'Tranquility', 'ru' => 'Спокойствие', 'es' => 'Tranquilidad',
                'zh' => '平静', 'hi' => 'शांति', 'bn' => 'প্রশান্তি', 'pt' => 'Tranquilidade', 'ar' => 'الطمأنينة',
            ],
            'description' => [
                'en' => 'Be not disturbed at trifles, or at accidents common or unavoidable.',
                'ru' => 'Не волнуйся из-за пустяков или обычных и неизбежных происшествий.',
                'es' => 'No te perturbes por nimiedades ni por accidentes comunes o inevitables.',
                'zh' => '不要因琐事或常见且不可避免的意外而烦恼。',
                'hi' => 'छोटी बातों या सामान्य और अपरिहार्य दुर्घटनाओं से विचलित मत हो।',
                'bn' => 'তুচ্ছ বিষয়ে বা সাধারণ ও অনিবার্য দুর্ঘটনায় বিচলিত হবেন না।',
                'pt' => 'Não se perturbe com trivialidades ou com acidentes comuns ou inevitáveis.', 'ar' => 'لا تنزعج من التوافه أو الحوادث العادية أو التي لا مفر منها.',
            ],
        ],
        [
            'name' => [
                'en' => 'Chastity', 'ru' => 'Целомудрие', 'es' => 'Castidad',
                'zh' => '贞洁', 'hi' => 'शुचिता', 'bn' => 'সতীত্ব', 'pt' => 'Castidade', 'ar' => 'العفة',
            ],
            'description' => [
                'en' => "Rarely use venery but for health or offspring, never to dullness, weakness, or the injury of your own or another's peace or reputation.",
                'ru' => 'Прибегай к плотским утехам лишь ради здоровья или потомства, никогда — до отупения, слабости или во вред покою и репутации.',
                'es' => 'Raramente recurre al placer carnal sino para la salud o la descendencia; nunca hasta el hastío o la debilidad.',
                'zh' => '除了为健康或后代，少行房事；绝不可至愚钝、虚弱，或损害自己或他人的安宁和名誉。',
                'hi' => 'केवल स्वास्थ्य या संतान के लिए ही काम-सुख का सेवन करो; कभी मंदता या दुर्बलता तक नहीं।',
                'bn' => 'শুধুমাত্র স্বাস্থ্য বা সন্তানের জন্যই কামসুখ গ্রহণ করুন; কখনোই জড়তা বা দুর্বলতা পর্যন্ত নয়।',
                'pt' => 'Raramente recorra aos prazeres carnais, senão para a saúde ou descendência; nunca até o torpor ou fraqueza.', 'ar' => 'نادرًا ما تلجأ للملذات الجسدية إلا من أجل الصحة أو النسل؛ ولا تصل أبدًا إلى البلادة أو الضعف.',
            ],
        ],
        [
            'name' => [
                'en' => 'Humility', 'ru' => 'Смирение', 'es' => 'Humildad',
                'zh' => '谦逊', 'hi' => 'विनम्रता', 'bn' => 'বিনয়', 'pt' => 'Humildade', 'ar' => 'التواضع',
            ],
            'description' => [
                'en' => 'Imitate Jesus and Socrates.',
                'ru' => 'Подражай Иисусу и Сократу.',
                'es' => 'Imita a Jesús y a Sócrates.',
                'zh' => '效仿耶稣和苏格拉底。',
                'hi' => 'ईसा और सुकरात का अनुसरण करो।',
                'bn' => 'যীশু ও সক্রেটিসকে অনুসরণ করুন।',
                'pt' => 'Imite Jesus e Sócrates.', 'ar' => 'اقتدِ بعيسى وسقراط.',
            ],
        ],
    ];

    /**
     * @var array<string, array{slug: string, name: array<string, string>, description: array<string, string>, sort_order: int}>
     */
    private const array CATEGORIES = [
        'franklins-virtues' => [
            'slug' => 'franklins-virtues',
            'name' => [
                'en' => "Franklin's Virtues", 'ru' => 'Добродетели Франклина', 'es' => 'Virtudes de Franklin',
                'zh' => '富兰克林的美德', 'hi' => 'फ्रैंकलिन के गुण', 'bn' => 'ফ্রাঙ্কলিনের গুণাবলি', 'pt' => 'Virtudes de Franklin', 'ar' => 'فضائل فرانكلين',
            ],
            'description' => [
                'en' => "Benjamin Franklin's 13 virtues for moral perfection. Each virtue is practiced for one week in a 13-week cycle, repeating 4 times per year.",
                'ru' => '13 добродетелей Бенджамина Франклина для нравственного совершенствования. Каждая добродетель практикуется одну неделю в 13-недельном цикле, повторяясь 4 раза в год.',
                'es' => 'Las 13 virtudes de Benjamin Franklin para la perfección moral. Cada virtud se practica durante una semana en un ciclo de 13 semanas, repitiéndose 4 veces al año.',
                'zh' => '本杰明·富兰克林的13条美德，追求道德完善。每条美德在13周周期中练习一周，每年重复4次。',
                'hi' => 'बेंजामिन फ्रैंकलिन के नैतिक पूर्णता के 13 गुण। प्रत्येक गुण 13 सप्ताह के चक्र में एक सप्ताह अभ्यास किया जाता है, वर्ष में 4 बार दोहराया जाता है।',
                'bn' => 'বেঞ্জামিন ফ্রাঙ্কলিনের নৈতিক পূর্ণতার ১৩টি গুণ। প্রতিটি গুণ ১৩ সপ্তাহের চক্রে এক সপ্তাহ অনুশীলন করা হয়, বছরে ৪ বার পুনরাবৃত্তি হয়।',
                'pt' => 'As 13 virtudes de Benjamin Franklin para a perfeição moral. Cada virtude é praticada por uma semana em um ciclo de 13 semanas, repetindo 4 vezes por ano.', 'ar' => 'فضائل بنجامين فرانكلين الثلاث عشرة للكمال الأخلاقي. تُمارَس كل فضيلة لمدة أسبوع في دورة من 13 أسبوعًا، تتكرر 4 مرات سنويًا.',
            ],
            'sort_order' => 1,
        ],
        'mindfulness' => [
            'slug' => 'mindfulness',
            'name' => [
                'en' => 'Mindfulness', 'ru' => 'Осознанность', 'es' => 'Atención plena',
                'zh' => '正念', 'hi' => 'सचेतनता', 'bn' => 'মননশীলতা', 'pt' => 'Atenção plena', 'ar' => 'اليقظة الذهنية',
            ],
            'description' => [
                'en' => 'Habits for mental clarity, self-awareness, and inner peace.',
                'ru' => 'Привычки для ясности ума, самоосознания и внутреннего покоя.',
                'es' => 'Hábitos para la claridad mental, la autoconciencia y la paz interior.',
                'zh' => '培养心理清明、自我意识和内心平静的习惯。',
                'hi' => 'मानसिक स्पष्टता, आत्म-जागरूकता और आंतरिक शांति के लिए आदतें।',
                'bn' => 'মানসিক স্বচ্ছতা, আত্ম-সচেতনতা এবং অন্তর্শান্তির জন্য অভ্যাস।',
                'pt' => 'Hábitos para clareza mental, autoconhecimento e paz interior.', 'ar' => 'عادات للصفاء الذهني والوعي الذاتي والسلام الداخلي.',
            ],
            'sort_order' => 2,
        ],
        'health-fitness' => [
            'slug' => 'health-fitness',
            'name' => [
                'en' => 'Health & Fitness', 'ru' => 'Здоровье и фитнес', 'es' => 'Salud y fitness',
                'zh' => '健康与健身', 'hi' => 'स्वास्थ्य और फिटनेस', 'bn' => 'স্বাস্থ্য ও ফিটনেস', 'pt' => 'Saúde e Fitness', 'ar' => 'الصحة واللياقة',
            ],
            'description' => [
                'en' => 'Physical health, exercise, nutrition, and body care habits.',
                'ru' => 'Привычки для физического здоровья, упражнений, питания и заботы о теле.',
                'es' => 'Hábitos de salud física, ejercicio, nutrición y cuidado corporal.',
                'zh' => '身体健康、锻炼、营养和身体护理习惯。',
                'hi' => 'शारीरिक स्वास्थ्य, व्यायाम, पोषण और शरीर की देखभाल की आदतें।',
                'bn' => 'শারীরিক স্বাস্থ্য, ব্যায়াম, পুষ্টি এবং শরীরের যত্নের অভ্যাস।',
                'pt' => 'Hábitos de saúde física, exercícios, nutrição e cuidados com o corpo.', 'ar' => 'عادات الصحة البدنية والتمارين والتغذية والعناية بالجسم.',
            ],
            'sort_order' => 3,
        ],
        'productivity' => [
            'slug' => 'productivity',
            'name' => [
                'en' => 'Productivity', 'ru' => 'Продуктивность', 'es' => 'Productividad',
                'zh' => '生产力', 'hi' => 'उत्पादकता', 'bn' => 'উৎপাদনশীলতা', 'pt' => 'Produtividade', 'ar' => 'الإنتاجية',
            ],
            'description' => [
                'en' => 'Habits for focus, efficiency, and getting things done.',
                'ru' => 'Привычки для концентрации, эффективности и достижения целей.',
                'es' => 'Hábitos para el enfoque, la eficiencia y la productividad.',
                'zh' => '专注、效率和完成任务的习惯。',
                'hi' => 'ध्यान, दक्षता और कार्य पूरा करने की आदतें।',
                'bn' => 'মনোযোগ, দক্ষতা এবং কাজ সম্পন্ন করার অভ্যাস।',
                'pt' => 'Hábitos para foco, eficiência e produtividade.', 'ar' => 'عادات للتركيز والكفاءة وإنجاز المهام.',
            ],
            'sort_order' => 4,
        ],
        'learning-growth' => [
            'slug' => 'learning-growth',
            'name' => [
                'en' => 'Learning & Growth', 'ru' => 'Обучение и развитие', 'es' => 'Aprendizaje y crecimiento',
                'zh' => '学习与成长', 'hi' => 'सीखना और विकास', 'bn' => 'শিক্ষা ও বিকাশ', 'pt' => 'Aprendizado e Crescimento', 'ar' => 'التعلم والنمو',
            ],
            'description' => [
                'en' => 'Continuous learning, skill development, and personal growth.',
                'ru' => 'Непрерывное обучение, развитие навыков и личностный рост.',
                'es' => 'Aprendizaje continuo, desarrollo de habilidades y crecimiento personal.',
                'zh' => '持续学习、技能发展和个人成长。',
                'hi' => 'निरंतर सीखना, कौशल विकास और व्यक्तिगत विकास।',
                'bn' => 'ক্রমাগত শিক্ষা, দক্ষতা উন্নয়ন এবং ব্যক্তিগত বিকাশ।',
                'pt' => 'Aprendizado contínuo, desenvolvimento de habilidades e crescimento pessoal.', 'ar' => 'التعلم المستمر وتطوير المهارات والنمو الشخصي.',
            ],
            'sort_order' => 5,
        ],
        'social-relationships' => [
            'slug' => 'social-relationships',
            'name' => [
                'en' => 'Social & Relationships', 'ru' => 'Общение и отношения', 'es' => 'Social y relaciones',
                'zh' => '社交与关系', 'hi' => 'सामाजिक और रिश्ते', 'bn' => 'সামাজিক ও সম্পর্ক', 'pt' => 'Social e Relacionamentos', 'ar' => 'العلاقات الاجتماعية',
            ],
            'description' => [
                'en' => 'Connecting with others, family, and community.',
                'ru' => 'Связь с другими, семьёй и обществом.',
                'es' => 'Conexión con otros, familia y comunidad.',
                'zh' => '与他人、家人和社区的联系。',
                'hi' => 'दूसरों, परिवार और समुदाय से जुड़ना।',
                'bn' => 'অন্যদের, পরিবার এবং সম্প্রদায়ের সাথে সংযোগ।',
                'pt' => 'Conexão com outros, família e comunidade.', 'ar' => 'التواصل مع الآخرين والعائلة والمجتمع.',
            ],
            'sort_order' => 6,
        ],
    ];

    public function __construct(
        private readonly RRuleService $rruleService
    ) {}

    public function run(): void
    {
        $categories = $this->createCategories();
        $this->createFranklinVirtues($categories['franklins-virtues']);
        $this->createHabits($categories);
    }

    /**
     * @return array<string, CategoryTemplate>
     */
    private function createCategories(): array
    {
        $result = [];

        foreach (self::CATEGORIES as $key => $data) {
            $result[$key] = CategoryTemplate::query()->updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'is_active' => true,
                    'copy_by_default' => $key === 'franklins-virtues',
                    'sort_order' => $data['sort_order'],
                ]
            );
        }

        return $result;
    }

    private function createFranklinVirtues(CategoryTemplate $category): void
    {
        foreach (self::VIRTUES as $index => $virtue) {
            $template = HabitTemplate::query()->updateOrCreate(
                ['category_template_id' => $category->id, 'sort_order' => $index + 1],
                [
                    'name' => $virtue['name'],
                    'description' => $virtue['description'],
                    'category_template_id' => $category->id,
                    'is_active' => true,
                    'copy_by_default' => true,
                    'show_in_templates' => false,
                    'sort_order' => $index + 1,
                ]
            );

            $weeksOfYear = [];
            for ($cycle = 0; $cycle < 4; $cycle++) {
                $weeksOfYear[] = ($index + 1) + ($cycle * 13);
            }

            $rrule = $this->rruleService->buildYearly($weeksOfYear);

            $template->update(['rrule' => $rrule]);
        }
    }

    /**
     * @param  array<string, CategoryTemplate>  $categories
     */
    private function createHabits(array $categories): void
    {
        $sortOrder = 100;

        $this->createHabit(
            category: $categories['health-fitness'],
            name: [
                'en' => 'Wake up at 7 AM or earlier', 'ru' => 'Подъём в 7 утра или раньше',
                'es' => 'Despertar a las 7 AM o antes', 'zh' => '早上7点或更早起床',
                'hi' => 'सुबह 7 बजे या उससे पहले उठें', 'bn' => 'সকাল ৭টা বা তার আগে উঠুন',
                'pt' => 'Acordar às 7h ou mais cedo', 'ar' => 'الاستيقاظ في الساعة 7 صباحًا أو قبل ذلك',
            ],
            description: [
                'en' => 'Start your day early to maximize productivity and establish a consistent routine.',
                'ru' => 'Начинай день рано, чтобы максимизировать продуктивность и установить постоянный распорядок.',
                'es' => 'Empieza el día temprano para maximizar la productividad y establecer una rutina consistente.',
                'zh' => '早起以最大化生产力并建立一致的日常作息。',
                'hi' => 'उत्पादकता को अधिकतम करने और एक निरंतर दिनचर्या स्थापित करने के लिए अपना दिन जल्दी शुरू करें।',
                'bn' => 'উৎপাদনশীলতা বাড়াতে এবং সুসংগত রুটিন তৈরি করতে তাড়াতাড়ি দিন শুরু করুন।',
                'pt' => 'Comece o dia cedo para maximizar a produtividade e estabelecer uma rotina consistente.', 'ar' => 'ابدأ يومك مبكرًا لتعظيم الإنتاجية وبناء روتين ثابت.',
            ],
            rrule: $this->rruleService->buildDaily(),
            sortOrder: $sortOrder++,
            copyByDefault: true,
        );
        $this->createHabit(
            category: $categories['health-fitness'],
            name: [
                'en' => 'Workout, 60 min', 'ru' => 'Тренировка, 60 мин',
                'es' => 'Ejercicio, 60 min', 'zh' => '锻炼，60分钟',
                'hi' => 'व्यायाम, 60 मिनट', 'bn' => 'ব্যায়াম, ৬০ মিনিট',
                'pt' => 'Treino, 60 min', 'ar' => 'تمرين، 60 دقيقة',
            ],
            description: [
                'en' => 'Exercise for 60 minutes: strength training, cardio, sports, or any physical activity.',
                'ru' => 'Занимайся 60 минут: силовые тренировки, кардио, спорт или любая физическая активность.',
                'es' => 'Ejercítate 60 minutos: entrenamiento de fuerza, cardio, deportes o cualquier actividad física.',
                'zh' => '锻炼60分钟：力量训练、有氧运动、体育运动或任何身体活动。',
                'hi' => '60 मिनट व्यायाम करें: शक्ति प्रशिक्षण, कार्डियो, खेल या कोई भी शारीरिक गतिविधि।',
                'bn' => '৬০ মিনিট ব্যায়াম করুন: শক্তি প্রশিক্ষণ, কার্ডিও, খেলাধুলা বা যেকোনো শারীরিক কার্যকলাপ।',
                'pt' => 'Exercite-se por 60 minutos: musculação, cardio, esportes ou qualquer atividade física.', 'ar' => 'تمرن لمدة 60 دقيقة: تدريب قوة، كارديو، رياضة، أو أي نشاط بدني.',
            ],
            rrule: $this->rruleService->buildWeekly([0, 1, 2, 3, 4, 5]),
            sortOrder: $sortOrder++,
            copyByDefault: true,
        );
        $this->createHabit(
            category: $categories['mindfulness'],
            name: [
                'en' => 'Meditate 10-20 min', 'ru' => 'Медитация 10-20 мин',
                'es' => 'Meditación 10-20 min', 'zh' => '冥想10-20分钟',
                'hi' => '10-20 मिनट ध्यान करें', 'bn' => '১০-২০ মিনিট ধ্যান করুন',
                'pt' => 'Meditar 10-20 min', 'ar' => 'تأمل 10-20 دقيقة',
            ],
            description: [
                'en' => 'Enhance your day with 10-20 minutes of meditation to refresh intention and clarity.',
                'ru' => 'Улучши свой день 10-20 минутами медитации для обновления намерений и ясности.',
                'es' => 'Mejora tu día con 10-20 minutos de meditación para renovar la intención y la claridad.',
                'zh' => '通过10-20分钟的冥想来提升你的一天，刷新意图和清晰度。',
                'hi' => '10-20 मिनट के ध्यान से अपने दिन को बेहतर बनाएं ताकि इरादे और स्पष्टता ताज़ा हो।',
                'bn' => '১০-২০ মিনিটের ধ্যানে আপনার দিন উন্নত করুন, উদ্দেশ্য ও স্বচ্ছতা সতেজ করতে।',
                'pt' => 'Melhore seu dia com 10-20 minutos de meditação para renovar intenção e clareza.', 'ar' => 'حسّن يومك بـ 10-20 دقيقة من التأمل لتجديد النية والوضوح.',
            ],
            rrule: $this->rruleService->buildDaily(),
            sortOrder: $sortOrder++,
            copyByDefault: true,
        );
        $this->createHabit(
            category: $categories['mindfulness'],
            name: [
                'en' => 'Morning pages: write 3 pages', 'ru' => 'Утренние страницы: написать 3 страницы',
                'es' => 'Páginas matutinas: escribir 3 páginas', 'zh' => '晨间日记：写3页',
                'hi' => 'सुबह के पन्ने: 3 पन्ने लिखें', 'bn' => 'সকালের পাতা: ৩ পাতা লিখুন',
                'pt' => 'Páginas matinais: escrever 3 páginas', 'ar' => 'صفحات الصباح: اكتب 3 صفحات',
            ],
            description: [
                'en' => 'Write 3 pages of stream-of-consciousness journaling first thing in the morning to clear your mind and spark creativity.',
                'ru' => 'Напиши 3 страницы потока сознания утром первым делом, чтобы очистить ум и пробудить творчество.',
                'es' => 'Escribe 3 páginas de diario de flujo de conciencia a primera hora de la mañana para aclarar la mente y despertar la creatividad.',
                'zh' => '早晨第一件事写3页意识流日记，清理思绪并激发创造力。',
                'hi' => 'सुबह सबसे पहले 3 पन्ने चेतना-प्रवाह पत्रिका लिखें ताकि मन साफ़ हो और रचनात्मकता जागे।',
                'bn' => 'সকালে প্রথম কাজ হিসেবে ৩ পাতা চেতনা-প্রবাহ জার্নালিং লিখুন, মন পরিষ্কার করতে ও সৃজনশীলতা জাগাতে।',
                'pt' => 'Escreva 3 páginas de escrita livre logo pela manhã para limpar a mente e despertar a criatividade.', 'ar' => 'اكتب 3 صفحات من الكتابة الحرة في الصباح لتصفية الذهن وإيقاظ الإبداع.',
            ],
            rrule: $this->rruleService->buildDaily(),
            sortOrder: $sortOrder++,
            copyByDefault: true,
        );

        $this->createHabit(
            category: $categories['health-fitness'],
            name: [
                'en' => 'Drink a glass of water', 'ru' => 'Выпить стакан воды',
                'es' => 'Beber un vaso de agua', 'zh' => '喝一杯水',
                'hi' => 'एक गिलास पानी पिएं', 'bn' => 'এক গ্লাস পানি পান করুন',
                'pt' => 'Beber um copo de água', 'ar' => 'اشرب كوب ماء',
            ],
            description: [
                'en' => 'Stay hydrated by drinking 8 glasses of water throughout the day.',
                'ru' => 'Поддерживай водный баланс, выпивая 8 стаканов воды в течение дня.',
                'es' => 'Mantente hidratado bebiendo 8 vasos de agua a lo largo del día.',
                'zh' => '全天喝8杯水保持水分充足。',
                'hi' => 'दिन भर में 8 गिलास पानी पीकर हाइड्रेटेड रहें।',
                'bn' => 'দিনভর ৮ গ্লাস পানি পান করে হাইড্রেটেড থাকুন।',
                'pt' => 'Mantenha-se hidratado bebendo 8 copos de água ao longo do dia.', 'ar' => 'حافظ على ترطيب جسمك بشرب 8 أكواب ماء على مدار اليوم.',
            ],
            rrule: $this->rruleService->buildDaily(),
            sortOrder: $sortOrder++,
            iterationsRequired: 8,
        );

        $this->createHabit(
            category: $categories['productivity'],
            name: [
                'en' => 'Deep work: 2-4h of focused effort', 'ru' => 'Глубокая работа: 2-4ч фокуса',
                'es' => 'Trabajo profundo: 2-4h de esfuerzo enfocado', 'zh' => '深度工作：2-4小时专注努力',
                'hi' => 'गहन कार्य: 2-4 घंटे केंद्रित प्रयास', 'bn' => 'গভীর কাজ: ২-৪ ঘণ্টা মনোযোগী প্রচেষ্টা',
                'pt' => 'Trabalho profundo: 2-4h de esforço focado', 'ar' => 'عمل عميق: 2-4 ساعات من الجهد المركّز',
            ],
            description: [
                'en' => 'Dedicate 2-4 hours to focused, uninterrupted work on your most important task.',
                'ru' => 'Посвяти 2-4 часа сосредоточенной, непрерывной работе над самой важной задачей.',
                'es' => 'Dedica 2-4 horas a trabajo enfocado e ininterrumpido en tu tarea más importante.',
                'zh' => '将2-4小时投入到最重要任务的专注、不间断工作中。',
                'hi' => 'अपने सबसे महत्वपूर्ण कार्य पर 2-4 घंटे केंद्रित, निर्बाध काम करें।',
                'bn' => 'আপনার সবচেয়ে গুরুত্বপূর্ণ কাজে ২-৪ ঘণ্টা মনোযোগী, নিরবচ্ছিন্ন কাজ করুন।',
                'pt' => 'Dedique 2-4 horas de trabalho focado e ininterrupto na sua tarefa mais importante.', 'ar' => 'خصص 2-4 ساعات للعمل المركّز وغير المنقطع على أهم مهامك.',
            ],
            rrule: $this->rruleService->buildWeekly([0, 1, 2, 3, 4]),
            sortOrder: $sortOrder++,
            copyByDefault: true,
        );
        $this->createHabit(
            category: $categories['productivity'],
            name: [
                'en' => 'No social media & news until 6 PM', 'ru' => 'Без соцсетей и новостей до 18:00',
                'es' => 'Sin redes sociales ni noticias hasta las 18h', 'zh' => '下午6点前不看社交媒体和新闻',
                'hi' => 'शाम 6 बजे तक सोशल मीडिया और समाचार नहीं', 'bn' => 'সন্ধ্যা ৬টা পর্যন্ত সোশ্যাল মিডিয়া ও সংবাদ নয়',
                'pt' => 'Sem redes sociais e notícias até as 18h', 'ar' => 'بدون وسائل التواصل والأخبار حتى السادسة مساءً',
            ],
            description: [
                'en' => 'Avoid social media and news until 6 PM.',
                'ru' => 'Избегай соцсетей и новостей до 18:00.',
                'es' => 'Evita las redes sociales y las noticias hasta las 18h.',
                'zh' => '下午6点之前避免社交媒体和新闻。',
                'hi' => 'शाम 6 बजे तक सोशल मीडिया और समाचार से बचें।',
                'bn' => 'সন্ধ্যা ৬টা পর্যন্ত সোশ্যাল মিডিয়া আর সংবাদ এড়িয়ে চলুন।',
                'pt' => 'Evite redes sociais e notícias até as 18h.', 'ar' => 'تجنب وسائل التواصل الاجتماعي والأخبار حتى السادسة مساءً.',
            ],
            rrule: $this->rruleService->buildWeekly([0, 1, 2, 3, 4]),
            sortOrder: $sortOrder++,
            copyByDefault: true,
        );

        $this->createHabit(
            category: $categories['health-fitness'],
            name: [
                'en' => 'Eat whole foods, skip processed & sugar', 'ru' => 'Ешь цельные продукты, избегай переработанных и сахара',
                'es' => 'Come alimentos integrales, evita procesados y azúcar', 'zh' => '吃天然食物，避免加工食品和糖',
                'hi' => 'संपूर्ण खाद्य खाएं, प्रसंस्कृत भोजन और चीनी छोड़ें', 'bn' => 'পুষ্টিকর খাবার খান, প্রক্রিয়াজাত ও চিনি এড়িয়ে চলুন',
                'pt' => 'Coma alimentos integrais, evite processados e açúcar', 'ar' => 'تناول الأطعمة الطبيعية، تجنب المعالجة والسكر',
            ],
            description: [
                'en' => 'Eat whole foods, stay hydrated, avoid processed food and excessive sugar.',
                'ru' => 'Ешь цельные продукты, поддерживай водный баланс, избегай переработанной еды и лишнего сахара.',
                'es' => 'Come alimentos integrales, mantente hidratado, evita alimentos procesados y azúcar excesiva.',
                'zh' => '吃天然食物，保持水分，避免加工食品和过多糖分。',
                'hi' => 'संपूर्ण खाद्य खाएं, हाइड्रेटेड रहें, प्रसंस्कृत भोजन और अतिरिक्त चीनी से बचें।',
                'bn' => 'পুষ্টিকর খাবার খান, হাইড্রেটেড থাকুন, প্রক্রিয়াজাত খাবার ও অতিরিক্ত চিনি এড়িয়ে চলুন।',
                'pt' => 'Coma alimentos integrais, mantenha-se hidratado, evite alimentos processados e açúcar em excesso.', 'ar' => 'تناول الأطعمة الطبيعية، حافظ على الترطيب، تجنب الأطعمة المعالجة والسكر الزائد.',
            ],
            rrule: $this->rruleService->buildDaily(),
            sortOrder: $sortOrder++,
            copyByDefault: true,
        );
        $this->createHabit(
            category: $categories['learning-growth'],
            name: [
                'en' => 'Practice a skill: language, craft, instrument', 'ru' => 'Практикуй навык: язык, ремесло, инструмент',
                'es' => 'Practica una habilidad: idioma, oficio, instrumento', 'zh' => '练习一项技能：语言、手艺、乐器',
                'hi' => 'एक कौशल का अभ्यास करें: भाषा, शिल्प, वाद्य', 'bn' => 'একটি দক্ষতা অনুশীলন করুন: ভাষা, কারুশিল্প, বাদ্যযন্ত্র',
                'pt' => 'Pratique uma habilidade: idioma, artesanato, instrumento', 'ar' => 'تدرّب على مهارة: لغة، حرفة، آلة موسيقية',
            ],
            description: [
                'en' => 'Practice a skill intentionally: language, instrument, craft, or profession.',
                'ru' => 'Осознанно практикуй навык: язык, инструмент, ремесло или профессию.',
                'es' => 'Practica una habilidad intencionalmente: idioma, instrumento, oficio o profesión.',
                'zh' => '有意识地练习一项技能：语言、乐器、手艺或专业。',
                'hi' => 'जानबूझकर एक कौशल का अभ्यास करें: भाषा, वाद्य, शिल्प या पेशा।',
                'bn' => 'সচেতনভাবে একটি দক্ষতা অনুশীলন করুন: ভাষা, বাদ্যযন্ত্র, কারুশিল্প বা পেশা।',
                'pt' => 'Pratique uma habilidade intencionalmente: idioma, instrumento, artesanato ou profissão.', 'ar' => 'تدرّب على مهارة بوعي: لغة، آلة موسيقية، حرفة أو مهنة.',
            ],
            rrule: $this->rruleService->buildWeekly([0, 1, 2, 3, 4]),
            sortOrder: $sortOrder++,
        );
        $this->createHabit(
            category: $categories['social-relationships'],
            name: [
                'en' => 'Quality time with loved ones, phones off', 'ru' => 'Время с близкими, телефоны выключены',
                'es' => 'Tiempo de calidad con seres queridos, sin teléfonos', 'zh' => '和亲人共度美好时光，关闭手机',
                'hi' => 'प्रियजनों के साथ गुणवत्ता समय, फोन बंद', 'bn' => 'প্রিয়জনদের সাথে মানসম্মত সময়, ফোন বন্ধ',
                'pt' => 'Tempo de qualidade com entes queridos, sem celular', 'ar' => 'وقت نوعي مع الأحبة، بدون هواتف',
            ],
            description: [
                'en' => 'Be fully present with family or friends. No phones, no distractions.',
                'ru' => 'Будь полностью присутствующим с семьёй или друзьями. Без телефонов, без отвлечений.',
                'es' => 'Estar plenamente presente con familia o amigos. Sin teléfonos, sin distracciones.',
                'zh' => '全身心陪伴家人或朋友。不用手机，不受干扰。',
                'hi' => 'परिवार या दोस्तों के साथ पूरी तरह उपस्थित रहें। फोन नहीं, कोई विचलन नहीं।',
                'bn' => 'পরিবার বা বন্ধুদের সাথে পূর্ণ উপস্থিত থাকুন। ফোন নেই, কোনো বিচ্যুতি নেই।',
                'pt' => 'Esteja totalmente presente com família ou amigos. Sem celulares, sem distrações.', 'ar' => 'كن حاضرًا بالكامل مع العائلة أو الأصدقاء. بدون هواتف، بدون مشتتات.',
            ],
            rrule: $this->rruleService->buildWeekly([5, 6]),
            sortOrder: $sortOrder++,
        );
        $this->createHabit(
            category: $categories['social-relationships'],
            name: [
                'en' => 'Reach out to a friend or family member', 'ru' => 'Позвони другу или родственнику',
                'es' => 'Contactar a un amigo o familiar', 'zh' => '联系一位朋友或家人',
                'hi' => 'किसी मित्र या परिवार के सदस्य से संपर्क करें', 'bn' => 'একজন বন্ধু বা পরিবারের সদস্যের সাথে যোগাযোগ করুন',
                'pt' => 'Entre em contato com um amigo ou familiar', 'ar' => 'تواصل مع صديق أو أحد أفراد العائلة',
            ],
            description: [
                'en' => 'Call, text, or meet a friend or family member to nurture the relationship.',
                'ru' => 'Позвони, напиши или встреться с другом или родственником, чтобы укрепить отношения.',
                'es' => 'Llama, escribe o reúnete con un amigo o familiar para nutrir la relación.',
                'zh' => '打电话、发信息或见面以培养关系。',
                'hi' => 'रिश्ते को मजबूत करने के लिए किसी मित्र या परिवार के सदस्य को कॉल करें, संदेश भेजें या मिलें।',
                'bn' => 'সম্পর্ক মজবুত করতে একজন বন্ধু বা পরিবারের সদস্যকে কল করুন, মেসেজ পাঠান বা দেখা করুন।',
                'pt' => 'Ligue, mande mensagem ou encontre um amigo ou familiar para fortalecer o relacionamento.', 'ar' => 'اتصل أو أرسل رسالة أو قابل صديقًا أو فردًا من العائلة لتعزيز العلاقة.',
            ],
            rrule: $this->rruleService->buildWeekly([2]),
            sortOrder: $sortOrder++,
        );
        $this->createHabit(
            category: $categories['learning-growth'],
            name: [
                'en' => 'Read for 30+ minutes', 'ru' => 'Чтение 30+ минут',
                'es' => 'Leer durante 30+ minutos', 'zh' => '阅读30分钟以上',
                'hi' => '30+ मिनट पढ़ें', 'bn' => '৩০+ মিনিট পড়ুন',
                'pt' => 'Ler por 30+ minutos', 'ar' => 'القراءة 30+ دقيقة',
            ],
            description: [
                'en' => 'Read for 30+ minutes: books, articles, or educational content.',
                'ru' => 'Читай 30+ минут: книги, статьи или образовательный контент.',
                'es' => 'Lee durante 30+ minutos: libros, artículos o contenido educativo.',
                'zh' => '阅读30分钟以上：书籍、文章或教育内容。',
                'hi' => '30+ मिनट पढ़ें: किताबें, लेख या शैक्षिक सामग्री।',
                'bn' => '৩০+ মিনিট পড়ুন: বই, নিবন্ধ বা শিক্ষামূলক বিষয়বস্তু।',
                'pt' => 'Leia por 30+ minutos: livros, artigos ou conteúdo educacional.', 'ar' => 'اقرأ لمدة 30+ دقيقة: كتب، مقالات، أو محتوى تعليمي.',
            ],
            rrule: $this->rruleService->buildDaily(),
            sortOrder: $sortOrder++,
        );

        $this->createHabit(
            category: $categories['mindfulness'],
            name: [
                'en' => 'Evening reflection: gratitude & lessons', 'ru' => 'Вечерняя рефлексия: благодарность и уроки',
                'es' => 'Reflexión nocturna: gratitud y lecciones', 'zh' => '晚间反思：感恩与教训',
                'hi' => 'शाम की चिंतन: कृतज्ञता और सबक', 'bn' => 'সন্ধ্যার প্রতিফলন: কৃতজ্ঞতা ও শিক্ষা',
                'pt' => 'Reflexão noturna: gratidão e lições', 'ar' => 'تأمل مسائي: امتنان ودروس',
            ],
            description: [
                'en' => "Review your day: 3 things you're grateful for, what went well, what to improve.",
                'ru' => 'Проанализируй свой день: 3 вещи, за которые благодарен, что прошло хорошо, что улучшить.',
                'es' => 'Revisa tu día: 3 cosas por las que estás agradecido, qué salió bien, qué mejorar.',
                'zh' => '回顾你的一天：3件感恩的事，什么做得好，什么需要改进。',
                'hi' => 'अपने दिन की समीक्षा करें: 3 चीजें जिनके लिए आभारी हैं, क्या अच्छा रहा, क्या सुधारना है।',
                'bn' => 'আপনার দিনের পর্যালোচনা করুন: ৩টি জিনিস যার জন্য কৃতজ্ঞ, কী ভালো হলো, কী উন্নতি করতে হবে।',
                'pt' => 'Revise seu dia: 3 coisas pelas quais é grato, o que deu certo, o que melhorar.', 'ar' => 'راجع يومك: 3 أشياء تشكر عليها، ما سار بشكل جيد، وما يمكن تحسينه.',
            ],
            rrule: $this->rruleService->buildDaily(),
            sortOrder: $sortOrder++,
        );
        $this->createHabit(
            category: $categories['health-fitness'],
            name: [
                'en' => 'Sleep before 11 PM', 'ru' => 'Сон до 23:00',
                'es' => 'Dormir antes de las 23h', 'zh' => '晚上11点前入睡',
                'hi' => 'रात 11 बजे से पहले सोएं', 'bn' => 'রাত ১১টার আগে ঘুমান',
                'pt' => 'Dormir antes das 23h', 'ar' => 'النوم قبل الحادية عشرة مساءً',
            ],
            description: [
                'en' => 'Aim for 7-8 hours of quality sleep by going to bed before 11 PM.',
                'ru' => 'Стремись к 7-8 часам качественного сна, ложась до 23:00.',
                'es' => 'Apunta a 7-8 horas de sueño de calidad acostándote antes de las 23h.',
                'zh' => '在晚上11点前上床，争取7-8小时的优质睡眠。',
                'hi' => 'रात 11 बजे से पहले सोकर 7-8 घंटे की गुणवत्तापूर्ण नींद का लक्ष्य रखें।',
                'bn' => 'রাত ১১টার আগে ঘুমিয়ে ৭-৮ ঘণ্টা মানসম্মত ঘুমের লক্ষ্য রাখুন।',
                'pt' => 'Mire 7-8 horas de sono de qualidade dormindo antes das 23h.', 'ar' => 'اهدف إلى 7-8 ساعات من النوم الجيد بالنوم قبل الحادية عشرة مساءً.',
            ],
            rrule: $this->rruleService->buildDaily(),
            sortOrder: $sortOrder++,
            copyByDefault: true,
        );

        $this->createHabit(
            category: $categories['productivity'],
            name: [
                'en' => 'Weekly review & planning', 'ru' => 'Еженедельный обзор и планирование',
                'es' => 'Revisión y planificación semanal', 'zh' => '每周回顾与计划',
                'hi' => 'साप्ताहिक समीक्षा और योजना', 'bn' => 'সাপ্তাহিক পর্যালোচনা ও পরিকল্পনা',
                'pt' => 'Revisão e planejamento semanal', 'ar' => 'مراجعة وتخطيط أسبوعي',
            ],
            description: [
                'en' => 'Review completed tasks, plan next week, align with long-term goals.',
                'ru' => 'Проанализируй выполненные задачи, спланируй следующую неделю, сверься с долгосрочными целями.',
                'es' => 'Revisa las tareas completadas, planifica la próxima semana, alinea con los objetivos a largo plazo.',
                'zh' => '回顾已完成的任务，计划下周，与长期目标对齐。',
                'hi' => 'पूर्ण किए गए कार्यों की समीक्षा करें, अगले सप्ताह की योजना बनाएं, दीर्घकालिक लक्ष्यों से तालमेल बिठाएं।',
                'bn' => 'সম্পন্ন কাজ পর্যালোচনা করুন, পরের সপ্তাহ পরিকল্পনা করুন, দীর্ঘমেয়াদী লক্ষ্যের সাথে সামঞ্জস্য করুন।',
                'pt' => 'Revise tarefas concluídas, planeje a próxima semana, alinhe com objetivos de longo prazo.', 'ar' => 'راجع المهام المنجزة، خطط للأسبوع القادم، واربط بالأهداف طويلة المدى.',
            ],
            rrule: $this->rruleService->buildWeekly([6]),
            sortOrder: $sortOrder++,
        );

        $this->createHabit(
            category: $categories['productivity'],
            name: [
                'en' => 'Monthly review & goal setting', 'ru' => 'Ежемесячный обзор и постановка целей',
                'es' => 'Revisión mensual y establecimiento de metas', 'zh' => '月度回顾与目标设定',
                'hi' => 'मासिक समीक्षा और लक्ष्य निर्धारण', 'bn' => 'মাসিক পর্যালোচনা ও লক্ষ্য নির্ধারণ',
                'pt' => 'Revisão mensal e definição de metas', 'ar' => 'مراجعة شهرية وتحديد الأهداف',
            ],
            description: [
                'en' => 'Reflect on the past month: wins, lessons learned, and set intentions for the next month.',
                'ru' => 'Осмысли прошедший месяц: победы, усвоенные уроки, и поставь намерения на следующий месяц.',
                'es' => 'Reflexiona sobre el mes pasado: logros, lecciones aprendidas, y establece intenciones para el próximo mes.',
                'zh' => '反思过去一个月：成就、经验教训，并为下个月设定意图。',
                'hi' => 'पिछले महीने पर चिंतन करें: जीत, सीखे गए सबक, और अगले महीने के लिए इरादे तय करें।',
                'bn' => 'গত মাসের প্রতিফলন করুন: সাফল্য, শেখা পাঠ, এবং পরের মাসের জন্য উদ্দেশ্য নির্ধারণ করুন।',
                'pt' => 'Reflita sobre o mês passado: conquistas, lições aprendidas, e defina intenções para o próximo mês.', 'ar' => 'تأمل في الشهر الماضي: الإنجازات، الدروس المستفادة، وحدد نواياك للشهر القادم.',
            ],
            rrule: $this->rruleService->buildMonthlyByWeekday(1, 5),
            sortOrder: $sortOrder++,
        );
    }

    /**
     * @param  array<string, string>  $name
     * @param  array<string, string>  $description
     */
    private function createHabit(
        CategoryTemplate $category,
        array $name,
        array $description,
        string $rrule,
        int $sortOrder,
        int $iterationsRequired = 1,
        bool $copyByDefault = false,
        bool $showInTemplates = true,
    ): void {
        HabitTemplate::query()->updateOrCreate(
            ['category_template_id' => $category->id, 'sort_order' => $sortOrder],
            [
                'name' => $name,
                'description' => $description,
                'category_template_id' => $category->id,
                'is_active' => true,
                'copy_by_default' => $copyByDefault,
                'show_in_templates' => $showInTemplates,
                'sort_order' => $sortOrder,
                'iterations_required' => $iterationsRequired,
                'rrule' => $rrule,
            ]
        );
    }
}
