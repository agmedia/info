<?php

namespace App\Livewire\Admin\Content\Call;

use App\Models\Content\Call\CallPost;
use App\Services\Settings\SystemSettingsService;
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

    public function delete(int $postId): void
    {
        $post = CallPost::query()->find($postId);
        if (!$post) {
            return;
        }

        $post->delete();

        $this->dispatch('notify', type: 'success', message: __('Call post deleted.'));
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

        $rows = CallPost::query()
            ->withCount('categories')
            ->with([
                'translations' => fn ($q) => $q->where('locale', $this->locale),
                'media',
            ])
            ->when($this->search !== '', function ($query): void {
                $query->where(function ($q): void {
                    $q->where('code', 'like', '%'.$this->search.'%')
                        ->orWhereHas('translations', function ($tq): void {
                            $tq->where('title', 'like', '%'.$this->search.'%')
                                ->orWhere('slug', 'like', '%'.$this->search.'%');
                        });
                });
            })
            ->orderByDesc('published_at')
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate($perPage);

        return view('livewire.admin.content.call.manager', [
            'rows' => $rows,
            'perPage' => $perPage,
        ]);
    }
}
