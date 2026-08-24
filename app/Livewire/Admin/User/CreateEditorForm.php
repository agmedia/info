<?php

namespace App\Livewire\Admin\User;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Silber\Bouncer\Database\Role;

class CreateEditorForm extends Component
{
    /**
     * @var array<string, mixed>
     */
    public array $form = [
        'name' => '',
        'email' => '',
        'email_verified' => true,
        'password' => '',
        'password_confirmation' => '',
        'profile' => [
            'first_name' => '',
            'last_name' => '',
            'phone' => '',
        ],
    ];

    public function mount(): void
    {
        $this->authorizeAccess();
    }

    public function save()
    {
        $validated = $this->validate($this->rules());
        $payload = $validated['form'];

        DB::transaction(function () use ($payload): void {
            $user = new User;
            $user->name = trim((string) $payload['name']);
            $user->email = trim((string) $payload['email']);
            $user->email_verified_at = (bool) $payload['email_verified'] ? now() : null;
            $user->password = (string) $payload['password'];
            $user->save();

            $editorRole = Role::query()->where('name', 'editor')->firstOrFail();
            $user->roles()->sync([$editorRole->id]);
            Bouncer::refreshFor($user);

            $profilePayload = $this->normalizeProfilePayload((array) ($payload['profile'] ?? []));
            $user->profile()->create($profilePayload);

            activity('admin_users')
                ->performedOn($user)
                ->causedBy(auth()->user())
                ->event('created')
                ->withProperties([
                    'role' => 'editor',
                    'email_verified' => (bool) $payload['email_verified'],
                    'profile' => $profilePayload,
                ])
                ->log('Editor user created');
        });

        return redirect()
            ->route('admin.users')
            ->with('notify', [
                'type' => 'success',
                'message' => __('Editor user created.'),
            ]);
    }

    public function backToList()
    {
        return redirect()->route('admin.users');
    }

    public function render()
    {
        return view('livewire.admin.user.create-editor-form');
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(): array
    {
        return [
            'form.name' => ['required', 'string', 'max:255'],
            'form.email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'form.email_verified' => ['boolean'],
            'form.password' => ['required', 'string', 'min:8', 'confirmed'],
            'form.profile.first_name' => ['nullable', 'string', 'max:120'],
            'form.profile.last_name' => ['nullable', 'string', 'max:120'],
            'form.profile.phone' => ['nullable', 'string', 'max:80'],
        ];
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
            $user && (Bouncer::is($user)->an('superadmin') || $user->can('users.editor.create')),
            403
        );
    }
}
