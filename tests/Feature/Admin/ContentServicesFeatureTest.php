<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Content\Service\Form as ServiceForm;
use App\Models\Content\Blog\BlogPost;
use App\Models\Content\Blog\BlogPostTranslation;
use App\Models\Content\Service\ServicePage;
use App\Models\Settings\Local\Language;
use App\Models\User;
use App\Support\Content\ServicePageTemplateRegistry;
use App\Support\Content\StructuredRichText;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Tests\TestCase;

class ContentServicesFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_services_index_page_is_seeded(): void
    {
        $page = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::SERVICES_INDEX)
            ->with('translations')
            ->first();

        $this->assertNotNull($page);
        $this->assertSame('services', $page->code);
        $this->assertTrue((bool) $page->is_active);
        $this->assertSame(
            'Usluge',
            (string) $page->translations->firstWhere('locale', 'hr')?->title
        );
    }

    public function test_default_tax_service_page_is_seeded(): void
    {
        $page = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::TAX)
            ->with('translations')
            ->first();

        $this->assertNotNull($page);
        $this->assertSame('tax', $page->code);
        $this->assertTrue((bool) $page->is_active);
        $this->assertSame(
            'Porezi',
            (string) $page->translations->firstWhere('locale', 'hr')?->title
        );
    }

    public function test_default_eu_funds_service_page_is_seeded(): void
    {
        $page = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::EU_FUNDS)
            ->with('translations')
            ->first();

        $this->assertNotNull($page);
        $this->assertSame('eu-fondovi', $page->code);
        $this->assertTrue((bool) $page->is_active);
        $this->assertSame(
            'EU fondovi',
            (string) $page->translations->firstWhere('locale', 'hr')?->title
        );
    }

    public function test_admin_can_open_service_pages_screen(): void
    {
        $user = $this->makeAdminUser();

        $this->actingAs($user)
            ->get('/admin/content/services?locale=hr')
            ->assertOk()
            ->assertSee('Usluge')
            ->assertSee('Bankovni krediti')
            ->assertSee('Zakon o poticanju ulaganja')
            ->assertDontSee('Obiteljski biznis')
            ->assertSee('Porezi');
    }

    public function test_direct_english_service_editor_stays_on_missing_locale_and_saves_no_default_copy(): void
    {
        $user = $this->makeAdminUser();
        Language::query()->updateOrCreate(['code' => 'en'], [
            'locale' => 'en',
            'name' => 'English',
            'native_name' => 'English',
            'direction' => 'ltr',
            'is_default' => false,
            'is_active' => true,
            'sort_order' => 20,
        ]);

        $page = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::AUDIT)
            ->firstOrFail();
        $page->translations()->where('locale', 'en')->delete();
        $croatian = $page->translations()->where('locale', 'hr')->firstOrFail();
        $croatianBefore = $croatian->only(['title', 'slug', 'meta_title', 'meta_description']);
        $croatianPayloadBefore = $croatian->getRawOriginal('payload');

        Livewire::withQueryParams(['locale' => 'en'])
            ->actingAs($user)
            ->test(ServiceForm::class, ['servicePageId' => $page->id])
            ->assertSet('form.locale', 'en')
            ->assertSet('form.title', '')
            ->assertSet('form.slug', '')
            ->assertSet('form.translation_payload.hero.subtitle_lead', '')
            ->assertSet('form.translation_payload.services.items.0.title', '')
            ->set('form.title', 'Audit')
            ->set('form.slug', 'audit')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.content.services.index', ['locale' => 'en']));

        $english = $page->fresh()->translations()->where('locale', 'en')->firstOrFail();
        $this->assertSame([], (array) $english->payload);
        $this->assertStringNotContainsString(
            'Trust in financial information begins with an independent and expert audit.',
            json_encode($english->payload, JSON_UNESCAPED_UNICODE) ?: '',
        );

        $croatian->refresh();
        $this->assertSame($croatianBefore, $croatian->only(array_keys($croatianBefore)));
        $this->assertSame($croatianPayloadBefore, $croatian->getRawOriginal('payload'));
    }

    public function test_noop_save_of_partial_english_service_payload_does_not_expand_defaults(): void
    {
        $user = $this->makeAdminUser();
        Language::query()->updateOrCreate(['code' => 'en'], [
            'locale' => 'en',
            'name' => 'English',
            'native_name' => 'English',
            'direction' => 'ltr',
            'is_default' => false,
            'is_active' => true,
            'sort_order' => 20,
        ]);

        $page = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::AUDIT)
            ->firstOrFail();
        $exactPayload = [
            'hero' => ['intro' => 'Exact English CMS audit introduction.'],
            'custom_extension' => ['preserve' => 'Exact CMS extension'],
        ];
        $english = $page->translations()->updateOrCreate(['locale' => 'en'], [
            'title' => 'Audit',
            'slug' => 'audit',
            'payload' => $exactPayload,
        ]);
        $englishPayloadBefore = $english->getRawOriginal('payload');
        $croatianPayloadBefore = $page->translations()->where('locale', 'hr')->firstOrFail()->getRawOriginal('payload');

        Livewire::withQueryParams(['locale' => 'en'])
            ->actingAs($user)
            ->test(ServiceForm::class, ['servicePageId' => $page->id])
            ->assertSet('form.locale', 'en')
            ->assertSet('form.translation_payload.hero.intro', 'Exact English CMS audit introduction.')
            ->assertSet('form.translation_payload.hero.subtitle_lead', '')
            ->assertSet('form.translation_payload.services.items.0.title', '')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.content.services.index', ['locale' => 'en']));

        $this->assertSame($exactPayload, $english->fresh()->payload);
        $this->assertSame($englishPayloadBefore, $english->fresh()->getRawOriginal('payload'));
        $this->assertSame(
            $croatianPayloadBefore,
            $page->fresh()->translations()->where('locale', 'hr')->firstOrFail()->getRawOriginal('payload'),
        );
    }

    public function test_admin_can_search_advisory_subpages_on_service_pages_screen(): void
    {
        $user = $this->makeAdminUser();

        $this->actingAs($user)
            ->get('/admin/content/services?locale=hr')
            ->assertOk()
            ->assertSee('Bankovni krediti');

        Livewire::actingAs($user)
            ->test(\App\Livewire\Admin\Content\Service\Manager::class)
            ->set('locale', 'hr')
            ->set('search', 'bankovni')
            ->assertSee('Savjetovanje')
            ->assertSee('Bankovni krediti');
    }

    public function test_each_advisory_row_links_to_its_own_page_editor(): void
    {
        $user = $this->makeAdminUser();
        $page = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::ADVISORY)
            ->firstOrFail();

        $response = $this->actingAs($user)->get('/admin/content/services?locale=hr');

        foreach (['financial', 'funding', 'bank_loans', 'zopu', 'ma', 'due_diligence', 'valuations', 'tax'] as $section) {
            $response->assertSee(route('admin.content.services.edit', [
                'servicePage' => $page->id,
                'locale' => 'hr',
                'section' => $section,
            ]));
        }
    }

    public function test_main_advisory_editor_only_shows_fields_from_the_main_route(): void
    {
        $user = $this->makeAdminUser();
        $page = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::ADVISORY)
            ->firstOrFail();

        Livewire::withQueryParams(['section' => 'main'])
            ->actingAs($user)
            ->test(ServiceForm::class, ['servicePageId' => $page->id])
            ->assertSet('contentSection', 'main')
            ->assertSeeHtml('wire:model="form.translation_payload.hero.subtitle_lead"')
            ->assertSeeHtml('wire:model.live.debounce.300ms="form.translation_payload.overview.body_html"')
            ->assertSeeHtml('wire:model.live.debounce.300ms="form.translation_payload.pandea.body_html"')
            ->assertSeeHtml('wire:model.live.debounce.300ms="form.translation_payload.approach.body_html"')
            ->assertSeeHtml('wire:model="form.translation_payload.meeting.title"')
            ->assertDontSeeHtml('wire:model="form.translation_payload.overview.body.0"')
            ->assertDontSeeHtml('wire:model="form.translation_payload.pandea.body.0"')
            ->assertDontSeeHtml('wire:model="form.translation_payload.approach.body.0"')
            ->assertDontSeeHtml('wire:model="form.translation_payload.financial.title"')
            ->assertDontSeeHtml('wire:model="form.translation_payload.funding.title"')
            ->assertDontSeeHtml('wire:model="form.translation_payload.ma.title"');
    }

    public function test_each_advisory_subpage_editor_only_shows_fields_for_that_route(): void
    {
        $user = $this->makeAdminUser();
        $page = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::ADVISORY)
            ->firstOrFail();

        $sections = [
            'financial' => 'Financijsko savjetovanje',
            'funding' => 'Pribavljanje financiranja',
            'bank_loans' => 'Bankovni krediti',
            'zopu' => 'Zakon o poticanju ulaganja',
            'ma' => 'Prodaja i kupnja poduzeća (M&amp;A)',
            'due_diligence' => 'Dubinska snimanja (Due Diligence)',
            'valuations' => 'Procjena vrijednosti društva',
            'tax' => 'Porezno savjetovanje',
        ];

        foreach ($sections as $section => $title) {
            $component = Livewire::withQueryParams(['section' => $section])
                ->actingAs($user)
                ->test(ServiceForm::class, ['servicePageId' => $page->id])
                ->assertSet('contentSection', $section)
                ->assertSeeHtml('wire:model="form.translation_payload.'.$section.'.meta_title"')
                ->assertSeeHtml('wire:model="form.translation_payload.'.$section.'.meta_description"')
                ->assertSeeHtml('wire:model="form.translation_payload.'.$section.'.meeting.title"')
                ->assertDontSeeHtml('wire:model="form.translation_payload.meeting.title"')
                ->assertDontSeeHtml('wire:model="form.translation_payload.hero.subtitle_lead"');

            if ($section === 'funding') {
                $component
                    ->assertSeeHtml('wire:model.live.debounce.300ms="form.translation_payload.funding.approach_body_html"')
                    ->assertDontSeeHtml('wire:model="form.translation_payload.funding.approach_body.0"');
            } else {
                $component
                    ->assertSeeHtml('wire:model.live.debounce.300ms="form.translation_payload.'.$section.'.overview_body_html"')
                    ->assertSeeHtml('wire:model.live.debounce.300ms="form.translation_payload.'.$section.'.services_body_html"')
                    ->assertSeeHtml('wire:model="form.translation_payload.'.$section.'.help_items_text"')
                    ->assertSeeHtml('wire:model.live.debounce.300ms="form.translation_payload.'.$section.'.approach_body_html"')
                    ->assertDontSeeHtml('wire:model="form.translation_payload.'.$section.'.overview_body.0"')
                    ->assertDontSeeHtml('wire:model="form.translation_payload.'.$section.'.services_body.0"')
                    ->assertDontSeeHtml('wire:model="form.translation_payload.'.$section.'.help_items.0"')
                    ->assertDontSeeHtml('wire:model="form.translation_payload.'.$section.'.approach_body.0"');
            }

            $this->assertStringContainsString($title, $component->html());

            foreach (array_keys($sections) as $otherSection) {
                if ($otherSection === $section) {
                    continue;
                }

                $this->assertStringNotContainsString(
                    'wire:model="form.translation_payload.'.$otherSection.'.title"',
                    $component->html()
                );
                $this->assertStringNotContainsString(
                    'wire:model="form.translation_payload.'.$otherSection.'.meta_title"',
                    $component->html()
                );
            }
        }
    }

    public function test_advisory_subpages_start_with_existing_shared_content_without_losing_custom_copy(): void
    {
        $merged = ServicePageTemplateRegistry::mergeTranslationPayload(
            ServicePageTemplateRegistry::ADVISORY,
            [
                'meeting' => [
                    'title' => 'Postojeći prilagođeni kontaktni naslov',
                ],
                'blog_section' => [
                    'title' => 'Postojeći prilagođeni naslov objava',
                ],
                'service_cards' => [
                    [
                        'title' => 'Pribavljanje financiranja',
                        'text' => 'Postojeća prilagođena hero poruka financiranja.',
                        'url' => '/savjetovanje/pribavljanje-financiranja',
                    ],
                ],
                'pandea' => [
                    'title' => 'Postojeći prilagođeni Pandea naslov',
                ],
            ],
            'hr'
        );

        $this->assertSame('Postojeći prilagođeni kontaktni naslov', data_get($merged, 'financial.meeting.title'));
        $this->assertSame('Postojeći prilagođeni naslov objava', data_get($merged, 'tax.blog_section.title'));
        $this->assertSame('Postojeća prilagođena hero poruka financiranja.', data_get($merged, 'funding.hero_intro'));
        $this->assertSame('Postojeći prilagođeni Pandea naslov', data_get($merged, 'ma.pandea.title'));
    }

    public function test_consolidated_service_editor_fields_preserve_legacy_and_explicit_content(): void
    {
        $audit = ServicePageTemplateRegistry::mergeTranslationPayload(
            ServicePageTemplateRegistry::AUDIT,
            [
                'overview' => [
                    'intro' => 'Prilagođeni uvod.',
                    'body' => ['Prvi odlomak.', 'Drugi odlomak.'],
                ],
                'obligors' => [
                    'primary_items' => [
                        ['text' => 'Strukturirani obveznik', 'children_text' => "Prvi kriterij\nDrugi kriterij"],
                    ],
                ],
            ],
            'hr'
        );

        $this->assertSame(
            '<p>Prilagođeni uvod.</p><p>Prvi odlomak.</p><p>Drugi odlomak.</p>',
            data_get($audit, 'overview.body_html')
        );
        $this->assertSame(
            ['Prvi kriterij', 'Drugi kriterij'],
            data_get($audit, 'obligors.primary_items.0.children')
        );

        $auditWithEmptyEditor = ServicePageTemplateRegistry::mergeTranslationPayload(
            ServicePageTemplateRegistry::AUDIT,
            ['overview' => ['body_html' => '', 'body' => ['Ovaj tekst ne smije biti vraćen.']]],
            'hr'
        );
        $this->assertSame('', data_get($auditWithEmptyEditor, 'overview.body_html'));

        $accounting = ServicePageTemplateRegistry::mergeTranslationPayload(
            ServicePageTemplateRegistry::ACCOUNTING,
            ['overview' => ['intro' => 'Uvod.', 'body' => ['Glavni tekst.', 'Partnerski tekst.']]],
            'hr'
        );
        $this->assertSame('<p>Uvod.</p><p>Glavni tekst.</p>', data_get($accounting, 'overview.body_html'));
        $this->assertSame('<p>Partnerski tekst.</p>', data_get($accounting, 'overview.partner_body_html'));

        $advisory = ServicePageTemplateRegistry::mergeTranslationPayload(
            ServicePageTemplateRegistry::ADVISORY,
            ['bank_loans' => ['help_items_text' => "Analiza sposobnosti\nPriprema dokumentacije"]],
            'hr'
        );
        $this->assertSame(
            ['Analiza sposobnosti', 'Priprema dokumentacije'],
            data_get($advisory, 'bank_loans.help_items')
        );

        $euFunds = ServicePageTemplateRegistry::mergeTranslationPayload(
            ServicePageTemplateRegistry::EU_FUNDS,
            [
                'resources' => ['cards' => [['body' => ['Prvi tekst programa.', 'Drugi tekst programa.']]]],
                'laws' => ['cards' => [['lists' => [['items_text' => "Prva stavka\nDruga stavka"]]]]],
            ],
            'hr'
        );
        $this->assertSame(
            '<p>Prvi tekst programa.</p><p>Drugi tekst programa.</p>',
            data_get($euFunds, 'resources.cards.0.body_html')
        );
        $this->assertSame(
            ['Prva stavka', 'Druga stavka'],
            data_get($euFunds, 'laws.cards.0.lists.0.items')
        );
    }

    public function test_service_wysiwyg_html_is_sanitized_without_losing_safe_formatting_and_links(): void
    {
        $merged = ServicePageTemplateRegistry::mergeTranslationPayload(
            ServicePageTemplateRegistry::AUDIT,
            [
                'overview' => [
                    'body_html' => '<p class="unsafe" onclick="alert(1)"><strong>Siguran podebljani tekst</strong><script>alert(2)</script> <em>i siguran kurziv</em> <a href="https://example.com/sigurna-poveznica" target="_blank" rel="opener" onmouseover="alert(3)">Sigurna poveznica</a> <a href="javascript:alert(4)">Opasna poveznica</a></p><iframe src="https://example.com/nezeljeni-video"></iframe>',
                ],
            ],
            'hr'
        );

        $html = (string) data_get($merged, 'overview.body_html');

        $this->assertSame(
            '<p><strong>Siguran podebljani tekst</strong> <em>i siguran kurziv</em> <a href="https://example.com/sigurna-poveznica" target="_blank" rel="noopener noreferrer">Sigurna poveznica</a> <a>Opasna poveznica</a></p>',
            $html
        );
        $this->assertStringNotContainsString('<script', $html);
        $this->assertStringNotContainsString('<iframe', $html);
        $this->assertStringNotContainsString('onclick=', $html);
        $this->assertStringNotContainsString('onmouseover=', $html);
        $this->assertStringNotContainsString('javascript:', $html);
    }

    public function test_structured_rich_text_blocks_preserve_nested_lists(): void
    {
        $nestedList = '<ol><li>Vanjska stavka<ol><li>Ugniježđena stavka</li></ol></li><li>Zadnja stavka</li></ol>';
        $html = $nestedList.'<p>Završni odlomak.</p>';

        $this->assertSame(
            [$nestedList, '<p>Završni odlomak.</p>'],
            StructuredRichText::blocks(StructuredRichText::sanitize($html))
        );
    }

    public function test_structured_service_editor_fields_keep_shape_but_not_copy_for_unsaved_locales(): void
    {
        $user = $this->makeAdminUser();

        Livewire::actingAs($user)
            ->test(ServiceForm::class)
            ->set('form.template_key', ServicePageTemplateRegistry::AUDIT)
            ->assertSet('form.template_key', ServicePageTemplateRegistry::AUDIT)
            ->assertSet('form.translation_payload.overview.body_html', '')
            ->assertSet('form.translation_payload.approach.body_html', '');

        $page = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::EU_FUNDS)
            ->firstOrFail();

        Livewire::withQueryParams(['locale' => 'hr'])
            ->actingAs($user)
            ->test(ServiceForm::class, ['servicePageId' => $page->id])
            ->set('form.locale', 'de')
            ->assertSet('form.locale', 'de')
            ->assertSet('form.translation_payload.overview.body_html', '')
            ->assertSet('form.translation_payload.approach.body_html', '')
            ->assertSet('form.translation_payload.resources.cards.0.body_html', '');
    }

    public function test_noop_save_of_legacy_croatian_payloads_preserves_only_exact_cms_copy(): void
    {
        config()->set('app.locale', 'hr');
        config()->set('app.fallback_locale', 'hr');

        $user = $this->makeAdminUser();
        $auditPage = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::AUDIT)
            ->firstOrFail();
        $legacyAuditBody = 'Revizija je neovisna provjera financijskih izvještaja društva s ciljem utvrđivanja daju li izvještaji istinit i pošten prikaz financijskog položaja. Revizor ne zastupa menadžment ni vlasnike - zastupa istinu u brojevima.';
        $legacyAuditHtml = StructuredRichText::fromParagraphs([$legacyAuditBody]);
        $legacyAuditPayload = [
            'hero' => [
                'intro' => 'Neovisna, stručna provjera vaših financijskih izvještaja. Povećavamo povjerenje, smanjujemo rizike i jačamo kredibilitet vašeg poslovanja.',
            ],
            'overview' => [
                'title' => 'Što je revizija?',
                'intro' => '',
                'body' => [$legacyAuditBody],
            ],
        ];

        $auditPage->translations()
            ->where('locale', 'hr')
            ->firstOrFail()
            ->forceFill([
                'payload' => $legacyAuditPayload,
            ])
            ->save();

        Livewire::withQueryParams(['locale' => 'hr'])
            ->actingAs($user)
            ->test(ServiceForm::class, ['servicePageId' => $auditPage->id])
            ->assertSet('form.translation_payload.overview.body_html', $legacyAuditHtml)
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.content.services.index', ['locale' => 'hr']));

        $savedAuditPayload = $auditPage->translations()
            ->where('locale', 'hr')
            ->firstOrFail()
            ->payload;

        $this->assertSame($legacyAuditPayload, $savedAuditPayload);
        $this->assertArrayNotHasKey('body_html', $savedAuditPayload['overview']);
        $this->get('/revizija')
            ->assertOk()
            ->assertSee($legacyAuditBody)
            ->assertDontSee('Revizija pruža neovisnu i objektivnu procjenu financijskih informacija');

        $accountingPage = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::ACCOUNTING)
            ->firstOrFail();
        $legacyAccountingBody = 'Računovodstvo je sustavan zapis poslovnih transakcija koji osigurava točan prikaz financijskog stanja društva. Dobro računovodstvo nije samo zakonska obveza - to je temelj za donošenje kvalitetnih poslovnih odluka.';
        $legacyAccountingHtml = StructuredRichText::fromParagraphs([$legacyAccountingBody]);
        $legacyAccountingPayload = [
            'hero' => [
                'intro' => 'Precizno, pravovremeno i transparentno - preuzimamo vođenje vaših poslovnih knjiga kako biste se fokusirali na ono što zaista donosi rast.',
            ],
            'overview' => [
                'title' => 'Što je računovodstvo?',
                'intro' => '',
                'body' => [$legacyAccountingBody],
            ],
        ];

        $accountingPage->translations()
            ->where('locale', 'hr')
            ->firstOrFail()
            ->forceFill([
                'payload' => $legacyAccountingPayload,
            ])
            ->save();

        Livewire::withQueryParams(['locale' => 'hr'])
            ->actingAs($user)
            ->test(ServiceForm::class, ['servicePageId' => $accountingPage->id])
            ->assertSet('form.translation_payload.overview.body_html', $legacyAccountingHtml)
            ->assertSet('form.translation_payload.overview.partner_body_html', '')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.content.services.index', ['locale' => 'hr']));

        $savedAccountingPayload = $accountingPage->translations()
            ->where('locale', 'hr')
            ->firstOrFail()
            ->payload;

        $this->assertSame($legacyAccountingPayload, $savedAccountingPayload);
        $this->assertArrayNotHasKey('body_html', $savedAccountingPayload['overview']);
        $this->assertArrayNotHasKey('partner_body_html', $savedAccountingPayload['overview']);
        $this->get('/racunovodstvo')
            ->assertOk()
            ->assertSee($legacyAccountingBody)
            ->assertDontSee('Mirnije poslovanje počinje jasnim i pouzdanim brojkama.');
    }

    public function test_croatian_service_editor_and_front_keep_explicit_cms_blanks_without_php_defaults(): void
    {
        config()->set('app.locale', 'hr');
        config()->set('app.fallback_locale', 'hr');

        $user = $this->makeAdminUser();
        $page = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::AUDIT)
            ->firstOrFail();
        $translation = $page->translations()->where('locale', 'hr')->firstOrFail();
        $payload = [
            'hero' => [
                'subtitle_lead' => 'Naslov isključivo iz CMS-a',
                'intro' => '',
                'image_alt' => '',
            ],
            'meeting' => [
                'title' => '',
                'intro' => '',
                'contact_title' => '',
                'button_label' => '',
                'status' => '',
            ],
            'blog_section' => [
                'title' => '',
                'all_posts_label' => '',
                'post_action_label' => '',
            ],
        ];

        $translation->forceFill(['payload' => $payload])->save();

        Livewire::withQueryParams(['locale' => 'hr'])
            ->actingAs($user)
            ->test(ServiceForm::class, ['servicePageId' => $page->id])
            ->assertSet('form.translation_payload.hero.subtitle_lead', 'Naslov isključivo iz CMS-a')
            ->assertSet('form.translation_payload.hero.intro', '')
            ->assertSet('form.translation_payload.hero.image_alt', '')
            ->assertSet('form.translation_payload.meeting.button_label', '')
            ->assertSet('form.translation_payload.blog_section.post_action_label', '')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame($payload, $translation->fresh()->payload);

        $this->get('/revizija')
            ->assertOk()
            ->assertSee('Naslov isključivo iz CMS-a')
            ->assertDontSee('Povjerenje u financijske informacije počinje neovisnom i stručnom revizijom.')
            ->assertDontSee('Dogovorite sastanak')
            ->assertDontSee('Pogledaj sve objave')
            ->assertDontSee('Opširnije');
    }

    public function test_services_index_keeps_an_explicitly_blank_cms_card_url_without_route_fallback(): void
    {
        config()->set('app.locale', 'hr');
        config()->set('app.fallback_locale', 'hr');

        $user = $this->makeAdminUser();
        $page = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::SERVICES_INDEX)
            ->firstOrFail();
        $translation = $page->translations()->where('locale', 'hr')->firstOrFail();
        $payload = (array) $translation->payload;
        data_set($payload, 'primary_pillars.0.url', '');
        $translation->forceFill(['payload' => $payload])->save();

        Livewire::withQueryParams(['locale' => 'hr'])
            ->actingAs($user)
            ->test(ServiceForm::class, ['servicePageId' => $page->id])
            ->assertSet('form.translation_payload.primary_pillars.0.url', '')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('', data_get($translation->fresh()->payload, 'primary_pillars.0.url'));

        $this->get('/usluge')
            ->assertOk()
            ->assertDontSee('data-service-key="audit"', false)
            ->assertSee('data-service-key="accounting"', false)
            ->assertSee('data-service-key="advisory"', false);
    }

    public function test_empty_english_advisory_subpage_saved_in_cms_is_hidden_without_touching_croatian(): void
    {
        config()->set('app.locale', 'hr');
        config()->set('app.fallback_locale', 'hr');

        Language::query()->updateOrCreate(['code' => 'en'], [
            'locale' => 'en_US',
            'name' => 'English',
            'native_name' => 'English',
            'direction' => 'ltr',
            'is_default' => false,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $user = $this->makeAdminUser();
        $page = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::ADVISORY)
            ->firstOrFail();
        $croatian = $page->translations()->where('locale', 'hr')->firstOrFail();
        $croatianPayload = (array) $croatian->payload;
        data_set($croatianPayload, 'zopu.title', 'HR CMS ZoPU sadržaj ostaje');
        data_set($croatianPayload, 'zopu.hero_intro', 'HR CMS ZoPU uvod ostaje netaknut.');
        $croatian->forceFill(['payload' => $croatianPayload])->save();
        $croatianPayloadBefore = $croatian->payload;

        Livewire::withQueryParams(['locale' => 'en', 'section' => 'zopu'])
            ->actingAs($user)
            ->test(ServiceForm::class, ['servicePageId' => $page->id])
            ->assertSet('form.locale', 'en')
            ->assertSet('contentSection', 'zopu')
            ->set('form.translation_payload.zopu', [])
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame($croatianPayloadBefore, $croatian->fresh()->payload);

        $this->withSession(['front_locale' => 'en'])
            ->get('/advisory/raising-finance/investment-incentives')
            ->assertNotFound();
    }

    public function test_explicitly_empty_multiline_service_lists_remain_empty(): void
    {
        $advisory = ServicePageTemplateRegistry::mergeTranslationPayload(
            ServicePageTemplateRegistry::ADVISORY,
            ['bank_loans' => ['help_items_text' => '']],
            'hr'
        );

        $this->assertSame('', data_get($advisory, 'bank_loans.help_items_text'));
        $this->assertSame([], data_get($advisory, 'bank_loans.help_items'));

        $euFunds = ServicePageTemplateRegistry::mergeTranslationPayload(
            ServicePageTemplateRegistry::EU_FUNDS,
            ['laws' => ['cards' => [['lists' => [['items_text' => '']]]]]],
            'hr'
        );

        $this->assertSame('', data_get($euFunds, 'laws.cards.0.lists.0.items_text'));
        $this->assertSame([], data_get($euFunds, 'laws.cards.0.lists.0.items'));
    }

    public function test_advisory_subpage_copy_is_saved_and_rendered_only_on_its_route(): void
    {
        config()->set('app.locale', 'hr');
        config()->set('app.fallback_locale', 'hr');

        $user = $this->makeAdminUser();
        $page = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::ADVISORY)
            ->firstOrFail();

        Livewire::withQueryParams(['section' => 'financial'])
            ->actingAs($user)
            ->test(ServiceForm::class, ['servicePageId' => $page->id])
            ->set('form.locale', 'hr')
            ->set('form.translation_payload.financial.hero_intro', 'Jedinstvena hero poruka samo za financijsko savjetovanje.')
            ->set('form.translation_payload.financial.meeting.title', 'Jedinstveni kontakt samo za financijsko savjetovanje')
            ->call('save')
            ->assertRedirect(route('admin.content.services.index', ['locale' => 'hr']));

        $this->get('/savjetovanje/financijsko-savjetovanje')
            ->assertOk()
            ->assertSee('Jedinstvena hero poruka samo za financijsko savjetovanje.')
            ->assertSee('Jedinstveni kontakt samo za financijsko savjetovanje');

        $this->get('/savjetovanje/prodaja-i-kupnja-poduzeca')
            ->assertOk()
            ->assertDontSee('Jedinstvena hero poruka samo za financijsko savjetovanje.')
            ->assertDontSee('Jedinstveni kontakt samo za financijsko savjetovanje');
    }

    public function test_advisory_subpage_seo_is_saved_per_page_without_losing_existing_payload(): void
    {
        config()->set('app.locale', 'hr');
        config()->set('app.fallback_locale', 'hr');

        $user = $this->makeAdminUser();
        $page = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::ADVISORY)
            ->firstOrFail();
        $translation = $page->translations()->where('locale', 'hr')->firstOrFail();
        $payload = (array) $translation->payload;
        data_set($payload, 'custom_extension.preserved_value', 'Ne diraj postojeći CMS payload.');
        $translation->forceFill(['payload' => $payload])->save();

        foreach ([
            'financial' => ['Financijski CMS meta naslov', 'Financijski CMS meta opis.'],
            'funding' => ['Financiranje CMS meta naslov', 'Financiranje CMS meta opis.'],
        ] as $section => [$metaTitle, $metaDescription]) {
            Livewire::withQueryParams(['locale' => 'hr', 'section' => $section])
                ->actingAs($user)
                ->test(ServiceForm::class, ['servicePageId' => $page->id])
                ->assertSet('contentSection', $section)
                ->set('form.locale', 'hr')
                ->set('form.translation_payload.'.$section.'.meta_title', $metaTitle)
                ->set('form.translation_payload.'.$section.'.meta_description', $metaDescription)
                ->call('save')
                ->assertHasNoErrors()
                ->assertRedirect(route('admin.content.services.index', ['locale' => 'hr']));
        }

        $savedPayload = (array) $translation->fresh()->payload;

        $this->assertSame('Financijski CMS meta naslov', data_get($savedPayload, 'financial.meta_title'));
        $this->assertSame('Financijski CMS meta opis.', data_get($savedPayload, 'financial.meta_description'));
        $this->assertSame('Financiranje CMS meta naslov', data_get($savedPayload, 'funding.meta_title'));
        $this->assertSame('Financiranje CMS meta opis.', data_get($savedPayload, 'funding.meta_description'));
        $this->assertSame('Ne diraj postojeći CMS payload.', data_get($savedPayload, 'custom_extension.preserved_value'));
    }

    public function test_admin_can_save_consolidated_main_advisory_content(): void
    {
        config()->set('app.locale', 'hr');
        config()->set('app.fallback_locale', 'hr');

        $user = $this->makeAdminUser();
        $page = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::ADVISORY)
            ->firstOrFail();

        Livewire::withQueryParams(['section' => 'main'])
            ->actingAs($user)
            ->test(ServiceForm::class, ['servicePageId' => $page->id])
            ->set('form.locale', 'hr')
            ->set('form.translation_payload.overview.body_html', '<p>Custom uvod Savjetovanja.</p><p><strong>Custom naglasak Savjetovanja.</strong></p>')
            ->set('form.translation_payload.pandea.body_html', '<p><em>Custom Pandea sadržaj.</em></p>')
            ->set('form.translation_payload.approach.body_html', '<p><u>Custom pristup Savjetovanju.</u></p>')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.content.services.index', ['locale' => 'hr']));

        $this->get('/savjetovanje')
            ->assertOk()
            ->assertSee('<p>Custom uvod Savjetovanja.</p>', false)
            ->assertSee('<p class="is-emphasis"><strong>Custom naglasak Savjetovanja.</strong></p>', false)
            ->assertSee('<p><em>Custom Pandea sadržaj.</em></p>', false)
            ->assertSee('<p><u>Custom pristup Savjetovanju.</u></p>', false);
    }

    public function test_audit_editor_follows_frontend_order_and_only_shows_visible_page_content(): void
    {
        $user = $this->makeAdminUser();
        $page = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::AUDIT)
            ->firstOrFail();

        Livewire::actingAs($user)
            ->test(ServiceForm::class, ['servicePageId' => $page->id])
            ->assertSet('form.template_key', ServicePageTemplateRegistry::AUDIT)
            ->assertSee('Sadržaj s fronta')
            ->assertSee('Stranica Revizija')
            ->assertSee('1. Hero i slika')
            ->assertSee('2. Zašto je revizija bitna')
            ->assertSee('3. Obveznici revizije')
            ->assertSee('4. Revizijske usluge')
            ->assertSee('5. Naš pristup')
            ->assertSee('6. Stručne objave')
            ->assertSee('7. Kontaktni poziv')
            ->assertSeeHtml('wire:model="auditHeroImageUpload"')
            ->assertSeeHtml('wire:model="form.translation_payload.hero.image_alt"')
            ->assertSeeHtml('wire:model.live.debounce.300ms="form.translation_payload.overview.body_html"')
            ->assertSeeHtml('wire:model="form.translation_payload.obligors.primary_items.2.children_text"')
            ->assertSeeHtml('wire:model.live.debounce.300ms="form.translation_payload.approach.body_html"')
            ->assertSeeHtml('wire:model="form.translation_payload.blog_section.all_posts_label"')
            ->assertSeeHtml('wire:model="form.translation_payload.meeting.status"')
            ->assertDontSeeHtml('wire:model="form.translation_payload.overview.body.0"')
            ->assertDontSeeHtml('wire:model="form.translation_payload.obligors.primary_items.2.children.0"')
            ->assertDontSeeHtml('wire:model="form.translation_payload.approach.body.0"')
            ->assertDontSeeHtml('wire:model="form.translation_payload.hero.brand_title"')
            ->assertDontSeeHtml('wire:model="form.translation_payload.hero.subtitle_accent"')
            ->assertDontSeeHtml('wire:model="form.translation_payload.hero.cta_label"')
            ->assertDontSeeHtml('wire:model="form.translation_payload.overview.highlight_title"')
            ->assertDontSeeHtml('wire:model="form.translation_payload.value.title"')
            ->assertDontSeeHtml('wire:model="form.translation_payload.approach.principles_title"')
            ->assertDontSeeHtml('wire:model="form.translation_payload.meeting.visit_title"')
            ->assertDontSeeHtml('wire:model="form.translation_payload.blog_section.intro"');
    }

    public function test_admin_can_update_audit_content_used_on_front(): void
    {
        config()->set('app.locale', 'hr');
        config()->set('app.fallback_locale', 'hr');

        $user = $this->makeAdminUser();
        $page = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::AUDIT)
            ->firstOrFail();
        $post = BlogPost::query()->create([
            'code' => 'audit-admin-content-test',
            'is_active' => true,
            'published_at' => now()->subDay(),
        ]);
        BlogPostTranslation::query()->create([
            'post_id' => $post->id,
            'locale' => 'hr',
            'title' => 'Custom testna objava Revizije',
            'slug' => 'custom-testna-objava-revizije',
            'excerpt' => 'Sažetak testne objave Revizije.',
            'body_html' => '<p>Testna objava.</p>',
        ]);

        Livewire::actingAs($user)
            ->test(ServiceForm::class, ['servicePageId' => $page->id])
            ->set('form.locale', 'hr')
            ->set('form.page_payload.blog_source.mode', 'manual')
            ->set('form.page_payload.blog_source.post_ids', [$post->id])
            ->set('form.meta_title', 'Revizija custom meta naslov')
            ->set('form.meta_description', 'Custom meta opis Revizije.')
            ->set('form.translation_payload.hero.subtitle_lead', 'Revizija custom')
            ->set('form.translation_payload.hero.intro', 'Custom hero poruka Revizije.')
            ->set('form.translation_payload.hero.image_alt', 'Custom opis hero slike Revizije')
            ->set('form.translation_payload.overview.title', 'Custom važnost revizije')
            ->set('form.translation_payload.overview.body_html', '<p>Custom uvodni odlomak Revizije.</p><p><strong>Custom istaknuti odlomak Revizije.</strong></p>')
            ->set('form.translation_payload.obligors.title', 'Custom obveznici revizije')
            ->set('form.translation_payload.obligors.primary_items.0', 'Custom prvi obveznik')
            ->set('form.translation_payload.obligors.primary_items.2.children_text', "Custom kriterij jedan\nCustom kriterij dva")
            ->set('form.translation_payload.services.title', 'Custom revizijske usluge')
            ->set('form.translation_payload.services.items.0.title', 'Custom usluga revizije')
            ->set('form.translation_payload.services.items.0.text', 'Custom opis usluge revizije.')
            ->set('form.translation_payload.approach.title', 'Custom pristup reviziji')
            ->set('form.translation_payload.approach.body_html', '<p><em>Custom tekst pristupa reviziji.</em></p>')
            ->set('form.translation_payload.blog_section.title', 'Custom stručne objave')
            ->set('form.translation_payload.blog_section.all_posts_label', 'SVE CUSTOM OBJAVE')
            ->set('form.translation_payload.blog_section.post_action_label', 'CUSTOM OPŠIRNIJE')
            ->set('form.translation_payload.meeting.title', 'Custom razgovor o reviziji')
            ->set('form.translation_payload.meeting.contact_title', 'Custom kontakt naslov')
            ->set('form.translation_payload.meeting.intro', 'Custom kontaktni tekst Revizije.')
            ->set('form.translation_payload.meeting.button_label', 'CUSTOM DOGOVOR')
            ->set('form.translation_payload.meeting.status', 'Custom status termina.')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.content.services.index', ['locale' => 'hr']));

        $this->get('/revizija')
            ->assertOk()
            ->assertSee('<title>Revizija custom meta naslov</title>', false)
            ->assertSee('<meta name="description" content="Custom meta opis Revizije.">', false)
            ->assertSee('Revizija custom')
            ->assertSee('Custom hero poruka Revizije.')
            ->assertSee('alt="Custom opis hero slike Revizije"', false)
            ->assertSee('Custom važnost revizije')
            ->assertSee('Custom uvodni odlomak Revizije.')
            ->assertSee('<p class="is-emphasis"><strong>Custom istaknuti odlomak Revizije.</strong></p>', false)
            ->assertSee('Custom obveznici revizije')
            ->assertSee('Custom prvi obveznik')
            ->assertSee('Custom kriterij jedan')
            ->assertSee('Custom kriterij dva')
            ->assertSee('Custom revizijske usluge')
            ->assertSee('Custom usluga revizije')
            ->assertSee('Custom opis usluge revizije.')
            ->assertSee('Custom pristup reviziji')
            ->assertSee('<p><em>Custom tekst pristupa reviziji.</em></p>', false)
            ->assertSee('Custom stručne objave')
            ->assertSee('SVE CUSTOM OBJAVE')
            ->assertSee('CUSTOM OPŠIRNIJE')
            ->assertSee('Custom razgovor o reviziji')
            ->assertSee('Custom kontakt naslov')
            ->assertSee('Custom kontaktni tekst Revizije.')
            ->assertSee('CUSTOM DOGOVOR')
            ->assertSee('Custom status termina.');
    }

    public function test_admin_can_replace_and_restore_the_audit_hero_image(): void
    {
        Storage::fake('public');

        $user = $this->makeAdminUser();
        $page = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::AUDIT)
            ->firstOrFail();

        Livewire::actingAs($user)
            ->test(ServiceForm::class, ['servicePageId' => $page->id])
            ->set('auditHeroImageUpload', UploadedFile::fake()->image('revizija-hero.jpg', 1920, 1080))
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.content.services.index', ['locale' => 'hr']));

        $media = $page->refresh()->getFirstMedia('service_hero_image');

        $this->assertNotNull($media);
        $this->assertFileExists($media->getPath());

        $this->get('/revizija')
            ->assertOk()
            ->assertSee($media->getUrl(), false);

        Livewire::actingAs($user)
            ->test(ServiceForm::class, ['servicePageId' => $page->id])
            ->call('removeAuditHeroImage');

        $this->assertNull($page->refresh()->getFirstMedia('service_hero_image'));
    }

    public function test_accounting_editor_follows_frontend_order_and_only_shows_visible_page_content(): void
    {
        $user = $this->makeAdminUser();
        $page = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::ACCOUNTING)
            ->firstOrFail();

        $component = Livewire::actingAs($user)
            ->test(ServiceForm::class, ['servicePageId' => $page->id])
            ->assertSet('form.template_key', ServicePageTemplateRegistry::ACCOUNTING)
            ->assertSee('Sadržaj s fronta')
            ->assertSee('Stranica Računovodstvo i porezi')
            ->assertSee('1. Hero i slika')
            ->assertSee('2. Zašto su računovodstvo i porezi bitni')
            ->assertSee('3. Partnerska poruka')
            ->assertSee('4. Računovodstvo i porezi')
            ->assertSee('5. Naš pristup')
            ->assertSee('6. Stručne objave')
            ->assertSee('7. Kontaktni poziv')
            ->assertSeeHtml('wire:model="accountingHeroImageUpload"')
            ->assertSeeHtml('wire:model="form.translation_payload.hero.image_alt"')
            ->assertSeeHtml('wire:model.live.debounce.300ms="form.translation_payload.overview.body_html"')
            ->assertSeeHtml('wire:model.live.debounce.300ms="form.translation_payload.overview.partner_body_html"')
            ->assertSeeHtml('wire:model.live.debounce.300ms="form.translation_payload.approach.body_html"')
            ->assertSeeHtml('wire:model="form.translation_payload.blog_section.all_posts_label"')
            ->assertSeeHtml('wire:model="form.translation_payload.meeting.status"')
            ->assertDontSeeHtml('wire:model="form.translation_payload.overview.body.0"')
            ->assertDontSeeHtml('wire:model="form.translation_payload.overview.body.1"')
            ->assertDontSeeHtml('wire:model="form.translation_payload.approach.body.0"')
            ->assertDontSeeHtml('wire:model="form.translation_payload.hero.brand_title"')
            ->assertDontSeeHtml('wire:model="form.translation_payload.hero.subtitle_accent"')
            ->assertDontSeeHtml('wire:model="form.translation_payload.hero.cta_label"')
            ->assertDontSeeHtml('wire:model="form.translation_payload.intro_section.title"')
            ->assertDontSeeHtml('wire:model="form.translation_payload.editorial_section.title"')
            ->assertDontSeeHtml('wire:model="form.translation_payload.detail_sections.0.title"')
            ->assertDontSeeHtml('wire:model="form.translation_payload.meeting.visit_title"')
            ->assertDontSeeHtml('wire:model="form.translation_payload.blog_section.intro"');

        $component
            ->call('setTab', 'sources')
            ->assertSeeHtml('wire:model.live="form.page_payload.blog_source.mode"')
            ->assertDontSeeHtml('wire:click="addVideoSource"')
            ->assertDontSee('Copy Sekcije Videa');
    }

    public function test_admin_can_update_accounting_content_used_on_front(): void
    {
        config()->set('app.locale', 'hr');
        config()->set('app.fallback_locale', 'hr');

        $user = $this->makeAdminUser();
        $page = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::ACCOUNTING)
            ->firstOrFail();
        $post = BlogPost::query()->create([
            'code' => 'accounting-admin-content-test',
            'is_active' => true,
            'published_at' => now()->subDay(),
        ]);
        BlogPostTranslation::query()->create([
            'post_id' => $post->id,
            'locale' => 'hr',
            'title' => 'Custom testna objava Računovodstva',
            'slug' => 'custom-testna-objava-racunovodstva',
            'excerpt' => 'Sažetak testne objave Računovodstva.',
            'body_html' => '<p>Testna objava.</p>',
        ]);

        Livewire::actingAs($user)
            ->test(ServiceForm::class, ['servicePageId' => $page->id])
            ->set('form.locale', 'hr')
            ->set('form.page_payload.blog_source.mode', 'manual')
            ->set('form.page_payload.blog_source.post_ids', [$post->id])
            ->set('form.meta_title', 'Računovodstvo custom meta naslov')
            ->set('form.meta_description', 'Custom meta opis Računovodstva.')
            ->set('form.translation_payload.hero.subtitle_lead', 'Računovodstvo custom')
            ->set('form.translation_payload.hero.intro', 'Custom hero poruka Računovodstva.')
            ->set('form.translation_payload.hero.image_alt', 'Custom opis hero slike Računovodstva')
            ->set('form.translation_payload.overview.title', 'Custom važnost računovodstva')
            ->set('form.translation_payload.overview.body_html', '<p><strong>Custom glavni odlomak Računovodstva.</strong></p>')
            ->set('form.translation_payload.overview.partner_body_html', '<p><em>Custom partnerska poruka Računovodstva.</em></p>')
            ->set('form.translation_payload.services.title', 'Custom računovodstvene usluge')
            ->set('form.translation_payload.services.items.0.title', 'Custom računovodstvena usluga')
            ->set('form.translation_payload.services.items.0.text', 'Custom opis računovodstvene usluge.')
            ->set('form.translation_payload.approach.title', 'Custom računovodstveni pristup')
            ->set('form.translation_payload.approach.body_html', '<p><u>Custom tekst pristupa Računovodstvu.</u></p>')
            ->set('form.translation_payload.blog_section.title', 'Custom stručne objave Računovodstva')
            ->set('form.translation_payload.blog_section.all_posts_label', 'SVE CUSTOM OBJAVE')
            ->set('form.translation_payload.blog_section.post_action_label', 'CUSTOM OPŠIRNIJE')
            ->set('form.translation_payload.meeting.title', 'Custom razgovor o računovodstvu')
            ->set('form.translation_payload.meeting.contact_title', 'Custom kontakt naslov')
            ->set('form.translation_payload.meeting.intro', 'Custom kontaktni tekst Računovodstva.')
            ->set('form.translation_payload.meeting.button_label', 'CUSTOM DOGOVOR')
            ->set('form.translation_payload.meeting.status', 'Custom status termina.')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.content.services.index', ['locale' => 'hr']));

        $this->get('/racunovodstvo')
            ->assertOk()
            ->assertSee('<title>Računovodstvo custom meta naslov</title>', false)
            ->assertSee('<meta name="description" content="Custom meta opis Računovodstva.">', false)
            ->assertSee('Računovodstvo custom')
            ->assertSee('Custom hero poruka Računovodstva.')
            ->assertSee('alt="Custom opis hero slike Računovodstva"', false)
            ->assertSee('Custom važnost računovodstva')
            ->assertSee('<p><strong>Custom glavni odlomak Računovodstva.</strong></p>', false)
            ->assertSee('<p class="ac-accounting-partner-note-text"><em>Custom partnerska poruka Računovodstva.</em></p>', false)
            ->assertSee('Custom računovodstvene usluge')
            ->assertSee('Custom računovodstvena usluga')
            ->assertSee('Custom opis računovodstvene usluge.')
            ->assertSee('Custom računovodstveni pristup')
            ->assertSee('<p><u>Custom tekst pristupa Računovodstvu.</u></p>', false)
            ->assertSee('Custom stručne objave Računovodstva')
            ->assertSee('SVE CUSTOM OBJAVE')
            ->assertSee('CUSTOM OPŠIRNIJE')
            ->assertSee('Custom razgovor o računovodstvu')
            ->assertSee('Custom kontakt naslov')
            ->assertSee('Custom kontaktni tekst Računovodstva.')
            ->assertSee('CUSTOM DOGOVOR')
            ->assertSee('Custom status termina.');
    }

    public function test_admin_can_replace_and_restore_the_accounting_hero_image(): void
    {
        Storage::fake('public');

        $user = $this->makeAdminUser();
        $page = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::ACCOUNTING)
            ->firstOrFail();

        Livewire::actingAs($user)
            ->test(ServiceForm::class, ['servicePageId' => $page->id])
            ->set('accountingHeroImageUpload', UploadedFile::fake()->image('racunovodstvo-hero.jpg', 1920, 1080))
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.content.services.index', ['locale' => 'hr']));

        $media = $page->refresh()->getFirstMedia('service_hero_image');

        $this->assertNotNull($media);
        $this->assertFileExists($media->getPath());

        $heroUrl = $media->hasGeneratedConversion('hero_1440x480')
            ? $media->getUrl('hero_1440x480')
            : $media->getUrl();

        $this->get('/racunovodstvo')
            ->assertOk()
            ->assertSee($heroUrl, false);

        Livewire::actingAs($user)
            ->test(ServiceForm::class, ['servicePageId' => $page->id])
            ->call('removeAccountingHeroImage');

        $this->assertNull($page->refresh()->getFirstMedia('service_hero_image'));
    }

    public function test_tax_service_page_edit_screen_hides_locked_template_and_shows_editor(): void
    {
        $user = $this->makeAdminUser();
        $page = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::TAX)
            ->firstOrFail();

        $component = Livewire::actingAs($user)
            ->test(ServiceForm::class, ['servicePageId' => $page->id]);

        $this->assertSame(ServicePageTemplateRegistry::TAX, $component->get('form.template_key'));
        $this->assertStringNotContainsString('wire:model.live="form.template_key"', $component->html());
        $this->assertStringContainsString('Navigacija poreza', $component->html());
        $this->assertStringContainsString('Blok usklađenosti', $component->html());
    }

    public function test_eu_funds_service_page_edit_screen_hides_locked_template_and_shows_editor(): void
    {
        $user = $this->makeAdminUser();
        $page = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::EU_FUNDS)
            ->firstOrFail();

        $component = Livewire::actingAs($user)
            ->test(ServiceForm::class, ['servicePageId' => $page->id]);

        $this->assertSame(ServicePageTemplateRegistry::EU_FUNDS, $component->get('form.template_key'));
        $this->assertStringNotContainsString('wire:model.live="form.template_key"', $component->html());
        $this->assertStringContainsString('Navigacija po sekcijama stranice EU fondovi', $component->html());
        $this->assertStringContainsString('Programi i instrumenti', $component->html());
        $this->assertStringContainsString('wire:model.live.debounce.300ms="form.translation_payload.overview.body_html"', $component->html());
        $this->assertStringContainsString('wire:model.live.debounce.300ms="form.translation_payload.approach.body_html"', $component->html());
        $this->assertStringContainsString('wire:model.live.debounce.300ms="form.translation_payload.resources.cards.0.body_html"', $component->html());
        $this->assertStringContainsString('wire:model="form.translation_payload.laws.cards.0.lists.0.items_text"', $component->html());
        $this->assertStringContainsString('wire:model="form.translation_payload.calls.download_link.locale"', $component->html());
        $this->assertStringContainsString('wire:model="form.translation_payload.calls.other_calls.title"', $component->html());
        $this->assertStringContainsString("addTranslationListItem('calls.other_calls.items', 'eu_funds_link_item')", $component->html());
        $this->assertStringContainsString("addTranslationListItem('resources.cards', 'eu_funds_resource_card')", $component->html());
        $this->assertStringContainsString('wire:model="form.translation_payload.laws.cards.0.secondary_link.locale"', $component->html());
        $this->assertStringNotContainsString('wire:model="form.translation_payload.overview.body.0"', $component->html());
        $this->assertStringNotContainsString('wire:model="form.translation_payload.approach.body.0"', $component->html());
        $this->assertStringNotContainsString('wire:model="form.translation_payload.testimonials.title"', $component->html());
        $this->assertStringNotContainsString('wire:model="form.translation_payload.chart.title"', $component->html());

        $component->call('setTab', 'sources');

        $this->assertStringContainsString('Auto (trenutna kategorija EU fondova)', $component->html());
    }

    public function test_admin_can_add_and_save_an_other_call_and_resource_card(): void
    {
        $user = $this->makeAdminUser();
        $page = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::EU_FUNDS)
            ->firstOrFail();
        $payload = (array) $page->translations()->where('locale', 'hr')->firstOrFail()->payload;
        $otherCallIndex = count((array) data_get($payload, 'calls.other_calls.items', []));
        $resourceCardIndex = count((array) data_get($payload, 'resources.cards', []));

        Livewire::actingAs($user)
            ->test(ServiceForm::class, ['servicePageId' => $page->id])
            ->call('addTranslationListItem', 'calls.other_calls.items', 'eu_funds_link_item')
            ->set('form.translation_payload.calls.other_calls.title', 'Ostali pozivi')
            ->set("form.translation_payload.calls.other_calls.items.$otherCallIndex.title", 'Dodatni poziv')
            ->set("form.translation_payload.calls.other_calls.items.$otherCallIndex.link.type", 'external')
            ->set("form.translation_payload.calls.other_calls.items.$otherCallIndex.link.url", '/dodatni-poziv')
            ->call('addTranslationListItem', 'resources.cards', 'eu_funds_resource_card')
            ->set("form.translation_payload.resources.cards.$resourceCardIndex.title", 'Dodatni program')
            ->call('save')
            ->assertHasNoErrors();

        $payload = (array) $page->translations()->where('locale', 'hr')->firstOrFail()->payload;
        $this->assertSame('Dodatni poziv', data_get($payload, "calls.other_calls.items.$otherCallIndex.title"));
        $this->assertSame('/dodatni-poziv', data_get($payload, "calls.other_calls.items.$otherCallIndex.link.url"));
        $this->assertSame('Dodatni program', data_get($payload, "resources.cards.$resourceCardIndex.title"));
    }

    public function test_admin_can_save_consolidated_eu_funds_content(): void
    {
        config()->set('app.locale', 'hr');
        config()->set('app.fallback_locale', 'hr');

        $user = $this->makeAdminUser();
        $page = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::EU_FUNDS)
            ->firstOrFail();

        Livewire::actingAs($user)
            ->test(ServiceForm::class, ['servicePageId' => $page->id])
            ->set('form.locale', 'hr')
            ->set('form.translation_payload.overview.body_html', '<p>Custom uvod EU fondova.</p><p><strong>Custom naglasak EU fondova.</strong></p>')
            ->set('form.translation_payload.approach.body_html', '<p><em>Custom pristup EU fondovima.</em></p>')
            ->set('form.translation_payload.resources.cards.0.body_html', '<p><u>Custom opis programa EU fondova.</u></p>')
            ->set('form.translation_payload.laws.cards.0.lists.0.items_text', "Custom zakonska stavka jedan\nCustom zakonska stavka dva")
            ->set('form.translation_payload.laws.cards.0.secondary_link.locale', 'hr')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.content.services.index', ['locale' => 'hr']));

        $this->get('/eu-fondovi')
            ->assertOk()
            ->assertSee('<p>Custom uvod EU fondova.</p>', false)
            ->assertSee('<p class="is-emphasis"><strong>Custom naglasak EU fondova.</strong></p>', false)
            ->assertSee('<p><em>Custom pristup EU fondovima.</em></p>', false)
            ->assertSee('<p><u>Custom opis programa EU fondova.</u></p>', false)
            ->assertSee('Custom zakonska stavka jedan')
            ->assertSee('Custom zakonska stavka dva');

        $savedTranslation = $page->translations()->where('locale', 'hr')->firstOrFail();
        $this->assertSame('hr', data_get($savedTranslation->payload, 'laws.cards.0.secondary_link.locale'));
    }

    public function test_eu_funds_pdf_language_must_be_an_active_cms_locale(): void
    {
        $user = $this->makeAdminUser();
        $page = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::EU_FUNDS)
            ->firstOrFail();

        Livewire::actingAs($user)
            ->test(ServiceForm::class, ['servicePageId' => $page->id])
            ->set('form.locale', 'hr')
            ->set('form.translation_payload.laws.cards.0.secondary_link.locale', 'zz')
            ->call('save')
            ->assertHasErrors(['form.translation_payload.laws.cards.0.secondary_link.locale']);
    }

    public function test_admin_can_upload_pdf_asset_for_eu_funds_service_page(): void
    {
        Storage::fake('public');

        $user = $this->makeAdminUser();
        $page = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::EU_FUNDS)
            ->firstOrFail();

        Livewire::withQueryParams(['section' => 'bank_loans'])
            ->actingAs($user)
            ->test(ServiceForm::class, ['servicePageId' => $page->id])
            ->set('form.locale', 'hr')
            ->set('form.translation_payload.calls.download_link.type', 'pdf')
            ->set('assetUploads.calls_download_link_path', UploadedFile::fake()->create('eu-fondovi.pdf', 120, 'application/pdf'))
            ->call('save')
            ->assertRedirect(route('admin.content.services.index', ['locale' => 'hr']));

        $page->refresh();
        $translation = $page->translation('hr')->first();
        $storedPath = (string) ($translation?->payload['calls']['download_link']['path'] ?? '');

        $this->assertNotSame('', $storedPath);
        $this->assertStringStartsWith('service-assets/eu-funds/', $storedPath);
        Storage::disk('public')->assertExists($storedPath);
    }

    public function test_failed_eu_funds_pdf_save_removes_only_the_new_asset(): void
    {
        Storage::fake('public');

        $user = $this->makeAdminUser();
        $page = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::EU_FUNDS)
            ->firstOrFail();
        $component = Livewire::withQueryParams(['locale' => 'hr'])
            ->actingAs($user)
            ->test(ServiceForm::class, ['servicePageId' => $page->id])
            ->set(
                'assetUploads.calls_download_link_path',
                UploadedFile::fake()->create('failed-save.pdf', 80, 'application/pdf'),
            );

        DB::partialMock()
            ->shouldReceive('transaction')
            ->once()
            ->andThrow(new \RuntimeException('Forced transaction failure after upload.'));

        try {
            $component->call('save');
            $this->fail('The forced transaction exception was not thrown.');
        } catch (\RuntimeException $exception) {
            $this->assertSame('Forced transaction failure after upload.', $exception->getMessage());
        }

        $this->assertSame([], Storage::disk('public')->allFiles('service-assets/eu-funds'));
    }

    public function test_eu_funds_pdf_replacement_preserves_shared_old_asset_then_deletes_it_when_unreferenced(): void
    {
        Storage::fake('public');

        $user = $this->makeAdminUser();
        $page = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::EU_FUNDS)
            ->firstOrFail();
        $sharedPath = 'service-assets/eu-funds/shared-reference.pdf';
        Storage::disk('public')->put($sharedPath, 'shared PDF');

        $croatian = $page->translations()->where('locale', 'hr')->firstOrFail();
        $croatianPayload = (array) $croatian->payload;
        data_set($croatianPayload, 'calls.download_link.path', $sharedPath);
        $croatian->forceFill(['payload' => $croatianPayload])->save();

        $englishPayload = $croatianPayload;
        $english = $page->translations()->updateOrCreate(['locale' => 'en'], [
            'title' => 'EU Funds',
            'slug' => 'eu-funds',
            'payload' => $englishPayload,
        ]);

        Livewire::withQueryParams(['locale' => 'hr'])
            ->actingAs($user)
            ->test(ServiceForm::class, ['servicePageId' => $page->id])
            ->set(
                'assetUploads.calls_download_link_path',
                UploadedFile::fake()->create('croatian-replacement.pdf', 80, 'application/pdf'),
            )
            ->call('save')
            ->assertHasNoErrors();

        $croatianNewPath = (string) data_get($croatian->fresh()->payload, 'calls.download_link.path');
        $this->assertNotSame($sharedPath, $croatianNewPath);
        Storage::disk('public')->assertExists($croatianNewPath);
        Storage::disk('public')->assertExists($sharedPath);

        Livewire::withQueryParams(['locale' => 'en'])
            ->actingAs($user)
            ->test(ServiceForm::class, ['servicePageId' => $page->id])
            ->set(
                'assetUploads.calls_download_link_path',
                UploadedFile::fake()->create('english-replacement.pdf', 80, 'application/pdf'),
            )
            ->call('save')
            ->assertHasNoErrors();

        $englishNewPath = (string) data_get($english->fresh()->payload, 'calls.download_link.path');
        $this->assertNotSame($sharedPath, $englishNewPath);
        Storage::disk('public')->assertExists($croatianNewPath);
        Storage::disk('public')->assertExists($englishNewPath);
        Storage::disk('public')->assertMissing($sharedPath);
    }

    public function test_admin_can_update_services_index_content_used_on_front(): void
    {
        config()->set('app.locale', 'hr');
        config()->set('app.fallback_locale', 'hr');

        $user = $this->makeAdminUser();
        $page = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::SERVICES_INDEX)
            ->firstOrFail();

        Livewire::actingAs($user)
            ->test(ServiceForm::class, ['servicePageId' => $page->id])
            ->set('form.locale', 'hr')
            ->set('form.meta_title', 'Usluge custom meta naslov')
            ->set('form.meta_description', 'Custom meta opis Usluge landing stranice.')
            ->set('form.translation_payload.showcase.title_lead', 'Sve usluge na jednom mjestu')
            ->set('form.translation_payload.showcase.intro', 'Custom uvod za pregled usluga iz admina.')
            ->set('form.translation_payload.showcase.value_cards.0.title', 'Kako stvaramo custom vrijednost')
            ->set('form.translation_payload.showcase.value_cards.0.items.0.title', 'Custom sigurnost')
            ->set('form.translation_payload.showcase.value_cards.0.items.0.text', 'Custom opis sigurnosti.')
            ->set('form.translation_payload.showcase.card_action_label', 'ISTRAŽITE USLUGU')
            ->set('form.translation_payload.primary_pillars.0.title', 'Revizija custom')
            ->set('form.translation_payload.primary_pillars.0.subtitle', 'Custom podnaslov revizije')
            ->set('form.translation_payload.primary_pillars.0.text', 'Custom tekst kartice revizije.')
            ->set('form.translation_payload.primary_pillars.0.image_alt', 'Custom opis fotografije revizije')
            ->call('save')
            ->assertRedirect(route('admin.content.services.index', ['locale' => 'hr']));

        $this->get('/usluge')
            ->assertOk()
            ->assertSee('<title>Usluge custom meta naslov</title>', false)
            ->assertSee('<meta name="description" content="Custom meta opis Usluge landing stranice.">', false)
            ->assertSee('Sve usluge na jednom mjestu')
            ->assertSee('Custom uvod za pregled usluga iz admina.')
            ->assertSee('Kako stvaramo custom vrijednost')
            ->assertSee('Custom sigurnost')
            ->assertSee('Custom opis sigurnosti.')
            ->assertSee('Revizija custom')
            ->assertSee('Custom podnaslov revizije')
            ->assertSee('Custom tekst kartice revizije.')
            ->assertSee('ISTRAŽITE USLUGU')
            ->assertSee('alt="Custom opis fotografije revizije"', false);
    }

    public function test_services_index_editor_follows_frontend_order_and_contains_every_visible_copy_field(): void
    {
        $user = $this->makeAdminUser();
        $page = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::SERVICES_INDEX)
            ->firstOrFail();

        Livewire::actingAs($user)
            ->test(ServiceForm::class, ['servicePageId' => $page->id])
            ->assertSee('Sadržaj s fronta')
            ->assertSee('1. Uvodna sekcija')
            ->assertSee('2. Stvaranje vrijednosti')
            ->assertSee('3. Kartice usluga')
            ->assertSee('Naslov')
            ->assertSee('Podnaslov')
            ->assertSee('Tekst poveznice na kartici')
            ->assertSee('Slika kartice')
            ->assertSee('Alternativni tekst slike')
            ->assertDontSeeHtml('wire:model="form.translation_payload.showcase.title_accent"')
            ->assertSeeHtml('wire:model="form.translation_payload.showcase.value_cards.0.title"')
            ->assertSeeHtml('wire:model="form.translation_payload.showcase.value_cards.0.items.0.title"')
            ->assertSeeHtml('wire:model="form.translation_payload.showcase.value_cards.0.items.0.text"')
            ->assertSeeHtml('wire:model="form.translation_payload.showcase.card_action_label"')
            ->assertSeeHtml('wire:model="form.translation_payload.primary_pillars.0.subtitle"')
            ->assertSeeHtml('wire:model="form.translation_payload.primary_pillars.0.image_alt"')
            ->assertSeeHtml('wire:model="landingImageUploads.audit"')
            ->assertDontSeeHtml('wire:model="form.translation_payload.audience.headline"')
            ->assertDontSeeHtml('wire:model="form.translation_payload.ffi.title"')
            ->assertDontSeeHtml('wire:model="form.translation_payload.advisory_approach.title"');
    }

    public function test_admin_can_replace_and_restore_a_services_index_card_image(): void
    {
        Storage::fake('public');

        $user = $this->makeAdminUser();
        $page = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::SERVICES_INDEX)
            ->firstOrFail();

        Livewire::actingAs($user)
            ->test(ServiceForm::class, ['servicePageId' => $page->id])
            ->set('landingImageUploads.audit', UploadedFile::fake()->image('revizija-custom.jpg', 1080, 1350))
            ->call('save')
            ->assertRedirect(route('admin.content.services.index', ['locale' => 'hr']));

        $page->refresh();
        $media = $page->getFirstMedia('services_index_audit_image');

        $this->assertNotNull($media);
        $this->assertFileExists($media->getPath());
        $expectedImageUrl = $media->hasGeneratedConversion('services_index_card_1080x1350')
            ? $media->getUrl('services_index_card_1080x1350')
            : $media->getUrl();

        $this->get('/usluge')
            ->assertOk()
            ->assertSee($expectedImageUrl, false);

        Livewire::actingAs($user)
            ->test(ServiceForm::class, ['servicePageId' => $page->id])
            ->call('removeServicesIndexCardImage', 'audit');

        $this->assertNull($page->refresh()->getFirstMedia('services_index_audit_image'));
    }

    public function test_admin_can_update_advisory_subpage_content_used_on_front(): void
    {
        config()->set('app.locale', 'hr');
        config()->set('app.fallback_locale', 'hr');

        $user = $this->makeAdminUser();
        $page = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::ADVISORY)
            ->firstOrFail();

        Livewire::actingAs($user)
            ->test(ServiceForm::class, ['servicePageId' => $page->id])
            ->set('form.locale', 'hr')
            ->set('form.translation_payload.bank_loans.overview_title', 'Custom bankovni krediti naslov')
            ->set('form.translation_payload.bank_loans.services_body_html', '<p><strong>Custom tekst usluge bankovnih kredita.</strong></p>')
            ->set('form.translation_payload.bank_loans.help_items_text', "custom analiza kreditne sposobnosti\ncustom priprema dokumentacije")
            ->set('form.translation_payload.bank_loans.approach_body_html', '<p><em>Custom pristup bankovnim kreditima.</em></p>')
            ->call('save')
            ->assertRedirect(route('admin.content.services.index', ['locale' => 'hr']));

        $this->get('/savjetovanje/pribavljanje-financiranja/bankovni-krediti')
            ->assertOk()
            ->assertSee('Custom bankovni krediti naslov')
            ->assertSee('<p><strong>Custom tekst usluge bankovnih kredita.</strong></p>', false)
            ->assertSee('custom analiza kreditne sposobnosti')
            ->assertSee('custom priprema dokumentacije')
            ->assertSee('<p><em>Custom pristup bankovnim kreditima.</em></p>', false);
    }

    private function makeAdminUser(): User
    {
        $user = User::factory()->create();

        Bouncer::role()->firstOrCreate(['name' => 'admin']);
        Bouncer::assign('admin')->to($user);

        return $user;
    }
}
