<?php

namespace App\Livewire\Admin\Message;

use App\Models\Content\Support\NewsletterSubscription;
use App\Services\Settings\SystemSettingsService;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class NewsletterSubscriptionManager extends Component
{
    use WithPagination;

    public string $search = '';

    public string $status = 'all';

    public string $locale = 'all';

    public function updatedSearch(): void
    {
        $this->resetPage('newsletterPage');
    }

    public function updatedStatus(): void
    {
        $this->resetPage('newsletterPage');
    }

    public function updatedLocale(): void
    {
        $this->resetPage('newsletterPage');
    }

    public function render()
    {
        $perPage = app(SystemSettingsService::class)->getInt(
            'admin_items_per_page',
            (int) config('admin_ui.pagination.admin_items_per_page', 20),
            5,
            200,
        );

        $rows = $this->filteredQuery()
            ->orderByDesc('last_attempt_at')
            ->orderByDesc('id')
            ->paginate($perPage, ['*'], 'newsletterPage');

        return view('livewire.admin.message.newsletter-subscription-manager', [
            'rows' => $rows,
            'perPage' => $perPage,
            'statusOptions' => $this->statusOptions(),
            'localeOptions' => $this->localeOptions(),
            'totals' => [
                'all' => NewsletterSubscription::query()->count(),
                'received' => NewsletterSubscription::query()
                    ->where('status', NewsletterSubscription::STATUS_RECEIVED)
                    ->count(),
                'awaiting_confirmation' => NewsletterSubscription::query()
                    ->where('status', NewsletterSubscription::STATUS_CONFIRMATION_PENDING)
                    ->count(),
                'subscribed' => NewsletterSubscription::query()
                    ->where('status', NewsletterSubscription::STATUS_SUBSCRIBED)
                    ->count(),
                'failed' => NewsletterSubscription::query()
                    ->where('status', NewsletterSubscription::STATUS_FAILED)
                    ->count(),
            ],
        ]);
    }

    private function filteredQuery(): Builder
    {
        return NewsletterSubscription::query()
            ->when(trim($this->search) !== '', function (Builder $query): void {
                $needle = '%'.trim($this->search).'%';

                $query->where(function (Builder $inner) use ($needle): void {
                    $inner->where('email', 'like', $needle)
                        ->orWhere('provider', 'like', $needle)
                        ->orWhere('provider_member_id', 'like', $needle)
                        ->orWhere('error_code', 'like', $needle)
                        ->orWhere('error_message', 'like', $needle);
                });
            })
            ->when(
                array_key_exists($this->status, $this->statusOptions()) && $this->status !== 'all',
                fn (Builder $query): Builder => $query->where('status', $this->status),
            )
            ->when(
                in_array($this->locale, $this->localeOptions(), true) && $this->locale !== 'all',
                fn (Builder $query): Builder => $query->where('locale', $this->locale),
            );
    }

    /**
     * @return array<string, string>
     */
    private function statusOptions(): array
    {
        return [
            'all' => __('admin.common.all'),
            NewsletterSubscription::STATUS_RECEIVED => __('admin.messages.newsletter.status.received'),
            NewsletterSubscription::STATUS_PENDING => __('admin.messages.newsletter.status.pending'),
            NewsletterSubscription::STATUS_CONFIRMATION_PENDING => __('admin.messages.newsletter.status.confirmation_pending'),
            NewsletterSubscription::STATUS_SUBSCRIBED => __('admin.messages.newsletter.status.subscribed'),
            NewsletterSubscription::STATUS_FAILED => __('admin.messages.newsletter.status.failed'),
        ];
    }

    /**
     * @return array<int, string>
     */
    private function localeOptions(): array
    {
        return NewsletterSubscription::query()
            ->whereNotNull('locale')
            ->where('locale', '!=', '')
            ->distinct()
            ->orderBy('locale')
            ->pluck('locale')
            ->map(static fn (mixed $locale): string => strtolower(trim((string) $locale)))
            ->filter()
            ->prepend('all')
            ->unique()
            ->values()
            ->all();
    }
}
