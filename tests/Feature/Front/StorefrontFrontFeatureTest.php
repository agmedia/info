<?php

namespace Tests\Feature\Front;

use App\Models\Catalog\Category\Category;
use App\Models\Catalog\Category\CategoryTranslation;
use App\Models\Content\Blog\BlogPost;
use App\Models\Content\Blog\BlogPostTranslation;
use App\Models\Content\Career\JobOpening;
use App\Models\Content\ContentBlock;
use App\Models\Content\ContentBlockSlot;
use App\Models\Content\Page\InfoPage;
use App\Models\Content\Page\InfoPageTranslation;
use App\Models\Content\Resource\ResourceDocument;
use App\Models\Content\Resource\ResourceDocumentTranslation;
use App\Models\Content\Service\ServicePage;
use App\Models\Content\Support\CareerApplication;
use App\Models\Content\Support\Comment;
use App\Models\Content\Support\ContactMessage;
use App\Models\Content\Team\TeamMember;
use App\Models\Content\Team\TeamMemberTranslation;
use App\Models\Settings\Local\Language;
use App\Services\Front\NavigationMenuService;
use App\Services\Settings\SystemSettingsService;
use App\Support\Content\AboutPageDefaults;
use App\Support\Content\CareerPageDefaults;
use App\Support\Content\ServicePageTemplateRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Tests\TestCase;

class StorefrontFrontFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(SystemSettingsService::class)->put(NavigationMenuService::CHROME_SETTINGS_KEY, [
            'hr' => [
                'header_primary_cta_label' => 'Zatraži ponudu',
                'header_calculator_cta_label' => 'MSFI 16 Kalkulator',
                'footer_newsletter_label' => 'Newsletter',
                'footer_newsletter_title' => 'Primajte važne novosti na',
                'footer_newsletter_accent' => 'vrijeme.',
                'footer_newsletter_email_placeholder' => 'Vaša email adresa',
                'footer_newsletter_submit_label' => 'Nastavite na prijavu za newsletter',
                'footer_newsletter_consent' => 'Želim primati newsletter i prihvaćam obradu podataka u tu svrhu.',
                'footer_tagline' => 'Vaš kompas kroz svijet financija.',
                'footer_services_label' => 'Usluge',
                'footer_contact_label' => 'Kontakt',
                'footer_copyright_text' => 'Alpha Capitalis d.o.o. Sva prava pridržana.',
                'footer_cookie_settings_label' => 'Postavke kolačića',
                'footer_back_to_top_label' => 'Na vrh',
            ],
            'en' => [
                'header_primary_cta_label' => 'Request an offer',
                'header_calculator_cta_label' => 'IFRS 16 Calculator',
                'footer_newsletter_label' => 'Newsletter',
                'footer_newsletter_title' => 'Receive important updates',
                'footer_newsletter_accent' => 'on time.',
                'footer_newsletter_email_placeholder' => 'Your email address',
                'footer_newsletter_submit_label' => 'Continue to newsletter signup',
                'footer_newsletter_consent' => 'I want to receive the newsletter and consent to data processing for this purpose.',
                'footer_tagline' => 'Your compass through the world of finance.',
                'footer_services_label' => 'Services',
                'footer_contact_label' => 'Contact',
                'footer_copyright_text' => 'Alpha Capitalis d.o.o. All rights reserved.',
                'footer_cookie_settings_label' => 'Cookie settings',
                'footer_back_to_top_label' => 'Back to top',
            ],
        ]);
    }

    public function test_public_site_uses_manrope_as_the_default_google_font(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('fonts.googleapis.com/css2?family=Manrope', false)
            ->assertSee('data-front-font="manrope"', false)
            ->assertSee('front-theme/styles/typography.css', false);
    }

    public function test_public_site_uses_the_google_font_selected_in_settings(): void
    {
        app(SystemSettingsService::class)->put('store_front_google_font', 'poppins');

        $this->get('/')
            ->assertOk()
            ->assertSee('fonts.googleapis.com/css2?family=Poppins', false)
            ->assertSee('data-front-font="poppins"', false);
    }

    public function test_public_site_supports_general_sans_from_fontshare(): void
    {
        app(SystemSettingsService::class)->put('store_front_google_font', 'general-sans');

        $this->get('/')
            ->assertOk()
            ->assertSee('api.fontshare.com/v2/css', false)
            ->assertSee('data-front-font="general-sans"', false)
            ->assertSee('https://cdn.fontshare.com', false)
            ->assertDontSee('fonts.googleapis.com/css2', false);
    }

    public function test_homepage_header_switches_to_the_solid_navigation_state_on_the_first_scroll(): void
    {
        $script = (string) file_get_contents(public_path('front-theme/scripts/alpha-redesign.js'));
        $styles = (string) file_get_contents(public_path('front-theme/styles/alpha-redesign.css'));

        $this->assertStringContainsString('const shouldUseStickyBar = window.scrollY > 0;', $script);
        $this->assertStringNotContainsString('homeHero.getBoundingClientRect().bottom <= headerHeight', $script);
        $this->assertMatchesRegularExpression(
            '/\.site-header\.is-scrolled\s*\{[^}]*background:\s*var\(--navy-deep\)/s',
            $styles,
        );
    }

    public function test_homepage_hero_uses_its_own_content_font_links_and_responsive_videos(): void
    {
        $this->seedHomeBlock('home_hero', 'home.hero', [
            'title' => 'CMS HERO TITLE',
            'subtitle' => 'CMS hero subtitle.',
            'cta_label' => 'CMS primary button',
            'cta_url' => '/contact',
            'payload' => [
                'page_title' => 'CMS homepage title',
                'secondary_cta_label' => 'CMS secondary button',
                'secondary_cta_url' => '/usluge',
            ],
        ]);

        app(SystemSettingsService::class)->putMany([
            'store_home_hero_font' => 'playfair-display',
            'store_home_hero_font_weight' => 700,
            'store_home_hero_title' => 'Hero naslov iz postavki',
            'store_home_hero_subtitle' => 'Hero podnaslov iz postavki.',
            'store_home_hero_primary_label' => 'Prvi hero gumb',
            'store_home_hero_primary_url' => '/contact',
            'store_home_hero_secondary_label' => 'Drugi hero gumb',
            'store_home_hero_secondary_url' => '/usluge',
            'store_home_hero_desktop_video_path' => 'store-settings/home-hero/desktop.mp4',
            'store_home_hero_mobile_video_path' => 'store-settings/home-hero/mobile.webm',
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('fonts.googleapis.com/css2?family=Playfair+Display', false)
            ->assertSee('data-front-font="playfair-display"', false)
            ->assertSee('data-front-font-weight="700"', false)
            ->assertSee('<title>CMS homepage title</title>', false)
            ->assertSee('<meta name="description" content="CMS hero subtitle.">', false)
            ->assertSee('"description":"CMS hero subtitle."', false)
            ->assertSee('CMS HERO TITLE')
            ->assertSee('CMS hero subtitle.')
            ->assertSee('href="/contact"', false)
            ->assertSee('CMS primary button')
            ->assertSee('href="/usluge"', false)
            ->assertSee('CMS secondary button')
            ->assertDontSee('Hero naslov iz postavki')
            ->assertDontSee('Hero podnaslov iz postavki.')
            ->assertDontSee('Prvi hero gumb')
            ->assertDontSee('Drugi hero gumb')
            ->assertSee('data-alpha-hero-video-desktop-src="'.Storage::disk('public')->url('store-settings/home-hero/desktop.mp4').'"', false)
            ->assertSee('data-alpha-hero-video-desktop-type="video/mp4"', false)
            ->assertSee('data-alpha-hero-video-mobile-src="'.Storage::disk('public')->url('store-settings/home-hero/mobile.webm').'"', false)
            ->assertSee('data-alpha-hero-video-mobile-type="video/webm"', false);
    }

    public function test_bundled_cms_media_resolves_to_its_deployed_public_asset(): void
    {
        $media = new Media([
            'id' => 999999,
            'file_name' => 'career-team-building.jpg',
            'disk' => 'public',
            'conversions_disk' => 'public',
            'manipulations' => [],
            'custom_properties' => [
                'bundled_asset_path' => 'front-theme/images/career/career-team-building.jpg',
            ],
            'generated_conversions' => [],
            'responsive_images' => [],
        ]);

        $this->assertSame(
            asset('front-theme/images/career/career-team-building.jpg'),
            $media->getUrl(),
        );
        $this->assertSame(
            public_path('front-theme/images/career/career-team-building.jpg'),
            $media->getPath(),
        );
        $this->assertFileExists($media->getPath());
    }

    public function test_public_content_routes_are_available(): void
    {
        [$post, $postSlug] = $this->seedBlogPost();
        [$page, $pageSlug] = $this->seedInfoPage();

        $this->get('/blog')->assertOk();
        $this->get('/blog/'.$postSlug)->assertOk();
        $this->get('/faq')->assertOk();
        $this->get('/'.$pageSlug)->assertOk();
        $this->get('/page/'.$pageSlug)
            ->assertStatus(301)
            ->assertRedirect(route('pages.show', ['slug' => $pageSlug]));
        $this->get('/alpha-capitalis-tim')->assertOk();
        $this->get('/obiteljski-biznis')->assertNotFound();
        $this->get('/kontakt')->assertOk();
        $this->get('/reference')->assertOk();
        $this->get('/ac-forma-robot')->assertOk();
        $this->get('/leasing-kalkulator')->assertOk();

        $this->assertNotNull($post);
        $this->assertNotNull($page);
    }

    public function test_home_page_renders_admin_managed_content_blocks(): void
    {
        Cache::flush();

        $hero = ContentBlock::query()->create([
            'code' => 'test-home-hero',
            'name' => 'Test Home Hero',
            'type' => 'home_hero',
            'is_active' => true,
            'payload' => null,
        ]);
        $hero->translations()->create([
            'locale' => 'hr',
            'title' => 'TEST HERO TITLE',
            'subtitle' => 'TEST HERO SUBTITLE',
            'cta_label' => 'Primarni gumb',
            'cta_url' => '/usluge',
            'payload' => [
                'secondary_cta_label' => 'Sekundarni gumb',
                'secondary_cta_url' => '/contact',
            ],
        ]);
        $hero->slots()->create([
            'placement' => 'home.hero',
            'frontend_variant' => 'desktop',
            'target_type' => null,
            'target_ref' => null,
            'sort_order' => 0,
            'is_active' => true,
        ]);

        $stats = ContentBlock::query()->create([
            'code' => 'test-home-stats',
            'name' => 'Test Home Stats',
            'type' => 'home_stats',
            'is_active' => true,
            'payload' => null,
        ]);
        $stats->translations()->create([
            'locale' => 'hr',
            'title' => 'Stats',
            'payload' => [
                'stats' => [
                    ['value' => '123', 'suffix' => '+', 'label' => 'Test projekata'],
                    ['value' => '45', 'suffix' => '+', 'label' => 'Test klijenata'],
                    ['value' => '6', 'suffix' => '+', 'label' => 'Test stručnjaka'],
                ],
            ],
        ]);
        $stats->slots()->create([
            'placement' => 'home.stats',
            'frontend_variant' => 'desktop',
            'target_type' => null,
            'target_ref' => null,
            'sort_order' => 0,
            'is_active' => true,
        ]);

        $services = ContentBlock::query()->create([
            'code' => 'test-home-services',
            'name' => 'Test Home Services',
            'type' => 'home_services',
            'is_active' => true,
            'payload' => null,
        ]);
        $services->translations()->create([
            'locale' => 'hr',
            'title' => 'Test usluge naslov',
            'subtitle' => 'Test usluge uvod.',
            'payload' => [
                'title_accent' => 'test istaknuti dio',
                'services' => [
                    [
                        'title' => 'Test usluga',
                        'subtitle' => '',
                        'text' => 'Tekst test usluge iz admin bloka.',
                        'url' => '/usluge',
                        'action_label' => 'Detaljnije',
                    ],
                ],
            ],
        ]);
        $services->slots()->create([
            'placement' => 'home.services',
            'frontend_variant' => 'desktop',
            'target_type' => null,
            'target_ref' => null,
            'sort_order' => 0,
            'is_active' => true,
        ]);

        $this->withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
        ])
            ->get('/')
            ->assertOk()
            ->assertSee('TEST HERO TITLE')
            ->assertSee('Primarni gumb')
            ->assertSee('Test projekata')
            ->assertSee('Test usluge naslov')
            ->assertSee('Tekst test usluge iz admin bloka.');
    }

    public function test_info_page_uses_clean_url_and_legacy_page_url_redirects(): void
    {
        [, $pageSlug] = $this->seedInfoPage();

        $this->get('/'.$pageSlug)
            ->assertOk()
            ->assertSee('ac-default-page', false)
            ->assertSee('front-theme/styles/pages/default.css', false)
            ->assertSee('data-image-reveal', false);
        $this->get('/page/'.$pageSlug)
            ->assertStatus(301)
            ->assertRedirect(route('pages.show', ['slug' => $pageSlug]));
    }

    public function test_legacy_about_category_url_redirects_to_about_page(): void
    {
        $this->get('/pages/category/o-nama?utm_source=legacy')
            ->assertStatus(301)
            ->assertRedirect(route('pages.show', ['slug' => 'o-nama']).'?utm_source=legacy');
    }

    public function test_career_page_renders_curated_cms_layout(): void
    {
        $this->seedCroatianCareerCmsPayload();

        $this->get('/karijera')
            ->assertOk()
            ->assertSee('Mjesto gdje ljudi i karijere rastu')
            ->assertSee('Tražimo ljude, ne samo životopise.')
            ->assertSee('ALPHA CAPITALIS danas okuplja 75 stručnjaka')
            ->assertSee('Sky Officeu')
            ->assertSee('Razvoj koji nije samo fraza')
            ->assertSee('class="ac-career-gallery"', false)
            ->assertSee('Ljudi zbog kojih ostaješ')
            ->assertSee('Otvorene pozicije')
            ->assertSee('Pošalji nam svoj životopis')
            ->assertSee('fa-duotone fa-thin fa-fw fa-handshake', false)
            ->assertSee('fa-duotone fa-thin fa-fw fa-hands-holding-heart', false)
            ->assertSee('fa-duotone fa-thin fa-fw fa-arrow-trend-up', false)
            ->assertSee('fa-duotone fa-thin fa-fw fa-lightbulb', false)
            ->assertSee('fa-duotone fa-thin fa-fw fa-people-group', false)
            ->assertDontSee('ac-career-card-number', false);
    }

    public function test_career_page_renders_custom_copy_from_translation_payload(): void
    {
        $careerPage = InfoPage::query()->where('code', 'career')->firstOrFail();

        $careerPage->translations()->where('locale', 'hr')->update([
            'payload' => [
                'career_page' => [
                    'intro' => [
                        'section_title' => 'Karijera po tvojoj mjeri',
                        'title' => 'Rasti s nama',
                        'highlight' => 'Custom uvodni highlight za karijera stranicu.',
                        'kicker' => 'Zajedno napredujemo',
                        'button_label' => 'POGLEDAJ POZICIJE',
                        'stat_value' => '75+',
                        'stat_label' => 'kolegica i kolega u našem timu',
                        'body' => [
                            'Custom uvodni odlomak za karijera stranicu.',
                        ],
                    ],
                    'values' => [
                        'povjerenje od prvog dana',
                    ],
                    'process' => [
                        'kicker' => 'Kako izgleda prijava',
                        'title_line_one' => 'Proces zapošljavanja u',
                        'title_line_two' => 'ALPHA CAPITALISU',
                        'intro' => 'Custom intro procesa.',
                        'steps' => [
                            [
                                'step' => 'Faza 01',
                                'title' => 'Prvi kontakt',
                                'description' => 'Custom opis prvog koraka.',
                            ],
                            [
                                'step' => 'Faza 02',
                                'title' => 'Provjera znanja',
                                'description' => 'Custom opis drugog koraka.',
                            ],
                        ],
                    ],
                    'application' => [
                        'kicker' => 'Pridruži se',
                        'title' => 'Pridruzi nam se danas',
                        'highlight' => 'Custom CTA highlight.',
                        'paragraphs' => [
                            'Custom CTA odlomak 1.',
                            'Custom CTA odlomak 2.',
                            'Custom CTA odlomak 3.',
                        ],
                    ],
                    'form' => [
                        'title' => 'Posalji otvorenu prijavu',
                        'intro' => 'Predstavi nam se u nekoliko koraka.',
                        'first_name' => 'Tvoje ime',
                        'submit' => 'Prijavi se sada',
                    ],
                    'stories_section' => [
                        'title' => 'Kako izgleda život kod nas',
                        'intro' => 'Tim koji te podržava',
                    ],
                    'stories' => [
                        [
                            'title' => 'Ljudi koji dijele znanje',
                            'body_html' => '<p>Jedan editor za cijeli tekst kartice.</p><p><strong>Formatirani tekst kartice.</strong></p>',
                        ],
                    ],
                ],
            ],
        ]);

        $this->get('/karijera')
            ->assertOk()
            ->assertSee('Rasti s nama')
            ->assertSee('Karijera po tvojoj mjeri')
            ->assertSee('Custom uvodni highlight za karijera stranicu.')
            ->assertSee('Custom uvodni odlomak za karijera stranicu.')
            ->assertSee('Zajedno napredujemo')
            ->assertSee('POGLEDAJ POZICIJE')
            ->assertSee('75+')
            ->assertSee('kolegica i kolega u našem timu')
            ->assertSee('povjerenje od prvog dana')
            ->assertSee('Kako izgleda prijava')
            ->assertSee('Proces zapošljavanja u')
            ->assertDontSee('Faza 01')
            ->assertSee('Prvi kontakt')
            ->assertSee('Custom opis prvog koraka.')
            ->assertSee('Pridruzi nam se danas')
            ->assertSee('Pridruži se')
            ->assertSee('Custom CTA highlight.')
            ->assertSee('Custom CTA odlomak 2.')
            ->assertSee('Kako izgleda život kod nas')
            ->assertSee('Tim koji te podržava')
            ->assertSee('Ljudi koji dijele znanje')
            ->assertSee('<p>Jedan editor za cijeli tekst kartice.</p><p><strong>Formatirani tekst kartice.</strong></p>', false)
            ->assertSee('Posalji otvorenu prijavu')
            ->assertSee('Predstavi nam se u nekoliko koraka.')
            ->assertSee('Tvoje ime')
            ->assertSee('Prijavi se sada')
            ->assertDontSee('Postani dio tima')
            ->assertDontSee('Pošaljite nam svoj CV');
    }

    public function test_career_page_renders_consolidated_editor_content(): void
    {
        $careerPage = InfoPage::query()->where('code', 'career')->firstOrFail();

        $careerPage->translations()->where('locale', 'hr')->update([
            'payload' => [
                'career_page' => [
                    'intro' => [
                        'body' => ['Uvodni tekst na svijetloj podlozi.'],
                        'hero_body_html' => '<p>Jedan editor za hero sadržaj.</p><p><strong>Formatirani hero tekst.</strong></p>',
                    ],
                    'values_text' => "prva uređena pogodnost\ndruga uređena pogodnost",
                    'process' => [
                        'title' => 'Jedinstveni naslov sekcije razvoja',
                    ],
                    'stories' => [
                        [
                            'title' => 'Priča iz jedinstvenog editora',
                            'body_html' => '<p>Jedan editor za priču.</p>',
                            'list_text' => "prva uređena stavka\ndruga uređena stavka",
                        ],
                    ],
                    'application' => [
                        'body_html' => '<p>Jedan editor za otvorene pozicije.</p><p><em>Formatirani poziv na prijavu.</em></p>',
                    ],
                ],
            ],
        ]);

        $this->get('/karijera')
            ->assertOk()
            ->assertSee('<p>Jedan editor za hero sadržaj.</p><p><strong>Formatirani hero tekst.</strong></p>', false)
            ->assertSee('prva uređena pogodnost')
            ->assertSee('druga uređena pogodnost')
            ->assertSee('Jedinstveni naslov sekcije razvoja')
            ->assertSee('Jedan editor za priču.')
            ->assertSee('prva uređena stavka')
            ->assertSee('druga uređena stavka')
            ->assertSee('<p>Jedan editor za otvorene pozicije.</p><p><em>Formatirani poziv na prijavu.</em></p>', false);
    }

    public function test_career_page_uses_uploaded_hero_image(): void
    {
        Storage::fake('public');
        config()->set('media-library.disk_name', 'public');
        config()->set('media-library.queue_conversions_by_default', false);

        $this->seedCroatianCareerCmsPayload();
        $careerPage = InfoPage::query()->where('code', 'career')->firstOrFail();
        $careerPage->clearMediaCollection('career_hero_image');
        $media = $careerPage
            ->addMedia(UploadedFile::fake()->image('custom-career-hero.png', 1440, 1059))
            ->withCustomProperties([
                'alt' => ['hr' => 'Novi portret ALPHA CAPITALIS tima u parku'],
            ])
            ->toMediaCollection('career_hero_image');
        $media = $media->fresh();
        $expectedHeroUrl = $media->hasGeneratedConversion('career_hero_1440x1059')
            ? $media->getUrl('career_hero_1440x1059')
            : $media->getUrl();

        $this->get('/karijera')
            ->assertOk()
            ->assertSee($expectedHeroUrl, false)
            ->assertSee('alt="Novi portret ALPHA CAPITALIS tima u parku"', false)
            ->assertDontSee('front-theme/images/career/karijera.png', false);
    }

    public function test_academy_page_renders_custom_cms_layout(): void
    {
        $this->get('/akademija')
            ->assertOk()
            ->assertSee('ALPHA CAPITALIS AKADEMIJA')
            ->assertSee('Predavanja i edukativni sadržaj na temu korporativnih financija')
            ->assertDontSee('This page has no body content.');
    }

    public function test_references_page_renders_uploaded_logo_media_items(): void
    {
        Storage::fake('public');
        config()->set('media-library.disk_name', 'public');
        config()->set('media-library.queue_conversions_by_default', false);

        $page = InfoPage::query()->create([
            'code' => 'references-demo',
            'layout' => 'references',
            'is_active' => true,
            'sort_order' => 0,
        ]);

        InfoPageTranslation::query()->create([
            'page_id' => $page->id,
            'locale' => 'hr',
            'title' => 'Reference Demo',
            'slug' => 'reference-demo',
            'excerpt' => 'Odabrani klijenti i partneri.',
        ]);

        $page->addMedia(UploadedFile::fake()->image('puratos-logo.png', 520, 160))
            ->usingName('Puratos Konding d.o.o.')
            ->withCustomProperties([
                'alt' => ['hr' => 'Puratos Konding d.o.o.'],
                'caption' => ['hr' => 'Puratos Konding d.o.o.'],
            ])
            ->toMediaCollection('reference_logos');

        $page->addMedia(UploadedFile::fake()->image('gauss-logo.png', 520, 160))
            ->usingName('Gauss d.o.o.')
            ->withCustomProperties([
                'alt' => ['hr' => 'Gauss d.o.o.'],
                'caption' => ['hr' => 'Gauss d.o.o.'],
            ])
            ->toMediaCollection('reference_logos');

        $this->get('/reference-demo')
            ->assertOk()
            ->assertSee('Reference Demo')
            ->assertSee('Puratos Konding d.o.o.')
            ->assertSee('Gauss d.o.o.')
            ->assertDontSee('Logotipi će uskoro biti dostupni i na ovoj stranici.');
    }

    public function test_academy_page_renders_program_copy_from_translation_payload(): void
    {
        $academyPage = InfoPage::query()->where('code', 'academy')->firstOrFail();

        $academyPage->translations()->where('locale', 'hr')->update([
            'payload' => [
                'academy_programs' => [
                    [
                        'title' => 'Financijski seminari za SME',
                        'intro' => 'Custom intro za prvi veliki box.',
                        'items' => [
                            [
                                'title' => 'Planiranje kapitala',
                                'text' => 'Custom tekst za prvi unutarnji box.',
                            ],
                        ],
                    ],
                    [
                        'title' => 'Specijalističke edukacije',
                    ],
                ],
            ],
        ]);

        $this->get('/akademija')
            ->assertOk()
            ->assertSee('Financijski seminari za SME')
            ->assertSee('Custom intro za prvi veliki box.')
            ->assertSee('Planiranje kapitala')
            ->assertSee('Custom tekst za prvi unutarnji box.')
            ->assertSee('Specijalističke edukacije')
            ->assertDontSee('Seminari za male i srednje poduzetnike')
            ->assertDontSee('Pribavljanje kapitala');
    }

    public function test_academy_page_shows_posts_from_selected_page_source_category(): void
    {
        $academyPage = InfoPage::query()->where('code', 'academy')->firstOrFail();

        $caseStudy = $this->seedBlogCategory('Case Study', 'case-study');
        $tax = $this->seedBlogCategory('Tax', 'tax');

        $this->seedBlogPost([$caseStudy->id], 'Case Study Alpha', 'case-study-alpha', 'Case study excerpt', now()->subHour());
        $this->seedBlogPost([$caseStudy->id], 'Case Study Beta', 'case-study-beta', 'Case study beta excerpt', now()->subDay());
        $this->seedBlogPost([$tax->id], 'Tax Gamma', 'tax-gamma', 'Tax excerpt', now()->subDays(2));

        $academyPage->update([
            'payload' => [
                'blog_source' => [
                    'mode' => 'category',
                    'category_id' => $caseStudy->id,
                    'limit' => 2,
                ],
            ],
        ]);

        $academyPage->translations()->where('locale', 'hr')->update([
            'payload' => [
                'academy_blog_section' => [
                    'title' => 'Latest Case Studies',
                    'intro' => 'Selected from the Case Study category.',
                ],
            ],
        ]);

        $this->get('/akademija')
            ->assertOk()
            ->assertSee('Latest Case Studies')
            ->assertSee('Case Study Alpha')
            ->assertSee('Case Study Beta')
            ->assertDontSee('Tax Gamma');
    }

    public function test_academy_page_shows_only_selected_download_documents_from_page_sources(): void
    {
        $academyPage = InfoPage::query()->where('code', 'academy')->firstOrFail();

        $caseStudy = $this->seedBlogCategory('Case Study', 'case-study');
        $this->seedBlogPost([$caseStudy->id], 'Case Study Alpha', 'case-study-alpha', 'Case study excerpt', now()->subHour());

        $selectedOne = $this->seedResourceDocument('academy-resource-alpha', 'Akademija dokument Alpha', 'academy-resource-alpha');
        $selectedTwo = $this->seedResourceDocument('academy-resource-beta', 'Akademija dokument Beta', 'academy-resource-beta');
        $hidden = $this->seedResourceDocument('academy-resource-hidden', 'Skriven dokument', 'academy-resource-hidden');

        $academyPage->update([
            'payload' => [
                'blog_source' => [
                    'mode' => 'category',
                    'category_id' => $caseStudy->id,
                    'limit' => 1,
                ],
                'resource_source' => [
                    'mode' => 'manual',
                    'document_ids' => [$selectedTwo->id, $selectedOne->id],
                ],
            ],
        ]);

        $academyPage->translations()->where('locale', 'hr')->update([
            'payload' => [
                'academy_blog_section' => [
                    'title' => 'Latest Case Studies',
                    'intro' => 'Selected from the Case Study category.',
                ],
                'academy_resource_section' => [
                    'title' => 'Dokumenti za preuzimanje',
                    'intro' => 'Odabrani dokumenti za Akademiju.',
                ],
            ],
        ]);

        $this->get('/akademija')
            ->assertOk()
            ->assertSeeInOrder(['Latest Case Studies', 'Dokumenti za preuzimanje'])
            ->assertSee('Dokumenti za preuzimanje')
            ->assertSee('Odabrani dokumenti za Akademiju.')
            ->assertSee('Akademija dokument Alpha')
            ->assertSee('Akademija dokument Beta')
            ->assertDontSee('Skriven dokument')
            ->assertSee(route('resources.show', ['slug' => 'academy-resource-alpha']), false)
            ->assertSee(route('resources.show', ['slug' => 'academy-resource-beta']), false);

        $this->assertNotNull($hidden);
    }

    public function test_academy_page_shows_embedded_videos_below_download_documents(): void
    {
        $academyPage = InfoPage::query()->where('code', 'academy')->firstOrFail();

        $caseStudy = $this->seedBlogCategory('Case Study', 'case-study');
        $this->seedBlogPost([$caseStudy->id], 'Case Study Alpha', 'case-study-alpha', 'Case study excerpt', now()->subHour());
        $document = $this->seedResourceDocument('academy-resource-alpha', 'Akademija dokument Alpha', 'academy-resource-alpha');

        $academyPage->update([
            'payload' => [
                'blog_source' => [
                    'mode' => 'category',
                    'category_id' => $caseStudy->id,
                    'limit' => 1,
                ],
                'resource_source' => [
                    'mode' => 'manual',
                    'document_ids' => [$document->id],
                ],
                'video_source' => [
                    'mode' => 'manual',
                    'items' => [
                        [
                            'title' => 'ALPHA CAPITALIS - Uvod u svijet financija',
                            'youtube_url' => 'https://www.youtube.com/watch?v=GivT5NzdO1c',
                        ],
                        [
                            'title' => 'ALPHA CAPITALIS - Kako izraditi poslovni plan',
                            'youtube_url' => 'https://www.youtube.com/watch?v=VA7LlrHMsiM',
                        ],
                    ],
                ],
            ],
        ]);

        $academyPage->translations()->where('locale', 'hr')->update([
            'payload' => [
                'academy_blog_section' => [
                    'title' => 'Latest Case Studies',
                    'intro' => 'Selected from the Case Study category.',
                ],
                'academy_resource_section' => [
                    'title' => 'Dokumenti za preuzimanje',
                    'intro' => 'Odabrani dokumenti za Akademiju.',
                ],
                'academy_video_section' => [
                    'title' => 'Online edukacija i personalizirani trening',
                    'intro' => 'Odabrani edukativni video sadržaj.',
                ],
            ],
        ]);

        $this->get('/akademija')
            ->assertOk()
            ->assertSeeInOrder([
                'Dokumenti za preuzimanje',
                'Online edukacija i personalizirani trening',
                'ALPHA CAPITALIS - Uvod u svijet financija',
            ])
            ->assertSee('Odabrani edukativni video sadržaj.')
            ->assertSee('https://www.youtube.com/embed/GivT5NzdO1c?rel=0&amp;modestbranding=1', false)
            ->assertSee('https://www.youtube.com/embed/VA7LlrHMsiM?rel=0&amp;modestbranding=1', false)
            ->assertDontSee('Pokaži još');
    }

    public function test_academy_page_shows_show_more_button_when_video_list_overflows(): void
    {
        $academyPage = InfoPage::query()->where('code', 'academy')->firstOrFail();

        $academyPage->update([
            'payload' => [
                'video_source' => [
                    'mode' => 'manual',
                    'items' => [
                        ['title' => 'Video 1', 'youtube_url' => 'https://www.youtube.com/watch?v=GivT5NzdO1c'],
                        ['title' => 'Video 2', 'youtube_url' => 'https://www.youtube.com/watch?v=VA7LlrHMsiM'],
                        ['title' => 'Video 3', 'youtube_url' => 'https://www.youtube.com/watch?v=caJnbuuKo_w'],
                        ['title' => 'Video 4', 'youtube_url' => 'https://www.youtube.com/watch?v=fTcFqkJE164'],
                        ['title' => 'Video 5', 'youtube_url' => 'https://www.youtube.com/watch?v=5FFawI7XCN4'],
                    ],
                ],
            ],
        ]);

        $academyPage->translations()->where('locale', 'hr')->update([
            'payload' => [
                'academy_video_section' => [
                    'title' => 'Online edukacija i personalizirani trening',
                    'intro' => 'Odabrani edukativni video sadržaj.',
                ],
            ],
        ]);

        $this->get('/akademija')
            ->assertOk()
            ->assertSee('Pokaži još')
            ->assertSee('data-academy-video-show-more', false)
            ->assertSee('data-academy-video-hidden', false);
    }

    public function test_page_blog_grid_block_shows_only_posts_from_selected_blog_category(): void
    {
        [$page, $pageSlug] = $this->seedInfoPage();

        $caseStudy = $this->seedBlogCategory('Case Study', 'case-study');
        $tax = $this->seedBlogCategory('Tax', 'tax');

        $this->seedBlogPost([$caseStudy->id], 'Case Study Alpha', 'case-study-alpha', 'Case study excerpt', now()->subHour());
        $this->seedBlogPost([$caseStudy->id], 'Case Study Beta', 'case-study-beta', 'Case study beta excerpt', now()->subDay());
        $this->seedBlogPost([$tax->id], 'Tax Gamma', 'tax-gamma', 'Tax excerpt', now()->subDays(2));

        $block = ContentBlock::query()->create([
            'code' => 'case-study-page-grid',
            'name' => 'Case Study Page Grid',
            'type' => 'blog_grid_3',
            'is_active' => true,
            'payload' => [
                'source' => 'query',
                'category_ids' => [$caseStudy->id],
                'sort' => 'newest',
            ],
        ]);

        $block->translations()->create([
            'locale' => 'en',
            'title' => 'Latest Case Studies',
            'subtitle' => 'Selected from the Case Study category.',
            'cta_label' => 'View category',
            'cta_url' => '',
            'payload' => [
                'items_limit' => 2,
            ],
        ]);
        $block->translations()->create([
            'locale' => 'hr',
            'title' => 'Latest Case Studies',
            'subtitle' => 'Selected from the Case Study category.',
            'cta_label' => 'View category',
            'cta_url' => '',
            'payload' => [
                'items_limit' => 2,
            ],
        ]);

        $block->slots()->create([
            'placement' => 'page.bottom',
            'frontend_variant' => 'all',
            'target_type' => 'page',
            'target_ref' => $pageSlug,
            'sort_order' => 0,
            'is_active' => true,
        ]);

        $this->get('/'.$pageSlug)
            ->assertOk()
            ->assertSee('Latest Case Studies')
            ->assertSee('Case Study Alpha')
            ->assertSee('Case Study Beta')
            ->assertDontSee('Tax Gamma')
            ->assertSee('/blog/case-study', false);

        $this->assertNotNull($page);
    }

    public function test_career_application_form_stores_submission_and_uploaded_cv(): void
    {
        Storage::fake('local');

        $this->post(route('career.applications.store'), [
            'first_name' => 'Ivana',
            'last_name' => 'Horvat',
            'email' => 'ivana@example.test',
            'message' => 'Veselim se prilici za razgovor i dodatno upoznavanje vašeg tima.',
            'cv' => UploadedFile::fake()->create('ivana-horvat-cv.pdf', 200, 'application/pdf'),
            'accept_terms' => '1',
        ])->assertRedirect('/karijera#career-cta');

        $application = CareerApplication::query()->latest('id')->first();

        $this->assertNotNull($application);
        $this->assertSame('Ivana', $application->first_name);
        $this->assertSame('Horvat', $application->last_name);
        $this->assertSame('ivana@example.test', $application->email);
        $this->assertSame(CareerApplication::STATUS_NEW, $application->status);
        Storage::disk('local')->assertExists((string) $application->cv_path);
    }

    public function test_english_career_form_uses_localized_action_and_redirect(): void
    {
        Storage::fake('local');

        Language::query()->updateOrCreate(['code' => 'hr'], [
            'locale' => 'hr_HR',
            'name' => 'Croatian',
            'native_name' => 'Hrvatski',
            'direction' => 'ltr',
            'is_default' => true,
            'is_active' => true,
            'sort_order' => 1,
        ]);
        Language::query()->updateOrCreate(['code' => 'en'], [
            'locale' => 'en_US',
            'name' => 'English',
            'native_name' => 'English',
            'direction' => 'ltr',
            'is_default' => false,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $careerPage = InfoPage::query()->where('code', 'career')->firstOrFail();
        $englishCareer = $careerPage->translation('en')->firstOrFail();
        $englishCareerPayload = (array) $englishCareer->payload;
        data_set($englishCareerPayload, 'career_page.application.title', 'Open positions');
        data_set($englishCareerPayload, 'career_page.form.title', 'Send us your CV');
        data_set($englishCareerPayload, 'career_page.form.intro', 'Submit your details and CV.');
        $englishCareer->update([
            'slug' => 'careers',
            'payload' => $englishCareerPayload,
        ]);

        $this->withSession(['front_locale' => 'en'])
            ->get('/careers')
            ->assertOk()
            ->assertSee('action="'.route('career.applications.store.en').'"', false)
            ->assertDontSee('action="'.route('career.applications.store').'"', false);

        $this->withSession(['front_locale' => 'en'])
            ->post(route('career.applications.store.en'), [
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'email' => 'jane@example.test',
                'message' => 'English career application.',
                'cv' => UploadedFile::fake()->create('jane-smith-cv.pdf', 200, 'application/pdf'),
                'accept_terms' => '1',
            ])
            ->assertRedirect('/careers#career-cta');

        $this->withSession(['front_locale' => 'en'])
            ->post(route('career.applications.store'))
            ->assertRedirect()
            ->assertSessionHas('front_locale', 'hr');

        $this->withSession(['front_locale' => 'hr'])
            ->post(route('career.applications.store.en'))
            ->assertRedirect()
            ->assertSessionHas('front_locale', 'en');
    }

    public function test_contact_form_stores_message(): void
    {
        $this->post('/kontakt', [
            'name' => 'Front Tester',
            'email' => 'front@example.test',
            'phone' => '+38591000000',
            'subject' => 'Wholesale inquiry',
            'message' => 'Please contact me with available B2B pricing details.',
            'accept_terms' => '1',
        ])->assertRedirect('/kontakt');

        $this->assertDatabaseHas('contact_messages', [
            'email' => 'front@example.test',
            'subject' => 'Wholesale inquiry',
            'status' => 'new',
        ]);

        $message = ContactMessage::query()
            ->where('email', 'front@example.test')
            ->latest('id')
            ->first();

        $this->assertNotNull($message);
        $this->assertSame(ContactMessage::FORM_TYPE_CONTACT, $message->payload['form_type'] ?? null);
        $this->assertSame('/kontakt', $message->payload['source_page'] ?? null);
    }

    public function test_contact_form_returns_ajax_success_without_redirecting(): void
    {
        $this->postJson('/kontakt', [
            'name' => 'Ajax Tester',
            'email' => 'ajax@example.test',
            'subject' => 'Ajax kontakt upit',
            'message' => 'Ova poruka potvrđuje slanje kontakt forme bez reloada stranice.',
            'accept_terms' => '1',
        ])
            ->assertOk()
            ->assertJson([
                'ok' => true,
                'message' => __('contact.sent_status'),
            ]);

        $this->assertDatabaseHas('contact_messages', [
            'email' => 'ajax@example.test',
            'subject' => 'Ajax kontakt upit',
            'status' => ContactMessage::STATUS_NEW,
        ]);
    }

    public function test_contact_form_returns_ajax_validation_errors(): void
    {
        $this->postJson('/kontakt', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email', 'message', 'accept_terms']);
    }

    public function test_finance_contact_form_returns_ajax_success_without_redirecting(): void
    {
        $this->postJson('/kontakt', [
            'first_name' => 'Iva',
            'last_name' => 'Ivić',
            'email' => 'finance-ajax@example.test',
            'message' => 'Želim dogovoriti sastanak za financijsko savjetovanje putem Ajax forme.',
            'accept_terms' => '1',
            'redirect_to' => '/financije#finance-sastanak',
        ])
            ->assertOk()
            ->assertJson([
                'ok' => true,
                'message' => __('contact.sent_status'),
            ]);

        $message = ContactMessage::query()
            ->where('email', 'finance-ajax@example.test')
            ->firstOrFail();

        $this->assertSame(ContactMessage::FORM_TYPE_SERVICE_CONTACT, $message->payload['form_type'] ?? null);
        $this->assertSame('/financije', $message->payload['source_page'] ?? null);
    }

    public function test_finance_contact_form_can_redirect_back_to_section(): void
    {
        $this->post('/kontakt', [
            'first_name' => 'Ana',
            'last_name' => 'Horvat',
            'company' => 'Horvat Finance d.o.o.',
            'email' => 'ana@example.test',
            'phone' => '+38598111222',
            'subject' => 'Dogovor sastanka',
            'message' => 'Želim dogovoriti inicijalni sastanak za financijsko savjetovanje.',
            'accept_terms' => '1',
            'redirect_to' => '/financije#finance-sastanak',
        ])->assertRedirect('/financije#finance-sastanak');

        $this->assertDatabaseHas('contact_messages', [
            'name' => 'Ana Horvat',
            'email' => 'ana@example.test',
            'subject' => 'Dogovor sastanka',
            'status' => 'new',
        ]);

        $message = ContactMessage::query()
            ->where('email', 'ana@example.test')
            ->latest('id')
            ->first();

        $this->assertNotNull($message);
        $this->assertSame(ContactMessage::FORM_TYPE_SERVICE_CONTACT, $message->payload['form_type'] ?? null);
        $this->assertSame('/financije', $message->payload['source_page'] ?? null);
    }

    public function test_contact_form_rejects_protocol_relative_redirect_target(): void
    {
        $this->post('/kontakt', [
            'name' => 'Sigurnosni test',
            'email' => 'redirect@example.test',
            'message' => 'Provjera da kontakt forma ne dopušta vanjsko preusmjeravanje.',
            'accept_terms' => '1',
            'redirect_to' => '//evil.example/collect',
        ])->assertRedirect('/kontakt');

        $message = ContactMessage::query()
            ->where('email', 'redirect@example.test')
            ->latest('id')
            ->first();

        $this->assertNotNull($message);
        $this->assertSame(ContactMessage::FORM_TYPE_CONTACT, $message->payload['form_type'] ?? null);
        $this->assertSame('/kontakt', $message->payload['source_page'] ?? null);
    }

    public function test_contact_page_renders_official_office_data(): void
    {
        $this->seedHomeBlock('home_stats', 'home.stats', [
            'title' => 'Kontakt sadržaj iz CMS-a',
            'payload' => [
                'contact_page' => [
                    'page_title' => 'Kontaktirajte nas',
                    'intro' => 'Kontakt uvod iz CMS-a.',
                    'form_title' => 'Pošaljite nam poruku',
                    'form_intro' => 'Kontakt obrazac iz CMS-a.',
                    'name_label' => 'Ime i prezime',
                    'email_label' => 'Email',
                    'phone_label' => 'Telefon (opcionalno)',
                    'subject_label' => 'Naslov',
                    'message_label' => 'Poruka',
                    'consent_label' => 'Slažem se s obradom osobnih podataka.',
                    'submit_label' => 'Pošalji poruku',
                    'direct_title' => 'Direktan kontakt',
                    'direct_body' => 'Kontaktirajte nas izravno.',
                    'direct_email' => 'info@alphacapitalis.com',
                    'direct_phone' => '+385 (0) 51 301 503',
                    'direct_email_label' => 'Email',
                    'direct_phone_label' => 'Telefon',
                    'direct_response_time_label' => 'Vrijeme odgovora',
                    'direct_response_fallback' => 'Unutar radnog vremena',
                    'help_title' => 'Prije slanja upita',
                    'help_body' => 'Navedite temu upita.',
                ],
                'locations' => [
                    'title' => 'Prisutni na 3 lokacije',
                    'map_link_label' => 'Pogledaj na karti',
                    'email_label' => 'Email',
                    'phone_label' => 'Telefon',
                    'items' => [
                        [
                            'entity_key' => 'alpha-capitalis',
                            'city' => 'Zagreb',
                            'short_city' => 'Zagreb',
                            'company' => 'ALPHA CAPITALIS d.o.o.',
                            'address' => 'Ulica R. F. Mihanovića 9, 10110 Zagreb, Sky Office / XIX. kat',
                            'map_query' => 'Ulica R. F. Mihanovića 9, 10110 Zagreb, Sky Office',
                            'email' => 'info@alphacapitalis.com',
                            'phone' => '+385 (1) 580 6656',
                        ],
                        [
                            'entity_key' => 'alpha-capitalis-east',
                            'city' => 'Vinkovci',
                            'short_city' => 'Vinkovci',
                            'company' => 'ALPHA CAPITALIS EAST d.o.o.',
                            'address' => 'Duga ulica 67, 32100 Vinkovci',
                            'map_query' => 'Duga ulica 67, 32100 Vinkovci',
                            'email' => 'info@alphacapitalis.com',
                            'phone' => '+385 (1) 580 6656',
                        ],
                        [
                            'entity_key' => 'alpha-capitalis-timia',
                            'city' => 'Rijeka',
                            'short_city' => 'Rijeka',
                            'company' => 'ALPHA CAPITALIS TIMIA d.o.o.',
                            'address' => 'Korzo 30, 51 000 Rijeka',
                            'map_query' => 'Korzo 30, 51000 Rijeka',
                            'email' => 'info@alphacapitalis.com',
                            'phone' => '+385 (0) 51 301 503',
                        ],
                    ],
                ],
            ],
        ]);

        $this->get('/kontakt')
            ->assertOk()
            ->assertSee('ALPHA CAPITALIS d.o.o.')
            ->assertSee('Ulica R. F. Mihanovića 9')
            ->assertSee('ALPHA CAPITALIS EAST d.o.o.')
            ->assertSee('Duga ulica 67')
            ->assertSee('ALPHA CAPITALIS TIMIA d.o.o.')
            ->assertSee('Korzo 30')
            ->assertSee('51 000 Rijeka')
            ->assertSee('info@alphacapitalis.com')
            ->assertSee('+385 (1) 580 6656')
            ->assertSee('+385 (0) 51 301 503')
            ->assertSee('id="contact-locations"', false)
            ->assertSee('class="locations-map"', false)
            ->assertSee('data-location-index="0"', false);
    }

    public function test_collaboration_assessment_page_renders(): void
    {
        $this->get('/ac-forma-robot')
            ->assertOk()
            ->assertSee(__('assessment.heading'))
            ->assertSee(__('assessment.form.company_name'))
            ->assertSee(__('assessment.form.outgoing_invoices_monthly'));
    }

    public function test_collaboration_assessment_form_stores_structured_message(): void
    {
        $this->post('/ac-forma-robot', [
            'company_name' => 'Alpha Test d.o.o.',
            'company_oib' => '12345678901',
            'activity' => 'Financijsko savjetovanje',
            'contact_email' => 'assessment@example.test',
            'contact_phone' => '+38591111222',
            'incoming_invoices_monthly' => '24',
            'outgoing_invoices_monthly' => '18',
            'bank_accounts_monthly' => '2',
            'payroll_calculations_monthly' => '6',
            'inventory_bookkeeping' => 'no',
            'cost_centers_tracking' => 'yes',
            'monthly_reporting' => 'yes',
            'accept_terms' => '1',
        ])->assertRedirect('/ac-forma-robot');

        $message = ContactMessage::query()
            ->where('email', 'assessment@example.test')
            ->latest('id')
            ->first();

        $this->assertNotNull($message);
        $this->assertSame(__('assessment.form.default_subject'), $message->subject);
        $this->assertSame(ContactMessage::FORM_TYPE_COLLABORATION_ASSESSMENT, $message->payload['form_type'] ?? null);
        $this->assertSame('Alpha Test d.o.o.', $message->payload['answers']['company_name'] ?? null);
        $this->assertSame('18', $message->payload['answers']['outgoing_invoices_monthly'] ?? null);
    }

    public function test_eu_funds_questionnaire_page_renders(): void
    {
        $this->get('/eu-fondovi/upitnik')
            ->assertOk()
            ->assertSee(__('eu_funds_questionnaire.heading'))
            ->assertSee(__('eu_funds_questionnaire.form.company_name'))
            ->assertSee(__('eu_funds_questionnaire.form.planned_costs'))
            ->assertSee(__('eu_funds_questionnaire.form.interested_services'));
    }

    public function test_eu_funds_questionnaire_validation_errors_render_inline_only(): void
    {
        $this->from('/eu-fondovi/upitnik')
            ->followingRedirects()
            ->post('/eu-fondovi/upitnik', [])
            ->assertOk()
            ->assertSee(__('eu_funds_questionnaire.validation.required', [
                'attribute' => __('eu_funds_questionnaire.form.company_name'),
            ]))
            ->assertDontSee('border border-rose-200 bg-rose-50', false)
            ->assertDontSee('list-disc space-y-1 pl-5', false);
    }

    public function test_eu_funds_questionnaire_form_stores_structured_message(): void
    {
        $this->post('/eu-fondovi/upitnik', [
            'company_name' => 'Kreativni studio d.o.o.',
            'company_oib' => '12345678901',
            'company_activity' => '90.03 Umjetničko stvaralaštvo',
            'employee_count' => '10_49',
            'related_companies' => 'yes',
            'project_sectors' => ['creative_industries', 'ict'],
            'investment_location' => 'Zagreb',
            'planned_costs' => ['equipment', 'digitalization'],
            'investment_amount' => '100k_500k',
            'interested_services' => ['loans', 'investment_incentives'],
            'additional_notes' => 'Povezano društvo: Studio projekt d.o.o.',
            'contact_name' => 'Ivana Horvat',
            'email' => 'eu-funds@example.test',
            'contact_phone' => '+38591111222',
            'accept_terms' => '1',
        ])->assertRedirect('/eu-fondovi/upitnik');

        $message = \App\Models\Content\Support\ContactMessage::query()
            ->where('email', 'eu-funds@example.test')
            ->latest('id')
            ->first();

        $this->assertNotNull($message);
        $this->assertSame(\App\Models\Content\Support\ContactMessage::SUBJECT_EU_FUNDS_QUESTIONNAIRE, $message->subject);
        $this->assertSame(\App\Models\Content\Support\ContactMessage::FORM_TYPE_EU_FUNDS_QUESTIONNAIRE, $message->payload['form_type'] ?? null);
        $this->assertSame('Kreativni studio d.o.o.', $message->payload['answers']['company_name'] ?? null);
        $this->assertSame('12345678901', $message->payload['answers']['company_oib'] ?? null);
        $this->assertSame('100.000,00 - 500.000,00 EUR', $message->payload['answers']['investment_amount'] ?? null);
        $this->assertSame('10-49', $message->payload['answers']['employee_count'] ?? null);
        $this->assertSame('Da', $message->payload['answers']['related_companies'] ?? null);
        $this->assertSame(['Kreativne industrije', 'Informacije i komunikacije (ICT)'], $message->payload['answers']['project_sectors'] ?? null);
        $this->assertSame(['Opremanje (strojevi, alati, oprema)', 'Digitalizacija i nabava IKT opreme - softver i hardver'], $message->payload['answers']['planned_costs'] ?? null);
    }

    public function test_lease_calculator_page_renders(): void
    {
        $this->get('/leasing-kalkulator')
            ->assertOk()
            ->assertSee(__('lease_calculator.heading'))
            ->assertSee(__('lease_calculator.form.start_date'))
            ->assertSee(__('lease_calculator.results.lease_liability'));
    }

    public function test_lease_calculator_header_link_only_renders_on_accounting_page(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('class="site-header"', false)
            ->assertDontSee('/leasing-kalkulator', false)
            ->assertDontSee('MSFI 16 Kalkulator');

        $this->get('/racunovodstvo')
            ->assertOk()
            ->assertSee('class="site-header"', false)
            ->assertSee('/leasing-kalkulator', false)
            ->assertSee('MSFI 16 Kalkulator');
    }

    public function test_home_services_section_renders_requested_service_order(): void
    {
        $this->seedBlogPost([], 'CMS naslov objave', 'cms-naslov-objave', 'CMS sažetak objave.');
        $this->seedHomeBlock('home_services', 'home.services', [
            'title' => 'Vi vodite poslovanje. Mi brinemo da brojke prate vaš rast.',
            'payload' => [
                'services' => $this->homeServicePayloadRows(),
                'news' => [
                    'title' => 'Rokovi, novosti i savjeti za sigurnije poslovanje.',
                    'all_posts_label' => 'Pogledaj sve objave',
                    'all_posts_url' => '/blog',
                    'post_action_label' => 'Opširnije',
                ],
            ],
        ]);

        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('Vi vodite poslovanje. Mi brinemo da brojke prate vaš rast.')
            ->assertSee('class="services-grid services-grid--count-3"', false)
            ->assertSee('class="news-section ac-home-news"', false)
            ->assertSee('sigurnost i povjerenje u brojke')
            ->assertSee('kontrola, jasnoća i porezna sigurnost')
            ->assertSee('rast, optimizacija i bolji financijski izbor')
            ->assertDontSee('Globalna partnerstva i stručna članstva')
            ->assertDontSee('Zadnje objave i novosti')
            ->assertDontSee('Iskustva naših klijenata');

        $content = $response->getContent();
        $this->assertIsString($content);

        $serviceTitles = [
            'Revizija',
            'Računovodstvo i porezi',
            'Savjetovanje',
        ];

        $positions = [];
        foreach ($serviceTitles as $title) {
            $position = strpos($content, 'class="service-card-title" data-words-slide-from-right aria-label="'.$title.'"');
            $this->assertNotFalse($position, 'Missing services card: '.$title);
            $positions[] = $position;
        }

        $sortedPositions = $positions;
        sort($sortedPositions);

        $this->assertSame($sortedPositions, $positions);
        $this->assertStringNotContainsString('ac-service-pillar-text-card-title', $content);
        $this->assertStringNotContainsString('Osiguravamo kapital za rast i razvoj poslovanja.', $content);
        $this->assertStringNotContainsString('Savjetovanje obiteljskih biznisa', $content);
        $this->assertStringNotContainsString('aria-label="Financije"', $content);
        $this->assertStringNotContainsString('aria-label="Porezi"', $content);
        $this->assertStringNotContainsString('aria-label="EU fondovi"', $content);
        $this->assertStringNotContainsString('aria-label="Obiteljski biznis"', $content);
    }

    public function test_home_page_uses_optimized_media_without_loading_unused_slider_assets(): void
    {
        $this->seedHomeBlock('home_services', 'home.services', [
            'title' => 'CMS usluge',
            'payload' => [
                'services' => [$this->homeServicePayloadRows()[0]],
            ],
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('alpha-zagreb-poster-640.webp', false)
            ->assertSee('fetchpriority="high"', false)
            ->assertSee('data-alpha-hero-video-mobile-src=', false)
            ->assertSee('alpha-zagreb-loop-mobile.mp4', false)
            ->assertSee('class="service-card-media"', false)
            ->assertSee('loading="lazy" decoding="async"', false)
            ->assertSee('data-deferred-stylesheet', false)
            ->assertDontSee('splide.min.css', false)
            ->assertDontSee('splide.min.js', false);
    }

    public function test_home_response_preloads_critical_stylesheets_before_html_parsing(): void
    {
        $response = $this->get('/');

        $response->assertOk();

        $linkHeader = $response->headers->get('Link');
        $alphaStylesheetPath = 'front-theme/styles/alpha-redesign.css';

        $this->assertIsString($linkHeader);
        $this->assertStringContainsString(
            '<'.\Illuminate\Support\Facades\Vite::asset('resources/css/app.css').'>; rel=preload; as=style',
            $linkHeader,
        );
        $this->assertStringContainsString(
            '<'.asset($alphaStylesheetPath).'?v='.filemtime(public_path($alphaStylesheetPath)).'>; rel=preload; as=style',
            $linkHeader,
        );
    }

    public function test_home_service_fallback_images_keep_their_responsive_webp_sources(): void
    {
        $this->seedHomeBlock('home_services', 'home.services', [
            'title' => 'CMS usluge',
            'payload' => [
                'services' => [$this->homeServicePayloadRows()[1]],
            ],
        ]);

        ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::SERVICES_INDEX)
            ->firstOrFail()
            ->clearMediaCollection('services_index_accounting_image');

        $this->get('/')
            ->assertOk()
            ->assertSee('alpha/service-racunovodstvo-480.webp 480w', false)
            ->assertSee('alpha/service-racunovodstvo-768.webp 768w', false)
            ->assertSee('alpha/service-racunovodstvo-1080.webp 1080w', false);
    }

    public function test_home_video_starts_after_the_first_post_load_paint(): void
    {
        $script = (string) file_get_contents(public_path('front-theme/scripts/alpha-redesign.js'));

        $this->assertStringContainsString('const scheduleHeroVideo = function () {', $script);
        $this->assertMatchesRegularExpression(
            '/requestAnimationFrame\(function \(\) \{\s*window\.requestAnimationFrame\(loadHeroVideo\);/s',
            $script,
        );
        $this->assertStringContainsString("window.addEventListener('load', scheduleHeroVideo, { once: true });", $script);
        $this->assertStringNotContainsString("window.addEventListener('load', loadHeroVideo, { once: true });", $script);
    }

    public function test_home_service_cards_use_images_from_the_services_cms_page(): void
    {
        Storage::fake('public');
        config()->set('media-library.disk_name', 'public');
        config()->set('media-library.queue_conversions_by_default', false);

        $this->seedHomeBlock('home_services', 'home.services', [
            'title' => 'CMS usluge',
            'payload' => [
                'services' => [$this->homeServicePayloadRows()[0]],
            ],
        ]);

        $servicesIndex = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::SERVICES_INDEX)
            ->firstOrFail();
        $auditPage = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::AUDIT)
            ->firstOrFail();

        $servicesIndex->clearMediaCollection('services_index_audit_image');
        $indexMedia = $servicesIndex
            ->addMedia(UploadedFile::fake()->image('services-cms-audit.jpg', 1080, 1350))
            ->toMediaCollection('services_index_audit_image');

        $auditPage->clearMediaCollection('service_hero_image');
        $auditHeroMedia = $auditPage
            ->addMedia(UploadedFile::fake()->image('audit-page-hero.jpg', 1440, 480))
            ->toMediaCollection('service_hero_image');

        $expectedImageUrl = $indexMedia->hasGeneratedConversion('services_index_card_1080x1350')
            ? $indexMedia->getUrl('services_index_card_1080x1350')
            : $indexMedia->getUrl();

        $this->get('/')
            ->assertOk()
            ->assertSee($expectedImageUrl, false)
            ->assertDontSee($auditHeroMedia->getUrl(), false);
    }

    public function test_header_renders_only_navigation_links_configured_in_cms(): void
    {
        app(SystemSettingsService::class)->put(NavigationMenuService::SETTINGS_KEY, [
            [
                'type' => 'custom',
                'label' => 'CMS Usluge',
                'url' => '/usluge',
                'open_in_new_tab' => false,
                'is_active' => true,
                'sort_order' => 0,
            ],
            [
                'type' => 'blog',
                'label' => 'CMS Objave',
                'url' => '',
                'open_in_new_tab' => false,
                'is_active' => true,
                'sort_order' => 1,
            ],
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('data-label="CMS Usluge"', false)
            ->assertSee('data-label="CMS Objave"', false)
            ->assertDontSee('data-label="Početna"', false)
            ->assertDontSee('data-label="O nama"', false)
            ->assertDontSee('front-nav-caret');
    }

    public function test_mobile_header_keeps_services_clickable_and_exposes_service_submenu(): void
    {
        app(SystemSettingsService::class)->put(NavigationMenuService::SETTINGS_KEY, [
            [
                'type' => 'custom',
                'label' => 'Usluge',
                'url' => '/usluge',
                'open_in_new_tab' => false,
                'is_active' => true,
                'sort_order' => 0,
            ],
            [
                'type' => 'contact',
                'label' => 'Kontakt',
                'url' => '',
                'open_in_new_tab' => false,
                'is_active' => true,
                'sort_order' => 1,
            ],
        ]);

        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('class="mobile-menu-brand"', false)
            ->assertSee('data-alpha-initial-panel="root"', false)
            ->assertSee('href="/usluge"', false)
            ->assertSee('data-alpha-submenu-open', false)
            ->assertSee('id="alpha-mobile-services-panel"', false)
            ->assertSee('href="'.route('audit.show').'"', false)
            ->assertSee('href="'.route('accounting.show').'"', false)
            ->assertSee('href="'.route('advisory.show').'"', false)
            ->assertSee('class="mobile-menu-subnav-link"', false)
            ->assertDontSee('mobile-menu-subnav-heading', false)
            ->assertDontSee('Sve usluge')
            ->assertSee('class="mobile-menu-item mobile-menu-link mobile-menu-link--offer"', false)
            ->assertDontSee('class="mobile-cta', false);

        $this->assertSame(3, substr_count((string) $response->getContent(), 'class="mobile-menu-subnav-link"'));
    }

    public function test_mobile_header_reopens_the_services_panel_on_service_pages(): void
    {
        app(SystemSettingsService::class)->put(NavigationMenuService::SETTINGS_KEY, [[
            'type' => 'custom',
            'label' => 'Usluge',
            'url' => '/usluge',
            'open_in_new_tab' => false,
            'show_dropdown' => true,
            'is_active' => true,
            'sort_order' => 0,
        ]]);

        $this->get('/revizija')
            ->assertOk()
            ->assertSee('data-alpha-initial-panel="services"', false)
            ->assertSee('class="mobile-menu-subnav-link is-active"', false)
            ->assertSee('href="'.route('audit.show').'"', false);
    }

    public function test_header_does_not_fall_back_to_hardcoded_navigation(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertDontSee('data-label="Početna"', false)
            ->assertDontSee('data-label="Usluge"', false)
            ->assertDontSee('data-label="Objave"', false);
    }

    public function test_blank_navigation_chrome_hides_editorial_header_and_footer_copy_without_language_fallbacks(): void
    {
        app(SystemSettingsService::class)->putMany([
            NavigationMenuService::CHROME_SETTINGS_KEY => ['hr' => []],
            'store_footer_hours' => '',
            'store_footer_bottom_copyright_text' => '',
        ]);

        $this->get('/usluge')
            ->assertOk()
            ->assertSee('class="site-header"', false)
            ->assertSee('class="search-link"', false)
            ->assertSee('class="site-footer"', false)
            ->assertDontSee('class="header-cta', false)
            ->assertDontSee('mobile-menu-link--offer', false)
            ->assertDontSee('class="footer-newsletter"', false)
            ->assertDontSee('footer-services-block', false)
            ->assertDontSee('footer-contact-block', false)
            ->assertDontSee('footer-cookie-consent-link', false)
            ->assertDontSee('footer-back-to-top', false)
            ->assertDontSee('Zatraži ponudu')
            ->assertDontSee('MSFI 16 Kalkulator')
            ->assertDontSee('Primajte važne novosti na')
            ->assertDontSee('Vaš kompas kroz svijet financija.')
            ->assertDontSee('Na vrh');
    }

    public function test_english_header_and_footer_only_render_links_with_english_content(): void
    {
        Language::query()->updateOrCreate(['code' => 'hr'], [
            'locale' => 'hr_HR',
            'name' => 'Croatian',
            'native_name' => 'Hrvatski',
            'direction' => 'ltr',
            'is_default' => true,
            'is_active' => true,
            'sort_order' => 1,
        ]);
        Language::query()->updateOrCreate(['code' => 'en'], [
            'locale' => 'en_US',
            'name' => 'English',
            'native_name' => 'English',
            'direction' => 'ltr',
            'is_default' => false,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $translatedPage = InfoPage::query()->create([
            'code' => 'navigation-translated-test',
            'layout' => 'default',
            'is_active' => true,
            'show_in_footer' => false,
            'sort_order' => 901,
        ]);
        $translatedPage->translations()->create([
            'locale' => 'hr',
            'title' => 'Prevedena testna stranica',
            'slug' => 'prevedena-testna-stranica',
        ]);
        $translatedPage->translations()->create([
            'locale' => 'en',
            'title' => 'Translated test page',
            'slug' => 'translated-test-page',
        ]);

        $croatianOnlyPage = InfoPage::query()->create([
            'code' => 'navigation-croatian-only-test',
            'layout' => 'default',
            'is_active' => true,
            'show_in_footer' => false,
            'sort_order' => 902,
        ]);
        $croatianOnlyPage->translations()->create([
            'locale' => 'hr',
            'title' => 'Samo hrvatska stranica',
            'slug' => 'samo-hrvatska-stranica',
        ]);

        foreach ([
            'services' => ['template' => 'services_index', 'title' => 'English Services', 'slug' => 'services'],
            'audit' => ['template' => 'audit', 'title' => 'English Audit Service', 'slug' => 'audit'],
            'racunovodstvo' => ['template' => 'accounting', 'title' => 'English Accounting Service', 'slug' => 'accounting'],
            'advisory' => ['template' => 'advisory', 'title' => 'English Advisory Service', 'slug' => 'advisory'],
        ] as $code => $serviceData) {
            $servicePage = ServicePage::query()->updateOrCreate(
                ['code' => $code],
                [
                    'template_key' => $serviceData['template'],
                    'is_active' => true,
                    'published_at' => null,
                    'sort_order' => 0,
                ]
            );
            $servicePage->translations()->updateOrCreate(
                ['locale' => 'en'],
                [
                    'title' => $serviceData['title'],
                    'slug' => $serviceData['slug'],
                ]
            );
        }

        $croatianBlogPost = BlogPost::query()->create([
            'code' => 'strict-navigation-blog-test',
            'is_active' => true,
            'published_at' => now()->subMinute(),
            'sort_order' => 0,
        ]);
        BlogPostTranslation::query()->where('locale', 'en')->delete();
        $croatianBlogPost->translations()->create([
            'locale' => 'hr',
            'title' => 'Samo hrvatska objava',
            'slug' => 'samo-hrvatska-objava',
            'body_html' => '<p>Hrvatski sadržaj.</p>',
        ]);

        app(SystemSettingsService::class)->putMany([
            NavigationMenuService::SETTINGS_KEY => [
                [
                    'type' => 'page',
                    'page_id' => $translatedPage->id,
                    'label_translations' => [],
                    'url_translations' => [],
                    'is_active' => true,
                    'sort_order' => 0,
                ],
                [
                    'type' => 'page',
                    'page_id' => $croatianOnlyPage->id,
                    'label_translations' => [],
                    'url_translations' => [],
                    'is_active' => true,
                    'sort_order' => 1,
                ],
                [
                    'type' => 'blog',
                    'label_translations' => ['hr' => 'Objave', 'en' => 'English Blog Navigation'],
                    'url_translations' => [],
                    'is_active' => true,
                    'sort_order' => 2,
                ],
                [
                    'type' => 'custom',
                    'label_translations' => ['hr' => 'Samo HR custom'],
                    'url_translations' => ['hr' => '/samo-hr-custom'],
                    'is_active' => true,
                    'sort_order' => 3,
                ],
                [
                    'type' => 'custom',
                    'label_translations' => ['hr' => 'Prevedeni custom', 'en' => 'Localized custom page'],
                    'url_translations' => ['hr' => '/prevedena-testna-stranica', 'en' => '/prevedena-testna-stranica'],
                    'is_active' => true,
                    'sort_order' => 4,
                ],
                [
                    'type' => 'custom',
                    'label_translations' => ['hr' => 'HR only target', 'en' => 'Missing English target'],
                    'url_translations' => ['hr' => '/samo-hrvatska-stranica', 'en' => '/samo-hrvatska-stranica'],
                    'is_active' => true,
                    'sort_order' => 5,
                ],
                [
                    'type' => 'custom',
                    'label_translations' => ['hr' => 'Usluge', 'en' => 'English Services Navigation'],
                    'url_translations' => ['hr' => '/usluge', 'en' => '/usluge'],
                    'is_active' => true,
                    'sort_order' => 6,
                ],
            ],
            NavigationMenuService::CHROME_SETTINGS_KEY => [
                'en' => [
                    'header_primary_cta_label' => 'English CMS Proposal',
                    'footer_newsletter_label' => 'English CMS Newsletter Label',
                    'footer_newsletter_title' => 'English CMS Newsletter',
                    'footer_newsletter_accent' => 'Today.',
                    'footer_newsletter_email_placeholder' => 'English CMS Email',
                    'footer_newsletter_submit_label' => 'English CMS Newsletter Submit',
                    'footer_newsletter_consent' => 'English CMS Newsletter Consent',
                    'footer_tagline' => 'English CMS Footer Tagline',
                    'footer_services_label' => 'English CMS Services',
                    'footer_contact_label' => 'English CMS Contact',
                    'footer_copyright_text' => 'English CMS Copyright',
                    'footer_cookie_settings_label' => 'English CMS Cookies',
                    'footer_back_to_top_label' => 'English CMS Top',
                ],
            ],
        ]);

        $response = $this->withSession(['front_locale' => 'en'])->get('/');
        $homeUrl = route('home');

        $response->assertOk()
            ->assertSee('class="header-language-switch"', false)
            ->assertSee('class="mobile-menu-language-switch"', false)
            ->assertSee('href="'.route('front.locale.switch', ['code' => 'hr', 'redirect' => $homeUrl]).'"', false)
            ->assertSee('href="'.route('front.locale.switch', ['code' => 'en', 'redirect' => $homeUrl]).'"', false)
            ->assertSee('data-label="Translated test page"', false)
            ->assertDontSee('Samo hrvatska stranica')
            ->assertDontSee('English Blog Navigation')
            ->assertDontSee('Samo HR custom')
            ->assertSee('data-label="Localized custom page"', false)
            ->assertSee('href="'.route('pages.show', ['slug' => 'translated-test-page']).'"', false)
            ->assertDontSee('Missing English target')
            ->assertSee('data-label="English Services Navigation"', false)
            ->assertSee('href="'.url('/services').'"', false)
            ->assertSee('English Audit Service')
            ->assertSee('href="'.url('/audit').'"', false)
            ->assertSee('English Accounting Service')
            ->assertSee('href="'.url('/accounting').'"', false)
            ->assertSee('English Advisory Service')
            ->assertSee('href="'.url('/advisory').'"', false)
            ->assertSee('English CMS Proposal')
            ->assertSee('English CMS Newsletter')
            ->assertSee('English CMS Footer Tagline')
            ->assertSee('English CMS Services')
            ->assertSee('English CMS Contact')
            ->assertSee('English CMS Copyright')
            ->assertSee('English CMS Cookies')
            ->assertSee('English CMS Top')
            ->assertDontSee('Politika privatnosti')
            ->assertDontSee('Uvjeti korištenja');

        $translatedPageResponse = $this->withSession(['front_locale' => 'en'])
            ->get('/translated-test-page');
        $croatianPageUrl = route('pages.show', ['slug' => 'prevedena-testna-stranica']);
        $croatianSwitchUrl = route('front.locale.switch', [
            'code' => 'hr',
            'redirect' => $croatianPageUrl,
        ]);

        $translatedPageResponse->assertOk()
            ->assertSee('href="'.$croatianSwitchUrl.'"', false);

        $this->withSession(['front_locale' => 'hr'])
            ->get('/translated-test-page?source=language-test')
            ->assertOk()
            ->assertSessionHas('front_locale', 'en');

        $this->withSession(['front_locale' => 'en'])
            ->get($croatianSwitchUrl)
            ->assertRedirect($croatianPageUrl)
            ->assertSessionHas('front_locale', 'hr');

        $englishServicesUrl = url('/services');
        $englishServicesSwitchUrl = route('front.locale.switch', [
            'code' => 'en',
            'redirect' => $englishServicesUrl,
        ]);

        $this->withSession(['front_locale' => 'hr'])
            ->get('/usluge')
            ->assertOk()
            ->assertSee('href="'.$englishServicesSwitchUrl.'"', false);

        $this->withSession(['front_locale' => 'hr'])
            ->get($englishServicesSwitchUrl)
            ->assertRedirect($englishServicesUrl)
            ->assertSessionHas('front_locale', 'en');

        $this->from('/')
            ->get(route('front.locale.switch', [
                'code' => 'en',
                'redirect' => 'https://malicious.example/redirect',
            ]))
            ->assertRedirect('/');

        $croatianBlogPost->translations()->create([
            'locale' => 'en',
            'title' => 'English translated post',
            'slug' => 'english-translated-post',
            'body_html' => '<p>English content.</p>',
        ]);
        app()->forgetInstance(NavigationMenuService::class);

        $englishBlogUrl = route('blog.show', ['slug' => 'english-translated-post']);
        $englishBlogSwitchUrl = route('front.locale.switch', [
            'code' => 'en',
            'redirect' => $englishBlogUrl,
        ]);

        $this->withSession(['front_locale' => 'hr'])
            ->get(route('blog.show', ['slug' => 'samo-hrvatska-objava']))
            ->assertOk()
            ->assertSee('href="'.$englishBlogSwitchUrl.'"', false);

        $this->withSession(['front_locale' => 'en'])
            ->get('/')
            ->assertOk()
            ->assertSee('data-label="English Blog Navigation"', false);
    }

    public function test_english_footer_uses_only_the_exact_cms_location_address(): void
    {
        Language::query()->updateOrCreate(['code' => 'hr'], [
            'locale' => 'hr_HR',
            'name' => 'Croatian',
            'native_name' => 'Hrvatski',
            'direction' => 'ltr',
            'is_default' => true,
            'is_active' => true,
            'sort_order' => 1,
        ]);
        Language::query()->updateOrCreate(['code' => 'en'], [
            'locale' => 'en_US',
            'name' => 'English',
            'native_name' => 'English',
            'direction' => 'ltr',
            'is_default' => false,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $block = ContentBlock::query()->updateOrCreate(['code' => 'home-alpha-stats'], [
            'name' => 'Homepage statistics and locations',
            'type' => 'home_stats',
            'is_active' => true,
        ]);
        $englishTranslation = $block->translations()->updateOrCreate(['locale' => 'en'], [
            'title' => 'English statistics and locations',
            'payload' => [],
        ]);
        $englishPayload = (array) $englishTranslation->payload;
        $englishLocations = (array) data_get($englishPayload, 'locations.items', []);
        $englishLocations[0]['entity_key'] = 'alpha-capitalis';
        $englishLocations[0]['address'] = 'English CMS footer address, 19th floor';
        data_set($englishPayload, 'locations.items', $englishLocations);
        $englishTranslation->update(['payload' => $englishPayload]);

        $response = $this->withSession(['front_locale' => 'en'])->get('/services');

        $response->assertOk()
            ->assertSee('English CMS footer address, 19th floor')
            ->assertDontSee('XIX. kat');
        $this->assertSame(2, substr_count((string) $response->getContent(), 'English CMS footer address, 19th floor'));

        $englishLocations[0]['address'] = '';
        data_set($englishPayload, 'locations.items', $englishLocations);
        $englishTranslation->update(['payload' => $englishPayload]);

        $this->withSession(['front_locale' => 'en'])
            ->get('/services')
            ->assertOk()
            ->assertDontSee('XIX. kat')
            ->assertDontSee('English CMS footer address, 19th floor');
    }

    public function test_footer_uses_exact_locale_cms_address_and_managed_phone_and_email_settings(): void
    {
        Language::query()->updateOrCreate(['code' => 'hr'], [
            'locale' => 'hr_HR',
            'name' => 'Croatian',
            'native_name' => 'Hrvatski',
            'direction' => 'ltr',
            'is_default' => true,
            'is_active' => true,
            'sort_order' => 1,
        ]);
        Language::query()->updateOrCreate(['code' => 'en'], [
            'locale' => 'en_US',
            'name' => 'English',
            'native_name' => 'English',
            'direction' => 'ltr',
            'is_default' => false,
            'is_active' => true,
            'sort_order' => 2,
        ]);
        app(SystemSettingsService::class)->putMany([
            'store_footer_phone' => '+385 1 555 0199',
            'store_footer_email_support' => 'footer-cms@example.test',
        ]);

        $block = ContentBlock::query()->create([
            'code' => 'home-alpha-stats',
            'name' => 'Homepage statistics and locations',
            'type' => 'home_stats',
            'is_active' => true,
        ]);
        $block->translations()->create([
            'locale' => 'hr',
            'title' => 'Hrvatske lokacije',
            'payload' => ['locations' => ['items' => [[
                'entity_key' => 'alpha-capitalis',
                'address' => 'Točna hrvatska CMS adresa',
                'map_query' => 'Hrvatska CMS karta',
            ]]]],
        ]);
        $block->translations()->create([
            'locale' => 'en',
            'title' => 'English locations',
            'payload' => ['locations' => ['items' => [[
                'entity_key' => 'alpha-capitalis',
                'address' => 'Exact English CMS address',
                'map_query' => 'English CMS map query',
            ]]]],
        ]);

        $this->get('/usluge')
            ->assertOk()
            ->assertSee('Točna hrvatska CMS adresa')
            ->assertDontSee('Exact English CMS address')
            ->assertSee('+385 1 555 0199')
            ->assertSee('footer-cms@example.test');

        $this->get('/services')
            ->assertOk()
            ->assertSee('Exact English CMS address')
            ->assertDontSee('Točna hrvatska CMS adresa')
            ->assertSee('+385 1 555 0199')
            ->assertSee('footer-cms@example.test');
    }

    public function test_public_contact_surfaces_use_managed_phone_and_support_email_settings(): void
    {
        app(SystemSettingsService::class)->putMany([
            'store_footer_phone' => '+385 1 555 0123',
            'store_footer_email_support' => 'office-cms@example.test',
            'store_footer_email_sales' => '',
        ]);

        foreach (['/eu-fondovi/upitnik', '/ac-forma-robot'] as $path) {
            $content = (string) $this->withSession(['front_locale' => 'hr'])
                ->get($path)
                ->assertOk()
                ->getContent();

            $this->assertSame(3, substr_count($content, 'href="mailto:office-cms@example.test"'));
            $this->assertSame(3, substr_count($content, 'href="tel:+38515550123"'));
        }

        foreach (['/leasing-kalkulator', '/alpha-capitalis-tim'] as $path) {
            $content = (string) $this->withSession(['front_locale' => 'hr'])
                ->get($path)
                ->assertOk()
                ->getContent();

            $this->assertSame(3, substr_count($content, 'href="mailto:office-cms@example.test'));
        }
    }

    public function test_blank_cms_office_fields_never_render_legacy_operational_fallbacks(): void
    {
        Language::query()->updateOrCreate(['code' => 'hr'], [
            'locale' => 'hr_HR',
            'name' => 'Croatian',
            'native_name' => 'Hrvatski',
            'direction' => 'ltr',
            'is_default' => true,
            'is_active' => true,
            'sort_order' => 1,
        ]);
        Language::query()->updateOrCreate(['code' => 'en'], [
            'locale' => 'en_US',
            'name' => 'English',
            'native_name' => 'English',
            'direction' => 'ltr',
            'is_default' => false,
            'is_active' => true,
            'sort_order' => 2,
        ]);
        app(SystemSettingsService::class)->putMany([
            'store_footer_phone' => '',
            'store_footer_email_support' => '',
            'store_footer_email_sales' => '',
        ]);

        $block = ContentBlock::query()->create([
            'code' => 'home-alpha-stats',
            'name' => 'Homepage statistics and locations',
            'type' => 'home_stats',
            'is_active' => true,
        ]);
        $block->translations()->create([
            'locale' => 'en',
            'title' => 'English locations',
            'payload' => [
                'contact_page' => ['page_title' => 'Contact without operational fallbacks'],
                'locations' => [
                    'title' => 'CMS offices',
                    'map_link_label' => 'Map',
                    'items' => [[
                        'entity_key' => 'alpha-capitalis',
                        'city' => 'CMS City',
                        'short_city' => 'CMS City',
                        'address' => 'CMS-only address',
                    ]],
                ],
            ],
        ]);
        $block->slots()->create([
            'placement' => 'home.stats',
            'frontend_variant' => 'all',
            'sort_order' => 0,
            'is_active' => true,
        ]);

        $this->get('/contact')
            ->assertOk()
            ->assertSee('CMS-only address')
            ->assertDontSee('ALPHA CAPITALIS d.o.o.')
            ->assertDontSee('info@alphacapitalis.com')
            ->assertDontSee('+385 (1) 580 6656')
            ->assertDontSee('+385 (0) 51 301 503')
            ->assertDontSee('XIX. kat');

        foreach (['/eu-fondovi/upitnik', '/ac-forma-robot', '/leasing-kalkulator', '/alpha-capitalis-tim'] as $path) {
            $this->withSession(['front_locale' => 'hr'])
                ->get($path)
                ->assertOk()
                ->assertDontSee('info@alphacapitalis.com')
                ->assertDontSee('+385 (1) 580 6656')
                ->assertDontSee('+385 (0) 51 301 503');
        }
    }

    public function test_redesigned_global_footer_renders_on_home_and_internal_pages(): void
    {
        app(SystemSettingsService::class)->putMany([
            'store_social_x_url' => 'https://x.com/alpha-capitalis-test',
            'store_social_facebook_url' => 'https://www.facebook.com/alpha-capitalis-test',
            'store_social_linkedin_url' => 'https://www.linkedin.com/company/alpha-capitalis-test',
            'store_social_instagram_url' => 'https://www.instagram.com/alpha-capitalis-test',
            'store_social_tiktok_url' => 'https://www.tiktok.com/@alpha-capitalis-test',
            'store_social_youtube_url' => 'https://www.youtube.com/@alpha-capitalis-test',
        ]);

        foreach (['/', '/usluge'] as $path) {
            $this->get($path)
                ->assertOk()
                ->assertSee('<body id="page-top"', false)
                ->assertSee('class="site-footer"', false)
                ->assertSee('Primajte važne novosti na')
                ->assertSee('class="footer-socials"', false)
                ->assertSee('fa-x-twitter', false)
                ->assertSee('fa-facebook-f', false)
                ->assertSee('fa-linkedin-in', false)
                ->assertSee('fa-instagram', false)
                ->assertSee('fa-tiktok', false)
                ->assertSee('fa-youtube', false)
                ->assertSee('https://x.com/alpha-capitalis-test', false)
                ->assertSee('https://www.facebook.com/alpha-capitalis-test', false)
                ->assertSee('https://www.linkedin.com/company/alpha-capitalis-test', false)
                ->assertSee('https://www.instagram.com/alpha-capitalis-test', false)
                ->assertSee('https://www.tiktok.com/@alpha-capitalis-test', false)
                ->assertSee('https://www.youtube.com/@alpha-capitalis-test', false)
                ->assertSee('Web by.')
                ->assertSee('<a href="https://www.agmedia.hr" target="_blank" rel="noopener noreferrer">AG media</a>', false)
                ->assertSee('class="footer-back-to-top" href="#page-top"', false)
                ->assertSee('Politika privatnosti')
                ->assertSee('Uvjeti korištenja');
        }
    }

    public function test_saved_global_settings_render_on_the_public_site_without_unused_or_secret_values(): void
    {
        app(SystemSettingsService::class)->putMany([
            'store_brand_name' => 'Alpha javni test',
            'store_brand_logo_path' => 'store-settings/public-logo.svg',
            'store_brand_favicon_32_path' => 'store-settings/favicon/public-32.png',
            'store_footer_phone' => '+385 1 555 0110',
            'store_footer_email_sales' => 'prodaja@example.test',
            'store_footer_email_support' => 'podrska@example.test',
            'store_footer_hours' => 'Pon–Pet 08:00–16:00',
            'store_footer_bottom_copyright_text' => 'Legacy copyright must not render.',
            'store_social_x_url' => 'https://x.com/alpha-public-test',
            'store_footer_social_x_enabled' => true,
            'store_social_facebook_url' => 'https://facebook.com/alpha-disabled-test',
            'store_footer_social_facebook_enabled' => false,
            'store_seo_default_title' => 'Naslov iz spremljenih postavki',
            'store_seo_default_description' => 'Opis iz spremljenih postavki.',
            'store_seo_robots' => 'index,follow,max-image-preview:large',
            'store_seo_canonical_policy' => 'self',
            'store_og_home_image_path' => 'store-settings/og/home-public.png',
            'store_schema_enabled' => true,
            'store_schema_org_enabled' => true,
            'store_schema_org_type' => 'LocalBusiness',
            'store_schema_business_name' => 'Alpha schema javni test',
            'store_schema_business_phone' => '+385 1 555 0111',
            'store_schema_business_email' => 'schema@example.test',
            'store_schema_address_street' => 'Testna 11',
            'store_schema_address_city' => 'Zagreb',
            'store_schema_address_region' => 'Grad Zagreb',
            'store_schema_address_postal_code' => '10000',
            'store_schema_address_country' => 'HR',
            'store_captcha_recaptcha_v3_secret_key' => 'recaptcha-secret-must-not-render',
            'store_email_smtp_password' => 'smtp-secret-must-not-render',
            'store_newsletter_mailchimp_api_key' => 'newsletter-secret-must-not-render',
        ]);

        $chrome = (array) app(SystemSettingsService::class)->get(NavigationMenuService::CHROME_SETTINGS_KEY, []);
        data_set($chrome, 'hr.footer_copyright_text', 'Testna prava pridržana.');
        app(SystemSettingsService::class)->put(NavigationMenuService::CHROME_SETTINGS_KEY, $chrome);

        $response = $this->get('/');
        $logoUrl = Storage::disk('public')->url('store-settings/public-logo.svg');
        $faviconUrl = Storage::disk('public')->url('store-settings/favicon/public-32.png');
        $ogImageUrl = Storage::disk('public')->url('store-settings/og/home-public.png');

        $response->assertOk()
            ->assertSee('<title>Naslov iz spremljenih postavki</title>', false)
            ->assertSee('content="Opis iz spremljenih postavki."', false)
            ->assertSee('content="index,follow,max-image-preview:large"', false)
            ->assertSee('rel="canonical" href="'.url('/').'"', false)
            ->assertSee('property="og:site_name" content="Alpha javni test"', false)
            ->assertSee('property="og:image" content="'.$ogImageUrl.'"', false)
            ->assertSee('rel="icon" type="image/png" sizes="32x32" href="'.$faviconUrl.'"', false)
            ->assertSee('src="'.$logoUrl.'" alt="Alpha javni test"', false)
            ->assertSee('+385 1 555 0110')
            ->assertSee('prodaja@example.test')
            ->assertSee('podrska@example.test')
            ->assertSee('Pon–Pet 08:00–16:00')
            ->assertSee('Testna prava pridržana.')
            ->assertDontSee('Legacy copyright must not render.')
            ->assertSee('https://x.com/alpha-public-test', false)
            ->assertDontSee('https://facebook.com/alpha-disabled-test', false)
            ->assertSee('"@type":"LocalBusiness"', false)
            ->assertSee('"streetAddress":"Testna 11"', false)
            ->assertSee('"addressLocality":"Zagreb"', false)
            ->assertSee('"addressRegion":"Grad Zagreb"', false)
            ->assertSee('"postalCode":"10000"', false)
            ->assertSee('"addressCountry":"HR"', false)
            ->assertDontSee('recaptcha-secret-must-not-render', false)
            ->assertDontSee('smtp-secret-must-not-render', false)
            ->assertDontSee('newsletter-secret-must-not-render', false);
    }

    public function test_services_index_renders_primary_pillars_from_brief(): void
    {
        $this->get('/usluge')
            ->assertOk()
            ->assertSee('Naše usluge')
            ->assertSee('values-section services-index-intro', false)
            ->assertSee('services-value-grid', false)
            ->assertSee('Kako stvaramo vrijednost')
            ->assertSee('Kome stvaramo vrijednost')
            ->assertSee('Pouzdana podrška za sigurnije poslovanje i donošenje odluka.')
            ->assertSee('Pouzdane informacije i stručna perspektiva za sigurnije poslovne odnose.')
            ->assertSee('services-index-cards-shell', false)
            ->assertSee('services-grid services-grid--count-3', false)
            ->assertSee('class="service-card"', false)
            ->assertSee('Revizija')
            ->assertSee('Računovodstvo i porezi')
            ->assertSee('Savjetovanje')
            ->assertSee('Neovisna provjera financijskih izvještaja koja povećava povjerenje vlasnika, investitora i partnera.')
            ->assertSee('Precizno vođenje knjiga, pravovremeno izvještavanje i porezno savjetovanje za sigurnije poslovne odluke.')
            ->assertSee('Financijsko i strateško savjetovanje te pribavljanje kapitala - sve na jednom mjestu.')
            ->assertSeeText('Stvaramo vrijednost za naše klijente u svim fazama razvoja njihova poslovanja')
            ->assertDontSee('Naša podrška omogućuje bolje upravljanje financijama, kvalitetnije strateško planiranje')
            ->assertDontSee('Tri područja poslovne podrške')
            ->assertDontSee('Saznaj više')
            ->assertDontSee('Obiteljski biznis')
            ->assertSee(route('advisory.show'), false);
    }

    public function test_audit_service_page_renders_redesign_brief_flow(): void
    {
        $response = $this->get('/revizija');

        $response->assertOk()
            ->assertSee('Povjerenje u financijske informacije počinje neovisnom i stručnom revizijom.')
            ->assertSee('Zašto Vam je revizija bitna?')
            ->assertSee('Revizija pruža neovisnu i objektivnu procjenu financijskih informacija')
            ->assertSee('Neovisna revizija daje Vam sigurnost da odluke donosite na temelju pouzdanih informacija.')
            ->assertSee('Obveznici revizije')
            ->assertSee('Revizija je zakonska obveza za:')
            ->assertSee('ac-audit-obligor-card--criteria', false)
            ->assertDontSee('ac-audit-obligor-card--wide', false)
            ->assertSee('fa-duotone fa-thin fa-fw fa-city', false)
            ->assertSee('Naše revizijske usluge')
            ->assertSee('Revizija financijskih izvještaja')
            ->assertSee('Konsolidirani financijski izvještaji')
            ->assertSee('Održivost i ESG')
            ->assertSee('IT revizija')
            ->assertSee('Naš pristup')
            ->assertSee('Razgovarajmo o vašem revizorskom angažmanu')
            ->assertSee('front-theme/styles/pages/audit.css', false)
            ->assertSee('ac-audit-hero-image', false)
            ->assertSee('fa-duotone fa-thin fa-fw fa-file-check', false)
            ->assertSee('contact-cta ac-audit-contact-cta', false)
            ->assertDontSee('ac-service-videos-section', false)
            ->assertDontSee('ac-audit-hero-actions', false)
            ->assertDontSee('ac-audit-scroll-cue', false)
            ->assertDontSee('ac-audit-kicker', false)
            ->assertDontSee('data-audit-blog-splide', false)
            ->assertDontSee('--audit-hero-image', false)
            ->assertDontSee('Što revizija donosi društvu')
            ->assertDontSee('ac-audit-service-number', false);
    }

    public function test_accounting_service_page_renders_redesign_brief_flow(): void
    {
        $response = $this->get('/racunovodstvo');

        $response->assertOk()
            ->assertSee('Računovodstvo i porezi')
            ->assertSee('Vi vodite poslovanje. Mi brinemo da Vaše brojke budu točne, pravovremene i spremne za svaku odluku.')
            ->assertSee('Zašto Vam je računovodstvo bitno?')
            ->assertSee('Mirnije poslovanje počinje jasnim i pouzdanim brojkama.')
            ->assertSee('Uz ALPHA CAPITALIS ne dobivate samo računovodstvenu uslugu')
            ->assertSee('Naše usluge računovodstva i poreza')
            ->assertSee('Financijsko računovodstvo')
            ->assertSee('Porezno savjetovanje')
            ->assertSee('Obračun plaća')
            ->assertSee('Porezne prijave')
            ->assertSee('Upravljačko izvještavanje')
            ->assertSee('Osnivanje i registracija')
            ->assertDontSee('Konsolidacija')
            ->assertSee('Naš pristup')
            ->assertSee('Nismo samo servis za vođenje knjiga')
            ->assertSee('Razgovarajmo o vašem računovodstvu')
            ->assertSee('front-theme/styles/pages/audit.css', false)
            ->assertSee('front-theme/styles/pages/accounting.css', false)
            ->assertSee('ac-audit-hero-image', false)
            ->assertSee('ac-accounting-partner-note', false)
            ->assertSee('ac-accounting-partner-note-quote', false)
            ->assertSee('ac-accounting-partner-note-text', false)
            ->assertSee('fa-duotone fa-thin fa-fw fa-book-copy', false)
            ->assertSee('fa-duotone fa-thin fa-fw fa-receipt', false)
            ->assertDontSee('fa-duotone fa-thin fa-fw fa-badge-percent', false)
            ->assertSee('fa-duotone fa-thin fa-fw fa-user-tie-hair', false)
            ->assertSee('fa-duotone fa-thin fa-fw fa-file-certificate', false)
            ->assertSee('fa-duotone fa-thin fa-fw fa-chart-waterfall', false)
            ->assertSee('fa-duotone fa-thin fa-fw fa-building-shield', false)
            ->assertDontSee('fa-duotone fa-thin fa-fw fa-diagram-project', false)
            ->assertSee('contact-cta ac-audit-contact-cta', false)
            ->assertDontSee('Pogledajte usluge')
            ->assertDontSee('Rent-a-računovođa')
            ->assertDontSee('Analiza financijskih izvještaja')
            ->assertDontSee('Manipulacija financijskim izvještajima')
            ->assertDontSee('Zašto ne raditi u obiteljskom biznisu?')
            ->assertDontSee('ac-accounting-detail-section', false)
            ->assertDontSee('ac-accounting-video-card', false)
            ->assertDontSee('data-accounting-blog-splide', false)
            ->assertDontSee('splide.min', false)
            ->assertDontSee('--audit-hero-image', false)
            ->assertDontSee('Što je računovodstvo?')
            ->assertDontSee('Računovodstvo je sustavan zapis poslovnih transakcija');
    }

    public function test_advisory_service_page_renders_structured_hub_flow(): void
    {
        $response = $this->get('/savjetovanje');

        $response->assertOk()
            ->assertSee('Savjetovanje')
            ->assertSee('Budućnost poslovanja oblikuju odluke koje donosite danas.')
            ->assertSee('Zašto Vam je savjetovanje bitno?')
            ->assertSee('Važne poslovne odluke rijetko imaju jednostavne odgovore.')
            ->assertSee('Naše savjetovanje povezuje stručnost iz različitih područja')
            ->assertSee('Usluge savjetovanja')
            ->assertSee('Pribavljanje financiranja')
            ->assertSee('M&amp;A savjetovanje', false)
            ->assertSee('Dubinska snimanja (Due Diligence)')
            ->assertSee('Procjena vrijednosti društva')
            ->assertDontSee('Porezno savjetovanje')
            ->assertSee('ALPHA CAPITALIS je član Pandea Global M&amp;A', false)
            ->assertSee('Naš pristup')
            ->assertSee('Razgovarajmo o vašim poslovnim odlukama')
            ->assertSee('/savjetovanje/prodaja-i-kupnja-poduzeca', false)
            ->assertSee('/savjetovanje/dubinska-snimanja', false)
            ->assertSee('/savjetovanje/procjena-vrijednosti-drustva', false)
            ->assertDontSee('/savjetovanje/porezno-savjetovanje', false)
            ->assertSee('front-theme/styles/pages/advisory.css', false)
            ->assertSee('ac-advisory-hero-image', false)
            ->assertSee('ac-advisory-network-grid', false)
            ->assertSee('ac-advisory-services-grid ac-advisory-services-grid--main', false)
            ->assertSee('fa-duotone fa-thin fa-fw fa-hand-holding-circle-dollar', false)
            ->assertSee('fa-duotone fa-thin fa-fw fa-people-arrows-left-right', false)
            ->assertSee('fa-duotone fa-thin fa-fw fa-magnifying-glass-dollar', false)
            ->assertSee('fa-duotone fa-thin fa-fw fa-chart-user', false)
            ->assertDontSee('fa-duotone fa-thin fa-fw fa-badge-percent', false)
            ->assertSee('contact-cta ac-advisory-contact-cta', false)
            ->assertDontSee('EU fondovi i poticaji')
            ->assertDontSee('Dostupni izvori financiranja')
            ->assertDontSee('Otvoreni natječaji')
            ->assertDontSee('Bankovni krediti')
            ->assertDontSee('ac-advisory-approach-panel', false)
            ->assertDontSee('Pogledajte usluge')
            ->assertDontSee('Poslovno savjetovanje')
            ->assertDontSee('poveznice su postavljene samo tamo gdje već postoji lokalni blog zapis ili lokalni dokument')
            ->assertDontSee('>01<', false)
            ->assertDontSee('ac-advisory-detail-card', false)
            ->assertDontSee('ac-service-videos-section', false)
            ->assertDontSee('data-advisory-blog-splide', false)
            ->assertDontSee('splide.min', false)
            ->assertDontSee('--audit-hero-image', false)
            ->assertDontSee('Što je savjetovanje?');

        $this->assertSame(
            4,
            substr_count((string) $response->getContent(), 'class="ac-advisory-service-card '),
        );

        $content = (string) $response->getContent();
        $this->assertTrue(
            strpos($content, 'ac-advisory-approach') < strpos($content, 'ac-advisory-network'),
            'The Pandea section should render after the approach section.',
        );
    }

    public function test_advisory_subpage_seo_uses_only_exact_locale_cms_values(): void
    {
        Language::query()->updateOrCreate(['code' => 'hr'], [
            'locale' => 'hr_HR',
            'name' => 'Croatian',
            'native_name' => 'Hrvatski',
            'direction' => 'ltr',
            'is_default' => true,
            'is_active' => true,
            'sort_order' => 1,
        ]);
        Language::query()->updateOrCreate(['code' => 'en'], [
            'locale' => 'en_US',
            'name' => 'English',
            'native_name' => 'English',
            'direction' => 'ltr',
            'is_default' => false,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $page = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::ADVISORY)
            ->firstOrFail();
        $croatianTranslation = $page->translations()->where('locale', 'hr')->firstOrFail();
        $croatianPayload = (array) $croatianTranslation->payload;
        data_set($croatianPayload, 'financial.meta_title', 'HR SEO NE SMIJE PROCURITI');
        data_set($croatianPayload, 'financial.meta_description', 'HR opis ne smije procuriti u EN stranicu.');
        $croatianTranslation->forceFill(['payload' => $croatianPayload])->save();

        $englishTranslation = $page->translations()->where('locale', 'en')->firstOrFail();
        $englishPayload = (array) $englishTranslation->payload;
        data_set($englishPayload, 'financial.title', 'English localized financial title');
        data_set($englishPayload, 'financial.hero_intro', 'English localized hero description.');
        data_set($englishPayload, 'financial.meta_title', 'English CMS financial SEO title');
        data_set($englishPayload, 'financial.meta_description', 'English CMS financial SEO description.');
        $englishTranslation->forceFill(['payload' => $englishPayload])->save();

        $this->withSession(['front_locale' => 'en'])
            ->get('/advisory/financial-advisory')
            ->assertOk()
            ->assertSee('<title>English CMS financial SEO title</title>', false)
            ->assertSee('<meta name="description" content="English CMS financial SEO description.">', false)
            ->assertDontSee('HR SEO NE SMIJE PROCURITI')
            ->assertDontSee('HR opis ne smije procuriti u EN stranicu.');

        data_set($englishPayload, 'financial.meta_title', '');
        data_set($englishPayload, 'financial.meta_description', '');
        $englishTranslation->forceFill(['payload' => $englishPayload])->save();

        $response = $this->withSession(['front_locale' => 'en'])
            ->get('/advisory/financial-advisory');

        $response
            ->assertOk()
            ->assertDontSee('<meta name="description" content="English localized hero description.">', false)
            ->assertDontSee('HR SEO NE SMIJE PROCURITI')
            ->assertDontSee('HR opis ne smije procuriti u EN stranicu.');

        preg_match('/<title>(.*?)<\/title>/su', $response->getContent(), $titleMatch);
        $this->assertNotSame(
            'English localized financial title',
            html_entity_decode(trim((string) ($titleMatch[1] ?? '')), ENT_QUOTES | ENT_HTML5, 'UTF-8')
        );
    }

    public function test_advisory_subpages_share_revizija_style_without_decorative_numbers(): void
    {
        foreach ([
            '/savjetovanje/pribavljanje-financiranja' => 'Pribavljanje financiranja',
            '/savjetovanje/pribavljanje-financiranja/bankovni-krediti' => 'Što su bankovni krediti?',
            '/savjetovanje/pribavljanje-financiranja/zakon-o-poticanju-ulaganja' => 'Što je Zakon o poticanju ulaganja?',
            '/savjetovanje/prodaja-i-kupnja-poduzeca' => 'Što je prodaja i kupnja poduzeća?',
            '/savjetovanje/dubinska-snimanja' => 'Što je dubinsko snimanje (Due Diligence)?',
            '/savjetovanje/procjena-vrijednosti-drustva' => 'Što je procjena vrijednosti?',
            '/savjetovanje/porezno-savjetovanje' => 'Što je porezno savjetovanje?',
        ] as $uri => $expectedText) {
            $response = $this->get($uri);

            $response
                ->assertOk()
                ->assertSee('aria-label="'.$expectedText.'"', false)
                ->assertSee('ac-advisory-hero', false)
                ->assertSee('ac-advisory-intro', false)
                ->assertSee('ac-advisory-services ac-advisory-subpage-services', false)
                ->assertSee('ac-advisory-approach', false)
                ->assertSee('contact-cta ac-advisory-contact-cta', false)
                ->assertSee('front-theme/styles/pages/advisory.css', false)
                ->assertDontSee('>01<', false)
                ->assertDontSee('ac-advisory-detail-card', false)
                ->assertDontSee('--audit-hero-image', false);

            if ($uri !== '/savjetovanje/pribavljanje-financiranja') {
                $response
                    ->assertSee('ac-advisory-subpage-service-card--capability', false)
                    ->assertDontSee('ac-advisory-check-grid', false)
                    ->assertDontSee('ac-advisory-check-pill', false);
            } else {
                $response
                    ->assertSee(route('eu-funds.show'), false)
                    ->assertSee(route('advisory.bank-loans.show'), false)
                    ->assertSee(route('advisory.investment-incentives.show'), false);
            }
        }
    }

    public function test_about_page_renders_cms_managed_content(): void
    {
        $this->seedCroatianAboutCmsPayload();

        $aboutPage = InfoPage::query()
            ->where('code', 'about-us')
            ->with('translations')
            ->firstOrFail();

        $this->assertSame('O nama', (string) $aboutPage->translations->firstWhere('locale', 'hr')?->title);
        $this->assertNull($aboutPage->translations->firstWhere('locale', 'hr')?->body_html);

        $croatianTranslation = $aboutPage->translations->firstWhere('locale', 'hr');
        $croatianPayload = (array) $croatianTranslation?->payload;
        data_set($croatianPayload, 'about_page.hero.image_alt', '');
        $croatianTranslation?->forceFill(['payload' => $croatianPayload])->save();
        $aboutPage->clearMediaCollection('about_hero_image');
        $aboutPage->clearMediaCollection('about_responsibility_image');

        $this->get('/o-nama')
            ->assertOk()
            ->assertSee('O nama')
            ->assertSee('class="values-title services-index-intro-title ac-about-intro-title"', false)
            ->assertSee('class="ac-about-values"', false)
            ->assertSee('class="ac-about-team"', false)
            ->assertSee('class="ac-about-team-stats"', false)
            ->assertSee('Naše reference')
            ->assertSee('Naše vrijednosti')
            ->assertSee('fa-duotone fa-thin fa-fw fa-brain-circuit', false)
            ->assertSee('fa-duotone fa-thin fa-fw fa-lightbulb-gear', false)
            ->assertSee('fa-duotone fa-thin fa-fw fa-hands-holding-heart', false)
            ->assertSee('fa-duotone fa-thin fa-fw fa-people-group', false)
            ->assertSee('fa-duotone fa-thin fa-fw fa-users-crown', false)
            ->assertSee('fa-duotone fa-thin fa-fw fa-handshake', false)
            ->assertSee('fa-duotone fa-thin fa-fw fa-buildings', false)
            ->assertSee('<a class="services-index-inline-link" href="'.route('contact.create').'">ALPHA CAPITALIS</a>', false)
            ->assertSee('<a class="services-index-inline-link" href="'.route('contact.create').'">ALPHA CAPITALISU</a>', false)
            ->assertSee('Jer poslovanje nije ravna linija.')
            ->assertSee('<strong>Jer poslovanje nije ravna linija.</strong>', false)
            ->assertSee('<strong>snažan multidisciplinarni tim</strong>', false)
            ->assertSee('Tim stručnjaka na jednom mjestu')
            ->assertSee('Uz vas prije, tijekom i nakon svake važne odluke')
            ->assertSee('Udruga AUXILIUM CAPITALIS - ulaganje u budućnost')
            ->assertSee('class="ac-about-responsibility-image image-reveal-media"', false)
            ->assertSee('front-theme/images/about/auxilium-capitalis-udruga.png', false)
            ->assertSee('data-count-target="75"', false)
            ->assertSee('data-count-target="700"', false)
            ->assertDontSee('alt="ALPHA CAPITALIS tim"', false)
            ->assertDontSee('class="footer-newsletter"', false)
            ->assertDontSee('This page has no body content.');
    }

    public function test_about_and_career_pages_do_not_expand_blank_croatian_cms_payloads(): void
    {
        $aboutPage = InfoPage::query()->where('code', 'about-us')->firstOrFail();
        $aboutPage->translation('hr')->firstOrFail()->update([
            'payload' => ['about_page' => []],
        ]);

        $this->get('/o-nama')
            ->assertOk()
            ->assertSee('class="ac-about-page"', false)
            ->assertDontSee('class="values-section services-index-intro ac-about-intro"', false)
            ->assertDontSee('class="ac-about-values"', false)
            ->assertDontSee('class="ac-about-team"', false)
            ->assertDontSee('class="ac-about-culture"', false)
            ->assertDontSee('class="ac-about-responsibility"', false)
            ->assertDontSee('class="ac-about-references"', false)
            ->assertDontSee('Kontaktirajte nas');

        $careerPage = InfoPage::query()->where('code', 'career')->firstOrFail();
        $careerPage->translation('hr')->firstOrFail()->update([
            'payload' => ['career_page' => []],
        ]);
        JobOpening::query()->update(['is_active' => false]);

        $this->get('/karijera')
            ->assertOk()
            ->assertSee('class="ac-career-page"', false)
            ->assertDontSee('class="ac-career-intro"', false)
            ->assertDontSee('class="ac-career-hero"', false)
            ->assertDontSee('class="ac-career-development"', false)
            ->assertDontSee('class="ac-career-stories"', false)
            ->assertDontSee('class="ac-career-openings"', false)
            ->assertDontSee('Karijera u ALPHA CAPITALISU')
            ->assertDontSee('Razvoj koji nije samo fraza')
            ->assertDontSee('Otvorene pozicije');
    }

    public function test_about_page_renders_custom_copy_from_translation_payload(): void
    {
        $page = InfoPage::query()->where('code', 'about-us')->firstOrFail();
        $translation = $page->translation('hr')->firstOrFail();
        $translation->update([
            'payload' => [
                'about_page' => [
                    'hero' => [
                        'title' => 'Priča po mjeri našeg tima',
                        'stat_value' => '75+',
                        'stat_label' => 'stručnjaka koji svakodnevno pomažu klijentima',
                    ],
                    'story' => [
                        'paragraphs' => [
                            'Prilagođeni uvodni odlomak O nama stranice.',
                        ],
                    ],
                    'values' => [
                        'label' => 'Vrijednosti koje živimo',
                    ],
                    'team' => [
                        'button_label' => 'Upoznaj sve kolege',
                    ],
                    'responsibility' => [
                        'cta_intro' => 'Poziv iz CMS-a.',
                        'cta_text' => 'CMS tekst poziva na suradnju.',
                        'cta_card_title' => 'Zajedno stvaramo prilike.',
                        'cta_status' => 'Javite nam se za suradnju.',
                    ],
                    'references' => [
                        'button_label' => 'Istraži reference',
                    ],
                ],
            ],
        ]);

        $this->get('/o-nama')
            ->assertOk()
            ->assertSee('Priča po mjeri našeg tima')
            ->assertSee('Prilagođeni uvodni odlomak O nama stranice.')
            ->assertSee('75+')
            ->assertSee('stručnjaka koji svakodnevno pomažu klijentima')
            ->assertSee('Vrijednosti koje živimo')
            ->assertSee('Zajedno stvaramo prilike.')
            ->assertSee('Javite nam se za suradnju.')
            ->assertSee('Istraži reference');
    }

    public function test_about_page_renders_consolidated_editor_content(): void
    {
        $page = InfoPage::query()->where('code', 'about-us')->firstOrFail();
        $translation = $page->translation('hr')->firstOrFail();
        $translation->update([
            'payload' => [
                'about_page' => [
                    'story' => [
                        'body_html' => '<p>Prvi blok jedinstvenog editora priče.</p><p><strong>Drugi formatirani blok priče.</strong></p>',
                    ],
                    'values' => [
                        'items' => [
                            [
                                'title' => 'Vrijednost iz CMS-a',
                                'body_html' => '<p>Jedinstveni editor prve kartice vrijednosti.</p><p><em>Formatirani tekst kartice.</em></p>',
                            ],
                        ],
                    ],
                    'team' => [
                        'body_html' => '<p>Istaknuti blok teksta o timu.</p><p>Drugi blok teksta o timu.</p>',
                    ],
                ],
            ],
        ]);

        $this->get('/o-nama')
            ->assertOk()
            ->assertSee('Prvi blok jedinstvenog editora priče.')
            ->assertSee('<p><strong>Drugi formatirani blok priče.</strong></p>', false)
            ->assertSee('<p class="ac-about-card-lead">Jedinstveni editor prve kartice vrijednosti.</p>', false)
            ->assertSee('<p><em>Formatirani tekst kartice.</em></p>', false)
            ->assertSee('Istaknuti blok teksta o timu.')
            ->assertSee('Drugi blok teksta o timu.');
    }

    public function test_about_page_uses_uploaded_hero_image(): void
    {
        Storage::fake('public');
        config()->set('media-library.disk_name', 'public');
        config()->set('media-library.queue_conversions_by_default', false);

        $this->seedCroatianAboutCmsPayload();
        $aboutPage = InfoPage::query()->where('code', 'about-us')->firstOrFail();
        $aboutPage->clearMediaCollection('about_hero_image');
        $media = $aboutPage
            ->addMedia(UploadedFile::fake()->image('custom-about-hero.jpg', 1440, 1059))
            ->withCustomProperties([
                'alt' => ['hr' => 'Novi portret ALPHA CAPITALIS tima'],
            ])
            ->toMediaCollection('about_hero_image');
        $media = $media->fresh();
        $expectedHeroUrl = $media->hasGeneratedConversion('about_hero_1440x1059')
            ? $media->getUrl('about_hero_1440x1059')
            : $media->getUrl();

        $this->get('/o-nama')
            ->assertOk()
            ->assertSee($expectedHeroUrl, false)
            ->assertSee('alt="Novi portret ALPHA CAPITALIS tima"', false)
            ->assertDontSee('front-theme/images/about/o-nama.jpg', false);
    }

    public function test_about_page_uses_uploaded_responsibility_image(): void
    {
        Storage::fake('public');
        config()->set('media-library.disk_name', 'public');
        config()->set('media-library.queue_conversions_by_default', false);

        $this->seedCroatianAboutCmsPayload();
        $aboutPage = InfoPage::query()->where('code', 'about-us')->firstOrFail();
        $aboutPage->clearMediaCollection('about_responsibility_image');
        $media = $aboutPage
            ->addMedia(UploadedFile::fake()->image('custom-auxilium.jpg', 1890, 1063))
            ->withCustomProperties([
                'alt' => ['hr' => 'Prilagođeni vizual Udruge AUXILIUM CAPITALIS'],
            ])
            ->toMediaCollection('about_responsibility_image')
            ->fresh();
        $expectedImageUrl = $media->hasGeneratedConversion('about_responsibility_1890x1063')
            ? $media->getUrl('about_responsibility_1890x1063')
            : $media->getUrl();

        $this->get('/o-nama')
            ->assertOk()
            ->assertSee($expectedImageUrl, false)
            ->assertDontSee('front-theme/images/about/auxilium-capitalis-udruga.png', false);
    }

    public function test_navigation_menu_service_resolves_page_and_custom_links(): void
    {
        [$page, $pageSlug] = $this->seedInfoPage();

        app(SystemSettingsService::class)->put(NavigationMenuService::SETTINGS_KEY, [
            [
                'type' => 'page',
                'label_translations' => ['hr' => 'Savjeti', 'en' => 'Advice'],
                'page_id' => $page->id,
                'url' => '',
                'open_in_new_tab' => false,
                'show_dropdown' => false,
                'is_active' => true,
                'sort_order' => 0,
            ],
            [
                'type' => 'custom',
                'label_translations' => ['hr' => 'Kontakt', 'en' => 'Contact'],
                'page_id' => 0,
                'url_translations' => ['hr' => '/kontakt', 'en' => '/contact'],
                'open_in_new_tab' => false,
                'show_dropdown' => false,
                'is_active' => true,
                'sort_order' => 1,
            ],
        ]);

        $items = app(NavigationMenuService::class)->forLocale('en');

        $this->assertCount(2, $items);
        $this->assertSame('Advice', $items[0]['label'] ?? null);
        $this->assertSame(route('pages.show', ['slug' => $pageSlug]), $items[0]['url'] ?? null);
        $this->assertFalse((bool) ($items[0]['open_in_new_tab'] ?? true));
        $this->assertFalse((bool) ($items[0]['show_dropdown'] ?? true));
        $this->assertSame('Contact', $items[1]['label'] ?? null);
        $this->assertSame('/contact', $items[1]['url'] ?? null);
        $this->assertFalse((bool) ($items[1]['open_in_new_tab'] ?? true));
        $this->assertFalse((bool) ($items[1]['show_dropdown'] ?? true));
    }

    public function test_blank_default_navigation_labels_use_managed_content_titles_or_remain_hidden(): void
    {
        [$page] = $this->seedInfoPage();
        $page->translations()->create([
            'locale' => 'hr',
            'title' => 'CMS naslov stranice',
            'slug' => 'cms-naslov-stranice',
        ]);
        $this->seedBlogPost();

        $contactBlock = ContentBlock::query()->updateOrCreate(
            ['code' => 'home-alpha-stats'],
            [
                'name' => 'Homepage statistics and locations',
                'type' => 'home_stats',
                'is_active' => true,
            ],
        );
        $contactBlock->translations()->updateOrCreate(
            ['locale' => 'hr'],
            [
                'title' => 'Kontakt',
                'payload' => ['contact_page' => ['page_title' => 'CMS naslov kontakta']],
            ],
        );

        app(SystemSettingsService::class)->putMany([
            'store_blog_header_title' => 'CMS naslov bloga',
            NavigationMenuService::SETTINGS_KEY => [
                ['type' => 'page', 'page_id' => $page->id, 'label_translations' => [], 'is_active' => true, 'sort_order' => 0],
                ['type' => 'blog', 'label_translations' => [], 'is_active' => true, 'sort_order' => 1],
                ['type' => 'contact', 'label_translations' => [], 'is_active' => true, 'sort_order' => 2],
                ['type' => 'faq', 'label_translations' => [], 'is_active' => true, 'sort_order' => 3],
            ],
        ]);

        $items = app(NavigationMenuService::class)->forLocale('hr');

        $this->assertSame(['page', 'blog', 'contact'], array_column($items, 'type'));
        $this->assertSame(
            ['CMS naslov stranice', 'CMS naslov bloga', 'CMS naslov kontakta'],
            array_column($items, 'label'),
        );
        $this->assertNotContains('FAQ', array_column($items, 'label'));
        $this->assertNotContains('Kontakt', array_column($items, 'label'));
    }

    public function test_navigation_menu_service_preserves_managed_flags_for_route_backed_items(): void
    {
        [$page] = $this->seedInfoPage();
        $page->translations()->create([
            'locale' => 'hr',
            'title' => 'Stranica zastavica',
            'slug' => 'stranica-zastavica',
        ]);

        app(SystemSettingsService::class)->put(NavigationMenuService::SETTINGS_KEY, [
            ['type' => 'page', 'label' => 'Page flag', 'page_id' => $page->id, 'open_in_new_tab' => true, 'show_dropdown' => false, 'is_active' => true, 'sort_order' => 0],
            ['type' => 'blog', 'label' => 'Blog flag', 'open_in_new_tab' => true, 'show_dropdown' => false, 'is_active' => true, 'sort_order' => 1],
            ['type' => 'contact', 'label' => 'Contact flag', 'open_in_new_tab' => true, 'show_dropdown' => false, 'is_active' => true, 'sort_order' => 2],
            ['type' => 'faq', 'label' => 'FAQ flag', 'open_in_new_tab' => true, 'show_dropdown' => false, 'is_active' => true, 'sort_order' => 3],
        ]);

        $items = app(NavigationMenuService::class)->forLocale('hr');

        $this->assertSame(['page', 'blog', 'contact', 'faq'], array_column($items, 'type'));
        foreach ($items as $item) {
            $this->assertTrue((bool) ($item['open_in_new_tab'] ?? false));
            $this->assertFalse((bool) ($item['show_dropdown'] ?? true));
        }
    }

    public function test_services_navigation_dropdown_obeys_the_cms_flag(): void
    {
        app(SystemSettingsService::class)->put(NavigationMenuService::SETTINGS_KEY, [[
            'type' => 'custom',
            'label' => 'Services without submenu',
            'url' => '/usluge',
            'open_in_new_tab' => true,
            'show_dropdown' => false,
            'is_active' => true,
            'sort_order' => 0,
        ]]);

        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('data-label="Services without submenu"', false)
            ->assertSee('target="_blank" rel="noopener noreferrer"', false)
            ->assertDontSee('data-alpha-submenu-open', false)
            ->assertDontSee('id="alpha-mobile-services-panel"', false);
    }

    public function test_home_hides_client_testimonials_section_for_now(): void
    {
        Comment::query()->create([
            'commentable_type' => null,
            'commentable_id' => null,
            'author_name' => 'Ivan Knezevic',
            'author_email' => null,
            'locale' => 'hr',
            'body' => 'Dobili smo puno jasniju sliku profitabilnosti nakon uvodenja kontrolinga.',
            'rating' => 5,
            'status' => Comment::STATUS_APPROVED,
            'is_featured' => true,
            'reviewed_at' => now(),
            'payload' => ['company' => 'Palma D.O.O.'],
        ]);

        $this->get('/')
            ->assertOk()
            ->assertDontSee('Iskustva naših klijenata')
            ->assertDontSee('Dobili smo puno jasniju sliku profitabilnosti nakon uvodenja kontrolinga.')
            ->assertDontSee('Ivan Knezevic')
            ->assertDontSee('Palma D.O.O.');
    }

    public function test_blog_index_supports_filters_and_uses_blog_settings_for_pagination(): void
    {
        $finance = $this->seedBlogCategory('Finance', 'finance');
        $tax = $this->seedBlogCategory('Tax', 'tax');

        $this->seedBlogPost([$finance->id], 'Alpha Review', 'alpha-review', 'Alpha review excerpt', now()->subHour());
        $this->seedBlogPost([$finance->id], 'Alpha Plan', 'alpha-plan', 'Alpha plan excerpt', now()->subDay());
        $this->seedBlogPost([$tax->id], 'Beta Taxes', 'beta-taxes', 'Beta taxes excerpt', now()->subDays(2));

        app(SystemSettingsService::class)->putMany([
            'store_blog_posts_per_page' => 1,
            'store_blog_category_preview_limit' => 1,
        ]);

        $this->get('/blog/finance?q=Alpha')
            ->assertOk()
            ->assertSee('Alpha Review')
            ->assertDontSee('Beta Taxes')
            ->assertSee(__('ui.blog.filters.show_more'))
            ->assertDontSee('aria-label="Breadcrumb"', false)
            ->assertSee('/blog/finance', false)
            ->assertSee('page=2', false)
            ->assertSee('q=Alpha', false);
    }

    public function test_blog_index_renders_store_configured_hero_copy(): void
    {
        $this->seedBlogPost();

        app(SystemSettingsService::class)->putMany([
            'store_blog_header_eyebrow' => 'Insights Desk',
            'store_blog_header_title' => 'Strategic Insights',
            'store_blog_header_intro' => 'Fresh perspective for owners and finance teams.',
            'store_blog_header_cta_label' => 'Book a consultation',
            'store_blog_header_cta_url' => '/contact',
        ]);

        $this->get('/blog')
            ->assertOk()
            ->assertSee('Strategic Insights')
            ->assertSee('Fresh perspective for owners and finance teams.')
            ->assertSee('Book a consultation');
    }

    public function test_team_page_renders_active_members_with_public_links(): void
    {
        Storage::fake('public');
        config()->set('media-library.disk_name', 'public');
        config()->set('media-library.queue_conversions_by_default', false);

        $member = TeamMember::query()->create([
            'code' => 'team-'.strtolower((string) str()->random(6)),
            'is_active' => true,
            'sort_order' => 1,
            'email' => 'team@example.test',
            'facebook_url' => 'https://facebook.com/alpha-team',
            'twitter_url' => 'https://twitter.com/alpha-team',
            'linkedin_url' => 'https://linkedin.com/company/alpha-team',
        ]);

        TeamMemberTranslation::query()->create([
            'team_member_id' => $member->id,
            'locale' => 'en',
            'name' => 'Ivana Horvat',
            'position' => 'Senior Manager',
            'departments' => "Finance\nTax",
            'description_html' => '<p>Leads strategic projects for owners and management teams.</p>',
        ]);

        TeamMemberTranslation::query()->create([
            'team_member_id' => $member->id,
            'locale' => 'hr',
            'name' => 'Ivana Horvat',
            'position' => 'Senior Manager',
            'departments' => "Financije\nPorezi",
            'description_html' => '<p>Vodi strateške projekte za vlasnike i menadžerske timove.</p>',
        ]);

        $member->addMedia(UploadedFile::fake()->image('ivana-horvat.jpg', 960, 1200))
            ->toMediaCollection('team_photo');

        $this->get('/alpha-capitalis-tim')
            ->assertOk()
            ->assertSee('Ivana Horvat')
            ->assertSee('Senior Manager')
            ->assertDontSee('Finance')
            ->assertDontSee('Tax')
            ->assertSee('team@example.test')
            ->assertSee('https://linkedin.com/company/alpha-team', false)
            ->assertSee('ivana-horvat.jpg', false)
            ->assertDontSee('data-team-lightbox-trigger', false)
            ->assertDontSee('data-team-lightbox', false)
            ->assertDontSee('fa-magnifying-glass-plus', false)
            ->assertDontSee('front-theme/scripts/team.js', false);
    }

    public function test_client_review_keeps_ana_mandic_active_with_public_profile_and_photo(): void
    {
        $ana = TeamMember::query()
            ->where('code', 'ana-mandic')
            ->with(['translations', 'media'])
            ->firstOrFail();

        $this->assertTrue($ana->is_active);
        $this->assertSame('ana.mandic@alphacapitalis.com', $ana->email);
        $this->assertSame('https://www.linkedin.com/in/ana-mandic-phd-aa572b44', $ana->linkedin_url);
        $this->assertSame('Ana Mandić', (string) $ana->translations->firstWhere('locale', 'hr')?->name);
        $this->assertSame('Menadžer / Savjetovanje', (string) $ana->translations->firstWhere('locale', 'hr')?->position);
        $this->assertSame('ana-mandic.png', $ana->getFirstMedia('team_photo')?->file_name);

        $this->get('/alpha-capitalis-tim')
            ->assertOk()
            ->assertSee('Ana Mandić')
            ->assertSee('Menadžer / Savjetovanje')
            ->assertSee('ana.mandic@alphacapitalis.com')
            ->assertSee('ana-mandic.png', false);
    }

    public function test_team_page_renders_admin_managed_intro_and_seo(): void
    {
        $page = InfoPage::query()->where('code', 'team-page')->firstOrFail();
        $page->translation('hr')->firstOrFail()->update([
            'excerpt' => 'Uvod kojim upravlja administrator.',
            'meta_title' => 'Stručni tim | Alpha Capitalis',
            'meta_description' => 'Prilagođeni SEO opis stranice tima.',
        ]);

        $this->get('/alpha-capitalis-tim')
            ->assertOk()
            ->assertSee('Uvod kojim upravlja administrator.')
            ->assertSee('<title>Stručni tim | Alpha Capitalis</title>', false)
            ->assertSee('<meta name="description" content="Prilagođeni SEO opis stranice tima.">', false);
    }

    public function test_blog_article_breadcrumb_links_primary_category_without_current_article(): void
    {
        $news = $this->seedBlogCategory('News', 'news');
        [, $postSlug] = $this->seedBlogPost([$news->id], 'Growth Update', 'growth-update');

        $this->get('/blog/'.$postSlug)
            ->assertOk()
            ->assertSee('/blog/news', false)
            ->assertSee('aria-label="Breadcrumb"', false)
            ->assertSee('class="front-scroll-breadcrumb-link">News</a>', false)
            ->assertDontSee('class="front-scroll-breadcrumb-current ac-blog-breadcrumb-current"', false);
    }

    public function test_blog_article_related_posts_fallback_to_similar_titles_when_same_category_posts_are_not_similar(): void
    {
        $news = $this->seedBlogCategory('News', 'news');
        $finance = $this->seedBlogCategory('Finance', 'finance');

        [, $postSlug] = $this->seedBlogPost([$news->id], 'Tax relief for startups', 'tax-relief-for-startups');
        $this->seedBlogPost([$news->id], 'Company culture retreat', 'company-culture-retreat');
        $this->seedBlogPost([$news->id], 'Startup tax relief checklist', 'startup-tax-relief-checklist');
        $this->seedBlogPost([$finance->id], 'Tax relief for startup founders', 'tax-relief-for-startup-founders');

        $this->get('/blog/'.$postSlug)
            ->assertOk()
            ->assertSeeInOrder([
                'Startup tax relief checklist',
                'Tax relief for startup founders',
            ])
            ->assertDontSee('Company culture retreat');
    }

    public function test_blog_article_related_posts_prioritize_same_category_matches_before_other_similar_titles(): void
    {
        $news = $this->seedBlogCategory('News', 'news');
        $finance = $this->seedBlogCategory('Finance', 'finance');

        [, $postSlug] = $this->seedBlogPost([$news->id], 'Export tax credit guide', 'export-tax-credit-guide');
        $this->seedBlogPost([$news->id], 'Export tax credit checklist', 'export-tax-credit-checklist');
        $this->seedBlogPost([$news->id], 'Guide to export tax credits', 'guide-to-export-tax-credits');
        $this->seedBlogPost([$news->id], 'Export credit tax planning', 'export-credit-tax-planning');
        $this->seedBlogPost([$finance->id], 'Export tax credit guide for founders', 'export-tax-credit-guide-for-founders');

        $this->get('/blog/'.$postSlug)
            ->assertOk()
            ->assertSee('Export tax credit checklist')
            ->assertSee('Guide to export tax credits')
            ->assertSee('Export credit tax planning')
            ->assertDontSee('Export tax credit guide for founders');
    }

    public function test_removed_public_auth_and_account_routes_are_not_available(): void
    {
        $this->get('/auth/login')->assertNotFound();
        $this->get('/auth/register')->assertNotFound();
        $this->get('/register')->assertNotFound();
        $this->get('/forgot-password')->assertNotFound();
        $this->get('/account')->assertNotFound();
        $this->get('/account/profile')->assertNotFound();
    }

    /**
     * @param  array<string, mixed>  $translation
     */
    private function seedHomeBlock(string $type, string $placement, array $translation): ContentBlock
    {
        ContentBlockSlot::query()
            ->where('placement', $placement)
            ->update(['is_active' => false]);

        $block = ContentBlock::query()->create([
            'code' => 'test-'.$type.'-'.strtolower((string) str()->random(8)),
            'name' => 'Test '.$type,
            'type' => $type,
            'is_active' => true,
            'payload' => null,
        ]);
        $block->translations()->create(array_merge([
            'locale' => 'hr',
            'title' => '',
            'subtitle' => null,
            'cta_label' => null,
            'cta_url' => null,
            'payload' => null,
        ], $translation));
        $block->slots()->create([
            'placement' => $placement,
            'frontend_variant' => 'desktop',
            'target_type' => null,
            'target_ref' => null,
            'sort_order' => 0,
            'is_active' => true,
        ]);

        Cache::flush();

        return $block;
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function homeServicePayloadRows(): array
    {
        return [
            [
                'key' => 'audit',
                'title' => 'Revizija',
                'subtitle' => 'sigurnost i povjerenje u brojke',
                'text' => 'Neovisna provjera financijskih izvještaja.',
                'image_alt' => 'Revizija financijskih izvještaja',
                'url' => '/revizija',
                'action_label' => 'Saznajte više',
            ],
            [
                'key' => 'accounting',
                'title' => 'Računovodstvo i porezi',
                'subtitle' => 'kontrola, jasnoća i porezna sigurnost',
                'text' => 'Precizno vođenje poslovnih knjiga i porezno savjetovanje.',
                'image_alt' => 'Računovodstvo i financijsko izvještavanje',
                'url' => '/racunovodstvo',
                'action_label' => 'Saznajte više',
            ],
            [
                'key' => 'advisory',
                'title' => 'Savjetovanje',
                'subtitle' => 'rast, optimizacija i bolji financijski izbor',
                'text' => 'Financijsko i strateško savjetovanje.',
                'image_alt' => 'Poslovno i financijsko savjetovanje',
                'url' => '/savjetovanje',
                'action_label' => 'Saznajte više',
            ],
        ];
    }

    /**
     * @return array{BlogPost,string}
     */
    private function seedBlogPost(
        array $categoryIds = [],
        ?string $title = null,
        ?string $slug = null,
        ?string $excerpt = null,
        $publishedAt = null
    ): array {
        $post = BlogPost::query()->create([
            'code' => 'blog-'.strtolower((string) str()->random(6)),
            'is_active' => true,
            'published_at' => $publishedAt ?? now()->subDay(),
            'sort_order' => 1,
        ]);

        $slug = $slug ?: 'blog-'.strtolower((string) str()->random(6));

        BlogPostTranslation::query()->create([
            'post_id' => $post->id,
            'locale' => 'en',
            'title' => $title ?: 'Blog '.$slug,
            'slug' => $slug,
            'excerpt' => $excerpt ?: 'Blog excerpt',
            'body_html' => '<p>Blog body</p>',
        ]);

        BlogPostTranslation::query()->create([
            'post_id' => $post->id,
            'locale' => 'hr',
            'title' => $title ?: 'Blog '.$slug,
            'slug' => $slug,
            'excerpt' => $excerpt ?: 'Blog excerpt',
            'body_html' => '<p>Blog body</p>',
        ]);

        if ($categoryIds !== []) {
            $post->categories()->sync(
                collect($categoryIds)
                    ->values()
                    ->mapWithKeys(fn ($categoryId, $index): array => [
                        (int) $categoryId => [
                            'sort_order' => $index,
                            'is_primary' => $index === 0,
                        ],
                    ])
                    ->all()
            );
        }

        return [$post, $slug];
    }

    private function seedBlogCategory(string $name, string $slug): Category
    {
        $category = Category::query()->create([
            'scope' => Category::SCOPE_BLOG,
            'code' => 'blog-cat-'.strtolower((string) str()->random(6)),
            'is_active' => true,
            'show_in_menu' => true,
            'sort_order' => 1,
        ]);

        CategoryTranslation::query()->create([
            'category_id' => $category->id,
            'scope' => Category::SCOPE_BLOG,
            'locale' => 'en',
            'name' => $name,
            'slug' => $slug,
            'description' => $name.' description',
        ]);

        CategoryTranslation::query()->create([
            'category_id' => $category->id,
            'scope' => Category::SCOPE_BLOG,
            'locale' => 'hr',
            'name' => $name,
            'slug' => $slug,
            'description' => $name.' description',
        ]);

        return $category;
    }

    private function seedResourceDocument(string $code, string $title, string $slug): ResourceDocument
    {
        $document = ResourceDocument::query()->create([
            'code' => $code,
            'group_code' => 'downloads',
            'is_active' => true,
            'published_at' => now()->subDay(),
            'sort_order' => 1,
            'download_url' => 'https://example.test/files/'.$code.'.pdf',
        ]);

        ResourceDocumentTranslation::query()->create([
            'document_id' => $document->id,
            'locale' => 'hr',
            'title' => $title,
            'slug' => $slug,
            'excerpt' => 'Resource excerpt',
        ]);

        ResourceDocumentTranslation::query()->create([
            'document_id' => $document->id,
            'locale' => 'en',
            'title' => $title,
            'slug' => $slug,
            'excerpt' => 'Resource excerpt',
        ]);

        return $document->load('translations');
    }

    /**
     * @return array{InfoPage,string}
     */
    private function seedInfoPage(): array
    {
        $page = InfoPage::query()->create([
            'code' => 'page-'.strtolower((string) str()->random(6)),
            'layout' => 'default',
            'is_active' => true,
            'published_at' => now()->subDay(),
            'sort_order' => 1,
        ]);

        $slug = 'page-'.strtolower((string) str()->random(6));

        InfoPageTranslation::query()->create([
            'page_id' => $page->id,
            'locale' => 'en',
            'title' => 'Page '.$slug,
            'slug' => $slug,
            'excerpt' => 'Page excerpt',
            'body_html' => '<p>Page body</p>',
        ]);

        return [$page, $slug];
    }

    private function seedCroatianAboutCmsPayload(): void
    {
        InfoPage::query()
            ->where('code', 'about-us')
            ->firstOrFail()
            ->translation('hr')
            ->firstOrFail()
            ->update(['payload' => ['about_page' => AboutPageDefaults::merge([], 'hr')]]);
    }

    private function seedCroatianCareerCmsPayload(): void
    {
        InfoPage::query()
            ->where('code', 'career')
            ->firstOrFail()
            ->translation('hr')
            ->firstOrFail()
            ->update(['payload' => ['career_page' => CareerPageDefaults::merge([], 'hr')]]);
    }
}
