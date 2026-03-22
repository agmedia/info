<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Message\CareerApplicationManager;
use App\Models\Content\Support\CareerApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Tests\TestCase;

class CareerApplicationsFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_career_applications_page(): void
    {
        $user = $this->makeAdminUser();

        CareerApplication::query()->create($this->applicationPayload([
            'first_name' => 'Ivana',
            'last_name' => 'Horvat',
            'email' => 'ivana@example.test',
        ]));

        $this->actingAs($user)
            ->get(route('admin.messages.career.index'))
            ->assertOk()
            ->assertSee(__('admin.messages.career.manager.title'))
            ->assertSee('Ivana Horvat');
    }

    public function test_admin_can_mark_career_application_as_read(): void
    {
        $user = $this->makeAdminUser();

        $application = CareerApplication::query()->create($this->applicationPayload());

        Livewire::actingAs($user)
            ->test(CareerApplicationManager::class)
            ->call('markAsRead', $application->id);

        $application->refresh();

        $this->assertSame(CareerApplication::STATUS_READ, $application->status);
        $this->assertSame($user->id, $application->reviewed_by);
        $this->assertNotNull($application->reviewed_at);
    }

    public function test_admin_can_download_uploaded_cv(): void
    {
        Storage::fake('local');

        $user = $this->makeAdminUser();

        $application = CareerApplication::query()->create($this->applicationPayload([
            'cv_path' => 'career-applications/cv/ivana-horvat-cv.pdf',
            'cv_original_name' => 'ivana-horvat-cv.pdf',
        ]));

        Storage::disk('local')->put($application->cv_path, 'career-cv');

        $this->actingAs($user)
            ->get(route('admin.messages.career.download', ['careerApplication' => $application->id]))
            ->assertOk()
            ->assertDownload('ivana-horvat-cv.pdf');
    }

    private function makeAdminUser(): User
    {
        $user = User::factory()->create();

        Bouncer::role()->firstOrCreate(['name' => 'admin']);
        Bouncer::assign('admin')->to($user);

        return $user;
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function applicationPayload(array $overrides = []): array
    {
        return array_merge([
            'first_name' => 'Ivana',
            'last_name' => 'Horvat',
            'email' => 'candidate@example.test',
            'message' => 'Zanima me mogućnost rada u vašem timu.',
            'cv_path' => 'career-applications/cv/default.pdf',
            'cv_disk' => 'local',
            'cv_original_name' => 'default.pdf',
            'cv_mime_type' => 'application/pdf',
            'cv_size' => 1024,
            'status' => CareerApplication::STATUS_NEW,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit',
        ], $overrides);
    }
}
