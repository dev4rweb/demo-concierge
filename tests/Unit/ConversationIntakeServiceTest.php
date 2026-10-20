<?php

namespace Tests\Unit;

use App\Services\ConversationIntakeService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ConversationIntakeServiceTest extends TestCase
{
    private ConversationIntakeService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ConversationIntakeService;
    }

    public function test_detects_language_from_message_script(): void
    {
        $this->assertSame('ar', $this->service->detectLanguage('مرحبا، أريد موقعاً'));
        $this->assertSame('zh', $this->service->detectLanguage('你好，我想了解价格'));
        $this->assertSame('en', $this->service->detectLanguage('Hello, I need a website'));
        $this->assertSame('ru', $this->service->detectLanguage('Здравствуйте, нужен сайт'));
    }

    public function test_falls_back_to_telegram_language_code_for_start(): void
    {
        $this->assertSame('fr', $this->service->detectLanguage('/start', 'fr'));
        $this->assertSame('ru', $this->service->detectLanguage('/start', 'ru-RU'));
        $this->assertSame('zh', $this->service->detectLanguage('/start', 'zh-Hans'));
        $this->assertSame('en', $this->service->detectLanguage('/start', 'xx'));
        $this->assertSame('en', $this->service->detectLanguage('/start', 'de'));
    }

    /**
     * @return list<array{0: string, 1: list<string>}>
     */
    public static function welcomeLanguageProvider(): array
    {
        return [
            ['en', ['hotels', 'hours', 'prices', 'services', 'directions', 'manager', 'человек']],
            ['ru', ['отелей', 'часы', 'цены', 'услуги', 'добраться', 'manager', 'человек']],
            ['zh', ['酒店', '营业时间', '价格', '服务', '路线', 'manager', 'человек']],
            ['fr', ['hôtels', 'horaires', 'prix', 'services', 'itinéraire', 'manager', 'человек']],
            ['es', ['hoteles', 'horarios', 'precios', 'servicios', 'llegar', 'manager', 'человек']],
            ['ar', ['فنادق', 'ساعات العمل', 'الأسعار', 'الخدمات', 'الاتجاهات', 'manager', 'человек']],
            ['pt', ['hotéis', 'horários', 'preços', 'serviços', 'indicações', 'manager', 'человек']],
        ];
    }

    /**
     * @param  list<string>  $needles
     */
    #[DataProvider('welcomeLanguageProvider')]
    public function test_welcome_message_covers_intro_topics_and_handoff(string $language, array $needles): void
    {
        $message = $this->service->welcomeMessage($language);

        foreach ($needles as $needle) {
            $this->assertStringContainsString($needle, $message);
        }
    }

    public function test_unknown_welcome_language_falls_back_to_english(): void
    {
        $this->assertSame(
            $this->service->welcomeMessage('en'),
            $this->service->welcomeMessage('de')
        );
    }

    public function test_welcome_keyboard_links_demo_admin_and_company_site(): void
    {
        $keyboard = $this->service->welcomeKeyboard('en');

        $this->assertSame('Open demo site', $keyboard['inline_keyboard'][0][0]['text']);
        $this->assertSame('https://demo-concierge.dev4rweb.com', $keyboard['inline_keyboard'][0][0]['url']);
        $this->assertSame('Admin panel', $keyboard['inline_keyboard'][0][1]['text']);
        $this->assertSame('https://demo-concierge.dev4rweb.com/login', $keyboard['inline_keyboard'][0][1]['url']);
        $this->assertSame('dev4rweb.com', $keyboard['inline_keyboard'][1][0]['text']);
        $this->assertSame('https://dev4rweb.com', $keyboard['inline_keyboard'][1][0]['url']);
    }

    public function test_welcome_keyboard_localizes_demo_urls(): void
    {
        $keyboard = $this->service->welcomeKeyboard('ru');

        $this->assertSame('Открыть демо', $keyboard['inline_keyboard'][0][0]['text']);
        $this->assertSame('https://demo-concierge.dev4rweb.com/ru', $keyboard['inline_keyboard'][0][0]['url']);
        $this->assertSame('https://demo-concierge.dev4rweb.com/ru/login', $keyboard['inline_keyboard'][0][1]['url']);
        $this->assertSame('https://dev4rweb.com', $keyboard['inline_keyboard'][1][0]['url']);
    }

    public function test_mentions_human_in_multiple_languages(): void
    {
        $this->assertTrue($this->service->mentionsHuman('Можно менеджера, пожалуйста'));
        $this->assertTrue($this->service->mentionsHuman('Please connect me with a manager'));
        $this->assertTrue($this->service->mentionsHuman('Je veux parler à un administrateur'));
        $this->assertTrue($this->service->mentionsHuman('Quiero hablar con una persona'));
        $this->assertFalse($this->service->mentionsHuman('How much does a landing page cost?'));
    }

    public function test_extracts_name_without_trailing_punctuation(): void
    {
        $fields = $this->service->extractLeadFields('My name is Marina Bondarenko');

        $this->assertSame('Marina Bondarenko', $fields['name']);
    }

    public function test_extracts_russian_name_without_period(): void
    {
        $fields = $this->service->extractLeadFields('Меня зовут Олена Шевченко');

        $this->assertSame('Олена Шевченко', $fields['name']);
    }

    public function test_extracts_email_and_ukrainian_phone(): void
    {
        $fields = $this->service->extractLeadFields('Email marina.bondarenko@example.com phone +380 50 123 45 67');

        $this->assertSame('marina.bondarenko@example.com', $fields['email']);
        $this->assertSame('+380 50 123 45 67', $fields['phone']);
        $this->assertTrue($this->service->hasContactData($fields));
    }

    public function test_has_no_contact_data_for_plain_question(): void
    {
        $fields = $this->service->extractLeadFields('What services do you provide?');

        $this->assertFalse($this->service->hasContactData($fields));
    }
}
