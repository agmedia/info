<?php

namespace App\Livewire\Admin\User;

use App\Models\User;
use App\Services\Settings\SystemSettingsService;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Silber\Bouncer\Database\Role;

class Manager extends Component
{
    use WithPagination;

    private const PAGE_NAME = 'adminUsersPage';

    public string $search = '';

    public string $role = '';

    public string $sortBy = 'created_at';

    public string $sortDir = 'desc';

    public function mount(): void
    {
        $this->authorizeAccess();
    }

    public function updatedSearch(): void
    {
        $this->resetPage(pageName: self::PAGE_NAME);
    }

    public function updatedRole(): void
    {
        $this->resetPage(pageName: self::PAGE_NAME);
    }

    public function sort(string $field): void
    {
        $allowed = ['id', 'name', 'email', 'email_verified_at', 'created_at'];
        if (! in_array($field, $allowed, true)) {
            return;
        }

        if ($this->sortBy === $field) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDir = $field === 'name' || $field === 'email' ? 'asc' : 'desc';
        }

        $this->resetPage(pageName: self::PAGE_NAME);
    }

    public function render()
    {
        $settings = app(SystemSettingsService::class);
        $perPage = $settings->getInt(
            'admin_items_per_page',
            (int) config('admin_ui.pagination.admin_items_per_page', 20),
            5,
            200
        );

        $rows = User::query()
            ->with(['roles:id,name,title'])
            ->whereHas('roles', function (Builder $query): void {
                $query->where('name', '!=', 'customer');
            })
            ->when($this->search !== '', function (Builder $query): void {
                $query->where(function (Builder $q): void {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->role !== '', function (Builder $query): void {
                $query->whereHas('roles', fn (Builder $q) => $q->where('name', $this->role));
            })
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate($perPage, ['*'], self::PAGE_NAME);

        $roles = Role::query()
            ->where('name', '!=', 'customer')
            ->when(! $this->canSeeSuperadminRole(), fn ($query) => $query->where('name', '!=', 'superadmin'))
            ->orderBy('name')
            ->get(['name', 'title']);

        return view('livewire.admin.user.manager', [
            'rows' => $rows,
            'roles' => $roles,
            'perPage' => $perPage,
            'canCreateEditor' => $this->canCreateEditor(),
            'canEditUsers' => $this->canEditUsers(),
        ]);
    }

    private function authorizeAccess(): void
    {
        $user = auth()->user();
        abort_unless(
            $user && (Bouncer::is($user)->an('superadmin') || $user->can('users.list.view')),
            403
        );
    }

    private function canSeeSuperadminRole(): bool
    {
        $current = auth()->user();

        return $current && Bouncer::is($current)->an('superadmin');
    }

    private function canCreateEditor(): bool
    {
        $current = auth()->user();

        return $current && (Bouncer::is($current)->an('superadmin') || $current->can('users.editor.create'));
    }

    private function canEditUsers(): bool
    {
        $current = auth()->user();

        return $current && (Bouncer::is($current)->an('superadmin') || $current->can('users.profile.update'));
    }
}
