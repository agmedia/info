<?php

namespace App\Livewire\Admin\Message;

use App\Livewire\Admin\MessageNotifications;
use App\Models\Content\Support\CareerApplication;
use App\Services\Settings\SystemSettingsService;
use Livewire\Component;
use Livewire\WithPagination;

class CareerApplicationManager extends Component
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

    public function markAsNew(int $applicationId): void
    {
        $this->updateStatus($applicationId, CareerApplication::STATUS_NEW);
    }

    public function markAsRead(int $applicationId): void
    {
        $this->updateStatus($applicationId, CareerApplication::STATUS_READ);
    }

    public function markAsResolved(int $applicationId): void
    {
        $this->updateStatus($applicationId, CareerApplication::STATUS_RESOLVED);
    }

    public function render()
    {
        $perPage = app(SystemSettingsService::class)->getInt(
            'admin_items_per_page',
            (int) config('admin_ui.pagination.admin_items_per_page', 20),
            5,
            200
        );

        $rows = CareerApplication::query()
            ->with('reviewer')
            ->when($this->search !== '', function ($query): void {
                $needle = '%'.$this->search.'%';

                $query->where(function ($inner) use ($needle): void {
                    $inner->where('first_name', 'like', $needle)
                        ->orWhere('last_name', 'like', $needle)
                        ->orWhere('email', 'like', $needle)
                        ->orWhere('message', 'like', $needle)
                        ->orWhere('cv_original_name', 'like', $needle);
                });
            })
            ->when($this->status !== 'all', fn ($query) => $query->where('status', $this->status))
            ->orderByRaw("case status when 'new' then 0 when 'read' then 1 when 'resolved' then 2 else 3 end")
            ->latest('created_at')
            ->paginate($perPage);

        return view('livewire.admin.message.career-application-manager', [
            'rows' => $rows,
            'perPage' => $perPage,
            'statusOptions' => $this->statusOptions(),
            'totals' => [
                'all' => CareerApplication::query()->count(),
                CareerApplication::STATUS_NEW => CareerApplication::query()->where('status', CareerApplication::STATUS_NEW)->count(),
                CareerApplication::STATUS_READ => CareerApplication::query()->where('status', CareerApplication::STATUS_READ)->count(),
                CareerApplication::STATUS_RESOLVED => CareerApplication::query()->where('status', CareerApplication::STATUS_RESOLVED)->count(),
            ],
        ]);
    }

    private function updateStatus(int $applicationId, string $status): void
    {
        $application = CareerApplication::query()->find($applicationId);

        if (! $application) {
            $this->dispatch('notify', type: 'danger', message: __('admin.messages.career.manager.notify_not_found'));

            return;
        }

        $payload = ['status' => $status];

        if ($status === CareerApplication::STATUS_NEW) {
            $payload['reviewed_by'] = null;
            $payload['reviewed_at'] = null;
        } else {
            $payload['reviewed_by'] = auth()->id();
            $payload['reviewed_at'] = now();
        }

        $application->update($payload);

        $this->dispatch(MessageNotifications::REFRESH_EVENT);
        $this->dispatch('notify', type: 'success', message: __('admin.messages.career.manager.notify_status_updated'));
    }

    /**
     * @return array<string, string>
     */
    private function statusOptions(): array
    {
        return [
            'all' => __('admin.common.all'),
            CareerApplication::STATUS_NEW => __('admin.messages.career.status.new'),
            CareerApplication::STATUS_READ => __('admin.messages.career.status.read'),
            CareerApplication::STATUS_RESOLVED => __('admin.messages.career.status.resolved'),
        ];
    }
}
