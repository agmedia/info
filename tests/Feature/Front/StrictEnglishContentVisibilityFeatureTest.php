<?php

namespace Tests\Feature\Front;

use App\Models\Content\Blog\BlogPost;
use App\Models\Content\Call\CallPost;
use App\Models\Content\ContentBlock;
use App\Models\Content\ContentBlockSlot;
use App\Models\Content\Page\InfoPage;
use App\Models\Content\Resource\ResourceDocument;
use App\Models\Content\Service\ServicePage;
use App\Models\Content\Support\Faq;
use App\Models\Content\Team\TeamMember;
use App\Models\Settings\Local\Language;
use App\Services\Content\ContentBlockResolver;
use App\Services\Settings\SystemSettingsService;
use App\Support\Content\AboutPageDefaults;
use App\Support\Content\ServicePageTemplateRegistry;
use App\Support\Localization\FrontendLocalePolicy;
use App\Support\Localization\FrontendRoute;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class StrictEnglishContentVisibilityFeatureTest extends TestCase
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

    public function test_english_blog_lists_only_posts_with_an_exact_english_translation(): void
    {
        $this->createBlogPost('hr-only-post', 'Samo hrvatska objava', 'samo-hrvatska-objava', 'hr');
        $this->createBlogPost('en-post', 'English translated post', 'english-translated-post', 'en');

        $this->withSession(['front_locale' => 'en'])
            ->get(route('blog.index'))
            ->assertOk()
            ->assertSeeText('English translated post')
            ->assertDontSeeText('Samo hrvatska objava');

        $this->withSession(['front_locale' => 'en'])
            ->get(route('blog.show', ['slug' => 'samo-hrvatska-objava']))
            ->assertNotFound()
            ->assertSeeText('Page not found')
            ->assertDontSeeText('Stranica nije pronađena')
            ->assertSee('href="https://info.test/contact"', false);

        $this->withSession(['front_locale' => 'en'])
            ->get(route('blog.show', ['slug' => 'english-translated-post']))
            ->assertOk()
            ->assertSeeText('English translated post');
    }

    public function test_blog_index_is_unavailable_in_english_until_an_exact_english_post_exists(): void
    {
        $this->createBlogPost('hr-only-archive', 'Hrvatska arhiva bez prijevoda', 'hrvatska-arhiva', 'hr');
        app(SystemSettingsService::class)->put('store_blog_header_title', 'HR blog hero must never leak');

        $this->app['session']->flush();
        $this->get(route('blog.index'))
            ->assertOk()
            ->assertSeeText('Hrvatska arhiva bez prijevoda')
            ->assertSee('aria-label="HR blog hero must never leak"', false);

        $this->withSession(['front_locale' => 'en'])
            ->get(route('blog.index'))
            ->assertNotFound()
            ->assertSessionHas('front_locale', 'en')
            ->assertDontSeeText('Hrvatska arhiva bez prijevoda')
            ->assertDontSee('HR blog hero must never leak');

        $this->app['session']->flush();
        $this->get(route('front.locale.switch', [
            'code' => 'en',
            'redirect' => route('blog.index'),
        ]))
            ->assertRedirect(route('blog.index'))
            ->assertSessionHas('front_locale', 'en');
        $this->get(route('blog.index'))
            ->assertNotFound()
            ->assertDontSee('HR blog hero must never leak');

        $this->withSession(['front_locale' => 'hr'])
            ->get(route('blog.index'))
            ->assertOk()
            ->assertSeeText('Hrvatska arhiva bez prijevoda');
    }

    public function test_runtime_app_locale_change_cannot_make_english_the_content_default(): void
    {
        App::setLocale('en');

        $this->assertTrue(FrontendLocalePolicy::requiresExactTranslation('en'));
        $this->assertSame('en', FrontendLocalePolicy::fallbackLocale('en', 'hr'));
    }

    public function test_english_content_block_resolver_never_returns_a_croatian_only_block(): void
    {
        $block = ContentBlock::query()->create([
            'code' => 'strict-hr-only-block',
            'name' => 'Strict HR-only block',
            'type' => 'rich_text',
            'is_active' => true,
        ]);
        $block->slots()->create([
            'placement' => 'strict.translation.test',
            'frontend_variant' => 'all',
            'sort_order' => 0,
            'is_active' => true,
        ]);
        $block->translations()->create([
            'locale' => 'hr',
            'title' => 'Hrvatski blok koji ne smije procuriti',
        ]);

        $resolver = app(ContentBlockResolver::class);

        $this->assertCount(0, $resolver->forPlacement('strict.translation.test', 'en'));
        $this->assertCount(1, $resolver->forPlacement('strict.translation.test', 'hr'));
    }

    public function test_english_academy_page_stays_hidden_without_exact_program_copy(): void
    {
        $page = InfoPage::query()->where('layout', 'academy')->firstOrFail();
        $page->update([
            'is_active' => true,
            'published_at' => now()->subDay(),
        ]);
        $page->translations()->updateOrCreate(['locale' => 'en'], [
            'title' => 'Academy',
            'slug' => 'academy',
            'excerpt' => 'Only a partial English translation.',
            'payload' => ['academy_video_section' => ['title' => 'Videos']],
        ]);

        $this->withSession(['front_locale' => 'en'])
            ->get('/academy')
            ->assertNotFound();

        $this->withSession(['front_locale' => 'hr'])
            ->get('/akademija')
            ->assertOk();
    }

    public function test_partial_english_academy_programs_never_synthesize_croatian_copy(): void
    {
        $page = InfoPage::query()->where('layout', 'academy')->firstOrFail();
        $page->update([
            'is_active' => true,
            'published_at' => now()->subDay(),
        ]);
        $page->translations()->updateOrCreate(['locale' => 'en'], [
            'title' => 'English Academy',
            'slug' => 'academy',
            'excerpt' => 'Only exact English programme copy is visible.',
            'payload' => [
                'academy_programs' => [[
                    'title' => 'Exact English programme title',
                    'items' => [[
                        'title' => 'Exact English topic title',
                    ]],
                ]],
            ],
        ]);

        $this->withSession(['front_locale' => 'en'])
            ->get('/academy')
            ->assertOk()
            ->assertSeeText('Exact English programme title')
            ->assertSeeText('Exact English topic title')
            ->assertSeeText('Academy programmes')
            ->assertDontSeeText('Programi Akademije')
            ->assertDontSeeText('Seminari za male i srednje poduzetnike')
            ->assertDontSeeText('Edukacija je namijenjena poduzetnicima')
            ->assertDontSeeText('Pribavljanje kapitala')
            ->assertDontSeeText('Struktura kapitala predstavlja');
    }

    public function test_cookie_consent_never_links_english_visitors_to_a_croatian_only_legal_page(): void
    {
        $privacyPage = InfoPage::query()->where('code', 'privacy-policy')->firstOrFail();

        $this->withSession(['front_locale' => 'en'])
            ->get('/')
            ->assertOk()
            ->assertSee('data-privacy-url=""', false)
            ->assertDontSee('data-privacy-url="https://info.test/politika-privatnosti"', false);

        $privacyPage->translations()->updateOrCreate(['locale' => 'en'], [
            'title' => 'Privacy policy',
            'slug' => 'privacy-policy',
        ]);

        $this->withSession(['front_locale' => 'en'])
            ->get('/')
            ->assertOk()
            ->assertSee('data-privacy-url="https://info.test/privacy-policy"', false);
    }

    public function test_service_routes_use_language_specific_paths(): void
    {
        $page = ServicePage::query()->firstOrCreate([
            'template_key' => ServicePageTemplateRegistry::SERVICES_INDEX,
        ], [
            'code' => 'services-strict-route-test',
            'is_active' => true,
            'published_at' => now()->subDay(),
        ]);
        $page->translations()->updateOrCreate(['locale' => 'en'], [
            'title' => 'Services',
            'slug' => 'services',
            'payload' => [],
        ]);

        $this->withSession(['front_locale' => 'en'])
            ->get('/services')
            ->assertOk();

        $this->withSession(['front_locale' => 'en'])
            ->get('/usluge')
            ->assertOk()
            ->assertSessionHas('front_locale', 'hr');

        $this->withSession(['front_locale' => 'hr'])
            ->get('/usluge')
            ->assertOk();

        $this->withSession(['front_locale' => 'hr'])
            ->get('/services')
            ->assertOk()
            ->assertSessionHas('front_locale', 'en');

        $this->assertSame('https://info.test/audit', route('audit.show.en'));
        $this->assertSame('https://info.test/accounting', route('accounting.show.en'));
        $this->assertSame('https://info.test/advisory/raising-finance', route('advisory.funding.show.en'));
        $this->assertSame('https://info.test/eu-funds', route('eu-funds.show.en'));
        $this->assertSame('/audit', FrontendRoute::localizeUrl('/revizija', 'en'));
        $this->assertSame(
            '/advisory/raising-finance/bank-loans',
            FrontendRoute::localizeUrl('/savjetovanje/pribavljanje-financiranja/bankovni-krediti', 'en')
        );
    }

    public function test_unique_page_slugs_select_their_own_language_without_cross_locale_copy(): void
    {
        $croatianPage = $this->createPage('croatian-page');
        $croatianPage->translations()->create([
            'locale' => 'hr',
            'title' => 'Stranica samo na hrvatskom',
            'slug' => 'stranica-samo-na-hrvatskom',
            'body_html' => '<p>Hrvatski sadržaj koji ne smije procuriti.</p>',
        ]);

        $englishPage = $this->createPage('english-page');
        $englishPage->translations()->create([
            'locale' => 'en',
            'title' => 'English CMS page',
            'slug' => 'english-cms-page',
            'body_html' => '<p>Exact English page content.</p>',
        ]);

        $this->withSession(['front_locale' => 'en'])
            ->get(route('pages.show', ['slug' => 'stranica-samo-na-hrvatskom']))
            ->assertOk()
            ->assertSessionHas('front_locale', 'hr')
            ->assertSeeText('Stranica samo na hrvatskom');

        $this->withSession(['front_locale' => 'en'])
            ->get(route('pages.show', ['slug' => 'english-cms-page']))
            ->assertOk()
            ->assertSeeText('Exact English page content.');
    }

    public function test_english_about_page_renders_only_exact_cms_sections_while_croatian_uses_its_cms_payload(): void
    {
        $this->assertSame([], AboutPageDefaults::merge(null, 'en'));
        $partialEnglishStory = AboutPageDefaults::merge([
            'story' => ['title' => 'English story without a translated body'],
        ], 'en');
        $this->assertSame('', data_get($partialEnglishStory, 'story.body_html'));
        $this->assertStringNotContainsString(
            'ALPHA CAPITALIS okuplja stručnjake',
            (string) data_get($partialEnglishStory, 'story.body_html')
        );

        $page = InfoPage::query()->where('code', 'about-us')->firstOrFail();
        $page->translations()->where('locale', 'hr')->firstOrFail()->update([
            'payload' => ['about_page' => AboutPageDefaults::merge([], 'hr')],
        ]);
        $page->translations()->updateOrCreate(['locale' => 'en'], [
            'title' => 'About strict CMS',
            'slug' => 'about-us',
            'meta_title' => 'About strict CMS | ALPHA CAPITALIS',
            'meta_description' => 'Exact English about metadata from the CMS.',
            'payload' => [
                'about_page' => [
                    'hero' => [
                        'title' => 'Our exact CMS story',
                        'lead' => 'Exact English hero lead from the CMS.',
                        'image_alt' => 'English CMS team photograph',
                    ],
                    'story' => [
                        'body_html' => '<p>Exact English story from the CMS.</p>',
                    ],
                    'values' => [
                        'label' => 'Our exact CMS values',
                        'title' => 'Exact English values title',
                        'intro' => 'Exact English values introduction.',
                        'items' => [[
                            'title' => 'Learn from CMS',
                            'body_html' => '<p>Exact English value copy.</p>',
                        ]],
                    ],
                    'why' => [
                        'kicker' => 'Why exact CMS exists',
                        'title' => 'Exact English purpose title',
                        'body_html' => '<p>Exact English purpose copy.</p>',
                    ],
                    'team' => [
                        'label' => 'Our exact CMS team',
                        'title' => 'Exact English team title',
                        'body_html' => '<p>Exact English team copy.</p>',
                        'stats' => [['value' => '75', 'label' => 'professionals']],
                        'button_label' => '',
                    ],
                ],
            ],
        ]);

        $this->withSession(['front_locale' => 'en'])
            ->get('/about-us')
            ->assertOk()
            ->assertSee('aria-label="Our exact CMS story"', false)
            ->assertSeeText('Exact English story from the CMS.')
            ->assertSee('aria-label="Our exact CMS values"', false)
            ->assertSeeText('Exact English purpose title')
            ->assertSee('aria-label="Exact English team title"', false)
            ->assertSee('alt="English CMS team photograph"', false)
            ->assertDontSee('class="ac-about-culture"', false)
            ->assertDontSee('class="ac-about-responsibility"', false)
            ->assertDontSee('class="ac-about-references"', false)
            ->assertDontSeeText('Quality business starts with quality relationships')
            ->assertDontSeeText('AUXILIUM CAPITALIS - investing in the future')
            ->assertDontSeeText('Client trust confirms the quality of our work');

        $this->withSession(['front_locale' => 'hr'])
            ->get('/o-nama')
            ->assertOk()
            ->assertSee('class="ac-about-culture"', false)
            ->assertSee('class="ac-about-responsibility"', false)
            ->assertSee('class="ac-about-references"', false)
            ->assertSee('aria-label="Naša kultura"', false)
            ->assertSee('aria-label="Društveno odgovorno poslovanje"', false)
            ->assertSee('aria-label="Naše reference"', false);
    }

    public function test_contact_page_uses_only_exact_locale_cms_copy_without_lang_or_croatian_fallbacks(): void
    {
        $block = ContentBlock::query()->create([
            'code' => 'strict-contact-copy',
            'name' => 'Strict contact copy',
            'type' => 'home_stats',
            'is_active' => true,
        ]);
        $block->slots()->create([
            'placement' => 'home.stats',
            'frontend_variant' => 'all',
            'sort_order' => 0,
            'is_active' => true,
        ]);
        $block->translations()->create([
            'locale' => 'en',
            'title' => 'English contact copy',
            'payload' => [],
        ]);
        $block->translations()->create([
            'locale' => 'hr',
            'title' => 'Hrvatski kontakt sadržaj',
            'payload' => ['contact_page' => []],
        ]);
        $englishTranslation = $block->translations()->where('locale', 'en')->firstOrFail();
        $croatianTranslation = $block->translations()->where('locale', 'hr')->firstOrFail();
        $englishPayload = (array) $englishTranslation->payload;
        $croatianPayload = (array) $croatianTranslation->payload;

        $englishPayload['contact_page'] = [
            'page_title' => 'English CMS contact title',
            'intro' => 'English CMS contact introduction.',
            'form_title' => 'English CMS form title',
            'form_intro' => 'English CMS form introduction.',
            'name_label' => 'English CMS full name label',
            'email_label' => 'English CMS email label',
            'phone_label' => 'English CMS phone label',
            'subject_label' => 'English CMS subject label',
            'message_label' => 'English CMS message label',
            'consent_label' => 'English CMS consent label',
            'submit_label' => 'English CMS submit label',
            'direct_title' => 'English CMS direct title',
            'direct_body' => 'English CMS direct body.',
            'direct_email_label' => 'English CMS direct email',
            'direct_phone_label' => 'English CMS direct phone',
            'direct_response_time_label' => 'English CMS response time',
            'direct_response_fallback' => 'English CMS business hours',
            'help_body' => 'English CMS help body.',
            'sent_status' => 'English CMS sent status.',
        ];
        $croatianPayload['contact_page']['name_label'] = 'HR oznaka koja ne smije procuriti';
        $croatianPayload['contact_page']['help_title'] = 'HR pomoć koja ne smije procuriti';

        $englishTranslation->update(['payload' => $englishPayload]);
        $croatianTranslation->update(['payload' => $croatianPayload]);

        $this->withSession(['front_locale' => 'en'])
            ->get('/contact')
            ->assertOk()
            ->assertSee('aria-label="English CMS contact title"', false)
            ->assertSeeText('English CMS contact introduction.')
            ->assertSeeText('English CMS form title')
            ->assertSeeText('English CMS full name label')
            ->assertSeeText('English CMS consent label')
            ->assertSeeText('English CMS submit label')
            ->assertSeeText('English CMS direct title')
            ->assertSeeText('English CMS help body.')
            ->assertDontSeeText('HR oznaka koja ne smije procuriti')
            ->assertDontSeeText('HR pomoć koja ne smije procuriti')
            ->assertDontSeeText('Before you send');

        $this->withSession(['front_locale' => 'en'])
            ->post('/contact', [
                'name' => 'English CMS Contact Tester',
                'email' => 'english-cms-contact@example.test',
                'message' => 'This message verifies the CMS success status.',
                'accept_terms' => '1',
            ])
            ->assertRedirect('/contact')
            ->assertSessionHas('status', 'English CMS sent status.');

        $this->withSession(['front_locale' => 'hr'])
            ->get('/kontakt')
            ->assertOk()
            ->assertSeeText('HR oznaka koja ne smije procuriti')
            ->assertSeeText('HR pomoć koja ne smije procuriti')
            ->assertDontSeeText('English CMS full name label');
    }

    public function test_english_faq_resources_and_calls_hide_croatian_only_records(): void
    {
        $croatianFaq = Faq::query()->create([
            'code' => 'croatian-faq',
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $croatianFaq->translations()->create([
            'locale' => 'hr',
            'question' => 'Hrvatsko pitanje bez prijevoda?',
            'slug' => 'hrvatsko-pitanje-bez-prijevoda',
            'answer_html' => '<p>Hrvatski odgovor.</p>',
        ]);

        $englishFaq = Faq::query()->create([
            'code' => 'english-faq',
            'is_active' => true,
            'sort_order' => 2,
        ]);
        $englishFaq->translations()->create([
            'locale' => 'en',
            'question' => 'English translated question?',
            'slug' => 'english-translated-question',
            'answer_html' => '<p>English answer.</p>',
        ]);

        $croatianDocument = $this->createResource('croatian-document');
        $croatianDocument->translations()->create([
            'locale' => 'hr',
            'title' => 'Hrvatski dokument bez prijevoda',
            'slug' => 'hrvatski-dokument-bez-prijevoda',
        ]);

        $englishDocument = $this->createResource('english-document');
        $englishDocument->translations()->create([
            'locale' => 'en',
            'title' => 'English translated document',
            'slug' => 'english-translated-document',
        ]);

        $croatianCall = $this->createCall('croatian-call');
        $croatianCall->translations()->create([
            'locale' => 'hr',
            'title' => 'Hrvatski poziv bez prijevoda',
            'slug' => 'hrvatski-poziv-bez-prijevoda',
            'body_html' => '<p>Hrvatski poziv.</p>',
        ]);

        $englishCall = $this->createCall('english-call');
        $englishCall->translations()->create([
            'locale' => 'en',
            'title' => 'English translated call',
            'slug' => 'english-translated-call',
            'body_html' => '<p>English call body.</p>',
        ]);

        $this->withSession(['front_locale' => 'en'])
            ->get(route('faq.index'))
            ->assertOk()
            ->assertSeeText('English translated question?')
            ->assertDontSeeText('Hrvatsko pitanje bez prijevoda?');

        $this->withSession(['front_locale' => 'en'])
            ->get(route('resources.index'))
            ->assertOk()
            ->assertSeeText('English translated document')
            ->assertDontSeeText('Hrvatski dokument bez prijevoda');

        $this->withSession(['front_locale' => 'en'])
            ->get(route('resources.show', ['slug' => 'hrvatski-dokument-bez-prijevoda']))
            ->assertNotFound();

        $this->withSession(['front_locale' => 'en'])
            ->get(route('eu-funds.calls.show', ['slug' => 'hrvatski-poziv-bez-prijevoda']))
            ->assertOk()
            ->assertSessionHas('front_locale', 'hr')
            ->assertSeeText('Hrvatski poziv bez prijevoda');

        $this->withSession(['front_locale' => 'en'])
            ->get(route('eu-funds.calls.show.en', ['slug' => 'english-translated-call']))
            ->assertOk()
            ->assertSeeText('English translated call');
    }

    public function test_english_faq_and_resource_indexes_require_active_published_exact_records(): void
    {
        $croatianFaq = Faq::query()->create([
            'code' => 'strict-index-hr-faq',
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $croatianFaq->translations()->create([
            'locale' => 'hr',
            'question' => 'Hrvatsko pitanje za strict indeks?',
            'slug' => 'hrvatsko-pitanje-za-strict-indeks',
            'answer_html' => '<p>Hrvatski odgovor.</p>',
        ]);

        $inactiveEnglishFaq = Faq::query()->create([
            'code' => 'strict-index-inactive-en-faq',
            'is_active' => false,
            'sort_order' => 2,
        ]);
        $inactiveEnglishFaq->translations()->create([
            'locale' => 'en',
            'question' => 'Inactive English question?',
            'slug' => 'inactive-english-question',
            'answer_html' => '<p>Inactive English answer.</p>',
        ]);

        $croatianResource = $this->createResource('strict-index-hr-resource');
        $croatianResource->translations()->create([
            'locale' => 'hr',
            'title' => 'Hrvatski resurs za strict indeks',
            'slug' => 'hrvatski-resurs-za-strict-indeks',
        ]);

        $inactiveEnglishResource = $this->createResource('strict-index-inactive-en-resource');
        $inactiveEnglishResource->update(['is_active' => false]);
        $inactiveEnglishResource->translations()->create([
            'locale' => 'en',
            'title' => 'Inactive English resource',
            'slug' => 'inactive-english-resource',
        ]);

        $futureEnglishResource = $this->createResource('strict-index-future-en-resource');
        $futureEnglishResource->update(['published_at' => now()->addDay()]);
        $futureEnglishResource->translations()->create([
            'locale' => 'en',
            'title' => 'Future English resource',
            'slug' => 'future-english-resource',
        ]);

        $this->withSession(['front_locale' => 'en'])
            ->get(route('faq.index'))
            ->assertNotFound()
            ->assertDontSeeText('Hrvatsko pitanje za strict indeks?');

        $this->withSession(['front_locale' => 'en'])
            ->get(route('resources.index'))
            ->assertNotFound()
            ->assertDontSeeText('Hrvatski resurs za strict indeks');

        $this->withSession(['front_locale' => 'hr'])
            ->get(route('faq.index'))
            ->assertOk()
            ->assertSeeText('Hrvatsko pitanje za strict indeks?');

        $this->withSession(['front_locale' => 'hr'])
            ->get(route('resources.index'))
            ->assertOk()
            ->assertSeeText('Hrvatski resurs za strict indeks');

        $inactiveEnglishFaq->update(['is_active' => true]);

        $this->withSession(['front_locale' => 'en'])
            ->get(route('faq.index'))
            ->assertOk()
            ->assertSeeText('Inactive English question?')
            ->assertDontSeeText('Hrvatsko pitanje za strict indeks?');

        $publishedEnglishResource = $this->createResource('strict-index-published-en-resource');
        $publishedEnglishResource->translations()->create([
            'locale' => 'en',
            'title' => 'Published English resource',
            'slug' => 'published-english-resource',
        ]);

        $this->withSession(['front_locale' => 'en'])
            ->get(route('resources.index'))
            ->assertOk()
            ->assertSeeText('Published English resource')
            ->assertDontSeeText('Hrvatski resurs za strict indeks')
            ->assertDontSeeText('Inactive English resource')
            ->assertDontSeeText('Future English resource');
    }

    public function test_english_call_detail_uses_only_exact_locale_eu_funds_cms_copy(): void
    {
        $servicePage = ServicePage::query()->firstOrCreate([
            'template_key' => ServicePageTemplateRegistry::EU_FUNDS,
        ], [
            'code' => 'eu-funds-call-detail-cms-copy',
            'is_active' => true,
            'published_at' => now()->subDay(),
        ]);
        $servicePage->update([
            'is_active' => true,
            'published_at' => now()->subDay(),
        ]);
        $servicePage->translations()->updateOrCreate(['locale' => 'en'], [
            'title' => 'CMS English EU Funds',
            'slug' => 'cms-english-eu-funds',
            'payload' => [
                'calls' => [
                    'title' => 'CMS English funding calls',
                    'intro' => 'CMS English empty-call copy.',
                ],
                'meeting' => [
                    'title' => 'CMS English meeting title',
                    'contact_title' => 'CMS English contact title',
                    'intro' => 'CMS English contact copy.',
                    'button_label' => 'CMS English meeting button',
                    'status' => 'CMS English meeting status.',
                ],
            ],
        ]);
        $servicePage->translations()->updateOrCreate(['locale' => 'hr'], [
            'title' => 'CMS hrvatski EU fondovi',
            'slug' => 'cms-hrvatski-eu-fondovi',
            'payload' => [
                'calls' => [
                    'title' => 'CMS hrvatski natječaji',
                    'intro' => 'CMS hrvatski tekst praznog poziva.',
                ],
                'meeting' => [
                    'title' => 'CMS hrvatski kontaktni naslov',
                    'contact_title' => 'CMS hrvatska kontaktna kartica',
                    'intro' => 'CMS hrvatski kontaktni tekst.',
                    'button_label' => 'CMS hrvatski gumb',
                    'status' => 'CMS hrvatski status.',
                ],
            ],
        ]);

        $call = $this->createCall('cms-copy-call');
        $call->translations()->create([
            'locale' => 'en',
            'title' => 'CMS-copy call without body',
            'slug' => 'cms-copy-call-without-body',
        ]);

        $this->withSession(['front_locale' => 'en'])
            ->get(route('eu-funds.calls.show.en', ['slug' => 'cms-copy-call-without-body']))
            ->assertOk()
            ->assertSeeText('CMS English EU Funds')
            ->assertSeeText('CMS English funding calls')
            ->assertSeeText('CMS English empty-call copy.')
            ->assertSee('aria-label="CMS English meeting title"', false)
            ->assertSeeText('CMS English contact title')
            ->assertSeeText('CMS English contact copy.')
            ->assertSeeText('CMS English meeting button')
            ->assertSeeText('CMS English meeting status.')
            ->assertDontSeeText('CMS hrvatski EU fondovi')
            ->assertDontSeeText('CMS hrvatski tekst praznog poziva.')
            ->assertDontSeeText('Content for this call has not been added yet.')
            ->assertDontSeeText('Let’s turn an opportunity into a successful EU project.')
            ->assertDontSeeText('Our team supports you from eligibility checks through application and implementation.');
    }

    public function test_english_eu_funds_call_status_uses_the_exact_cms_category_translation(): void
    {
        $servicePage = ServicePage::query()->firstOrCreate([
            'template_key' => ServicePageTemplateRegistry::EU_FUNDS,
        ], [
            'code' => 'eu-funds-call-status-cms-copy',
            'is_active' => true,
            'published_at' => now()->subDay(),
        ]);
        $servicePage->update([
            'is_active' => true,
            'published_at' => now()->subDay(),
        ]);
        $servicePage->translations()->updateOrCreate(['locale' => 'en'], [
            'title' => 'EU Funds',
            'slug' => 'eu-funds',
            'payload' => [
                'calls' => [
                    'kicker' => 'FUNDING CALLS',
                    'title' => 'Funding calls by status',
                    'intro' => '',
                    'view_all_label' => 'View all funding calls',
                ],
            ],
        ]);

        $category = \App\Models\Catalog\Category\Category::query()->create([
            'scope' => \App\Models\Catalog\Category\Category::SCOPE_CALL,
            'code' => 'open-calls-cms-status',
            'is_active' => true,
            'show_in_menu' => false,
            'sort_order' => 1,
        ]);
        $category->translations()->create([
            'scope' => \App\Models\Catalog\Category\Category::SCOPE_CALL,
            'locale' => 'hr',
            'name' => 'Otvoreni pozivi iz CMS-a',
            'slug' => 'otvoreni-pozivi-iz-cms-a',
        ]);
        $category->translations()->create([
            'scope' => \App\Models\Catalog\Category\Category::SCOPE_CALL,
            'locale' => 'en',
            'name' => 'Open calls from CMS',
            'slug' => 'open-calls-from-cms',
        ]);

        $call = $this->createCall('english-call-status');
        $call->translations()->create([
            'locale' => 'en',
            'title' => 'English call status item',
            'slug' => 'english-call-status-item',
            'body_html' => '<p>English call body.</p>',
        ]);
        $call->categories()->attach($category->id, ['sort_order' => 0, 'is_primary' => true]);

        $this->withSession(['front_locale' => 'en'])
            ->get('/eu-funds')
            ->assertOk()
            ->assertSeeText('Open calls from CMS')
            ->assertSeeText('English call status item')
            ->assertDontSeeText('Otvoreno')
            ->assertDontSeeText('Otvoreni pozivi iz CMS-a');
    }

    public function test_english_team_feed_hides_members_without_an_english_translation(): void
    {
        $teamPage = InfoPage::query()->firstOrCreate(['code' => 'team-page'], [
            'layout' => 'default',
            'is_active' => true,
            'published_at' => now()->subDay(),
        ]);
        $teamPage->update([
            'is_active' => true,
            'published_at' => now()->subDay(),
        ]);
        $teamPage->translations()->updateOrCreate(['locale' => 'en'], [
            'title' => 'Our team',
            'slug' => 'our-team',
            'excerpt' => 'Meet our translated team.',
        ]);

        $croatianMember = TeamMember::query()->create([
            'code' => 'croatian-member',
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $croatianMember->translations()->create([
            'locale' => 'hr',
            'name' => 'Hrvatski Član Bez Prijevoda',
            'position' => 'Direktor',
        ]);

        $englishMember = TeamMember::query()->create([
            'code' => 'english-member',
            'is_active' => true,
            'sort_order' => 2,
        ]);
        $englishMember->translations()->create([
            'locale' => 'en',
            'name' => 'English Translated Member',
            'position' => 'Director',
        ]);

        $this->withSession(['front_locale' => 'hr'])
            ->get(route('team.index.en'))
            ->assertOk()
            ->assertSessionHas('front_locale', 'en')
            ->assertSee('class="ac-team-page"', false)
            ->assertSee('rel="canonical" href="'.route('team.index.en').'"', false)
            ->assertSee('href="'.route('front.locale.switch', [
                'code' => 'hr',
                'redirect' => route('team.index'),
            ]).'"', false)
            ->assertSeeText('English Translated Member')
            ->assertDontSeeText('Hrvatski Član Bez Prijevoda');

        $this->withSession(['front_locale' => 'en'])
            ->get(route('team.index'))
            ->assertOk()
            ->assertSessionHas('front_locale', 'hr')
            ->assertSee('class="ac-team-page"', false)
            ->assertSee('rel="canonical" href="'.route('team.index').'"', false);
    }

    public function test_english_search_excludes_records_without_an_english_translation(): void
    {
        $this->createBlogPost('search-hr', 'Strictneedle hrvatski rezultat', 'strictneedle-hr', 'hr');
        $this->createBlogPost('search-en', 'Strictneedle English result', 'strictneedle-en', 'en');

        $this->withSession(['front_locale' => 'en'])
            ->get(FrontendRoute::url('search.index', ['q' => 'strictneedle'], 'en'))
            ->assertOk()
            ->assertSeeText('Strictneedle English result')
            ->assertDontSeeText('Strictneedle hrvatski rezultat');
    }

    public function test_english_home_service_card_with_an_empty_cms_url_uses_the_english_route(): void
    {
        ContentBlockSlot::query()
            ->where('placement', 'home.services')
            ->update(['is_active' => false]);

        $block = ContentBlock::query()->create([
            'code' => 'strict-english-home-services-route',
            'name' => 'Strict English home services route',
            'type' => 'home_services',
            'is_active' => true,
        ]);
        $block->translations()->create([
            'locale' => 'en',
            'title' => 'English services from CMS',
            'payload' => [
                'services' => [[
                    'key' => 'audit',
                    'title' => 'English audit card',
                    'url' => '',
                ]],
            ],
        ]);
        $block->slots()->create([
            'placement' => 'home.services',
            'frontend_variant' => 'desktop',
            'sort_order' => 0,
            'is_active' => true,
        ]);
        ContentBlockResolver::bumpCacheVersion();

        $this->withSession(['front_locale' => 'en'])
            ->get('/')
            ->assertOk()
            ->assertSee('class="service-card" href="https://info.test/audit"', false)
            ->assertDontSee('class="service-card" href="https://info.test/revizija"', false);
    }

    private function createBlogPost(string $code, string $title, string $slug, string $locale): BlogPost
    {
        $post = BlogPost::query()->create([
            'code' => $code,
            'is_active' => true,
            'published_at' => now()->subDay(),
        ]);

        $post->translations()->create([
            'locale' => $locale,
            'title' => $title,
            'slug' => $slug,
            'excerpt' => $title.' excerpt',
            'body_html' => '<p>'.$title.' body.</p>',
        ]);

        return $post;
    }

    private function createPage(string $code): InfoPage
    {
        return InfoPage::query()->create([
            'code' => $code,
            'layout' => 'default',
            'is_active' => true,
            'published_at' => now()->subDay(),
        ]);
    }

    private function createResource(string $code): ResourceDocument
    {
        return ResourceDocument::query()->create([
            'code' => $code,
            'group_code' => 'downloads',
            'is_active' => true,
            'published_at' => now()->subDay(),
        ]);
    }

    private function createCall(string $code): CallPost
    {
        return CallPost::query()->create([
            'code' => $code,
            'is_active' => true,
            'published_at' => now()->subDay(),
        ]);
    }
}
