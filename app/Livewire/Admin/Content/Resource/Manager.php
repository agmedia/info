<?php

namespace App\Livewire\Admin\Content\Resource;

use App\Models\Content\Resource\ResourceDocument;
use App\Services\Settings\SystemSettingsService;
use App\Support\Content\ResourceDocumentGroupRegistry;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Manager extends Component
{
    use WithPagination;

    public string $search = '';

    public string $locale = 'hr';

    public string $groupCode = '';

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

    public function updatedGroupCode(): void
    {
        $this->resetPage();
    }

    public function delete(int $documentId): void
    {
        $document = ResourceDocument::query()->find($documentId);
        if (! $document) {
            $this->dispatch('notify', type: 'danger', message: __('admin.content.resources.manager.notify_not_found'));

            return;
        }

        DB::transaction(function () use ($document): void {
            $document->translations()->delete();
            $document->delete();
        });

        $this->dispatch('notify', type: 'success', message: __('admin.content.resources.manager.notify_deleted'));
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

        $rows = ResourceDocument::query()
            ->with([
                'translations' => fn ($query) => $query->where('locale', $this->locale),
            ])
            ->when($this->groupCode !== '', fn ($query) => $query->where('group_code', $this->groupCode))
            ->when($this->search !== '', function ($query): void {
                $query->where(function ($nested): void {
                    $nested->where('code', 'like', '%'.$this->search.'%')
                        ->orWhere('group_code', 'like', '%'.$this->search.'%')
                        ->orWhere('download_url', 'like', '%'.$this->search.'%')
                        ->orWhereHas('translations', function ($translations): void {
                            $translations->where('title', 'like', '%'.$this->search.'%')
                                ->orWhere('slug', 'like', '%'.$this->search.'%')
                                ->orWhere('excerpt', 'like', '%'.$this->search.'%');
                        });
                });
            })
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate($perPage);

        return view('livewire.admin.content.resource.manager', [
            'rows' => $rows,
            'perPage' => $perPage,
            'groupLabels' => ResourceDocumentGroupRegistry::labels(),
        ]);
    }
}
