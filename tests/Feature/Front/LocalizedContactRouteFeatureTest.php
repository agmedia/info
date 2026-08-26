<?php

namespace Tests\Feature\Front;

use App\Models\Content\ContentBlock;
use App\Models\Content\ContentBlockSlot;
use App\Models\Settings\Local\Language;
use App\Support\Localization\FrontendRoute;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocalizedContactRouteFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('app.locale', 'hr');
        config()->set('app.fallback_locale', 'hr');

        Language::query()->updateOrCreate(['code' => 'hr'], [
            'locale' => 'hr_HR',
            'name' => 'Croatian',
            'native_name' => 'Hrvatski',
            'is_default' => true,
            'is_active' => true,
            'sort_order' => 1,
        ]);
        Language::query()->updateOrCreate(['code' => 'en'], [
            'locale' => 'en_GB',
            'name' => 'English',
            'native_name' => 'English',
            'is_default' => false,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        ContentBlockSlot::query()
            ->where('placement', 'home.stats')
            ->update(['is_active' => false]);

        $block = ContentBlock::query()->create([
            'code' => 'home-alpha-stats',
            'name' => 'Localized contact route test',
            'type' => 'home_stats',
            'is_active' => true,
        ]);
        $block->translations()->create([
            'locale' => 'hr',
            'title' => 'Hrvatski kontakt',
            'payload' => [
                'contact_page' => [
                    'page_title' => 'Hrvatski kontakt naslov',
                    'form_title' => 'Hrvatski kontakt obrazac',
                ],
            ],
        ]);
        $block->translations()->create([
            'locale' => 'en',
            'title' => 'English contact',
            'payload' => [
                'contact_page' => [
                    'page_title' => 'English contact title',
                    'intro' => 'English contact SEO intro.',
                    'form_title' => 'English contact form',
                ],
            ],
        ]);
        $block->slots()->create([
            'placement' => 'home.stats',
            'frontend_variant' => 'all',
            'sort_order' => 0,
            'is_active' => true,
        ]);
    }

    public function test_contact_routes_are_localized_and_infer_locale_without_a_cookie(): void
    {
        $this->assertSame('https://info.test/kontakt', route('contact.create'));
        $this->assertSame('https://info.test/kontakt', route('contact.store'));
        $this->assertSame('https://info.test/contact', route('contact.create.en'));
        $this->assertSame('https://info.test/contact', route('contact.store.en'));
        $this->assertSame('/contact', FrontendRoute::localizeUrl('/kontakt', 'en'));
        $this->assertSame('/kontakt', FrontendRoute::localizeUrl('/contact', 'hr'));

        $this->getFresh('/contact')
            ->assertOk()
            ->assertSessionHas('front_locale', 'en')
            ->assertSeeText('English contact title')
            ->assertSee('action="'.route('contact.store.en').'"', false)
            ->assertSee('<meta name="description" content="English contact SEO intro.">', false)
            ->assertSee('rel="canonical" href="https://info.test/contact"', false)
            ->assertSee('"@type":"ContactPage","name":"English contact title"', false)
            ->assertSee('"@type":"BreadcrumbList"', false);

        $this->getFresh('/kontakt')
            ->assertOk()
            ->assertSessionHas('front_locale', 'hr')
            ->assertSeeText('Hrvatski kontakt naslov')
            ->assertSee('action="'.route('contact.store').'"', false)
            ->assertSee('rel="canonical" href="https://info.test/kontakt"', false);
    }

    public function test_contact_path_overrides_a_stale_session_and_switches_to_its_counterpart(): void
    {
        $croatianSwitchUrl = route('front.locale.switch', [
            'code' => 'hr',
            'redirect' => route('contact.create'),
        ]);
        $englishSwitchUrl = route('front.locale.switch', [
            'code' => 'en',
            'redirect' => route('contact.create.en'),
        ]);

        $this->withSession(['front_locale' => 'hr'])
            ->get('/contact')
            ->assertOk()
            ->assertSessionHas('front_locale', 'en')
            ->assertSee('href="'.$croatianSwitchUrl.'"', false)
            ->assertSee('action="'.route('contact.store.en').'"', false);

        $this->withSession(['front_locale' => 'en'])
            ->get('/kontakt')
            ->assertOk()
            ->assertSessionHas('front_locale', 'hr')
            ->assertSee('href="'.$englishSwitchUrl.'"', false)
            ->assertSee('action="'.route('contact.store').'"', false);
    }

    public function test_contact_validation_redirect_stays_on_the_canonical_locale_path(): void
    {
        $this->from('/contact')
            ->withSession(['front_locale' => 'hr'])
            ->post('/contact')
            ->assertRedirect('/contact')
            ->assertSessionHas('front_locale', 'en')
            ->assertSessionHasErrors(['email', 'message', 'accept_terms']);

        $this->from('/kontakt')
            ->withSession(['front_locale' => 'en'])
            ->post('/kontakt')
            ->assertRedirect('/kontakt')
            ->assertSessionHas('front_locale', 'hr')
            ->assertSessionHasErrors(['email', 'message', 'accept_terms']);
    }

    public function test_contact_legacy_prefix_redirects_and_invalid_or_unavailable_paths_fail_closed(): void
    {
        $this->get('/page/contact?source=legacy')
            ->assertStatus(301)
            ->assertRedirect('/contact?source=legacy');
        $this->get('/page/kontakt?source=legacy')
            ->assertStatus(301)
            ->assertRedirect('/kontakt?source=legacy');
        $this->get('/contact/extra')->assertNotFound();
        $this->get('/kontakt/extra')->assertNotFound();

        Language::query()->where('code', 'en')->update(['is_active' => false]);
        $this->app['session']->flush();

        $this->get('/contact')->assertNotFound();
        $this->post('/contact')->assertNotFound();
    }

    public function test_strict_english_contact_requires_exact_cms_copy(): void
    {
        ContentBlock::query()
            ->where('code', 'home-alpha-stats')
            ->firstOrFail()
            ->translations()
            ->where('locale', 'en')
            ->delete();

        $this->getFresh('/contact')->assertNotFound();
        $this->post('/contact')->assertNotFound();
    }

    private function getFresh(string $path): \Illuminate\Testing\TestResponse
    {
        $this->app['session']->flush();

        return $this->get($path);
    }
}
