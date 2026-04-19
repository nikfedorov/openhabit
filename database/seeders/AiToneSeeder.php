<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AiTone;
use Illuminate\Database\Seeder;

final class AiToneSeeder extends Seeder
{
    public function run(): void
    {
        $tones = [
            [
                'slug' => 'friendly',
                'name' => [
                    'en' => 'Friendly', 'ru' => 'Дружелюбный', 'es' => 'Amigable',
                    'zh' => '友好', 'hi' => 'दोस्ताना', 'bn' => 'বন্ধুসুলভ', 'pt' => 'Amigável', 'ar' => 'ودود',
                ],
                'description' => [
                    'en' => 'Warm and easygoing, like talking to a good friend who is on your side.',
                    'ru' => 'Тёплый и лёгкий в общении, будто разговариваешь с хорошим другом, который на твоей стороне.',
                    'es' => 'Cálido y cercano, como hablar con un buen amigo que está de tu lado.',
                    'zh' => '温暖又随和，就像在和一个真心支持你的朋友聊天。',
                    'hi' => 'गरमजोशी भरा और सहज, जैसे किसी ऐसे अच्छे दोस्त से बात करना जो सच में आपके साथ हो।',
                    'bn' => 'উষ্ণ আর সহজ, যেন এমন এক ভালো বন্ধুর সঙ্গে কথা বলা যে সত্যিই তোমার পাশে আছে।',
                    'pt' => 'Caloroso e leve, como conversar com um bom amigo que está do seu lado.', 'ar' => 'دافئ وبسيط، كأنك تتحدث مع صديق مقرّب يقف فعلًا إلى جانبك.',
                ],
                'icon' => 'sun',
                'system_instruction' => 'Be warm, casual, and encouraging. Sound like a good friend who genuinely cares. Celebrate small wins, keep the mood light, and make encouragement feel natural rather than over the top.',
                'is_default' => true,
            ],
            [
                'slug' => 'supportive',
                'name' => [
                    'en' => 'Supportive', 'ru' => 'Заботливый', 'es' => 'Comprensivo',
                    'zh' => '体贴', 'hi' => 'सहायक', 'bn' => 'সহমর্মী', 'pt' => 'Acolhedor', 'ar' => 'متفهم',
                ],
                'description' => [
                    'en' => 'Gentle and reassuring, with a strong focus on empathy and emotional support.',
                    'ru' => 'Бережный и понимающий тон с акцентом на эмпатию и эмоциональную поддержку.',
                    'es' => 'Suave y comprensivo, con foco en la empatía y el apoyo emocional.',
                    'zh' => '温和而体贴，重点在共情和情绪支持。',
                    'hi' => 'नरम और समझदार, जिसमें सहानुभूति और भावनात्मक सहारे पर ज़ोर हो।',
                    'bn' => 'নরম আর সহমর্মী, যেখানে জোর থাকে অনুভূতি বোঝা আর মানসিক সমর্থনে।',
                    'pt' => 'Gentil e acolhedor, com foco em empatia e apoio emocional.', 'ar' => 'هادئ ومتفهّم، ويركّز على التعاطف والدعم العاطفي.',
                ],
                'icon' => 'heart',
                'system_instruction' => "Be gentle, empathetic, and validating. Acknowledge the user's feelings before offering suggestions. Let them feel understood, and frame advice in a calm, supportive way.",
                'is_default' => false,
            ],
            [
                'slug' => 'coach',
                'name' => [
                    'en' => 'Coach', 'ru' => 'Тренер', 'es' => 'Entrenador',
                    'zh' => '教练', 'hi' => 'कोच', 'bn' => 'কোচ', 'pt' => 'Treinador', 'ar' => 'مدرب',
                ],
                'description' => [
                    'en' => 'Energetic and action-focused, like a coach who wants to see real progress.',
                    'ru' => 'Энергичный и нацеленный на действие, как тренер, которому важен реальный прогресс.',
                    'es' => 'Enérgico y orientado a la acción, como un entrenador que quiere ver progreso de verdad.',
                    'zh' => '有冲劲、讲行动，像一位真心想看到你进步的教练。',
                    'hi' => 'ऊर्जावान और काम पर केंद्रित, जैसे कोई कोच जो सच में आपकी प्रगति देखना चाहता हो।',
                    'bn' => 'উদ্যমী আর কাজকেন্দ্রিক, যেন এমন এক কোচ যে সত্যিকারের অগ্রগতি দেখতে চায়।',
                    'pt' => 'Enérgico e voltado para a ação, como um treinador que quer ver progresso de verdade.', 'ar' => 'حيوي ويركّز على العمل، مثل مدرب يريد أن يرى تقدّمًا حقيقيًا.',
                ],
                'icon' => 'bolt',
                'system_instruction' => 'Be clear, motivating, and action-focused. Push the user toward concrete next steps and higher standards, but keep it respectful. Sound like a coach who believes they can do more.',
                'is_default' => false,
            ],
            [
                'slug' => 'strict',
                'name' => [
                    'en' => 'Strict', 'ru' => 'Строгий', 'es' => 'Estricto',
                    'zh' => '严格', 'hi' => 'सख्त', 'bn' => 'কঠোর', 'pt' => 'Rigoroso', 'ar' => 'صارم',
                ],
                'description' => [
                    'en' => 'Direct and no-frills, for people who want clear accountability.',
                    'ru' => 'Без лишних слов и с упором на ответственность для тех, кому нужна честная требовательность.',
                    'es' => 'Directo y centrado en la responsabilidad, para quienes quieren una exigencia clara y honesta.',
                    'zh' => '直截了当、强调责任感，适合想要被认真督促的人。',
                    'hi' => 'सीधा और जवाबदेही पर केंद्रित, उन लोगों के लिए जो साफ़ और ईमानदार अनुशासन चाहते हैं।',
                    'bn' => 'সোজাসাপ্টা আর জবাবদিহিমুখী, তাদের জন্য যারা স্পষ্ট আর সৎ কঠোরতা চান।',
                    'pt' => 'Direto e focado em responsabilidade, para quem quer uma cobrança clara e honesta.', 'ar' => 'مباشر ويركّز على المساءلة، لمن يريدون صراحة واضحة وانضباطًا حقيقيًا.',
                ],
                'icon' => 'shield',
                'system_instruction' => 'Be blunt, firm, and accountability-driven. Call out missed commitments clearly. Do not soften poor follow-through, but stay fair and respectful. The goal is honest pressure, not cruelty.',
                'is_default' => false,
            ],
        ];

        foreach ($tones as $tone) {
            AiTone::query()->updateOrCreate(
                ['slug' => $tone['slug']],
                $tone,
            );
        }
    }
}
