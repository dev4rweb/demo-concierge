<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuidePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_guide_page_renders_in_english(): void
    {
        $this->get('/how-to')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Guide')
                ->where('locale', 'en')
            );
    }

    public function test_guide_page_renders_in_arabic(): void
    {
        $this->get('/ar/how-to')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Guide')
                ->where('locale', 'ar')
                ->where('dir', 'rtl')
            );
    }
}
