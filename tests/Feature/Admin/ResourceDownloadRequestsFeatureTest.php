<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Message\ResourceDownloadRequestManager;
use App\Models\Content\Resource\ResourceDownloadRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Tests\TestCase;

class ResourceDownloadRequestsFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_download_requests_page(): void
    {
        $user = $this->makeAdminUser();

        ResourceDownloadRequest::query()->create($this->requestPayload([
            'name' => 'Ivana Horvat',
            'email' => 'ivana@example.test',
            'document_title' => 'Analiza sektora: Proizvodnja tekstila',
        ]));

        $this->actingAs($user)
            ->get(route('admin.messages.download-requests.index'))
            ->assertOk()
            ->assertSee(__('admin.messages.download_requests.manager.title'))
            ->assertSee('Ivana Horvat')
            ->assertSee('Analiza sektora: Proizvodnja tekstila');
    }

    public function test_admin_can_mark_download_request_as_read(): void
    {
        $user = $this->makeAdminUser();

        $request = ResourceDownloadRequest::query()->create($this->requestPayload());

        Livewire::actingAs($user)
            ->test(ResourceDownloadRequestManager::class)
            ->call('markAsRead', $request->id);

        $request->refresh();

        $this->assertSame(ResourceDownloadRequest::STATUS_READ, $request->status);
        $this->assertSame($user->id, $request->reviewed_by);
        $this->assertNotNull($request->reviewed_at);
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
    private function requestPayload(array $overrides = []): array
    {
        return array_merge([
            'document_code' => 'analiza-sektora-proizvodnja-tekstila',
            'document_title' => 'Analiza sektora: Proizvodnja tekstila',
            'document_slug' => 'analiza-sektora-proizvodnja-tekstila',
            'document_group_code' => 'sector-analysis',
            'document_download_url' => 'https://alphacapitalis.com/wp-content/uploads/2024/01/Analiza-sektora_C13_2024.pdf',
            'name' => 'Ivana Horvat',
            'email' => 'ivana@example.test',
            'phone' => '+38591111222',
            'company' => 'Alpha Test d.o.o.',
            'status' => ResourceDownloadRequest::STATUS_NEW,
            'locale' => 'hr',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit',
        ], $overrides);
    }
}
