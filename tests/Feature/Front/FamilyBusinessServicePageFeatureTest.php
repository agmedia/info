<?php

namespace Tests\Feature\Front;

use App\Models\Content\Blog\BlogPost;
use App\Models\Content\Blog\BlogPostTranslation;
use App\Models\Content\Service\ServicePage;
use App\Models\Content\Support\Faq;
use App\Models\Content\Support\FaqTranslation;
use App\Models\Content\Team\TeamMember;
use App\Models\Content\Team\TeamMemberTranslation;
use App\Support\Content\ServicePageTemplateRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FamilyBusinessServicePageFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_family_business_page_uses_service_page_content_and_manual_sources(): void
    {
        config()->set('app.locale', 'hr');
        config()->set('app.fallback_locale', 'hr');

        $page = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::FAMILY_BUSINESS)
            ->firstOrFail();

        $post = BlogPost::query()->create([
            'code' => 'manual-family-post',
            'is_active' => true,
            'published_at' => now()->subDay(),
        ]);

        BlogPostTranslation::query()->create([
            'post_id' => $post->id,
            'locale' => 'hr',
            'title' => 'Ručno odabran blog post',
            'slug' => 'rucno-odabran-blog-post',
            'excerpt' => 'Sažetak ručno odabranog blog posta.',
            'body_html' => '<p>Sadržaj blog posta.</p>',
        ]);

        $faq = Faq::query()->create([
            'code' => 'family-faq-manual',
            'group_code' => 'general',
            'is_active' => true,
        ]);

        FaqTranslation::query()->create([
            'faq_id' => $faq->id,
            'locale' => 'hr',
            'question' => 'Kako izgleda prva konzultacija?',
            'slug' => 'kako-izgleda-prva-konzultacija',
            'answer_html' => '<p>Početna konzultacija mapira prioritete obitelji i poslovanja.</p>',
        ]);

        $member = TeamMember::query()->create([
            'code' => 'ivana-horvat',
            'is_active' => true,
            'email' => 'ivana@example.test',
        ]);

        TeamMemberTranslation::query()->create([
            'team_member_id' => $member->id,
            'locale' => 'hr',
            'name' => 'Ivana Horvat',
            'position' => 'Partnerica',
            'departments' => 'Obiteljski biznis',
            'description_html' => '<p>Vodi savjetovanja za tranziciju i upravljanje.</p>',
        ]);

        $page->update([
            'payload' => [
                'blog_source' => [
                    'mode' => 'manual',
                    'post_ids' => [$post->id],
                    'limit' => 6,
                ],
                'faq_source' => [
                    'mode' => 'manual',
                    'faq_ids' => [$faq->id],
                ],
                'team_source' => [
                    'mode' => 'manual',
                    'member_ids' => [$member->id],
                ],
                'brochure_url' => '/docs/family-business.pdf',
            ],
        ]);

        $page->translations()
            ->where('locale', 'hr')
            ->update([
                'payload' => [
                    'hero' => [
                        'brand_title' => 'ALPHA CAPITALIS CUSTOM',
                        'intro' => 'Custom uvodni tekst za obiteljski biznis.',
                    ],
                    'capability_cta' => [
                        'label' => 'Dogovorite konzultacije',
                    ],
                    'meeting' => [
                        'direct_phone_label' => 'Telefon ureda',
                        'form_labels' => [
                            'first_name' => 'Ime osobe',
                        ],
                    ],
                    'brochure_label' => 'Preuzmite prilagođenu brošuru',
                ],
            ]);

        $this->get('/obiteljski-biznis')
            ->assertOk()
            ->assertSee('ALPHA CAPITALIS CUSTOM')
            ->assertSee('Custom uvodni tekst za obiteljski biznis.')
            ->assertSee('Ručno odabran blog post')
            ->assertSee('Kako izgleda prva konzultacija?')
            ->assertSee('Ivana Horvat')
            ->assertSee('Dogovorite konzultacije')
            ->assertSee('Telefon ureda')
            ->assertSee('Ime osobe')
            ->assertSee('Preuzmite prilagođenu brošuru');
    }
}
