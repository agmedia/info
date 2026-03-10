<?php

namespace App\Livewire\Admin\User;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Silber\Bouncer\Database\Role;

class Form extends Component
{
    public int $userId;

    /**
     * @var array<string, mixed>
     */
    public array $form = [
        'name' => '',
        'email' => '',
        'role' => '',
        'email_verified' => true,
        'password' => '',
        'password_confirmation' => '',
        'profile' => [
            'first_name' => '',
            'last_name' => '',
            'phone' => '',
        ],
    ];

    public function mount(int $userId): void
    {
        $this->authorizeAccess();
        $this->userId = $userId;
        $this->loadUser();
    }

    public function save()
    {
        $validated = $this->validate($this->rules());
        $payload = $validated['form'];

        DB::transaction(function () use ($payload): void {
            $user = User::query()
                ->with(['roles:id,name,title', 'profile'])
                ->findOrFail($this->userId);
            $this->ensureCanManageTargetUser($user);

            $user->name = trim((string) $payload['name']);
            $user->email = trim((string) $payload['email']);
            $user->email_verified_at = (bool) $payload['email_verified'] ? ($user->email_verified_at ?: now()) : null;

            if (! empty($payload['password'])) {
                $user->password = (string) $payload['password'];
            }

            $user->save();

            $role = Role::query()->where('name', (string) $payload['role'])->firstOrFail();
            $user->roles()->sync([$role->id]);
            Bouncer::refreshFor($user);

            $profilePayload = $this->normalizeProfilePayload((array) ($payload['profile'] ?? []));
            $user->profile()->updateOrCreate([], $profilePayload);
            $user->addresses()->delete();

            activity('admin_users')
                ->performedOn($user)
                ->causedBy(auth()->user())
                ->event('updated')
                ->withProperties([
                    'role' => $role->name,
                    'email_verified' => (bool) $payload['email_verified'],
                    'profile' => $profilePayload,
                ])
                ->log('Admin user updated');
        });

        return redirect()
            ->route('admin.users')
            ->with('notify', [
                'type' => 'success',
                'message' => __('User updated.'),
            ]);
    }

    public function backToList()
    {
        return redirect()->route('admin.users');
    }

    public function render()
    {
        return view('livewire.admin.user.form', [
            'roles' => $this->assignableRoles(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(): array
    {
        return [
            'form.name' => ['required', 'string', 'max:255'],
            'form.email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->userId)],
            'form.role' => ['required', 'string', Rule::in($this->assignableRoleNames())],
            'form.email_verified' => ['boolean'],
            'form.password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'form.profile.first_name' => ['nullable', 'string', 'max:120'],
            'form.profile.last_name' => ['nullable', 'string', 'max:120'],
            'form.profile.phone' => ['nullable', 'string', 'max:80'],
        ];
    }

    private function loadUser(): void
    {
        $user = User::query()
            ->with(['roles:id,name,title', 'profile'])
            ->findOrFail($this->userId);
        $this->ensureCanManageTargetUser($user);

        $roleName = $this->resolvePrimaryRoleName($user->roles);

        $this->form['name'] = (string) $user->name;
        $this->form['email'] = (string) $user->email;
        $this->form['role'] = $roleName;
        $this->form['email_verified'] = (bool) $user->email_verified_at;
        $this->form['password'] = '';
        $this->form['password_confirmation'] = '';
        $this->form['profile'] = [
            'first_name' => (string) ($user->profile?->first_name ?? ''),
            'last_name' => (string) ($user->profile?->last_name ?? ''),
            'phone' => (string) ($user->profile?->phone ?? ''),
        ];
    }

    /**
     * @param  Collection<int, Role>  $roles
     */
    private function resolvePrimaryRoleName(Collection $roles): string
    {
        $primary = $roles
            ->reject(fn (Role $role): bool => $role->name === 'customer')
            ->sortBy('id')
            ->first();

        return (string) ($primary?->name ?: 'admin');
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function normalizeProfilePayload(array $payload): array
    {
        return [
            'first_name' => $this->nullableString($payload['first_name'] ?? null),
            'last_name' => $this->nullableString($payload['last_name'] ?? null),
            'phone' => $this->nullableString($payload['phone'] ?? null),
            'company' => null,
            'oib' => null,
            'birthday' => null,
            'gender' => null,
            'affiliate_name' => null,
            'bio' => null,
            'newsletter_opt_in' => false,
        ];
    }

    private function nullableString(mixed $value): ?string
    {
        $string = trim((string) ($value ?? ''));

        return $string !== '' ? $string : null;
    }

    private function authorizeAccess(): void
    {
        $user = auth()->user();
        abort_unless(
            $user && (Bouncer::is($user)->an('superadmin') || $user->can('users.profile.update')),
            403
        );
    }

    /**
     * @return Collection<int, Role>
     */
    private function assignableRoles(): Collection
    {
        return Role::query()
            ->where('name', '!=', 'customer')
            ->when(! $this->canAssignSuperadmin(), fn ($query) => $query->where('name', '!=', 'superadmin'))
            ->orderBy('name')
            ->get(['name', 'title']);
    }

    /**
     * @return array<int, string>
     */
    private function assignableRoleNames(): array
    {
        return $this->assignableRoles()
            ->pluck('name')
            ->map(fn ($name): string => (string) $name)
            ->values()
            ->all();
    }

    private function canAssignSuperadmin(): bool
    {
        $current = auth()->user();

        return $current && Bouncer::is($current)->an('superadmin');
    }

    private function ensureCanManageTargetUser(User $user): void
    {
        if (! $this->canAssignSuperadmin() && $user->isA('superadmin')) {
            abort(403, 'Only superadmin can manage superadmin users.');
        }

        if (! $user->roles->contains(fn (Role $role): bool => $role->name !== 'customer')) {
            abort(404, 'Only admin users can be managed here.');
        }
    }
}
