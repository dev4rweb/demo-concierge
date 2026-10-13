<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocalizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_welcome_page_is_english_by_default(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Welcome')
            ->where('locale', 'en')
            ->where('dir', 'ltr')
        );
    }

    public function test_locale_prefix_switches_language_and_rtl(): void
    {
        $this->get('/ar')->assertOk()->assertInertia(fn ($page) => $page
            ->component('Welcome')
            ->where('locale', 'ar')
            ->where('dir', 'rtl')
        );

        $this->get('/fr/how-to')->assertOk()->assertInertia(fn ($page) => $page
            ->component('Guide')
            ->where('locale', 'fr')
        );
    }

    public function test_hidden_russian_is_available_by_direct_url(): void
    {
        $this->get('/ru')->assertOk()->assertInertia(fn ($page) => $page
            ->where('locale', 'ru')
        );
    }

    public function test_language_selector_does_not_include_russian(): void
    {
        $response = $this->get('/');

        $response->assertInertia(fn ($page) => $page
            ->where('locales', fn ($locales) => collect($locales)->pluck('code')->doesntContain('ru')
                && collect($locales)->pluck('code')->contains('en'))
        );
    }

    public function test_accept_language_can_select_french_but_not_russian(): void
    {
        $this->withHeader('Accept-Language', 'fr-FR,fr;q=0.9')
            ->get('/')
            ->assertInertia(fn ($page) => $page->where('locale', 'fr'));

        $this->withHeader('Accept-Language', 'ru-RU,ru;q=0.9,en;q=0.8')
            ->get('/')
            ->assertInertia(fn ($page) => $page->where('locale', 'en'));
    }

    public function test_login_and_admin_routes_work_with_locale_prefix(): void
    {
        $this->get('/es/login')->assertOk();
        $this->get('/zh/how-to')->assertOk();
    }
}
