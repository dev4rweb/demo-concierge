<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AboutPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_about_page_renders_in_english(): void
    {
        $this->get('/about')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('About')
                ->where('locale', 'en')
                ->where('dir', 'ltr')
                ->has('botUsername')
            );
    }

    public function test_about_page_renders_in_arabic_rtl(): void
    {
        $this->get('/ar/about')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('About')
                ->where('locale', 'ar')
                ->where('dir', 'rtl')
            );
    }

    public function test_hidden_russian_about_is_available_by_direct_url(): void
    {
        $this->get('/ru/about')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('About')
                ->where('locale', 'ru')
            );
    }

    public function test_about_copy_is_written_for_business_owners_not_engineers(): void
    {
        foreach (config('localization.supported') as $locale) {
            $copy = json_encode(trans('app.about', locale: $locale), JSON_UNESCAPED_UNICODE);

            $this->assertStringNotContainsStringIgnoringCase('webhook', $copy, $locale);
            $this->assertStringNotContainsStringIgnoringCase('openrouter', $copy, $locale);
            $this->assertStringNotContainsStringIgnoringCase('llm', $copy, $locale);
            $this->assertStringNotContainsStringIgnoringCase('cron', $copy, $locale);
        }
    }

    public function test_about_page_exposes_page_specific_seo_props(): void
    {
        $this->get('/about')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->where('seo.title', trans('app.about.meta.title'))
                ->where('seo.description', trans('app.about.meta.description'))
            );
    }

    public function test_about_copy_exists_in_every_supported_locale(): void
    {
        $locales = config('localization.supported');

        foreach ($locales as $locale) {
            $path = $locale === config('localization.default', 'en')
                ? '/about'
                : '/'.$locale.'/about';

            $this->get($path)
                ->assertOk()
                ->assertInertia(fn ($page) => $page
                    ->component('About')
                    ->where('locale', $locale)
                    ->where('translations.about.title', trans('app.about.title', locale: $locale))
                    ->where('translations.nav.about', trans('app.nav.about', locale: $locale))
                );
        }
    }

    public function test_about_page_shares_demo_login_from_seeders(): void
    {
        $this->get('/about')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->where('demo.email', 'admin@demo.local')
                ->where('demo.password', 'demo123456')
            );
    }

    public function test_welcome_page_links_to_about(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Welcome')
                ->where('translations.welcome.product', trans('app.welcome.product'))
            );
    }
}
