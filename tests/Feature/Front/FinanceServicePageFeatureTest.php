<?php

namespace Tests\Feature\Front;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceServicePageFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_legacy_finance_service_url_redirects_to_canonical_advisory_page(): void
    {
        $this->get('/financije')
            ->assertStatus(301)
            ->assertRedirect(route('advisory.finance.show'));
    }

    public function test_legacy_finance_service_redirect_preserves_query_string(): void
    {
        $this->get('/financije?utm_source=legacy')
            ->assertStatus(301)
            ->assertRedirect(route('advisory.finance.show').'?utm_source=legacy');
    }
}
