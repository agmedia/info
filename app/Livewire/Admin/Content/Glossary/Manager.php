<?php

namespace App\Livewire\Admin\Content\Glossary;

use App\Models\Content\Glossary\GlossaryTerm;
use App\Services\Content\GlossaryImportService;
use App\Services\Settings\SystemSettingsService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Manager extends Component
{
    use WithPagination;

    public string $search = '';
    public string $locale = 'en';
    public string $collection = 'all';
    public string $state = 'active';

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

    public function updatedCollection(): void
    {
        $this->resetPage();
    }

    public function updatedState(): void
    {
        $this->resetPage();
    }

    public function delete(int $termId): void
    {
        $term = GlossaryTerm::query()->find($termId);
        if (! $term) {
            $this->dispatch('notify', type: 'danger', message: (string) __('admin.content.glossary.manager.notify_not_found'));
            return;
        }

        DB::transaction(function () use ($term): void {
            $term->translations()->delete();
            $term->delete();
        });

        $this->dispatch('notify', type: 'success', message: (string) __('admin.content.glossary.manager.notify_deleted'));
        $this->resetPage();
    }

    public function getCollectionOptionsProperty(): Collection
    {
        return GlossaryTerm::query()
            ->select('collection_code')
            ->whereNotNull('collection_code')
            ->distinct()
            ->orderBy('collection_code')
            ->pluck('collection_code')
            ->prepend(GlossaryImportService::DEFAULT_COLLECTION)
            ->unique()
            ->values();
    }

    public function render()
    {
        $perPage = app(SystemSettingsService::class)->getInt(
            'admin_items_per_page',
            (int) config('admin_ui.pagination.admin_items_per_page', 20),
            5,
            200
        );

        $rows = GlossaryTerm::query()
            ->with([
                'translations' => fn ($query) => $query->where('locale', $this->locale),
            ])
            ->when($this->collection !== 'all', function ($query): void {
                $query->where('collection_code', $this->collection);
            })
            ->when($this->state === 'active', function ($query): void {
                $query->where('is_active', true);
            })
            ->when($this->state === 'inactive', function ($query): void {
                $query->where('is_active', false);
            })
            ->when($this->search !== '', function ($query): void {
                $query->where(function ($nestedQuery): void {
                    $nestedQuery->where('code', 'like', '%'.$this->search.'%')
                        ->orWhere('collection_code', 'like', '%'.$this->search.'%')
                        ->orWhereHas('translations', function ($translationQuery): void {
                            $translationQuery->where('title', 'like', '%'.$this->search.'%')
                                ->orWhere('slug', 'like', '%'.$this->search.'%')
                                ->orWhere('excerpt', 'like', '%'.$this->search.'%');
                        });
                });
            })
            ->orderBy('collection_code')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate($perPage);

        return view('livewire.admin.content.glossary.manager', [
            'rows' => $rows,
            'perPage' => $perPage,
        ]);
    }
}
