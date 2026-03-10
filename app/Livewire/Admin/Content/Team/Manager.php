<?php

namespace App\Livewire\Admin\Content\Team;

use App\Models\Content\Team\TeamMember;
use App\Services\Settings\SystemSettingsService;
use Livewire\Component;
use Livewire\WithPagination;

class Manager extends Component
{
    use WithPagination;

    public string $search = '';
    public string $locale = 'hr';

    public function mount(): void
    {
        $this->locale = (string) (request()->query('locale') ?: app()->getLocale() ?: config('admin_ui.locale.default', 'hr'));
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedLocale(): void
    {
        $this->resetPage();
    }

    public function delete(int $memberId): void
    {
        $member = TeamMember::query()->find($memberId);
        if (! $member) {
            $this->dispatch('notify', type: 'danger', message: (string) __('admin.content.team.manager.notify_not_found'));

            return;
        }

        $member->delete();

        $this->dispatch('notify', type: 'success', message: (string) __('admin.content.team.manager.notify_deleted'));
        $this->resetPage();
    }

    public function render()
    {
        $perPage = app(SystemSettingsService::class)->getInt(
            'admin_items_per_page',
            (int) config('admin_ui.pagination.admin_items_per_page', 20),
            5,
            200
        );

        $rows = TeamMember::query()
            ->with([
                'translations' => fn ($q) => $q->where('locale', $this->locale),
                'media',
            ])
            ->when($this->search !== '', function ($query): void {
                $query->where(function ($q): void {
                    $q->where('code', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%')
                        ->orWhere('mobile_phone', 'like', '%'.$this->search.'%')
                        ->orWhereHas('translations', function ($tq): void {
                            $tq->where('name', 'like', '%'.$this->search.'%')
                                ->orWhere('position', 'like', '%'.$this->search.'%')
                                ->orWhere('departments', 'like', '%'.$this->search.'%');
                        });
                });
            })
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate($perPage);

        return view('livewire.admin.content.team.manager', [
            'rows' => $rows,
            'perPage' => $perPage,
        ]);
    }
}
