<?php

namespace App\Livewire\Admin\Settings\User;

use App\Services\Settings\SystemSettingsService;
use Livewire\Component;

class UserFeatures extends Component
{
    public array $form = [
        'user_tracking_enabled' => true,
    ];

    public function mount(): void
    {
        /** @var array<string, mixed> $defaults */
        $defaults = [
            'user_tracking_enabled' => (bool) config('user_features.flags.user_tracking_enabled', true),
        ];
        $settings = app(SystemSettingsService::class);
        foreach ($defaults as $key => $defaultValue) {
            $this->form[$key] = $settings->get($key, $defaultValue);
        }
    }

    public function toggle(string $key): void
    {
        if ($key !== 'user_tracking_enabled') {
            return;
        }

        $this->form[$key] = ! (bool) ($this->form[$key] ?? false);
    }

    public function save(): void
    {
        $validated = $this->validate([
            'form.user_tracking_enabled' => ['required', 'boolean'],
        ]);

        $payload = array_merge([
            'user_tracking_enabled' => (bool) $validated['form']['user_tracking_enabled'],
        ], $this->legacyUserCleanupPayload());

        app(SystemSettingsService::class)->putMany($payload);

        $this->dispatch('notify', type: 'success', message: __('User settings saved.'));
    }

    public function resetToDefaults(): void
    {
        $this->form['user_tracking_enabled'] = (bool) config('user_features.flags.user_tracking_enabled', true);

        $this->dispatch('notify', type: 'info', message: __('Default user settings loaded in form (save to persist).'));
    }

    public function render()
    {
        return view('livewire.admin.settings.user.user-features');
    }

    /**
     * @return array<string, mixed>
     */
    private function legacyUserCleanupPayload(): array
    {
        return [
            'user_loyalty_enabled' => false,
            'loyalty_points_per_currency' => 0.0,
            'loyalty_min_order_total' => 0.0,
            'loyalty_reversal_mode' => 'zero_out',
        ];
    }
}
