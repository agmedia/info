<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Content\Blog\Form as BlogForm;
use App\Livewire\Admin\Content\Page\Form as PageForm;
use App\Models\Content\Blog\BlogPost;
use App\Models\Content\Blog\BlogPostTranslation;
use App\Models\Content\Page\InfoPage;
use App\Models\User;
use App\Services\Settings\SystemSettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
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

    private function makeAdminUser(): User
    {
        $user = User::factory()->create();

        Bouncer::role()->firstOrCreate(['name' => 'admin']);
        Bouncer::assign('admin')->to($user);

        return $user;
    }
}
