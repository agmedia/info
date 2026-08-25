<?php

namespace App\Livewire\Admin\Message;

use App\Livewire\Admin\MessageNotifications;
use App\Models\Content\Resource\ResourceDownloadRequest;
use App\Services\Settings\SystemSettingsService;
use Livewire\Component;
use Livewire\WithPagination;

class ResourceDownloadRequestManager extends Component
{
    use WithPagination;

    public string $search = '';

    public string $status = 'all';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function markAsNew(int $requestId): void
    {
        $this->updateStatus($requestId, ResourceDownloadRequest::STATUS_NEW);
    }

    public function markAsRead(int $requestId): void
    {
        $this->updateStatus($requestId, ResourceDownloadRequest::STATUS_READ);
    }

    public function markAsResolved(int $requestId): void
    {
        $this->updateStatus($requestId, ResourceDownloadRequest::STATUS_RESOLVED);
    }

    public function render()
    {
        $perPage = app(SystemSettingsService::class)->getInt(
            'admin_items_per_page',
            (int) config('admin_ui.pagination.admin_items_per_page', 20),
            5,
            200
        );

        $rows = ResourceDownloadRequest::query()
            ->with('reviewer')
            ->when($this->search !== '', function ($query): void {
                $needle = '%'.$this->search.'%';

                $query->where(function ($inner) use ($needle): void {
                    $inner->where('name', 'like', $needle)
                        ->orWhere('email', 'like', $needle)
                        ->orWhere('phone', 'like', $needle)
                        ->orWhere('company', 'like', $needle)
                        ->orWhere('document_title', 'like', $needle)
                        ->orWhere('document_code', 'like', $needle);
                });
            })
            ->when($this->status !== 'all', fn ($query) => $query->where('status', $this->status))
            ->orderByRaw("case status when 'new' then 0 when 'read' then 1 when 'resolved' then 2 else 3 end")
            ->latest('created_at')
            ->paginate($perPage);

        return view('livewire.admin.message.resource-download-request-manager', [
            'rows' => $rows,
            'perPage' => $perPage,
            'statusOptions' => $this->statusOptions(),
            'totals' => [
                'all' => ResourceDownloadRequest::query()->count(),
                ResourceDownloadRequest::STATUS_NEW => ResourceDownloadRequest::query()->where('status', ResourceDownloadRequest::STATUS_NEW)->count(),
                ResourceDownloadRequest::STATUS_READ => ResourceDownloadRequest::query()->where('status', ResourceDownloadRequest::STATUS_READ)->count(),
                ResourceDownloadRequest::STATUS_RESOLVED => ResourceDownloadRequest::query()->where('status', ResourceDownloadRequest::STATUS_RESOLVED)->count(),
            ],
        ]);
    }

    private function updateStatus(int $requestId, string $status): void
    {
        $downloadRequest = ResourceDownloadRequest::query()->find($requestId);

        if (! $downloadRequest) {
            $this->dispatch('notify', type: 'danger', message: __('admin.messages.download_requests.manager.notify_not_found'));

            return;
        }

        $payload = ['status' => $status];

        if ($status === ResourceDownloadRequest::STATUS_NEW) {
            $payload['reviewed_by'] = null;
            $payload['reviewed_at'] = null;
        } else {
            $payload['reviewed_by'] = auth()->id();
            $payload['reviewed_at'] = now();
        }

        $downloadRequest->update($payload);

        $this->dispatch(MessageNotifications::REFRESH_EVENT);
        $this->dispatch('notify', type: 'success', message: __('admin.messages.download_requests.manager.notify_status_updated'));
    }

    /**
     * @return array<string, string>
     */
    private function statusOptions(): array
    {
        return [
            'all' => __('admin.common.all'),
            ResourceDownloadRequest::STATUS_NEW => __('admin.messages.download_requests.status.new'),
            ResourceDownloadRequest::STATUS_READ => __('admin.messages.download_requests.status.read'),
            ResourceDownloadRequest::STATUS_RESOLVED => __('admin.messages.download_requests.status.resolved'),
        ];
    }
}
