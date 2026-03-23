<?php

namespace Tests\Feature\Admin;

use App\Models\Catalog\Category\Category;
use App\Livewire\Admin\Content\Blog\Form as BlogForm;
use App\Livewire\Admin\Content\Page\Form as PageForm;
use App\Models\Content\Blog\BlogPost;
use App\Models\Content\Blog\BlogPostTranslation;
use App\Models\Content\Page\InfoPage;
use App\Models\Content\Resource\ResourceDocument;
use App\Models\Content\Resource\ResourceDocumentTranslation;
use App\Models\User;
use App\Services\Settings\SystemSettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Tests\TestCase;

class ContentBlogPagesFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_blog_routes_are_available_in_admin_content_area(): void
    {
        $user = $this->makeAdminUser();

        $this->actingAs($user)
            ->get('/admin/content/blog')
            ->assertOk();
    }

    public function test_admin_can_create_blog_post(): void
    {
        app(SystemSettingsService::class)->put('catalog_use_blog', true);

        $user = $this->makeAdminUser();

        Livewire::actingAs($user)
            ->test(BlogForm::class)
            ->set('form.is_active', true)
            ->set('form.is_featured', true)
            ->set('form.locale', 'en')
            ->set('form.title', 'First Blog Post')
            ->set('form.code', 'blog-post-1')
            ->set('form.slug', 'first-blog-post')
            ->call('save')
            ->assertRedirect(route('admin.content.blog.index', ['locale' => 'en']));

        $post = BlogPost::query()->where('code', 'blog-post-1')->first();

        $this->assertNotNull($post);
        $this->assertTrue((bool) $post->is_featured);
        $this->assertSame('First Blog Post', (string) $post->translation('en')->first()?->title);
    }

    public function test_admin_can_create_info_page(): void
    {
        $user = $this->makeAdminUser();

        Livewire::actingAs($user)
            ->test(PageForm::class)
            ->set('form.code', 'shipping-info')
            ->set('form.layout', 'default')
            ->set('form.is_active', true)
            ->set('form.locale', 'en')
            ->set('form.title', 'Shipping Info')
            ->set('form.slug', 'shipping-info')
            ->call('save')
            ->assertRedirect(route('admin.content.pages.index', ['locale' => 'en']));

        $page = InfoPage::query()->where('code', 'shipping-info')->first();

        $this->assertNotNull($page);
        $this->assertTrue((bool) $page->is_active);
        $this->assertSame('Shipping Info', (string) $page->translation('en')->first()?->title);
    }

    public function test_admin_can_save_academy_blog_source_settings_on_info_page(): void
    {
        $user = $this->makeAdminUser();

        $blogCategory = Category::query()->create([
            'scope' => Category::SCOPE_BLOG,
            'code' => 'case-study',
            'is_active' => true,
            'show_in_menu' => true,
            'sort_order' => 10,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
        $blogCategory->translations()->create([
            'scope' => Category::SCOPE_BLOG,
            'locale' => 'en',
            'name' => 'Case Study',
            'slug' => 'case-study',
        ]);

        Livewire::actingAs($user)
            ->test(PageForm::class)
            ->set('form.code', 'academy-page')
            ->set('form.layout', 'academy')
            ->set('form.is_active', true)
            ->set('form.locale', 'en')
            ->set('form.title', 'Academy')
            ->set('form.slug', 'academy')
            ->call('setTab', 'sources')
            ->assertSet('activeTab', 'sources')
            ->set('form.academy_blog_category_id', $blogCategory->id)
            ->set('form.academy_blog_limit', 3)
            ->set('form.academy_blog_title', 'Latest Case Studies')
            ->set('form.academy_blog_intro', 'Selected from the Case Study category.')
            ->call('save')
            ->assertRedirect(route('admin.content.pages.index', ['locale' => 'en']));

        $page = InfoPage::query()->where('code', 'academy-page')->first();

        $this->assertNotNull($page);
        $this->assertSame('academy', $page->layout);
        $this->assertSame([
            'mode' => 'category',
            'category_id' => $blogCategory->id,
            'limit' => 3,
        ], $page->payload['blog_source'] ?? null);
        $this->assertSame('Latest Case Studies', (string) data_get($page->translation('en')->first()?->payload, 'academy_blog_section.title'));
        $this->assertSame('Selected from the Case Study category.', (string) data_get($page->translation('en')->first()?->payload, 'academy_blog_section.intro'));
    }

    public function test_admin_can_save_academy_download_documents_source_settings_on_info_page(): void
    {
        $user = $this->makeAdminUser();

        $firstDocument = ResourceDocument::query()->create([
            'code' => 'academy-doc-1',
            'group_code' => 'downloads',
            'is_active' => true,
            'sort_order' => 1,
            'download_url' => 'https://example.test/files/academy-doc-1.pdf',
        ]);
        ResourceDocumentTranslation::query()->create([
            'document_id' => $firstDocument->id,
            'locale' => 'en',
            'title' => 'Academy Document 1',
            'slug' => 'academy-document-1',
        ]);

        $secondDocument = ResourceDocument::query()->create([
            'code' => 'academy-doc-2',
            'group_code' => 'transaction-analysis',
            'is_active' => true,
            'sort_order' => 2,
            'download_url' => 'https://example.test/files/academy-doc-2.pdf',
        ]);
        ResourceDocumentTranslation::query()->create([
            'document_id' => $secondDocument->id,
            'locale' => 'en',
            'title' => 'Academy Document 2',
            'slug' => 'academy-document-2',
        ]);

        Livewire::actingAs($user)
            ->test(PageForm::class)
            ->set('form.code', 'academy-page')
            ->set('form.layout', 'academy')
            ->set('form.is_active', true)
            ->set('form.locale', 'en')
            ->set('form.title', 'Academy')
            ->set('form.slug', 'academy')
            ->call('setTab', 'sources')
            ->assertSet('activeTab', 'sources')
            ->set('form.academy_resource_document_ids', [$secondDocument->id, $firstDocument->id])
            ->set('form.academy_resource_title', 'Download Documents')
            ->set('form.academy_resource_intro', 'Selected documents for the academy page.')
            ->call('save')
            ->assertRedirect(route('admin.content.pages.index', ['locale' => 'en']));

        $page = InfoPage::query()->where('code', 'academy-page')->first();

        $this->assertNotNull($page);
        $this->assertSame([
            'mode' => 'manual',
            'document_ids' => [$secondDocument->id, $firstDocument->id],
        ], $page->payload['resource_source'] ?? null);
        $this->assertSame('Download Documents', (string) data_get($page->translation('en')->first()?->payload, 'academy_resource_section.title'));
        $this->assertSame('Selected documents for the academy page.', (string) data_get($page->translation('en')->first()?->payload, 'academy_resource_section.intro'));
    }

    public function test_admin_can_save_academy_video_source_settings_on_info_page(): void
    {
        $user = $this->makeAdminUser();

        Livewire::actingAs($user)
            ->test(PageForm::class)
            ->set('form.code', 'academy-page')
            ->set('form.layout', 'academy')
            ->set('form.is_active', true)
            ->set('form.locale', 'en')
            ->set('form.title', 'Academy')
            ->set('form.slug', 'academy')
            ->call('setTab', 'sources')
            ->assertSet('activeTab', 'sources')
            ->set('form.academy_video_items', [
                [
                    'title' => 'Intro to Finance',
                    'youtube_url' => 'https://youtu.be/GivT5NzdO1c',
                ],
                [
                    'title' => 'Business Planning',
                    'youtube_url' => 'https://www.youtube.com/watch?v=VA7LlrHMsiM',
                ],
            ])
            ->set('form.academy_video_title', 'Online education and personalized training')
            ->set('form.academy_video_intro', 'Curated YouTube videos for the academy page.')
            ->call('save')
            ->assertRedirect(route('admin.content.pages.index', ['locale' => 'en']));

        $page = InfoPage::query()->where('code', 'academy-page')->first();

        $this->assertNotNull($page);
        $this->assertSame([
            'mode' => 'manual',
            'items' => [
                [
                    'title' => 'Intro to Finance',
                    'youtube_url' => 'https://www.youtube.com/watch?v=GivT5NzdO1c',
                ],
                [
                    'title' => 'Business Planning',
                    'youtube_url' => 'https://www.youtube.com/watch?v=VA7LlrHMsiM',
                ],
            ],
        ], $page->payload['video_source'] ?? null);
        $this->assertSame('Online education and personalized training', (string) data_get($page->translation('en')->first()?->payload, 'academy_video_section.title'));
        $this->assertSame('Curated YouTube videos for the academy page.', (string) data_get($page->translation('en')->first()?->payload, 'academy_video_section.intro'));
    }

    public function test_admin_can_save_academy_program_copy_on_info_page(): void
    {
        $user = $this->makeAdminUser();

        Livewire::actingAs($user)
            ->test(PageForm::class)
            ->set('form.code', 'academy-page')
            ->set('form.layout', 'academy')
            ->set('form.is_active', true)
            ->set('form.locale', 'en')
            ->set('form.title', 'Academy')
            ->set('form.slug', 'academy')
            ->set('form.academy_programs.0.title', 'SME Finance Lab')
            ->set('form.academy_programs.0.intro', 'Custom intro for the first academy card.')
            ->set('form.academy_programs.0.items.0.title', 'Capital planning')
            ->set('form.academy_programs.0.items.0.text', 'Custom editable copy for the first inner box.')
            ->set('form.academy_programs.3.title', 'Tax Masterclasses')
            ->set('form.academy_programs.3.items.1.title', 'Tax audit readiness')
            ->call('save')
            ->assertRedirect(route('admin.content.pages.index', ['locale' => 'en']));

        $page = InfoPage::query()->where('code', 'academy-page')->first();

        $this->assertNotNull($page);
        $this->assertSame('SME Finance Lab', (string) data_get($page->translation('en')->first()?->payload, 'academy_programs.0.title'));
        $this->assertSame('Custom intro for the first academy card.', (string) data_get($page->translation('en')->first()?->payload, 'academy_programs.0.intro'));
        $this->assertSame('Capital planning', (string) data_get($page->translation('en')->first()?->payload, 'academy_programs.0.items.0.title'));
        $this->assertSame('Custom editable copy for the first inner box.', (string) data_get($page->translation('en')->first()?->payload, 'academy_programs.0.items.0.text'));
        $this->assertSame('Tax Masterclasses', (string) data_get($page->translation('en')->first()?->payload, 'academy_programs.3.title'));
        $this->assertSame('Tax audit readiness', (string) data_get($page->translation('en')->first()?->payload, 'academy_programs.3.items.1.title'));
    }

    public function test_admin_cannot_use_reserved_clean_slug_for_info_page(): void
    {
        $user = $this->makeAdminUser();

        Livewire::actingAs($user)
            ->test(PageForm::class)
            ->set('form.code', 'blog-landing')
            ->set('form.layout', 'default')
            ->set('form.is_active', true)
            ->set('form.locale', 'en')
            ->set('form.title', 'Blog Landing')
            ->set('form.slug', 'blog')
            ->call('save')
            ->assertHasErrors(['form.slug' => 'not_in']);

        $this->assertNull(InfoPage::query()->where('code', 'blog-landing')->first());
    }

    public function test_blog_manager_renders_cover_preview_when_post_has_image(): void
    {
        Storage::fake('public');
        config()->set('media-library.disk_name', 'public');
        config()->set('media-library.queue_conversions_by_default', false);

        app(SystemSettingsService::class)->put('catalog_use_blog', true);

        $user = $this->makeAdminUser();

        $post = BlogPost::query()->create([
            'code' => 'cover-preview-post',
            'is_active' => true,
            'is_featured' => false,
            'sort_order' => 0,
        ]);

        BlogPostTranslation::query()->create([
            'post_id' => $post->id,
            'locale' => 'en',
            'title' => 'Cover Preview',
            'slug' => 'cover-preview',
        ]);

        $file = UploadedFile::fake()->image('cover-preview.jpg', 1200, 800);
        $post->addMedia($file->getPathname())
            ->usingName('Cover Preview')
            ->usingFileName('cover-preview.jpg')
            ->toMediaCollection('blog_cover');

        $this->actingAs($user)
            ->get('/admin/content/blog?locale=en')
            ->assertOk()
            ->assertSee('<img src="', false)
            ->assertSee('Cover Preview');
    }

    public function test_admin_can_upload_blog_editor_image(): void
    {
        Storage::fake('public');

        $user = $this->makeAdminUser();

        $response = $this->actingAs($user)->post(route('admin.content.blog.editor-image.upload'), [
            'image' => UploadedFile::fake()->image('inline-hero.png', 960, 540),
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('ok', true);

        $path = (string) $response->json('path');
        $url = (string) $response->json('url');

        $this->assertNotSame('', $path);
        $this->assertStringStartsWith('blog/editor-images/', $path);
        $this->assertSame(Storage::disk('public')->url($path), $url);
        Storage::disk('public')->assertExists($path);
    }

    public function test_front_blog_show_hides_gallery_items_already_embedded_in_article_body(): void
    {
        config()->set('app.locale', 'en');
        config()->set('app.fallback_locale', 'en');
        Storage::fake('public');
        config()->set('media-library.disk_name', 'public');
        config()->set('media-library.queue_conversions_by_default', false);

        $post = BlogPost::query()->create([
            'code' => 'gallery-filter-post',
            'is_active' => true,
            'is_featured' => false,
            'published_at' => now()->subDay(),
            'sort_order' => 0,
        ]);

        $inlineGalleryFile = UploadedFile::fake()->image('inline-gallery.jpg', 1200, 800);
        $inlineGalleryMedia = $post->addMedia($inlineGalleryFile->getPathname())
            ->usingName('Inline Gallery')
            ->usingFileName('inline-gallery.jpg')
            ->toMediaCollection('blog_gallery');

        $extraGalleryFile = UploadedFile::fake()->image('extra-gallery.jpg', 1200, 800);
        $extraGalleryMedia = $post->addMedia($extraGalleryFile->getPathname())
            ->usingName('Extra Gallery')
            ->usingFileName('extra-gallery.jpg')
            ->toMediaCollection('blog_gallery');

        BlogPostTranslation::query()->create([
            'post_id' => $post->id,
            'locale' => 'en',
            'title' => 'Gallery Filter Post',
            'slug' => 'gallery-filter-post',
            'body_html' => sprintf('<p>Intro text.</p><p><img src="%s" alt="Inline Gallery"></p>', $inlineGalleryMedia->getUrl()),
        ]);

        $response = $this->get(route('blog.show', ['slug' => 'gallery-filter-post']));

        $response->assertOk();

        $content = $response->getContent();

        $this->assertSame(1, substr_count($content, $inlineGalleryMedia->getUrl()));
        $this->assertSame(2, substr_count($content, $extraGalleryMedia->getUrl()));
    }

    public function test_wordpress_blog_import_command_imports_limited_posts_into_single_category(): void
    {
        config()->set('app.locale', 'hr');
        config()->set('app.fallback_locale', 'hr');
        Storage::fake('public');
        config()->set('media-library.disk_name', 'public');
        config()->set('media-library.queue_conversions_by_default', false);

        $cover = UploadedFile::fake()->image('wordpress-cover.jpg', 1200, 800);
        $coverBytes = file_get_contents($cover->getPathname());
        Http::fake([
            'https://alphacapitalis.com/wp-content/uploads/*' => Http::response($coverBytes, 200, ['Content-Type' => 'image/jpeg']),
        ]);

        $this->artisan('content:import-wordpress-blog', [
            'file' => base_path('public/assets/xml-import/alphacapitalis.WordPress.2026-03-20.xml'),
            '--limit' => 3,
            '--locale' => 'hr',
            '--category-mode' => 'single',
            '--category-name' => 'Novosti',
            '--category-slug' => 'novosti',
        ])->assertExitCode(0);

        $this->assertSame(3, BlogPost::query()->count());

        $category = Category::query()
            ->where('scope', Category::SCOPE_BLOG)
            ->where('code', 'novosti')
            ->first();

        $this->assertNotNull($category);
        $this->assertSame('Novosti', $category->translations()->where('locale', 'hr')->value('name'));

        $post = BlogPost::query()->where('code', 'wordpress-post-18769')->first();
        $this->assertNotNull($post);
        $this->assertTrue($post->categories->contains(fn (Category $row): bool => (int) $row->id === (int) $category->id));

        $translation = $post->translations()->where('locale', 'hr')->first();
        $this->assertNotNull($translation);
        $this->assertSame(
            '/2019/05/01/drustvo-alpha-capitalis-uvrsteno-na-popis-savjetnika-kod-ebrd-a',
            $translation->payload['legacy_path'] ?? null
        );
        $this->assertNotNull($post->getFirstMedia('blog_cover'));

        $briefPost = BlogPost::query()->where('code', 'wordpress-post-18771')->first();
        $this->assertNotNull($briefPost);
        $briefTranslation = $briefPost->translations()->where('locale', 'hr')->first();
        $this->assertNotNull($briefTranslation);
        $this->assertStringNotContainsString('[icon', (string) $briefTranslation->body_html);

        $formattedPost = BlogPost::query()->where('code', 'wordpress-post-18773')->first();
        $this->assertNotNull($formattedPost);
        $formattedTranslation = $formattedPost->translations()->where('locale', 'hr')->first();
        $this->assertNotNull($formattedTranslation);
        $this->assertStringContainsString('<p><strong>Naziv projekta:</strong>', (string) $formattedTranslation->body_html);
        $this->assertStringContainsString('<figure>', (string) $formattedTranslation->body_html);
        $this->assertCount(3, $formattedPost->getMedia('blog_gallery'));
    }

    public function test_legacy_wordpress_blog_url_redirects_to_canonical_blog_url(): void
    {
        config()->set('app.locale', 'hr');
        config()->set('app.fallback_locale', 'hr');

        $post = BlogPost::query()->create([
            'code' => 'wordpress-post-999',
            'is_active' => true,
            'is_featured' => false,
            'published_at' => now()->subDay(),
            'sort_order' => 0,
        ]);

        BlogPostTranslation::query()->create([
            'post_id' => $post->id,
            'locale' => 'hr',
            'title' => 'Legacy SEO Post',
            'slug' => 'legacy-seo-post',
            'excerpt' => 'Legacy excerpt',
            'payload' => [
                'legacy_path' => '/2019/05/01/legacy-seo-post',
            ],
        ]);

        $this->get('/2019/05/01/legacy-seo-post')
            ->assertRedirect(route('blog.show', ['slug' => 'legacy-seo-post']));
    }

    public function test_wordpress_import_removes_duplicate_lead_image_and_sanitizes_tables(): void
    {
        config()->set('app.locale', 'hr');
        config()->set('app.fallback_locale', 'hr');
        Storage::fake('public');
        config()->set('media-library.disk_name', 'public');
        config()->set('media-library.queue_conversions_by_default', false);

        $cover = UploadedFile::fake()->image('wordpress-cover.jpg', 1200, 800);
        $coverBytes = file_get_contents($cover->getPathname());
        Http::fake([
            'https://alphacapitalis.com/wp-content/uploads/*' => Http::response($coverBytes, 200, ['Content-Type' => 'image/jpeg']),
        ]);

        $this->artisan('content:import-wordpress-blog', [
            'file' => base_path('public/assets/xml-import/alphacapitalis.WordPress.2026-03-20.xml'),
            '--locale' => 'hr',
            '--category-mode' => 'single',
            '--category-name' => 'Novosti',
            '--category-slug' => 'novosti',
            '--slugs' => [
                'objavljen-poziv-proizvodnja-elektricne-energije-iz-obnovljivih-izvora-u-preradivackoj-industriji-i-toplinarstvu-referentni-broj-mf-2023-1-1',
            ],
        ])->assertExitCode(0);

        $post = BlogPost::query()->where('code', 'wordpress-post-30444')->first();
        $this->assertNotNull($post);

        $translation = $post->translations()->where('locale', 'hr')->first();
        $this->assertNotNull($translation);

        $bodyHtml = (string) $translation->body_html;

        $this->assertStringStartsWith('<p>', ltrim($bodyHtml));
        $this->assertStringNotContainsString('<figure>', $bodyHtml);
        $this->assertStringContainsString('<table>', $bodyHtml);
        $this->assertStringNotContainsString('style=', $bodyHtml);
        $this->assertStringNotContainsString('<td><p', $bodyHtml);
        $this->assertStringContainsString('data-align="center"', $bodyHtml);
        $this->assertStringContainsString('<li>Ušteda energije</li>', $bodyHtml);
        $this->assertCount(0, $post->getMedia('blog_gallery'));

        $dom = new DOMDocument('1.0', 'UTF-8');
        @$dom->loadHTML('<?xml encoding="utf-8" ?><div>'.$bodyHtml.'</div>', LIBXML_HTML_NODEFDTD | LIBXML_NONET);
        $xpath = new DOMXPath($dom);

        foreach ($xpath->query('//li') as $listItem) {
            $this->assertSame(0, $xpath->query('.//br', $listItem)->length);
        }
    }

    public function test_wordpress_import_removes_wordpress_emoji_svg_images_from_body(): void
    {
        config()->set('app.locale', 'hr');
        config()->set('app.fallback_locale', 'hr');
        Storage::fake('public');
        config()->set('media-library.disk_name', 'public');
        config()->set('media-library.queue_conversions_by_default', false);

        $cover = UploadedFile::fake()->image('wordpress-cover.jpg', 1200, 800);
        $coverBytes = file_get_contents($cover->getPathname());
        Http::fake([
            'https://alphacapitalis.com/wp-content/uploads/*' => Http::response($coverBytes, 200, ['Content-Type' => 'image/jpeg']),
        ]);

        $this->artisan('content:import-wordpress-blog', [
            'file' => base_path('public/assets/xml-import/alphacapitalis.WordPress.2026-03-20.xml'),
            '--locale' => 'hr',
            '--category-mode' => 'single',
            '--category-name' => 'Novosti',
            '--category-slug' => 'novosti',
            '--slugs' => [
                'alpha-capitalis-postaje-clan-tag-alliances-a',
            ],
        ])->assertExitCode(0);

        $post = BlogPost::query()->where('code', 'wordpress-post-38021')->first();
        $this->assertNotNull($post);

        $translation = $post->translations()->where('locale', 'hr')->first();
        $this->assertNotNull($translation);

        $bodyHtml = (string) $translation->body_html;

        $this->assertStringNotContainsString('s.w.org/images/core/emoji', $bodyHtml);
        $this->assertStringNotContainsString('1f449.svg', $bodyHtml);
        $this->assertStringContainsString('Više o TAG Alliances-u saznajte na službenoj stranici:', $bodyHtml);
        $this->assertCount(0, $post->getMedia('blog_gallery'));
    }

    public function test_wordpress_import_removes_leading_body_image_but_keeps_cover_image(): void
    {
        config()->set('app.locale', 'hr');
        config()->set('app.fallback_locale', 'hr');
        Storage::fake('public');
        config()->set('media-library.disk_name', 'public');
        config()->set('media-library.queue_conversions_by_default', false);

        $cover = UploadedFile::fake()->image('wordpress-cover.jpg', 1200, 800);
        $coverBytes = file_get_contents($cover->getPathname());
        Http::fake([
            'https://alphacapitalis.com/wp-content/uploads/*' => Http::response($coverBytes, 200, ['Content-Type' => 'image/jpeg']),
        ]);

        $this->artisan('content:import-wordpress-blog', [
            'file' => base_path('public/assets/xml-import/alphacapitalis.WordPress.2026-03-20.xml'),
            '--locale' => 'hr',
            '--category-mode' => 'single',
            '--category-name' => 'Novosti',
            '--category-slug' => 'novosti',
            '--slugs' => [
                'otvoren-natjecaj-stipendije-za-studente',
            ],
        ])->assertExitCode(0);

        $post = BlogPost::query()->where('code', 'wordpress-post-38043')->first();
        $this->assertNotNull($post);
        $this->assertNotNull($post->getFirstMedia('blog_cover'));
        $this->assertCount(0, $post->getMedia('blog_gallery'));

        $translation = $post->translations()->where('locale', 'hr')->first();
        $this->assertNotNull($translation);

        $bodyHtml = (string) $translation->body_html;

        $this->assertStringStartsWith('<p><strong>Natječaj za dodjelu stipendija studentima u akademskoj godini 2025./2026.</strong></p>', ltrim($bodyHtml));
        $this->assertStringNotContainsString('<figure>', $bodyHtml);
        $this->assertStringNotContainsString('<img', $bodyHtml);
        $this->assertStringContainsString('Za prijavu na natječaj potrebno je priložiti sljedeće dokumente:', $bodyHtml);
    }

    private function makeAdminUser(): User
    {
        $user = User::factory()->create();

        Bouncer::role()->firstOrCreate(['name' => 'admin']);
        Bouncer::assign('admin')->to($user);

        return $user;
    }
}
