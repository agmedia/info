<?php

namespace Tests\Feature\Front;

use App\Models\Catalog\Category\Category;
use App\Models\Catalog\Category\CategoryTranslation;
use App\Models\Content\Blog\BlogPost;
use App\Models\Content\Blog\BlogPostTranslation;
use App\Models\Content\ContentBlock;
use App\Models\Content\Page\InfoPage;
use App\Models\Content\Page\InfoPageTranslation;
use App\Models\Content\Resource\ResourceDocument;
use App\Models\Content\Resource\ResourceDocumentTranslation;
use App\Models\Content\Support\CareerApplication;
use App\Models\Content\Support\ContactMessage;
use App\Models\Content\Support\Comment;
use App\Models\Content\Team\TeamMember;
use App\Models\Content\Team\TeamMemberTranslation;
use App\Services\Front\NavigationMenuService;
use App\Services\Settings\SystemSettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StorefrontFrontFeatureTest extends TestCase
{
    use RefreshDatabase;

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
        $this->get('/obiteljski-biznis')->assertOk();
        $this->get('/contact')->assertOk();
        $this->get('/reference')->assertOk();
        $this->get('/ac-forma-robot')->assertOk();
        $this->get('/leasing-kalkulator')->assertOk();

        $this->assertNotNull($post);
        $this->assertNotNull($page);
    }

    public function test_info_page_uses_clean_url_and_legacy_page_url_redirects(): void
    {
        [, $pageSlug] = $this->seedInfoPage();

        $this->get('/'.$pageSlug)->assertOk();
        $this->get('/page/'.$pageSlug)
            ->assertStatus(301)
            ->assertRedirect(route('pages.show', ['slug' => $pageSlug]));
    }

    public function test_career_page_renders_curated_cms_layout(): void
    {
        $this->get('/karijera')
            ->assertOk()
            ->assertSee('Postani dio tima')
            ->assertSee('Selekcijski proces u ALPHA CAPITALISU')
            ->assertSee('Pošaljite nam svoj CV')
            ->assertSee('ALPHA CAPITALIS postoji od 2012. godine s ciljem pružanja podrške klijentima u svijetu financija kroz sve faze razvoja poslovanja.');
    }

    public function test_career_page_renders_custom_copy_from_translation_payload(): void
    {
        $careerPage = InfoPage::query()->where('code', 'career')->firstOrFail();

        $careerPage->translations()->where('locale', 'hr')->update([
            'payload' => [
                'career_page' => [
                    'intro' => [
                        'title' => 'Rasti s nama',
                        'highlight' => 'Custom uvodni highlight za karijera stranicu.',
                        'body' => [
                            'Custom uvodni odlomak za karijera stranicu.',
                        ],
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
                    ],
                ],
            ],
        ]);

        $this->get('/karijera')
            ->assertOk()
            ->assertSee('Rasti s nama')
            ->assertSee('Custom uvodni highlight za karijera stranicu.')
            ->assertSee('Custom uvodni odlomak za karijera stranicu.')
            ->assertSee('Kako izgleda prijava')
            ->assertSee('Proces zapošljavanja u')
            ->assertSee('Faza 01')
            ->assertSee('Prvi kontakt')
            ->assertSee('Custom opis prvog koraka.')
            ->assertSee('Pridruzi nam se danas')
            ->assertSee('Custom CTA highlight.')
            ->assertSee('Custom CTA odlomak 2.')
            ->assertSee('Posalji otvorenu prijavu')
            ->assertDontSee('Postani dio tima')
            ->assertDontSee('Pošaljite nam svoj CV');
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

    public function test_contact_form_stores_message(): void
    {
        $this->post('/contact', [
            'name' => 'Front Tester',
            'email' => 'front@example.test',
            'phone' => '+38591000000',
            'subject' => 'Wholesale inquiry',
            'message' => 'Please contact me with available B2B pricing details.',
            'accept_terms' => '1',
        ])->assertRedirect('/contact');

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
        $this->assertSame('/contact', $message->payload['source_page'] ?? null);
    }

    public function test_family_business_contact_form_can_redirect_back_to_section(): void
    {
        $this->post('/contact', [
            'first_name' => 'Ana',
            'last_name' => 'Horvat',
            'company' => 'Obitelj Horvat d.o.o.',
            'email' => 'ana@example.test',
            'phone' => '+38598111222',
            'subject' => 'Dogovor sastanka',
            'message' => 'Želim dogovoriti inicijalni sastanak za temu tranzicije vlasništva.',
            'accept_terms' => '1',
            'redirect_to' => '/obiteljski-biznis#family-business-sastanak',
        ])->assertRedirect('/obiteljski-biznis#family-business-sastanak');

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
        $this->assertSame('/obiteljski-biznis', $message->payload['source_page'] ?? null);
    }

    public function test_contact_page_renders_official_office_data(): void
    {
        $this->get('/contact')
            ->assertOk()
            ->assertSee('ALPHA CAPITALIS d.o.o.')
            ->assertSee('Ulica R. F. Mihanovića 9')
            ->assertSee('ALPHA CAPITALIS EAST d.o.o.')
            ->assertSee('Duga ulica 67')
            ->assertSee('ALPHA CAPITALIS TIMIA d.o.o.')
            ->assertSee('Korzo 30')
            ->assertSee('51 000 Rijeka')
            ->assertSee('040282129')
            ->assertSee('HR6524020061500165841')
            ->assertSee('info@alphacapitalis.com')
            ->assertSee('+385 (1) 580 6656')
            ->assertSee('+385 (0) 51 301 503');
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

    public function test_home_header_links_to_lease_calculator_page(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('/leasing-kalkulator', false)
            ->assertDontSee('#msfi16-kalkulator', false);
    }

    public function test_home_header_links_team_navigation_item_to_public_team_page(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('ALPHA CAPITALIS Tim')
            ->assertSee(route('team.index'), false)
            ->assertDontSee('#tim', false);
    }

    public function test_home_header_links_eu_projects_navigation_item_to_clean_page_url(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee(route('pages.show', ['slug' => 'eu-projekti']), false)
            ->assertDontSee('#eu-projekti', false);
    }

    public function test_home_header_links_career_navigation_item_to_clean_page_url(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee(route('pages.show', ['slug' => 'karijera']), false)
            ->assertDontSee('#karijera', false);
    }

    public function test_home_header_links_references_navigation_item_to_clean_page_url(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee(route('pages.show', ['slug' => 'reference']), false)
            ->assertDontSee('#reference', false);
    }

    public function test_home_header_links_academy_navigation_item_to_clean_page_url(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee(route('pages.show', ['slug' => 'akademija']), false)
            ->assertDontSee('#edukacija-akademija', false);
    }

    public function test_home_services_section_renders_requested_service_order(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('EU fondovi');

        $content = $response->getContent();
        $this->assertIsString($content);

        $anchors = [
            'odjel-financije',
            'odjel-racunovodstvo',
            'odjel-revizija',
            'odjel-porezi',
            'odjel-eu-fondovi',
            'odjel-obiteljski-biznisi',
        ];

        $positions = [];
        foreach ($anchors as $anchor) {
            $position = strpos($content, 'id="'.$anchor.'"');
            $this->assertNotFalse($position, 'Missing services anchor: '.$anchor);
            $positions[] = $position;
        }

        $sortedPositions = $positions;
        sort($sortedPositions);

        $this->assertSame($sortedPositions, $positions);
    }

    public function test_navigation_menu_service_resolves_page_and_custom_links(): void
    {
        [$page, $pageSlug] = $this->seedInfoPage();

        app(SystemSettingsService::class)->put(NavigationMenuService::SETTINGS_KEY, [
            [
                'type' => 'page',
                'label' => 'Savjeti',
                'page_id' => $page->id,
                'url' => '',
                'open_in_new_tab' => false,
                'show_dropdown' => false,
                'is_active' => true,
                'sort_order' => 0,
            ],
            [
                'type' => 'custom',
                'label' => 'Kontakt',
                'page_id' => 0,
                'url' => '/contact',
                'open_in_new_tab' => false,
                'show_dropdown' => false,
                'is_active' => true,
                'sort_order' => 1,
            ],
        ]);

        $items = app(NavigationMenuService::class)->forLocale('en');

        $this->assertCount(2, $items);
        $this->assertSame('Savjeti', $items[0]['label'] ?? null);
        $this->assertSame(route('pages.show', ['slug' => $pageSlug]), $items[0]['url'] ?? null);
        $this->assertSame('Kontakt', $items[1]['label'] ?? null);
        $this->assertSame('/contact', $items[1]['url'] ?? null);
    }

    public function test_home_renders_client_testimonials_section(): void
    {
        Comment::query()->create([
            'commentable_type' => null,
            'commentable_id' => null,
            'author_name' => 'Ivan Knezevic',
            'author_email' => null,
            'locale' => 'en',
            'body' => 'We gained a much clearer picture of profitability after implementing the controlling system.',
            'rating' => 5,
            'status' => Comment::STATUS_APPROVED,
            'is_featured' => true,
            'reviewed_at' => now(),
            'payload' => ['company' => 'Palma D.O.O.'],
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('What Our Clients Say')
            ->assertSee('We gained a much clearer picture of profitability after implementing the controlling system.')
            ->assertSee('Ivan Knezevic')
            ->assertSee('Palma D.O.O.');
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
            ->assertSee('<span class="front-scroll-breadcrumb-current">Finance</span>', false)
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

        $this->get('/alpha-capitalis-tim')
            ->assertOk()
            ->assertSee('Ivana Horvat')
            ->assertSee('Senior Manager')
            ->assertDontSee('Finance')
            ->assertDontSee('Tax')
            ->assertSee('team@example.test')
            ->assertSee('https://linkedin.com/company/alpha-team', false);
    }

    public function test_blog_article_breadcrumb_includes_primary_category_archive(): void
    {
        $news = $this->seedBlogCategory('News', 'news');
        [, $postSlug] = $this->seedBlogPost([$news->id], 'Growth Update', 'growth-update');

        $this->get('/blog/'.$postSlug)
            ->assertOk()
            ->assertSee('/blog/news', false)
            ->assertSee('class="front-scroll-breadcrumb-link">News</a>', false)
            ->assertSee('class="front-scroll-breadcrumb-current ac-blog-breadcrumb-current"', false);
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

    public function test_family_business_page_shows_only_family_business_blog_posts_when_category_exists(): void
    {
        $familyBusiness = $this->seedBlogCategory('Family Business', 'family-business');
        $tax = $this->seedBlogCategory('Tax', 'tax');

        $this->seedBlogPost([$familyBusiness->id], 'Succession Playbook', 'succession-playbook');
        $this->seedBlogPost([$tax->id], 'VAT Reminder', 'vat-reminder');

        $this->get('/obiteljski-biznis')
            ->assertOk()
            ->assertSee('Obiteljski biznis')
            ->assertSee('Succession Playbook')
            ->assertDontSee('VAT Reminder')
            ->assertSee('Najnovije objave iz kategorije');
    }

    public function test_family_business_page_shows_only_team_members_from_family_business_department(): void
    {
        $familyMember = TeamMember::query()->create([
            'code' => 'family-team-'.strtolower((string) str()->random(6)),
            'is_active' => true,
            'sort_order' => 1,
            'email' => 'family@example.test',
            'linkedin_url' => 'https://linkedin.com/in/family-team',
        ]);

        TeamMemberTranslation::query()->create([
            'team_member_id' => $familyMember->id,
            'locale' => 'en',
            'name' => 'Danijel Pevec',
            'position' => 'Partner',
            'departments' => "obiteljski-biznis\nSavjetovanje",
            'description_html' => '<p>Radi s obiteljskim poduzećima kroz tranzicije i upravljanje.</p>',
        ]);

        $otherMember = TeamMember::query()->create([
            'code' => 'tax-team-'.strtolower((string) str()->random(6)),
            'is_active' => true,
            'sort_order' => 2,
            'email' => 'tax@example.test',
        ]);

        TeamMemberTranslation::query()->create([
            'team_member_id' => $otherMember->id,
            'locale' => 'en',
            'name' => 'Tax Specialist',
            'position' => 'Senior Manager',
            'departments' => "tax\nfinance",
            'description_html' => '<p>Porezno savjetovanje.</p>',
        ]);

        $this->get('/obiteljski-biznis')
            ->assertOk()
            ->assertSee('Naš tim za obiteljsko savjetovanje')
            ->assertSee('Tu smo kako biste zadobili uvid u cjelovitu perspektivu.')
            ->assertSee('Ugovorite sastanak')
            ->assertSee('Danijel Pevec')
            ->assertSee('family@example.test')
            ->assertDontSee('Tax Specialist');
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
     * @return array{BlogPost,string}
     */
    private function seedBlogPost(
        array $categoryIds = [],
        ?string $title = null,
        ?string $slug = null,
        ?string $excerpt = null,
        $publishedAt = null
    ): array
    {
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
}
