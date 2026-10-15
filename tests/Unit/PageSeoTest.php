<?php

namespace Tests\Unit;

use App\Support\PageSeo;
use Illuminate\Http\Request;
use Tests\TestCase;

class PageSeoTest extends TestCase
{
    public function test_localized_url_omits_the_default_locale_prefix(): void
    {
        $this->assertSame(url('/about'), PageSeo::localizedUrl('/about', 'en'));
        $this->assertSame(url('/fr/about'), PageSeo::localizedUrl('/about', 'fr'));
        $this->assertSame(url('/ar'), PageSeo::localizedUrl('/', 'ar'));
        $this->assertSame(url('/'), PageSeo::localizedUrl('/', 'en'));
    }

    public function test_unprefixed_path_strips_a_supported_locale(): void
    {
        $this->assertSame('/about', PageSeo::unprefixedPath(Request::create('https://example.test/zh/about')));
        $this->assertSame('/how-to', PageSeo::unprefixedPath(Request::create('https://example.test/ru/how-to')));
        $this->assertSame('/', PageSeo::unprefixedPath(Request::create('https://example.test/es')));
        $this->assertSame('/about', PageSeo::unprefixedPath(Request::create('https://example.test/about')));
    }

    public function test_alternates_cover_every_supported_locale(): void
    {
        $alternates = PageSeo::alternatesForPath('/about');
        $codes = array_column($alternates, 'code');

        $this->assertSame(config('localization.supported'), $codes);
        $this->assertStringEndsWith('/about', $alternates[0]['url']);
        $this->assertStringContainsString('/pt/about', collect($alternates)->firstWhere('code', 'pt')['url']);
    }
}
