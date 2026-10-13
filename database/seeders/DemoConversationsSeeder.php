<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\Lead;
use App\Models\Message;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DemoConversationsSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->scenarios() as $scenario) {
            $this->seedScenario($scenario);
        }
    }

    /**
     * @param  array{
     *     telegram_user_id: string,
     *     telegram_username: string,
     *     telegram_first_name: string,
     *     telegram_last_name: string,
     *     needs_attention: bool,
     *     started_at: Carbon,
     *     messages: list<array{role: string, content: string, minutes: int}>,
     *     lead: ?array<string, mixed>
     * }  $scenario
     */
    private function seedScenario(array $scenario): void
    {
        $conversation = Conversation::updateOrCreate(
            ['telegram_user_id' => $scenario['telegram_user_id']],
            [
                'telegram_username' => $scenario['telegram_username'],
                'telegram_first_name' => $scenario['telegram_first_name'],
                'telegram_last_name' => $scenario['telegram_last_name'],
                'needs_attention' => $scenario['needs_attention'],
                'is_active' => true,
                'created_at' => $scenario['started_at'],
            ]
        );

        $conversation->messages()->delete();
        $conversation->lead?->delete();

        $lastAt = $scenario['started_at'];

        foreach ($scenario['messages'] as $message) {
            $createdAt = $scenario['started_at']->copy()->addMinutes($message['minutes']);
            $lastAt = $createdAt;

            Message::create([
                'conversation_id' => $conversation->id,
                'role' => $message['role'],
                'content' => $message['content'],
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }

        $conversation->update([
            'last_message_at' => $lastAt,
            'needs_attention' => $scenario['needs_attention'],
        ]);

        if ($scenario['lead']) {
            Lead::updateOrCreate(
                ['conversation_id' => $conversation->id],
                $scenario['lead']
            );
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function scenarios(): array
    {
        $now = Carbon::now();

        return [
            [
                'telegram_user_id' => 'demo-900001',
                'telegram_username' => 'olena_shev',
                'telegram_first_name' => 'Olena',
                'telegram_last_name' => 'Shevchenko',
                'needs_attention' => false,
                'started_at' => $now->copy()->subDays(5)->setTime(9, 12),
                'lead' => null,
                'messages' => [
                    ['role' => 'user', 'minutes' => 0, 'content' => 'Hi — what services do you provide?'],
                    ['role' => 'assistant', 'minutes' => 1, 'content' => 'We design and build web applications, marketing sites, SaaS products, API integrations, Telegram bots, and AI concierge experiences.'],
                    ['role' => 'user', 'minutes' => 4, 'content' => 'How much does a corporate website cost, and how long does it take?'],
                    ['role' => 'assistant', 'minutes' => 5, 'content' => 'A corporate website starts at $2,000 and usually takes 3–4 weeks. The exact quote depends on pages, integrations, and content readiness.'],
                ],
            ],
            [
                'telegram_user_id' => 'demo-900002',
                'telegram_username' => 'marina.bond',
                'telegram_first_name' => 'Marina',
                'telegram_last_name' => 'Bondarenko',
                'needs_attention' => false,
                'started_at' => $now->copy()->subDays(3)->setTime(14, 40),
                'lead' => [
                    'name' => 'Marina Bondarenko',
                    'phone' => '+380 50 123 45 67',
                    'email' => 'marina.bondarenko@example.com',
                    'telegram_contact' => '@marina.bond',
                    'needs' => 'Telegram bot for salon appointment booking. Budget about $2–3k, delivery in one month.',
                    'status' => 'new',
                ],
                'messages' => [
                    ['role' => 'user', 'minutes' => 0, 'content' => 'I need a Telegram bot for booking clients in a beauty salon.'],
                    ['role' => 'assistant', 'minutes' => 1, 'content' => 'We build Telegram booking bots. Typical starting price is $1,800. What budget and timeline do you have in mind?'],
                    ['role' => 'user', 'minutes' => 6, 'content' => 'Budget is about $2–3k. We need it in a month.'],
                    ['role' => 'assistant', 'minutes' => 7, 'content' => 'That range and a 3–5 week delivery fit well. Please share your name, phone, and email so a manager can confirm the scope.'],
                    ['role' => 'user', 'minutes' => 12, 'content' => 'My name is Marina Bondarenko, phone +380 50 123 45 67, email marina.bondarenko@example.com'],
                    ['role' => 'assistant', 'minutes' => 13, 'content' => 'Thank you, Marina. I saved your contacts. A manager will follow up about the salon booking bot.'],
                ],
            ],
            [
                'telegram_user_id' => 'demo-900003',
                'telegram_username' => 'andrii_m',
                'telegram_first_name' => 'Andrii',
                'telegram_last_name' => 'Melnyk',
                'needs_attention' => true,
                'started_at' => $now->copy()->subDay()->setTime(11, 5),
                'lead' => null,
                'messages' => [
                    ['role' => 'user', 'minutes' => 0, 'content' => 'We need an integration between our CRM and ERP — custom fields, two-way sync, and SSO.'],
                    ['role' => 'assistant', 'minutes' => 2, 'content' => 'That is a specialist integration. I can outline options, but a manager should review the systems and data model with you.'],
                    ['role' => 'user', 'minutes' => 8, 'content' => 'Please connect me with a manager. This is too specific for a bot.'],
                    ['role' => 'assistant', 'minutes' => 9, 'content' => 'Understood. Your request has been passed to an administrator. We will contact you shortly.'],
                ],
            ],
            [
                'telegram_user_id' => 'demo-900004',
                'telegram_username' => 'sofia.p',
                'telegram_first_name' => 'Sofia',
                'telegram_last_name' => 'Petrenko',
                'needs_attention' => false,
                'started_at' => $now->copy()->subDays(2)->setTime(16, 20),
                'lead' => null,
                'messages' => [
                    ['role' => 'user', 'minutes' => 0, 'content' => 'The checkout on our staging site fails after 3D Secure. Can a human look at this?'],
                    ['role' => 'assistant', 'minutes' => 1, 'content' => 'I am escalating this to an administrator so they can review the payment flow with you.'],
                    ['role' => 'admin', 'minutes' => 40, 'content' => 'Hi Sofia — this is Alex from the team. We reproduced the 3D Secure return URL issue and will ship a fix to staging today. I will message you here when it is ready.'],
                    ['role' => 'user', 'minutes' => 55, 'content' => 'Great, thank you. I will retest this evening.'],
                ],
            ],
            [
                'telegram_user_id' => 'demo-900005',
                'telegram_username' => 'dmytro_k',
                'telegram_first_name' => 'Dmytro',
                'telegram_last_name' => 'Kravets',
                'needs_attention' => false,
                'started_at' => $now->copy()->subHours(26)->setTime(10, 15),
                'lead' => null,
                'messages' => [
                    ['role' => 'user', 'minutes' => 0, 'content' => 'Do you provide support after launch? What are the plans?'],
                    ['role' => 'assistant', 'minutes' => 1, 'content' => 'Yes. Basic from $250/month for critical fixes, Standard from $600/month with small improvements, and Premium from $1,400/month for full care and product evolution.'],
                    ['role' => 'user', 'minutes' => 6, 'content' => 'What is included in Standard versus Premium?'],
                    ['role' => 'assistant', 'minutes' => 7, 'content' => 'Standard covers monitoring, bugfixes, and a small monthly change budget. Premium adds a dedicated cadence, backlog grooming, and ongoing feature work.'],
                ],
            ],
            [
                'telegram_user_id' => 'demo-900006',
                'telegram_username' => 'jwhitaker',
                'telegram_first_name' => 'James',
                'telegram_last_name' => 'Whitaker',
                'needs_attention' => false,
                'started_at' => $now->copy()->subHours(5)->setTime(18, 2),
                'lead' => null,
                'messages' => [
                    ['role' => 'user', 'minutes' => 0, 'content' => 'We want a SaaS dashboard for usage analytics. What stack would you recommend?'],
                    ['role' => 'assistant', 'minutes' => 1, 'content' => 'For a SaaS dashboard we usually use Laravel + Vue (Inertia) or React, PostgreSQL, Redis, and Docker. That stack keeps billing, auth, and realtime widgets straightforward.'],
                    ['role' => 'user', 'minutes' => 8, 'content' => 'Would you keep the API in Laravel or split a Node service for the charts?'],
                    ['role' => 'assistant', 'minutes' => 9, 'content' => 'Start with Laravel for the API and queue heavy aggregations. Split a Node service later only if chart compute becomes a bottleneck.'],
                ],
            ],
        ];
    }
}
