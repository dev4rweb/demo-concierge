<?php

namespace Database\Seeders;

use App\Models\KnowledgeBase;
use Illuminate\Database\Seeder;

class KnowledgeBaseSeeder extends Seeder
{
    public function run(): void
    {
        $knowledgeBase = [
            [
                'category' => 'Услуги',
                'question' => 'Какие услуги вы предоставляете?',
                'answer' => 'Мы предоставляем разработку веб-приложений, создание сайтов, консультации по SaaS-решениям, интеграцию с API, создание Telegram-ботов и AI-интеграции.',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'category' => 'Услуги',
                'question' => 'Сколько стоят ваши услуги?',
                'answer' => 'Стоимость зависит от сложности проекта. Простой лендинг от 30 000 руб, корпоративный сайт от 80 000 руб, веб-приложение от 200 000 руб. Точную стоимость рассчитаем после обсуждения требований.',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'category' => 'Процесс работы',
                'question' => 'Как долго занимает разработка?',
                'answer' => 'Простой сайт - 1-2 недели, корпоративный сайт - 3-4 недели, веб-приложение - от 1 до 3 месяцев в зависимости от функциональности.',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'category' => 'Процесс работы',
                'question' => 'Какие технологии вы используете?',
                'answer' => 'Laravel, Vue.js, React, Node.js, Python, PostgreSQL, MySQL, Redis, Docker. Выбор стека зависит от задач проекта.',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'category' => 'Контакты',
                'question' => 'Как с вами связаться?',
                'answer' => 'Вы можете написать нам здесь в Telegram, оставить заявку на сайте или позвонить. Администратор свяжется с вами в ближайшее время.',
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'category' => 'Процесс работы',
                'question' => 'Предоставляете ли вы поддержку после запуска?',
                'answer' => 'Да, мы предоставляем техническую поддержку и сопровождение проектов. Есть разные тарифы: базовый (исправление критичных ошибок), стандарт (+ мелкие доработки), премиум (полное сопровождение и развитие).',
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

