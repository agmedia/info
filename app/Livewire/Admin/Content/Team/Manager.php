<?php

namespace App\Livewire\Admin\Content\Team;

use App\Models\Content\Page\InfoPage;
use App\Models\Content\Team\TeamMember;
use App\Services\Settings\SystemSettingsService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Manager extends Component
{
    use WithPagination;

    public string $search = '';

    public string $locale = 'hr';

    public array $pageSettings = [
        'intro' => '',
        'meta_title' => '',
        'meta_description' => '',
    ];

    public function mount(): void
    {
        $this->locale = (string) (request()->query('locale') ?: app()->getLocale() ?: config('admin_ui.locale.default', 'hr'));
        $this->loadPageSettings();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedLocale(): void
    {
        $this->resetPage();
        $this->resetValidation();
        $this->loadPageSettings();
    }

    public function savePageSettings(): void
    {
        $validated = $this->validate([
            'pageSettings.intro' => ['required', 'string', 'max:1000'],
            'pageSettings.meta_title' => ['nullable', 'string', 'max:255'],
            'pageSettings.meta_description' => ['nullable', 'string', 'max:1000'],
        ]);

        $defaults = $this->pageDefaults();
        $userId = auth()->id();

        DB::transaction(function () use ($validated, $defaults, $userId): void {
            $page = InfoPage::query()->firstOrNew(['code' => 'team-page']);
            $wasRecentlyCreated = ! $page->exists;

            $page->fill([
                'layout' => 'team',
                'is_active' => true,
                'show_in_footer' => false,
                'published_at' => $page->published_at ?: now(),
                'sort_order' => $page->sort_order ?: 30,
                'updated_by' => $userId,
            ]);

            if ($wasRecentlyCreated) {
                $page->created_by = $userId;
            }

            $page->save();

            $page->translations()->updateOrCreate(
                ['locale' => $this->locale],
                [
                    'title' => $defaults['title'],
                    'slug' => $defaults['slug'],
                    'excerpt' => trim((string) $validated['pageSettings']['intro']),
                    'meta_title' => $this->nullableString($validated['pageSettings']['meta_title'] ?? null),
                    'meta_description' => $this->nullableString($validated['pageSettings']['meta_description'] ?? null),
                ]
            );

            activity('content_team')
                ->performedOn($page)
                ->causedBy(auth()->user())
                ->event('updated')
                ->withProperties(['locale' => $this->locale])
                ->log('Team page settings saved');
        });

        $this->loadPageSettings();
        $this->dispatch(
            'notify',
            type: 'success',
            message: (string) __('admin.content.team.manager.page_settings.notify_saved')
        );
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

    private function loadPageSettings(): void
    {
        $defaults = $this->pageDefaults();
        $translation = InfoPage::query()
            ->where('code', 'team-page')
            ->first()
            ?->translation($this->locale)
            ->first();

        $this->pageSettings = [
            'intro' => trim((string) ($translation?->excerpt ?? '')) ?: $defaults['intro'],
            'meta_title' => trim((string) ($translation?->meta_title ?? '')),
            'meta_description' => trim((string) ($translation?->meta_description ?? '')),
        ];
    }

    /**
     * @return array{title: string, slug: string, intro: string}
     */
    private function pageDefaults(): array
    {
        $normalizedLocale = strtolower(trim($this->locale));
        $isCroatian = str_starts_with($normalizedLocale, 'hr');

        return [
            'title' => (string) __('ui.team.page_title', [], $this->locale),
            'slug' => $isCroatian
                ? 'alpha-capitalis-tim'
                : ($normalizedLocale === 'en' ? 'alpha-capitalis-team' : 'alpha-capitalis-team-'.$normalizedLocale),
            'intro' => (string) __('ui.team.subtitle', [], $this->locale),
        ];
    }

    private function nullableString(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value !== '' ? $value : null;
    }
}
