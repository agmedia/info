<?php

namespace App\Livewire\Admin\Content\JobOpening;

use App\Models\Content\Career\JobOpening;
use App\Services\Settings\SystemSettingsService;
use App\Support\Admin\AdminLocale;
use Livewire\Component;
use Livewire\WithPagination;

class Manager extends Component
{
    use WithPagination;

    public string $search = '';

    public string $locale = 'hr';

    public function mount(): void
    {
        $this->locale = AdminLocale::normalize((string) (
            request()->query('locale')
            ?: app()->getLocale()
            ?: AdminLocale::default()
        )) ?: AdminLocale::default();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedLocale(): void
    {
        $this->resetPage();
    }

    public function delete(int $jobOpeningId): void
    {
        $opening = JobOpening::query()->find($jobOpeningId);
        if (! $opening) {
            $this->dispatch(
                'notify',
                type: 'danger',
                message: __('admin.content.job_openings.manager.notify_not_found'),
            );

            return;
        }

        $opening->delete();

        $this->dispatch(
            'notify',
            type: 'success',
            message: __('admin.content.job_openings.manager.notify_deleted'),
        );
        $this->resetPage();
    }

    public function render()
    {
        $perPage = app(SystemSettingsService::class)->getInt(
            'admin_items_per_page',
            (int) config('admin_ui.pagination.admin_items_per_page', 20),
            5,
            200,
        );

        $search = trim($this->search);
        $rows = JobOpening::query()
            ->with([
                'translations' => fn ($query) => $query->where('locale', $this->locale),
            ])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($searchQuery) use ($search): void {
                    $searchQuery
                        ->where('code', 'like', '%'.$search.'%')
                        ->orWhereHas('translations', function ($translationQuery) use ($search): void {
                            $translationQuery
                                ->where('locale', $this->locale)
                                ->where(function ($localizedQuery) use ($search): void {
                                    $localizedQuery
                                        ->where('title', 'like', '%'.$search.'%')
                                        ->orWhere('slug', 'like', '%'.$search.'%')
                                        ->orWhere('locations', 'like', '%'.$search.'%');
                                });
                        });
                });
            })
            ->orderByDesc('published_at')
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate($perPage);

        $user = auth()->user();
        $isSuperadmin = $user?->isA('superadmin') ?? false;

        return view('livewire.admin.content.job-opening.manager', [
            'rows' => $rows,
            'perPage' => $perPage,
            'canCreate' => $user && ($isSuperadmin || $user->can('content.job_openings.create')),
            'canUpdate' => $user && ($isSuperadmin || $user->can('content.job_openings.update')),
            'canDelete' => $user && ($isSuperadmin || $user->can('content.job_openings.delete')),
        ]);
    }
}
