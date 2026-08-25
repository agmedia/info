<?php

namespace App\Livewire\Admin\Message;

use App\Livewire\Admin\MessageNotifications;
use App\Models\Content\Support\ContactMessage;
use App\Services\Settings\SystemSettingsService;
use Livewire\Component;
use Livewire\WithPagination;

class ContactMessageManager extends Component
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

    public function markAsNew(int $messageId): void
    {
        $this->updateStatus($messageId, ContactMessage::STATUS_NEW);
    }

    public function markAsRead(int $messageId): void
    {
        $this->updateStatus($messageId, ContactMessage::STATUS_READ);
    }

    public function markAsResolved(int $messageId): void
    {
        $this->updateStatus($messageId, ContactMessage::STATUS_RESOLVED);
    }

    public function render()
    {
        $perPage = app(SystemSettingsService::class)->getInt(
            'admin_items_per_page',
            (int) config('admin_ui.pagination.admin_items_per_page', 20),
            5,
            200
        );

        $rows = $this->baseQuery()
            ->with('reviewer')
            ->when($this->search !== '', function ($query): void {
                $needle = '%'.$this->search.'%';

                $query->where(function ($inner) use ($needle): void {
                    $inner->where('name', 'like', $needle)
                        ->orWhere('email', 'like', $needle)
                        ->orWhere('phone', 'like', $needle)
                        ->orWhere('subject', 'like', $needle)
                        ->orWhere('message', 'like', $needle)
                        ->orWhere('payload->company', 'like', $needle)
                        ->orWhere('payload->source_page', 'like', $needle)
                        ->orWhere('payload->url', 'like', $needle);
                });
            })
            ->when($this->status !== 'all', fn ($query) => $query->where('status', $this->status))
            ->orderByRaw("case status when 'new' then 0 when 'read' then 1 when 'resolved' then 2 else 3 end")
            ->latest('created_at')
            ->paginate($perPage);

        return view('livewire.admin.message.contact-message-manager', [
            'rows' => $rows,
            'perPage' => $perPage,
            'statusOptions' => $this->statusOptions(),
            'totals' => [
                'all' => (clone $this->baseQuery())->count(),
                ContactMessage::STATUS_NEW => (clone $this->baseQuery())->where('status', ContactMessage::STATUS_NEW)->count(),
                ContactMessage::STATUS_READ => (clone $this->baseQuery())->where('status', ContactMessage::STATUS_READ)->count(),
                ContactMessage::STATUS_RESOLVED => (clone $this->baseQuery())->where('status', ContactMessage::STATUS_RESOLVED)->count(),
            ],
        ]);
    }

    private function updateStatus(int $messageId, string $status): void
    {
        $message = $this->baseQuery()->find($messageId);

        if (! $message) {
            $this->dispatch('notify', type: 'danger', message: __('admin.messages.contact.manager.notify_not_found'));

            return;
        }

        $payload = ['status' => $status];

        if ($status === ContactMessage::STATUS_NEW) {
            $payload['reviewed_by'] = null;
            $payload['reviewed_at'] = null;
        } else {
            $payload['reviewed_by'] = auth()->id();
            $payload['reviewed_at'] = now();
        }

        $message->update($payload);

        $this->dispatch(MessageNotifications::REFRESH_EVENT);
        $this->dispatch('notify', type: 'success', message: __('admin.messages.contact.manager.notify_status_updated'));
    }

    private function baseQuery()
    {
        return ContactMessage::query()
            ->whereIn('form_type', [
                ContactMessage::FORM_TYPE_CONTACT,
                ContactMessage::FORM_TYPE_SERVICE_CONTACT,
            ]);
    }

    /**
     * @return array<string, string>
     */
    private function statusOptions(): array
    {
        return [
            'all' => __('admin.common.all'),
            ContactMessage::STATUS_NEW => __('admin.messages.contact.status.new'),
            ContactMessage::STATUS_READ => __('admin.messages.contact.status.read'),
            ContactMessage::STATUS_RESOLVED => __('admin.messages.contact.status.resolved'),
        ];
    }
}
