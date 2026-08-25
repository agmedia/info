<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Services\Admin\AdminMessageNotificationService;
use Livewire\Attributes\On;
use Livewire\Component;

class MessageNotifications extends Component
{
    public const REFRESH_EVENT = 'admin-message-notifications-refresh';

    #[On(self::REFRESH_EVENT)]
    public function refreshSummary(): void
    {
        // The event-triggered Livewire render recalculates the ACL-filtered summary.
    }

    public function render()
    {
        $user = auth()->user();
        $notifications = $user instanceof User
            ? app(AdminMessageNotificationService::class)->summaryFor($user)
            : ['total' => 0, 'groups' => []];

        return view('livewire.admin.message-notifications', [
            'notifications' => $notifications,
        ]);
    }
}
