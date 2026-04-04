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
                    'zh' => '友好的', 'hi' => 'मित्रवत', 'bn' => 'বন্ধুত্বপূর্ণ', 'pt' => 'Amigável', 'ar' => 'ودود',
                ],
                'description' => [
                    'en' => 'Warm and supportive, like chatting with a good friend.',
                    'ru' => 'Тёплый и поддерживающий, как общение с хорошим другом.',
                    'es' => 'Cálido y solidario, como charlar con un buen amigo.',
                    'zh' => '温暖而支持，就像和好朋友聊天一样。',
                    'hi' => 'गर्म और सहायक, जैसे एक अच्छे दोस्त से बातचीत।',
                    'bn' => 'উষ্ণ এবং সহায়ক, যেন একজন ভালো বন্ধুর সাথে আড্ডা।',
                    'pt' => 'Caloroso e solidário, como conversar com um bom amigo.', 'ar' => 'دافئ وداعم، مثل الدردشة مع صديق جيد.',
                ],
                'icon' => 'sun',
                'system_instruction' => 'Be casual, warm, and encouraging. Use a friendly tone like talking to a good friend. Celebrate small wins, use positive reinforcement, and be uplifting. Keep it light and approachable.',
                'is_default' => true,
            ],
            [
                'slug' => 'supportive',
                'name' => [
                    'en' => 'Supportive', 'ru' => 'Поддерживающий', 'es' => 'Comprensivo',
                    'zh' => '支持的', 'hi' => 'सहायक', 'bn' => 'সহায়ক', 'pt' => 'Acolhedor', 'ar' => 'داعم',
                ],
                'description' => [
                    'en' => 'Understanding and empathetic, focused on emotional support.',
                    'ru' => 'Понимающий и чуткий, сфокусирован на эмоциональной поддержке.',
                    'es' => 'Comprensivo y empático, centrado en el apoyo emocional.',
                    'zh' => '理解和同理心，专注于情感支持。',
                    'hi' => 'समझदार और सहानुभूतिपूर्ण, भावनात्मक समर्थन पर केंद्रित।',
                    'bn' => 'বোধগম্য এবং সহানুভূতিশীল, মানসিক সমর্থনে মনোযোগী।',
                    'pt' => 'Compreensivo e empático, focado no suporte emocional.', 'ar' => 'متفهم ومتعاطف، يركز على الدعم العاطفي.',
                ],
                'icon' => 'heart',
                'system_instruction' => 'Be empathetic and understanding. Focus on emotional support and validation. Acknowledge struggles before suggesting improvements. Use gentle language and show you understand how building habits can be challenging.',
                'is_default' => false,
            ],
            [
                'slug' => 'coach',
                'name' => [
                    'en' => 'Coach', 'ru' => 'Коуч', 'es' => 'Entrenador',
                    'zh' => '教练', 'hi' => 'कोच', 'bn' => 'কোচ', 'pt' => 'Treinador', 'ar' => 'مدرب',
                ],
                'description' => [
                    'en' => 'Motivating and action-oriented, like a personal trainer.',
                    'ru' => 'Мотивирующий и нацеленный на действия, как персональный тренер.',
                    'es' => 'Motivador y orientado a la acción, como un entrenador personal.',
                    'zh' => '激励和行动导向，像一个私人教练。',
                    'hi' => 'प्रेरक और कार्य-उन्मुख, जैसे एक निजी प्रशिक्षक।',
                    'bn' => 'প্রেরণাদায়ক এবং কর্ম-ভিত্তিক, যেন একজন ব্যক্তিগত প্রশিক্ষক।',
                    'pt' => 'Motivador e orientado para ação, como um treinador pessoal.', 'ar' => 'محفز وموجه نحو العمل، مثل مدرب شخصي.',
                ],
                'icon' => 'bolt',
                'system_instruction' => 'Be direct and action-oriented. Focus on performance, improvement, and pushing the user to do better. Use motivating language, set high expectations, but remain respectful. Think like a sports coach.',
                'is_default' => false,
            ],
            [
                'slug' => 'strict',
                'name' => [
                    'en' => 'Strict', 'ru' => 'Строгий', 'es' => 'Estricto',
                    'zh' => '严格的', 'hi' => 'सख्त', 'bn' => 'কঠোর', 'pt' => 'Rigoroso', 'ar' => 'صارم',
                ],
                'description' => [
                    'en' => 'No-nonsense accountability partner who holds you to your commitments.',
                    'ru' => 'Серьёзный партнёр по ответственности, который держит тебя у слова.',
                    'es' => 'Compañero de responsabilidad serio que te mantiene fiel a tus compromisos.',
                    'zh' => '不废话的责任伙伴，让你坚守承诺。',
                    'hi' => 'गंभीर जवाबदेही साथी जो आपको आपकी प्रतिबद्धताओं पर टिकाए रखता है।',
                    'bn' => 'গুরুত্বপূর্ণ জবাবদিহি অংশীদার যে আপনাকে আপনার প্রতিশ্রুতিতে ধরে রাখে।',
                    'pt' => 'Parceiro de responsabilidade sério que te mantém fiel aos seus compromissos.', 'ar' => 'شريك مسؤولية جاد يبقيك ملتزمًا بتعهداتك.',
                ],
                'icon' => 'shield',
                'system_instruction' => "Be no-nonsense and accountability-focused. Point out missed habits directly. Don't sugarcoat poor performance. Be firm but fair. The user chose this tone because they want honest, direct feedback.",
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
