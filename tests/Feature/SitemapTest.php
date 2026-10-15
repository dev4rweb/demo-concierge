<?php

namespace Tests\Feature;

use Tests\TestCase;

class SitemapTest extends TestCase
{
    public function test_sitemap_lists_public_pages_in_all_locales(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
        $response->assertSee('<urlset', false);
        $response->assertSee('/about', false);
        $response->assertSee('/how-to', false);
        $response->assertSee('hreflang="x-default"', false);

        foreach (config('localization.supported') as $locale) {
            $response->assertSee('hreflang="'.$locale.'"', false);

            if ($locale === config('localization.default', 'en')) {
                continue;
            }

            $response->assertSee('/'.$locale.'/about', false);
            $response->assertSee('/'.$locale.'/how-to', false);
        }
    }

    public function test_robots_txt_points_to_the_sitemap(): void
    {
        $robots = file_get_contents(public_path('robots.txt'));

        $this->assertNotFalse($robots);
        $this->assertStringContainsString('User-agent: *', $robots);
        $this->assertStringContainsString('Sitemap: https://demo-concierge.dev4rweb.com/sitemap.xml', $robots);
    }
}
