<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Database\Seeder;

final class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        Invoice::query()->updateOrCreate(
            ['slug' => 'premium'],
            [
                'title' => [
                    'en' => '⭐ Premium Subscription',
                    'ru' => '⭐ Премиум подписка',
                    'es' => '⭐ Suscripción Premium',
                    'zh' => '⭐ 高级订阅',
                    'hi' => '⭐ प्रीमियम सदस्यता',
                    'bn' => '⭐ প্রিমিয়াম সাবস্ক্রিপশন',
                    'pt' => '⭐ Assinatura Premium',
                    'ar' => '⭐ اشتراك مميز',
                ],
                'description' => [
                    'en' => 'Premium features for 30 days: AI-powered daily digests, advanced analytics, and more.',
                    'ru' => 'Премиум-функции на 30 дней: AI-дайджесты, расширенная аналитика и многое другое.',
                    'es' => 'Funciones premium por 30 días: resúmenes diarios con IA, análisis avanzado y más.',
                    'zh' => '30天高级功能：AI驱动的每日摘要、高级分析等。',
                    'hi' => '30 दिनों के लिए प्रीमियम सुविधाएँ: AI-संचालित दैनिक डाइजेस्ट, उन्नत विश्लेषण, और अधिक।',
                    'bn' => '৩০ দিনের জন্য প্রিমিয়াম বৈশিষ্ট্য: AI-চালিত দৈনিক ডাইজেস্ট, উন্নত বিশ্লেষণ এবং আরও অনেক কিছু।',
                    'pt' => 'Recursos premium por 30 dias: resumos diários com IA, análises avançadas e mais.',
                    'ar' => 'ميزات مميزة لمدة 30 يومًا: ملخصات يومية بالذكاء الاصطناعي، تحليلات متقدمة، والمزيد.',
                ],
                'stars' => app()->isLocal() ? 1 : 1299,
                'subscription_period' => User::PREMIUM_PERIOD_SECONDS,
            ],
        );
    }
}
