<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Content\Block\Form as BlockForm;
use App\Models\Catalog\Category\Category;
use App\Models\Content\ContentBlock;
use App\Models\Settings\Local\Language;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Livewire\Livewire;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Tests\TestCase;

class ContentBlocksFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_edit_form_preselects_saved_type_global_target_and_selected_items(): void
    {
        $user = $this->makeAdminUser();

        $categoryA = Category::query()->create([
            'scope' => Category::SCOPE_CATALOG,
            'code' => 'cat-a',
            'is_active' => true,
            'show_in_menu' => true,
            'sort_order' => 10,
            'payload' => null,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
        $categoryA->translations()->create([
            'scope' => Category::SCOPE_CATALOG,
            'locale' => 'en',
            'name' => 'Category A',
            'slug' => 'category-a',
        ]);

        $categoryB = Category::query()->create([
            'scope' => Category::SCOPE_CATALOG,
            'code' => 'cat-b',
            'is_active' => true,
            'show_in_menu' => true,
            'sort_order' => 20,
            'payload' => null,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
        $categoryB->translations()->create([
            'scope' => Category::SCOPE_CATALOG,
            'locale' => 'en',
            'name' => 'Category B',
            'slug' => 'category-b',
        ]);

        $block = ContentBlock::query()->create([
            'code' => 'front-shop-by-category',
            'name' => 'Front Shop by Category',
            'type' => 'categories',
            'is_active' => true,
            'payload' => null,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $block->translations()->create([
            'locale' => 'en',
            'title' => 'Shop by category',
            'subtitle' => 'Subtitle',
            'cta_label' => 'Explore collection',
            'cta_url' => '#categories',
            'payload' => null,
        ]);

        $block->slots()->create([
            'placement' => 'home.categories',
            'frontend_variant' => 'mobile',
            'target_type' => null,
            'target_ref' => null,
            'sort_order' => 0,
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $block->items()->create([
            'item_type' => 'category',
            'item_id' => $categoryA->id,
            'sort_order' => 0,
        ]);
        $block->items()->create([
            'item_type' => 'category',
            'item_id' => $categoryB->id,
            'sort_order' => 1,
        ]);

        Livewire::actingAs($user)
            ->test(BlockForm::class, ['blockId' => $block->id])
            ->assertSet('form.type', 'categories')
            ->assertSet('form.slot_placement', 'home.categories')
            ->assertSet('form.slot_frontend_variant', 'mobile')
            ->assertSet('form.slot_target_type', '')
            ->assertSet('form.slot_target_ref', '')
            ->assertSet('form.selected_item_ids', [$categoryA->id, $categoryB->id])
            ->assertSee('value="categories" selected', false)
            ->assertSee('value="mobile" selected', false)
            ->assertSee('value="" selected', false)
            ->assertSee('Category A')
            ->assertSee('Category B');
    }

    public function test_type_switch_auto_sets_surface_for_mobile_and_desktop_hero_types(): void
    {
        $user = $this->makeAdminUser();

        Livewire::actingAs($user)
            ->test(BlockForm::class)
            ->assertSet('form.slot_frontend_variant', 'all')
            ->set('form.type', 'mobile_hero_banner')
            ->assertSet('form.slot_frontend_variant', 'mobile')
            ->set('form.type', 'desktop_hero_banner')
            ->assertSet('form.slot_frontend_variant', 'desktop');
    }

    public function test_edit_form_honours_active_locale_query_before_loading_and_saving_translation(): void
    {
        $user = $this->makeAdminUser();
        Language::query()->updateOrCreate(['code' => 'en'], [
            'locale' => 'en_US',
            'name' => 'English',
            'native_name' => 'English',
            'direction' => 'ltr',
            'is_default' => false,
            'is_active' => true,
            'sort_order' => 2,
        ]);
        $block = $this->makeHomeBlock($user, 'home-locale-query-test', 'home_hero', 'home.hero');
        $block->translations()->create([
            'locale' => 'hr',
            'title' => 'Hrvatski hero naslov',
            'payload' => null,
        ]);
        $templatePath = resource_path('views/front/content-blocks/instances/home-locale-query-test.blade.php');

        try {
            Livewire::withQueryParams(['locale' => 'en'])
                ->actingAs($user)
                ->test(BlockForm::class, ['blockId' => $block->id])
                ->assertSet('form.locale', 'en')
                ->assertSet('form.title', 'English homepage content')
                ->set('form.title', 'Updated English hero title')
                ->call('save')
                ->assertHasNoErrors();

            $translations = $block->fresh()->translations->keyBy('locale');
            $this->assertSame('Updated English hero title', $translations->get('en')?->title);
            $this->assertSame('Hrvatski hero naslov', $translations->get('hr')?->title);
        } finally {
            File::delete($templatePath);
        }
    }

    public function test_blog_grid_type_loads_saved_blog_category_source_settings(): void
    {
        $user = $this->makeAdminUser();

        $blogCategory = Category::query()->create([
            'scope' => Category::SCOPE_BLOG,
            'code' => 'case-study',
            'is_active' => true,
            'show_in_menu' => true,
            'sort_order' => 10,
            'payload' => null,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
        $blogCategory->translations()->create([
            'scope' => Category::SCOPE_BLOG,
            'locale' => 'en',
            'name' => 'Case Study',
            'slug' => 'case-study',
        ]);

        $block = ContentBlock::query()->create([
            'code' => 'case-study-grid',
            'name' => 'Case Study Grid',
            'type' => 'blog_grid_3',
            'is_active' => true,
            'payload' => [
                'source' => 'query',
                'category_ids' => [$blogCategory->id],
                'sort' => 'featured',
            ],
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $block->translations()->create([
            'locale' => 'en',
            'title' => 'Latest case studies',
            'subtitle' => 'Selected from one category',
            'cta_label' => 'View all',
            'cta_url' => '/blog/case-study',
            'payload' => [
                'items_limit' => 3,
            ],
        ]);

        $block->slots()->create([
            'placement' => 'page.bottom',
            'frontend_variant' => 'all',
            'target_type' => 'page',
            'target_ref' => 'akademija',
            'sort_order' => 0,
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        Livewire::actingAs($user)
            ->test(BlockForm::class, ['blockId' => $block->id])
            ->assertSet('form.type', 'blog_grid_3')
            ->assertSet('form.blog_category_id', $blogCategory->id)
            ->assertSet('form.blog_sort', 'featured')
            ->assertSet('form.items_limit', 3)
            ->assertSet('form.slot_target_type', 'page')
            ->assertSet('form.slot_target_ref', 'akademija')
            ->assertSee(__('Sources'))
            ->call('setTab', 'sources')
            ->assertSet('activeTab', 'sources')
            ->assertSee(__('Blog Category'))
            ->assertSee('Case Study');
    }

    public function test_home_stats_editor_saves_english_locations_home_and_contact_copy(): void
    {
        $user = $this->makeAdminUser();
        $block = $this->makeHomeBlock($user, 'home-shared-copy-test', 'home_stats', 'home.stats');
        $templatePath = resource_path('views/front/content-blocks/instances/home-shared-copy-test.blade.php');

        try {
            Livewire::actingAs($user)
                ->test(BlockForm::class, ['blockId' => $block->id])
                ->set('form.locale', 'en')
                ->set('form.title', 'Homepage locations and statistics')
                ->set('form.home_stats', [
                    ['value' => '300', 'suffix' => '+', 'label' => 'Completed projects'],
                ])
                ->set('form.contact_stats', [
                    ['value' => '600', 'suffix' => '', 'label' => 'Recurring clients'],
                ])
                ->set('form.home_locations', [
                    'title' => 'Presence across 3 locations',
                    'intro_lead' => 'Zagreb, Rijeka and Vinkovci',
                    'intro_text' => 'Supporting clients across Croatia.',
                    'hero_aria_label' => 'Our locations',
                    'map_aria_label' => 'Office map',
                    'map_image_alt' => 'Map of Croatia showing our offices',
                    'map_link_label' => 'View on map',
                    'email_label' => 'Email',
                    'phone_label' => 'Phone',
                    'stats_aria_label' => 'Company statistics',
                    'region_label' => 'Croatia / 3 offices',
                    'items' => [[
                        'entity_key' => 'alpha-capitalis',
                        'city' => 'Zagreb — Headquarters',
                        'short_city' => 'Zagreb',
                        'office_label' => 'Headquarters',
                        'company' => 'Alpha CMS Company Ltd.',
                        'address' => 'R. F. Mihanovića Street 9, 10110 Zagreb',
                        'map_query' => 'R. F. Mihanovića Street 9, Zagreb',
                        'email' => 'office-en@example.test',
                        'phone' => '+385 1 555 0104',
                        'number' => '01',
                        'coordinates_label' => 'Zagreb coordinates',
                        'marker_aria_label' => 'Show Zagreb office contact details',
                    ]],
                ])
                ->assertSee('wire:model="form.home_locations.items.0.company"', false)
                ->assertSee('wire:model="form.home_locations.items.0.email"', false)
                ->assertSee('wire:model="form.home_locations.items.0.phone"', false)
                ->assertSee('wire:model="form.home_contact_page.direct_email"', false)
                ->assertSee('wire:model="form.home_contact_page.direct_phone"', false)
                ->set('form.home_contact_page', [
                    'page_title' => 'Contact us',
                    'intro' => 'We are here to support you.',
                    'form_title' => 'Send us a message',
                    'form_intro' => 'Briefly describe the nature of your enquiry.',
                    'name_label' => 'Full name',
                    'email_label' => 'Email',
                    'phone_label' => 'Phone (optional)',
                    'subject_label' => 'Subject',
                    'message_label' => 'Message',
                    'consent_label' => 'I agree with GDPR consent and personal data processing.',
                    'submit_label' => 'Send message',
                    'direct_title' => 'Direct contact',
                    'direct_body' => 'Contact us directly for initial information.',
                    'direct_email' => 'direct-en@example.test',
                    'direct_phone' => '+385 51 555 0105',
                    'direct_email_label' => 'Email',
                    'direct_phone_label' => 'Phone',
                    'direct_response_time_label' => 'Response time',
                    'direct_response_fallback' => 'Within business hours',
                    'help_title' => 'Before you send',
                    'help_body' => 'Include the topic and company name.',
                    'sent_status' => 'Thanks. Your message has been sent successfully.',
                ])
                ->call('save')
                ->assertHasNoErrors();

            $payload = $block->fresh()->translations()->where('locale', 'en')->firstOrFail()->payload;

            $this->assertSame('Completed projects', data_get($payload, 'stats.0.label'));
            $this->assertSame('Recurring clients', data_get($payload, 'contact_stats.0.label'));
            $this->assertSame('Presence across 3 locations', data_get($payload, 'locations.title'));
            $this->assertSame('Map of Croatia showing our offices', data_get($payload, 'locations.map_image_alt'));
            $this->assertSame('Alpha CMS Company Ltd.', data_get($payload, 'locations.items.0.company'));
            $this->assertSame('office-en@example.test', data_get($payload, 'locations.items.0.email'));
            $this->assertSame('+385 1 555 0104', data_get($payload, 'locations.items.0.phone'));
            $this->assertSame('R. F. Mihanovića Street 9, Zagreb', data_get($payload, 'locations.items.0.map_query'));
            $this->assertSame('Contact us', data_get($payload, 'contact_page.page_title'));
            $this->assertSame('Send us a message', data_get($payload, 'contact_page.form_title'));
            $this->assertSame('Full name', data_get($payload, 'contact_page.name_label'));
            $this->assertSame('I agree with GDPR consent and personal data processing.', data_get($payload, 'contact_page.consent_label'));
            $this->assertSame('Direct contact', data_get($payload, 'contact_page.direct_title'));
            $this->assertSame('direct-en@example.test', data_get($payload, 'contact_page.direct_email'));
            $this->assertSame('+385 51 555 0105', data_get($payload, 'contact_page.direct_phone'));
            $this->assertSame('Before you send', data_get($payload, 'contact_page.help_title'));
            $this->assertSame('Thanks. Your message has been sent successfully.', data_get($payload, 'contact_page.sent_status'));
        } finally {
            File::delete($templatePath);
        }
    }

    public function test_home_stats_editor_round_trips_persisted_office_and_contact_emails_per_locale(): void
    {
        $user = $this->makeAdminUser();
        foreach ([
            'hr' => ['locale' => 'hr_HR', 'name' => 'Croatian', 'native_name' => 'Hrvatski', 'is_default' => true, 'sort_order' => 1],
            'en' => ['locale' => 'en_US', 'name' => 'English', 'native_name' => 'English', 'is_default' => false, 'sort_order' => 2],
        ] as $code => $language) {
            Language::query()->updateOrCreate(['code' => $code], $language + [
                'direction' => 'ltr',
                'is_active' => true,
            ]);
        }

        $block = $this->makeHomeBlock($user, 'home-office-email-round-trip-test', 'home_stats', 'home.stats');
        $templatePath = resource_path('views/front/content-blocks/instances/home-office-email-round-trip-test.blade.php');

        try {
            foreach (['hr', 'en'] as $locale) {
                $payload = [
                    'locations' => [
                        'items' => [
                            ['city' => strtoupper($locale).' office 1', 'email' => "office-1-{$locale}@example.test"],
                            ['city' => strtoupper($locale).' office 2', 'email' => "office-2-{$locale}@example.test"],
                            ['city' => strtoupper($locale).' office 3', 'email' => "office-3-{$locale}@example.test"],
                        ],
                    ],
                    'contact_page' => [
                        'direct_email' => "direct-{$locale}@example.test",
                    ],
                ];

                $block->translations()->updateOrCreate(
                    ['locale' => $locale],
                    ['title' => strtoupper($locale).' homepage content', 'payload' => $payload]
                );
            }

            foreach (['hr', 'en'] as $locale) {
                $component = Livewire::withQueryParams(['locale' => $locale])
                    ->actingAs($user)
                    ->test(BlockForm::class, ['blockId' => $block->id])
                    ->assertSet('form.locale', $locale)
                    ->assertSet('form.home_locations.items.0.email', "office-1-{$locale}@example.test")
                    ->assertSet('form.home_locations.items.1.email', "office-2-{$locale}@example.test")
                    ->assertSet('form.home_locations.items.2.email', "office-3-{$locale}@example.test")
                    ->assertSet('form.home_contact_page.direct_email', "direct-{$locale}@example.test");

                $html = $component->html();
                foreach ([
                    'form.home_locations.items.0.email' => "office-1-{$locale}@example.test",
                    'form.home_locations.items.1.email' => "office-2-{$locale}@example.test",
                    'form.home_locations.items.2.email' => "office-3-{$locale}@example.test",
                    'form.home_contact_page.direct_email' => "direct-{$locale}@example.test",
                ] as $binding => $value) {
                    $this->assertMatchesRegularExpression(
                        '~<input\b(?=[^>]*\bwire:model="'.preg_quote($binding, '~').'"\s)'
                        .'(?=[^>]*\bvalue="'.preg_quote($value, '~').'")[^>]*>~',
                        $html
                    );
                }

                $component->call('save')->assertHasNoErrors();
            }

            $translations = $block->fresh()->translations->keyBy('locale');
            foreach (['hr', 'en'] as $locale) {
                $payload = $translations->get($locale)?->payload;

                $this->assertSame("office-1-{$locale}@example.test", data_get($payload, 'locations.items.0.email'));
                $this->assertSame("office-2-{$locale}@example.test", data_get($payload, 'locations.items.1.email'));
                $this->assertSame("office-3-{$locale}@example.test", data_get($payload, 'locations.items.2.email'));
                $this->assertSame("direct-{$locale}@example.test", data_get($payload, 'contact_page.direct_email'));
            }
        } finally {
            File::delete($templatePath);
        }
    }

    public function test_home_services_editor_saves_english_values_process_news_and_cta_copy(): void
    {
        $user = $this->makeAdminUser();
        $block = $this->makeHomeBlock($user, 'home-services-copy-test', 'home_services', 'home.services');
        $templatePath = resource_path('views/front/content-blocks/instances/home-services-copy-test.blade.php');

        try {
            Livewire::actingAs($user)
                ->test(BlockForm::class, ['blockId' => $block->id])
                ->set('form.locale', 'en')
                ->set('form.title', 'You run your business.')
                ->set('form.home_services', [[
                    'key' => 'audit',
                    'title' => 'Audit',
                    'subtitle' => 'Confidence in every decision.',
                    'text' => 'Independent review of financial statements.',
                    'bullets_text' => '',
                    'image_alt' => 'Professionals reviewing financial statements',
                    'url' => '/revizija',
                    'action_label' => 'Learn more',
                ]])
                ->set('form.home_values', [
                    'title' => 'Creating value at every stage of growth.',
                    'intro' => 'Security in your business and clarity in your finances.',
                    'items' => [['title' => 'Expertise and experience', 'text' => 'Years of experience.']],
                ])
                ->set('form.home_process', [
                    'title' => 'A clear process.',
                    'items' => [['title' => 'Getting to know your business', 'text' => 'We take time to understand it.']],
                ])
                ->set('form.home_news', [
                    'title' => 'Deadlines, news and advice.',
                    'all_posts_label' => 'View all posts',
                    'all_posts_url' => '/blog',
                    'post_action_label' => 'Read more',
                    'category_fallback' => 'News',
                    'excerpt_fallback' => 'Read our latest update.',
                ])
                ->set('form.home_contact_cta', [
                    'title' => 'Let us talk about your next stage.',
                    'card_title' => 'Take the right next step.',
                    'text' => 'Schedule an introductory meeting.',
                    'button_label' => 'Schedule a meeting',
                    'button_url' => '/contact',
                    'status' => 'We adapt the meeting time to you.',
                ])
                ->call('save')
                ->assertHasNoErrors();

            $payload = $block->fresh()->translations()->where('locale', 'en')->firstOrFail()->payload;

            $this->assertSame('audit', data_get($payload, 'services.0.key'));
            $this->assertSame('Professionals reviewing financial statements', data_get($payload, 'services.0.image_alt'));
            $this->assertSame('Expertise and experience', data_get($payload, 'values.items.0.title'));
            $this->assertSame('Getting to know your business', data_get($payload, 'process.items.0.title'));
            $this->assertSame('View all posts', data_get($payload, 'news.all_posts_label'));
            $this->assertSame('Schedule a meeting', data_get($payload, 'contact_cta.button_label'));
        } finally {
            File::delete($templatePath);
        }
    }

    private function makeHomeBlock(User $user, string $code, string $type, string $placement): ContentBlock
    {
        $block = ContentBlock::query()->create([
            'code' => $code,
            'name' => $code,
            'type' => $type,
            'is_active' => true,
            'payload' => null,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
        $block->translations()->create([
            'locale' => 'en',
            'title' => 'English homepage content',
            'payload' => null,
        ]);
        $block->slots()->create([
            'placement' => $placement,
            'frontend_variant' => 'all',
            'sort_order' => 0,
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        return $block;
    }

    private function makeAdminUser(): User
    {
        $user = User::factory()->create();

        Bouncer::role()->firstOrCreate(['name' => 'admin']);
        Bouncer::assign('admin')->to($user);

        return $user;
    }
}
