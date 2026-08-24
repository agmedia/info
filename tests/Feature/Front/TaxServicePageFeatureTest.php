<?php

namespace Tests\Feature\Front;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaxServicePageFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_legacy_tax_service_url_redirects_to_canonical_advisory_page(): void
    {
        $this->get('/porezi')
            ->assertStatus(301)
            ->assertRedirect(route('advisory.tax.show'));
    }

    public function test_legacy_tax_service_redirect_preserves_query_string(): void
    {
        $this->get('/porezi?utm_source=legacy')
            ->assertStatus(301)
            ->assertRedirect(route('advisory.tax.show').'?utm_source=legacy');
    }
}
