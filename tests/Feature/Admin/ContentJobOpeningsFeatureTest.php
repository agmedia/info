<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Content\JobOpening\Form as JobOpeningForm;
use App\Models\Content\Career\JobOpening;
use App\Models\Settings\Local\Language;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Tests\TestCase;

class ContentJobOpeningsFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_job_openings_manager(): void
    {
        $this->actingAs($this->makeAdminUser())
            ->get('/admin/content/job-openings')
            ->assertOk()
            ->assertSee(__('admin.content.job_openings.manager.title'));
    }

    public function test_admin_can_create_a_sanitized_scheduled_job_opening(): void
    {
        $user = $this->makeAdminUser();

        Livewire::actingAs($user)
            ->test(JobOpeningForm::class)
            ->set('form.locale', 'hr')
            ->set('form.title', 'Voditelj računovodstva')
            ->set('form.locations', 'Zagreb | Rijeka')
            ->set('form.published_at', '2026-09-02T12:15')
            ->set('form.excerpt', 'Vodite naš računovodstveni tim.')
            ->set('form.body_html', '<p>Siguran <strong>sadržaj</strong>.</p><script>alert(1)</script>')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.content.job-openings.index', ['locale' => 'hr']));

        $opening = JobOpening::query()->where('code', 'voditelj-racunovodstva')->firstOrFail();
        $translation = $opening->translation('hr')->firstOrFail();

        $this->assertSame('2026-09-02 10:15:00', $opening->published_at?->utc()->format('Y-m-d H:i:s'));
        $this->assertSame('voditelj-racunovodstva', $translation->slug);
        $this->assertSame('Voditelj računovodstva', $translation->meta_title);
        $this->assertSame('Vodite naš računovodstveni tim.', $translation->meta_description);
        $this->assertStringContainsString('<strong>sadržaj</strong>', $translation->body_html);
        $this->assertStringNotContainsString('<script', $translation->body_html);
    }

    public function test_missing_locale_loads_blank_fields_and_does_not_overwrite_existing_translation(): void
    {
        $user = $this->makeAdminUser();
        $this->activateEnglish();

        $opening = JobOpening::query()
            ->where('code', 'racunovoda-asistent-u-racunovodstvu')
            ->firstOrFail();
        $croatianTitle = (string) $opening->translation('hr')->value('title');
        $code = (string) $opening->code;

        Livewire::withQueryParams(['locale' => 'en'])
            ->actingAs($user)
            ->test(JobOpeningForm::class, ['jobOpeningId' => $opening->id])
            ->assertSet('form.locale', 'en')
            ->assertSet('form.title', '')
            ->assertSet('form.slug', '')
            ->assertSet('form.locations', '')
            ->set('form.title', 'Accountant / Accounting Assistant')
            ->set('form.slug', 'accountant-accounting-assistant')
            ->set('form.locations', 'Zagreb | Rijeka | Vinkovci')
            ->set('form.excerpt', 'Join our accounting team.')
            ->set('form.body_html', '<p>Build your career with our accounting team.</p>')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.content.job-openings.index', ['locale' => 'en']));

        $opening->refresh();

        $this->assertSame($code, $opening->code);
        $this->assertSame($croatianTitle, (string) $opening->translation('hr')->value('title'));
        $this->assertSame('Accountant / Accounting Assistant', (string) $opening->translation('en')->value('title'));
    }

    public function test_ambiguous_zagreb_publication_time_is_rejected(): void
    {
        $user = $this->makeAdminUser();

        Livewire::actingAs($user)
            ->test(JobOpeningForm::class)
            ->set('form.locale', 'hr')
            ->set('form.title', 'Kontroler')
            ->set('form.locations', 'Zagreb')
            ->set('form.published_at', '2026-10-25T02:30')
            ->set('form.body_html', '<p>Opis pozicije.</p>')
            ->call('save')
            ->assertHasErrors(['form.published_at']);

        $this->assertFalse(JobOpening::query()->where('code', 'kontroler')->exists());
    }

    public function test_acl_gives_editors_crud_without_delete_and_protects_index_delete_calls(): void
    {
        $editorAbilities = config('admin_acl.roles.editor', []);

        $this->assertContains('content.job_openings.view', $editorAbilities);
        $this->assertContains('content.job_openings.create', $editorAbilities);
        $this->assertContains('content.job_openings.update', $editorAbilities);
        $this->assertNotContains('content.job_openings.delete', $editorAbilities);
        $this->assertSame(
            ['content.job_openings.delete'],
            config('admin_authorization.route_rules')['admin.content.job-openings.index']['delete'] ?? null,
        );
    }

    private function activateEnglish(): void
    {
        Language::query()->updateOrCreate(['code' => 'en'], [
            'locale' => 'en',
            'name' => 'English',
            'native_name' => 'English',
            'direction' => 'ltr',
            'is_default' => false,
            'is_active' => true,
            'sort_order' => 20,
        ]);
    }

    private function makeAdminUser(): User
    {
        $user = User::factory()->create();

        Bouncer::role()->firstOrCreate(['name' => 'admin']);
        Bouncer::assign('admin')->to($user);

        return $user;
    }
}
