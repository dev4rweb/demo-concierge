<?php

namespace Database\Seeders;

use App\Models\KnowledgeBase;
use Illuminate\Database\Seeder;

class KnowledgeBaseSeeder extends Seeder
{
    public function run(): void
    {
        $legacyQuestions = [
            'Какие услуги вы предоставляете?',
            'Сколько стоят ваши услуги?',
            'Как долго занимает разработка?',
            'Какие технологии вы используете?',
            'Как с вами связаться?',
            'Предоставляете ли вы поддержку после запуска?',
        ];

        KnowledgeBase::query()->whereIn('question', $legacyQuestions)->delete();
        KnowledgeBase::query()->where('answer', 'like', '%руб%')->delete();

        $knowledgeBase = [
            [
                'category' => 'Services',
                'question' => 'What services do you provide?',
                'answer' => 'We design and build web applications, marketing sites, SaaS products, API integrations, Telegram bots, and AI concierge experiences for teams worldwide.',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'category' => 'Services',
                'question' => 'How much do your services cost?',
                'answer' => 'Pricing depends on scope. A simple landing page starts at $800, a corporate website from $2,000, and a custom web application from $5,000. Telegram bots typically start at $1,800. We confirm an exact quote after a short discovery call.',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'category' => 'Process',
                'question' => 'How long does development take?',
                'answer' => 'A simple site usually takes 1–2 weeks, a corporate website 3–4 weeks, and a web application 1–3 months depending on features. A focused Telegram bot is often delivered in 3–5 weeks.',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'category' => 'Process',
                'question' => 'Which technologies do you use?',
                'answer' => 'Laravel, Vue.js, React, Node.js, Python, PostgreSQL, MySQL, Redis, and Docker. The stack is chosen for the product, not the other way around.',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'category' => 'Contact',
                'question' => 'How can I get in touch?',
                'answer' => 'Write here in Telegram, leave a request on the website, or ask the bot to connect you with a manager. An administrator will follow up shortly.',
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'category' => 'Process',
                'question' => 'Do you provide support after launch?',
                'answer' => 'Yes. Support plans: Basic (critical-fix coverage) from $250/month, Standard (small improvements included) from $600/month, and Premium (full care and product evolution) from $1,400/month.',
                'is_active' => true,
                'sort_order' => 6,
            ],
        ];

        foreach ($knowledgeBase as $item) {
            KnowledgeBase::updateOrCreate(
                ['question' => $item['question']],
                $item
            );
        }
    }
}
