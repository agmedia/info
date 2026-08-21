<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Catalog\Category\Form as CategoryForm;
use App\Livewire\Admin\Content\Blog\Form as BlogForm;
use App\Livewire\Admin\Content\Call\Form as CallForm;
use App\Livewire\Admin\Content\Page\Form as PageForm;
use App\Livewire\Admin\Content\Service\Form as ServiceForm;
use App\Models\Catalog\Category\Category;
use App\Models\Content\Blog\BlogPost;
use App\Models\Content\Page\InfoPage;
use App\Models\Content\Service\ServicePage;
use App\Models\User;
use App\Services\Settings\SystemSettingsService;
use App\Support\Content\ServicePageTemplateRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Tests\TestCase;

class AdminContentWorkflowFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_editor_can_create_category_and_blog_post_that_match_the_frontend(): void
    {
        config()->set('app.locale', 'hr');
        config()->set('app.fallback_locale', 'hr');
        app(SystemSettingsService::class)->put('catalog_use_blog', true);

        $user = $this->makeAdminUser();

        Livewire::actingAs($user)
            ->test(CategoryForm::class)
            ->set('form.scope', Category::SCOPE_BLOG)
            ->set('form.locale', 'hr')
            ->set('form.name', 'Testna admin kategorija')
            ->set('form.slug', '')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.categories', [
                'scope' => Category::SCOPE_BLOG,
                'locale' => 'hr',
            ]));

        $category = Category::query()
            ->whereHas('translations', fn ($query) => $query
                ->where('locale', 'hr')
                ->where('slug', 'testna-admin-kategorija'))
            ->firstOrFail();

        $this->actingAs($user)
            ->get('/admin/categories?scope=blog&locale=hr')
            ->assertOk()
            ->assertSee('Testna admin kategorija');

        Livewire::actingAs($user)
            ->test(BlogForm::class)
            ->set('form.locale', 'hr')
            ->set('form.title', 'Urednički smoke test')
            ->set('form.code', '')
            ->set('form.slug', '')
            ->set('form.is_active', true)
            ->set('form.published_at', now()->subDay()->format('Y-m-d'))
            ->set('form.excerpt', 'Sažetak spremljen kroz pojednostavljeni admin obrazac.')
            ->set('form.body_html', '<p>Tekst članka spremljen u backendu i prikazan na frontu.</p>')
            ->set('form.category_ids', [$category->id])
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.content.blog.index', ['locale' => 'hr']));

        $post = BlogPost::query()
            ->where('code', 'urednicki-smoke-test')
            ->with(['translations', 'categories'])
            ->firstOrFail();
        $translation = $post->translations->firstWhere('locale', 'hr');

        $this->assertSame('Urednički smoke test', $translation?->title);
        $this->assertSame('urednicki-smoke-test', $translation?->slug);
        $this->assertSame('Tekst članka spremljen u backendu i prikazan na frontu.', strip_tags((string) $translation?->body_html));
        $this->assertSame($category->id, $post->categories->first()?->id);
        $this->assertTrue((bool) $post->categories->first()?->pivot?->is_primary);

        $this->get('/blog/urednicki-smoke-test')
            ->assertOk()
            ->assertSee('Urednički smoke test')
            ->assertSee('Testna admin kategorija')
            ->assertSee('Tekst članka spremljen u backendu i prikazan na frontu.');
    }

    public function test_editor_updates_existing_service_and_page_copy_used_on_the_frontend(): void
    {
        config()->set('app.locale', 'hr');
        config()->set('app.fallback_locale', 'hr');

        $user = $this->makeAdminUser();
        $auditPage = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::AUDIT)
            ->firstOrFail();

        Livewire::actingAs($user)
            ->test(ServiceForm::class, ['servicePageId' => $auditPage->id])
            ->set('form.locale', 'hr')
            ->set('form.translation_payload.hero.subtitle_lead', 'Revizija iz admin smoke testa')
            ->set('form.translation_payload.overview.title', 'Naslov revizije iz backenda')
            ->set('form.translation_payload.meeting.title', 'Kontaktni naslov iz backenda')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.content.services.index', ['locale' => 'hr']));

        $auditPayload = $auditPage->fresh()->translation('hr')->firstOrFail()->payload;
        $this->assertSame('Revizija iz admin smoke testa', data_get($auditPayload, 'hero.subtitle_lead'));
        $this->assertSame('Naslov revizije iz backenda', data_get($auditPayload, 'overview.title'));

        $this->get('/revizija')
            ->assertOk()
            ->assertSee('Revizija iz admin smoke testa')
            ->assertSee('Naslov revizije iz backenda')
            ->assertSee('Kontaktni naslov iz backenda');

        $aboutPage = InfoPage::query()->where('code', 'about-us')->firstOrFail();

        Livewire::actingAs($user)
            ->test(PageForm::class, ['pageId' => $aboutPage->id])
            ->set('form.about_content.hero.title', 'O nama naslov iz admina')
            ->set('form.about_content.story.body_html', '<p>Tekst postojeće stranice spremljen kroz backend.</p>')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.content.pages.index', ['locale' => 'hr']));

        $aboutPayload = $aboutPage->fresh()->translation('hr')->firstOrFail()->payload;
        $this->assertSame('O nama naslov iz admina', data_get($aboutPayload, 'about_page.hero.title'));
        $this->assertSame(
            '<p>Tekst postojeće stranice spremljen kroz backend.</p>',
            data_get($aboutPayload, 'about_page.story.body_html')
        );

        $this->get('/o-nama')
            ->assertOk()
            ->assertSee('O nama naslov iz admina')
            ->assertSee('Tekst postojeće stranice spremljen kroz backend.');
    }

    public function test_admin_forms_hide_technical_fields_and_use_a_separate_blog_categories_tab(): void
    {
        $user = $this->makeAdminUser();

        Livewire::actingAs($user)
            ->test(CategoryForm::class)
            ->assertDontSeeHtml('wire:model="form.code"')
            ->assertDontSeeHtml('wire:model="form.sort_order"')
            ->assertDontSee('Payload JSON');

        Livewire::actingAs($user)
            ->test(BlogForm::class)
            ->assertSee('Kategorije')
            ->assertDontSee('Smart Link')
            ->assertDontSeeHtml('wire:model.live.debounce.250ms="form.code"')
            ->assertDontSeeHtml('wire:model="form.sort_order"')
            ->assertDontSee('Payload JSON')
            ->assertSeeHtml('data-admin-dirty-form')
            ->assertSee('Nema nespremljenih izmjena')
            ->call('setTab', 'categories')
            ->assertSet('activeTab', 'categories')
            ->assertSee('Brzo dodaj novu kategoriju')
            ->assertSee('Dostupne kategorije')
            ->assertSee('Odabrane kategorije')
            ->assertSee('Prva odabrana kategorija je primarna');

        Livewire::actingAs($user)
            ->test(CallForm::class)
            ->assertDontSee('Smart Link');

        Livewire::actingAs($user)
            ->test(PageForm::class)
            ->assertDontSeeHtml('wire:model="form.code"')
            ->assertDontSeeHtml('wire:model="form.sort_order"')
            ->assertDontSee('Payload JSON');

        $auditPage = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::AUDIT)
            ->firstOrFail();

        Livewire::actingAs($user)
            ->test(ServiceForm::class, ['servicePageId' => $auditPage->id])
            ->assertDontSeeHtml('wire:model="form.code"')
            ->assertDontSeeHtml('wire:model="form.sort_order"')
            ->assertDontSeeHtml('wire:model="form.published_at"');
    }

    public function test_blog_editor_can_create_and_select_a_category_without_leaving_the_post(): void
    {
        $user = $this->makeAdminUser();

        $component = Livewire::actingAs($user)
            ->test(BlogForm::class)
            ->set('form.locale', 'hr')
            ->set('newCategoryName', 'Brza UX kategorija')
            ->call('quickCreateCategory')
            ->assertHasNoErrors()
            ->assertSet('newCategoryName', '')
            ->assertDispatched('notify');

        $category = Category::query()
            ->where('scope', Category::SCOPE_BLOG)
            ->whereHas('translations', fn ($query) => $query
                ->where('locale', 'hr')
                ->where('slug', 'brza-ux-kategorija'))
            ->firstOrFail();

        $component->assertSet('form.category_ids', [$category->id]);
    }

    public function test_removed_family_business_page_is_hidden_from_front_and_admin(): void
    {
        $user = $this->makeAdminUser();
        $retiredPage = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::FAMILY_BUSINESS)
            ->firstOrFail();

        $this->get('/obiteljski-biznis')->assertNotFound();

        $this->actingAs($user)
            ->get('/admin/content/services?locale=hr')
            ->assertOk()
            ->assertDontSee('Obiteljski biznis');

        $this->actingAs($user)
            ->get("/admin/content/services/{$retiredPage->id}/edit?locale=hr")
            ->assertNotFound();
    }

    private function makeAdminUser(): User
    {
        $user = User::factory()->create();

        Bouncer::role()->firstOrCreate(['name' => 'admin']);
        Bouncer::assign('admin')->to($user);

        return $user;
    }
}
