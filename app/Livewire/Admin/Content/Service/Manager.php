<?php

namespace App\Livewire\Admin\Content\Service;

use App\Models\Content\Service\ServicePage;
use App\Services\Settings\SystemSettingsService;
use App\Support\Content\ServicePageTemplateRegistry;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Manager extends Component
{
    use WithPagination;

    public string $search = '';

    public string $locale = 'en';

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

    public function delete(int $servicePageId): void
    {
        $servicePage = ServicePage::query()->find($servicePageId);
        if (! $servicePage) {
            $this->dispatch('notify', type: 'danger', message: __('admin.content.services.manager.notify_not_found'));

            return;
        }

        DB::transaction(function () use ($servicePage): void {
            $servicePage->translations()->delete();
            $servicePage->delete();
        });

        $this->dispatch('notify', type: 'success', message: __('admin.content.services.manager.notify_deleted'));
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

        $rows = ServicePage::query()
            ->with([
                'translations' => fn ($q) => $q->where('locale', $this->locale),
            ])
            ->when($this->search !== '', function ($query): void {
                $query->where(function ($q): void {
                    $q->where('code', 'like', '%'.$this->search.'%')
                        ->orWhere('template_key', 'like', '%'.$this->search.'%')
                        ->orWhereHas('translations', function ($tq): void {
                            $tq->where('title', 'like', '%'.$this->search.'%')
                                ->orWhere('slug', 'like', '%'.$this->search.'%');
                        });
                });
            })
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate($perPage);

        return view('livewire.admin.content.service.manager', [
            'rows' => $rows,
            'perPage' => $perPage,
            'templateLabels' => ServicePageTemplateRegistry::labels(),
        ]);
    }
}
