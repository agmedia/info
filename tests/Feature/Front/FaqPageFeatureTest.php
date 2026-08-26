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
            'answer_html' => '<p>Suradnja počinje razgovorom. '.str_repeat('Detaljan odgovor. ', 140).'Potpuni završetak odgovora.</p>',
        ]);

        $response = $this->get('/faq');

        $response
            ->assertOk()
            ->assertSee('front-theme/styles/pages/faq.css', false)
            ->assertSee('Kako izgleda suradnja?')
            ->assertSee('class="ac-faq-item', false)
            ->assertSee('"@type":"FAQPage"', false)
            ->assertSee('Potpuni završetak odgovora.', false);

        $this->get('/')
            ->assertOk()
            ->assertDontSee('"@type":"FAQPage"', false);

        preg_match_all(
            '#<script type="application/ld\+json">(.*?)</script>#s',
            $response->getContent(),
            $schemaMatches,
        );
        $faqSchema = collect($schemaMatches[1] ?? [])
            ->map(static fn (string $json): mixed => json_decode($json, true))
            ->firstWhere('@type', 'FAQPage');

        $this->assertIsArray($faqSchema);
        $this->assertStringEndsWith(
            'Potpuni završetak odgovora.',
            (string) data_get($faqSchema, 'mainEntity.0.acceptedAnswer.text'),
        );

        $this->assertDoesNotMatchRegularExpression(
            '/<details[^>]*\sopen(?:\s|>)/i',
            $response->getContent()
        );
    }
}
