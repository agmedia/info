<?php

namespace Tests\Feature\Front;

use App\Models\Settings\Local\Language;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotFoundPageFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('app.locale', 'hr');
        config()->set('app.fallback_locale', 'hr');

        Language::query()->create([
            'code' => 'hr',
            'locale' => 'hr_HR',
            'name' => 'Croatian',
            'native_name' => 'Hrvatski',
            'is_default' => true,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Language::query()->create([
            'code' => 'en',
            'locale' => 'en_GB',
            'name' => 'English',
            'native_name' => 'English',
            'is_default' => false,
            'is_active' => true,
            'sort_order' => 2,
        ]);
    }

    public function test_not_found_page_uses_the_shared_navigation_and_footer_without_newsletter(): void
    {
        $stylesheetPath = 'front-theme/styles/pages/error-404.css';

        $response = $this->get('/stranica/koja-ne-postoji');

        $response
            ->assertNotFound()
            ->assertSee('<html lang="hr">', false)
            ->assertSee('<meta name="robots" content="noindex,follow">', false)
            ->assertDontSee('rel="canonical"', false)
            ->assertSee('class="site-header"', false)
            ->assertSee('class="desktop-nav"', false)
            ->assertSee('data-alpha-mobile-menu', false)
            ->assertSee('class="ac-error-page"', false)
            ->assertSee('aria-labelledby="not-found-title"', false)
            ->assertSeeText('Stranica nije pronađena')
            ->assertSee('href="'.route('home').'"', false)
            ->assertSee('href="'.route('search.index').'"', false)
            ->assertSee('href="'.route('contact.create').'"', false)
            ->assertSee('class="site-footer"', false)
            ->assertDontSee('class="footer-newsletter"', false)
            ->assertSee(asset($stylesheetPath).'?v='.filemtime(public_path($stylesheetPath)), false);

        $blade = file_get_contents(resource_path('views/errors/404.blade.php'));

        $this->assertIsString($blade);
        $this->assertStringNotContainsString('<style', $blade);
        $this->assertStringNotContainsString(' style=', $blade);
    }

    public function test_route_bound_english_not_found_page_keeps_english_actions(): void
    {
        $this->withSession(['front_locale' => 'en'])
            ->get('/blog/definitely-not-an-existing-post')
            ->assertNotFound()
            ->assertSessionHas('front_locale', 'en')
            ->assertSee('<html lang="en">', false)
            ->assertSeeText('Page not found')
            ->assertDontSeeText('Stranica nije pronađena')
            ->assertSee('href="'.route('search.index.en').'"', false)
            ->assertSee('href="'.route('contact.create.en').'"', false);
    }
}
