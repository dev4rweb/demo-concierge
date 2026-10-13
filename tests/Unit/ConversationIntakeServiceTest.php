<?php

namespace Tests\Unit;

use App\Services\ConversationIntakeService;
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
        $this->assertSame('en', $this->service->detectLanguage('/start', 'xx'));
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
