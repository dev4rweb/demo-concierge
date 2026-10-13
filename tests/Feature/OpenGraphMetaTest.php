<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OpenGraphMetaTest extends TestCase
{
    use RefreshDatabase;

    public function test_welcome_page_includes_open_graph_and_twitter_tags(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('<meta name="description" content="Telegram AI concierge with a Laravel admin panel: knowledge-base answers, lead capture, and human handoff.">', false);
        $response->assertSee('<meta property="og:title" content="AI Concierge Demo">', false);
        $response->assertSee('<meta property="og:description" content="Telegram AI concierge with a Laravel admin panel: knowledge-base answers, lead capture, and human handoff.">', false);
        $response->assertSee('<meta property="og:image" content="https://demo-concierge.dev4rweb.com/og-image.jpg">', false);
        $response->assertSee('<meta property="og:image:width" content="1200">', false);
        $response->assertSee('<meta property="og:image:height" content="630">', false);
        $response->assertSee('<meta property="og:type" content="website">', false);
        $response->assertSee('<meta property="og:site_name" content="AI Concierge Demo">', false);
        $response->assertSee('<meta name="twitter:card" content="summary_large_image">', false);
        $response->assertSee('property="og:url"', false);
    }

    public function test_open_graph_description_follows_the_locale_prefix(): void
    {
        $this->get('/fr')->assertSee(
            'Concierge IA Telegram avec un panneau d’admin Laravel',
            false
        );

        $this->get('/ar')->assertSee(
            'كونسيرج ذكاء اصطناعي على تيليغرام',
            false
        );
    }
}
