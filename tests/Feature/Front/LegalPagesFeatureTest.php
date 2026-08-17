<?php

namespace Tests\Feature\Front;

use App\Models\Content\Page\InfoPage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LegalPagesFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_privacy_policy_renders_current_company_and_data_protection_information(): void
    {
        config()->set('app.locale', 'hr');
        config()->set('app.fallback_locale', 'hr');

        $response = $this->get('/politika-privatnosti');

        $response
            ->assertOk()
            ->assertSeeText('Politika privatnosti')
            ->assertSeeText('ALPHA CAPITALIS D.O.O.')
            ->assertSeeText('OIB: 40742241290')
            ->assertSeeText('Vaša prava')
            ->assertSeeText('Agenciji za zaštitu osobnih podataka')
            ->assertSee('data-cookie-consent-trigger', false)
            ->assertSee('front-theme/styles/pages/legal.css', false)
            ->assertDontSeeText('DPD Croatia')
            ->assertDontSeeText('Overseas Express');

        $this->assertSame(
            'legal',
            InfoPage::query()->where('code', 'privacy-policy')->value('layout')
        );
    }

    public function test_terms_page_renders_professional_service_terms_without_legacy_store_copy(): void
    {
        config()->set('app.locale', 'hr');
        config()->set('app.fallback_locale', 'hr');

        $this->get('/uvjeti-koristenja')
            ->assertOk()
            ->assertSeeText('Uvjeti korištenja')
            ->assertSeeText('Priroda sadržaja i profesionalnih usluga')
            ->assertSeeText('Mjerodavno pravo i rješavanje sporova')
            ->assertSee('/politika-privatnosti', false)
            ->assertDontSeeText('Jedan dva d.o.o.')
            ->assertDontSeeText('suda u Virovitici')
            ->assertDontSeeText('košarici za kupnju');

        $this->assertSame(
            'legal',
            InfoPage::query()->where('code', 'terms-of-use')->value('layout')
        );
    }
}
