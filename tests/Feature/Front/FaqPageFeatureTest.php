<?php

namespace Tests\Feature\Front;

use App\Models\Content\Support\Faq;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FaqPageFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_faq_items_are_closed_by_default_and_page_styles_are_loaded(): void
    {
        $faq = Faq::query()->create([
            'code' => 'faq-front-test',
            'group_code' => 'general',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $faq->translations()->create([
            'locale' => app()->getLocale(),
            'question' => 'Kako izgleda suradnja?',
            'slug' => 'kako-izgleda-suradnja',
            'answer_html' => '<p>Suradnja počinje razgovorom.</p>',
        ]);

        $response = $this->get('/faq');

        $response
            ->assertOk()
            ->assertSee('front-theme/styles/pages/faq.css', false)
            ->assertSee('Kako izgleda suradnja?')
            ->assertSee('class="ac-faq-item', false);

        $this->assertDoesNotMatchRegularExpression(
            '/<details[^>]*\sopen(?:\s|>)/i',
            $response->getContent()
        );
    }
}
