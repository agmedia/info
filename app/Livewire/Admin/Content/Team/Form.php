<?php

namespace App\Livewire\Admin\Content\Team;

use App\Models\Content\Team\TeamMember;
use App\Models\Content\Team\TeamMemberTranslation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Form extends Component
{
    private const CUSTOM_DEPARTMENT_OPTION = '__custom__';

    public ?int $memberId = null;
    public string $activeTab = 'content';
    public array $existingDepartments = [];
    public array $selectedDepartments = [];
    public string $departmentSelection = '';
    public string $customDepartment = '';

    public array $form = [
        'code' => '',
        'is_active' => true,
        'sort_order' => 0,
        'payload_text' => '',
        'locale' => 'hr',
        'name' => '',
        'position' => '',
        'departments' => '',
        'description_html' => '',
        'email' => '',
        'mobile_phone' => '',
        'facebook_url' => '',
        'twitter_url' => '',
        'linkedin_url' => '',
    ];

    public function mount(?int $memberId = null): void
    {
        $this->form['locale'] = (string) (request()->query('locale') ?: app()->getLocale() ?: config('admin_ui.locale.default', 'hr'));
        $this->hydrateDepartmentOptions();

        if ($memberId) {
            $this->memberId = $memberId;
            $this->loadMember();

            return;
        }

        $this->syncSelectedDepartmentsFromForm();
    }

    public function updatedFormLocale(): void
    {
        $this->loadTranslationForLocale();
    }

    public function updatedSelectedDepartments($value): void
    {
        $this->selectedDepartments = $this->normalizeDepartments(is_array($value) ? $value : []);
        $this->form['departments'] = $this->resolvedDepartments();
        $this->existingDepartments = $this->mergeDepartmentOptions($this->selectedDepartments);
    }

    public function updatedFormDepartments($value): void
    {
        $this->form['departments'] = (string) $value;
        $this->syncSelectedDepartmentsFromForm();
    }

    public function updatedDepartmentSelection(string $value): void
    {
        if ($value !== self::CUSTOM_DEPARTMENT_OPTION) {
            $this->customDepartment = '';
        }
    }

    public function addDepartment(): void
    {
        $department = $this->departmentSelection === self::CUSTOM_DEPARTMENT_OPTION
            ? trim($this->customDepartment)
            : trim($this->departmentSelection);

        if ($department === '') {
            return;
        }

        $this->selectedDepartments = $this->normalizeDepartments([
            ...$this->selectedDepartments,
            $department,
        ]);
        $this->form['departments'] = $this->resolvedDepartments();
        $this->existingDepartments = $this->mergeDepartmentOptions($this->selectedDepartments);
        $this->departmentSelection = '';
        $this->customDepartment = '';
    }

    public function removeDepartment(int $index): void
    {
        if (! array_key_exists($index, $this->selectedDepartments)) {
            return;
        }

        unset($this->selectedDepartments[$index]);
        $this->selectedDepartments = array_values($this->normalizeDepartments($this->selectedDepartments));
        $this->form['departments'] = $this->resolvedDepartments();
    }

    public function updatedFormName($value): void
    {
        if (trim((string) $this->form['code']) !== '') {
            return;
        }

        $base = Str::slug((string) $value);
        if ($base === '') {
            return;
        }

        $this->form['code'] = $this->uniqueCodeFromBase($base);
    }

    public function generateCode(): void
    {
        $base = Str::slug((string) $this->form['name']);
        if ($base === '') {
            return;
        }

        $this->form['code'] = $this->uniqueCodeFromBase($base);
    }

    public function setTab(string $tab): void
    {
        if (! in_array($tab, ['content', 'media'], true)) {
            return;
        }

        $this->activeTab = $tab;
    }

    public function save()
    {
        $this->form['departments'] = $this->resolvedDepartments();
        $validated = $this->validate($this->rules());
        $wasEditing = (bool) $this->memberId;

        $payload = $this->decodeJsonField('form.payload_text');
        if ($payload === false) {
            return null;
        }

        $userId = auth()->id();

        DB::transaction(function () use ($validated, $payload, $userId, $wasEditing): void {
            $memberData = [
                'code' => trim((string) $validated['form']['code']),
                'is_active' => (bool) $validated['form']['is_active'],
                'sort_order' => (int) $validated['form']['sort_order'],
                'email' => $this->nullableString($validated['form']['email'] ?? null),
                'mobile_phone' => $this->nullableString($validated['form']['mobile_phone'] ?? null),
                'facebook_url' => $this->nullableString($validated['form']['facebook_url'] ?? null),
                'twitter_url' => $this->nullableString($validated['form']['twitter_url'] ?? null),
                'linkedin_url' => $this->nullableString($validated['form']['linkedin_url'] ?? null),
                'payload' => $payload,
                'updated_by' => $userId,
            ];

            if ($this->memberId) {
                $member = TeamMember::query()->findOrFail($this->memberId);
                $member->fill($memberData)->save();
            } else {
                $member = TeamMember::query()->create($memberData + ['created_by' => $userId]);
                $this->memberId = (int) $member->id;
            }

            $member->translations()->updateOrCreate(
                ['locale' => $validated['form']['locale']],
                [
                    'name' => trim((string) $validated['form']['name']),
                    'position' => $this->nullableString($validated['form']['position'] ?? null),
                    'departments' => $this->nullableString($this->resolvedDepartments()),
                    'description_html' => $this->nullableString($validated['form']['description_html'] ?? null),
                ]
            );

            activity('content_team')
                ->performedOn($member)
                ->causedBy(auth()->user())
                ->event($wasEditing ? 'updated' : 'created')
                ->withProperties([
                    'locale' => $validated['form']['locale'],
                    'code' => $validated['form']['code'],
                    'email' => $validated['form']['email'] ?? null,
                ])
                ->log('Team member saved');
        });

        $message = $wasEditing
            ? (string) __('admin.content.team.form.notify_updated')
            : (string) __('admin.content.team.form.notify_created');

        return redirect()
            ->route('admin.content.team.index', ['locale' => $this->form['locale']])
            ->with('notify', [
                'type' => 'success',
                'message' => $message,
            ]);
    }

    public function backToList()
    {
        return redirect()->route('admin.content.team.index', ['locale' => $this->form['locale']]);
    }

    public function render()
    {
        return view('livewire.admin.content.team.form', [
            'isEdit' => (bool) $this->memberId,
            'departmentOptions' => $this->existingDepartments,
            'customDepartmentOptionValue' => self::CUSTOM_DEPARTMENT_OPTION,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(): array
    {
        return [
            'form.code' => ['required', 'string', 'max:120', Rule::unique('content_team_members', 'code')->ignore($this->memberId)],
            'form.is_active' => ['boolean'],
            'form.sort_order' => ['nullable', 'integer', 'min:0'],
            'form.payload_text' => ['nullable', 'string'],

            'form.locale' => ['required', 'string', 'max:12'],
            'form.name' => ['required', 'string', 'max:255'],
            'form.position' => ['nullable', 'string', 'max:255'],
            'form.departments' => ['nullable', 'string'],
            'form.description_html' => ['nullable', 'string'],
            'selectedDepartments' => ['nullable', 'array'],
            'selectedDepartments.*' => ['nullable', 'string', 'max:255'],

            'form.email' => ['nullable', 'email:rfc', 'max:190'],
            'form.mobile_phone' => ['nullable', 'string', 'max:80'],
            'form.facebook_url' => ['nullable', 'url', 'max:2048'],
            'form.twitter_url' => ['nullable', 'url', 'max:2048'],
            'form.linkedin_url' => ['nullable', 'url', 'max:2048'],
        ];
    }

    private function loadMember(): void
    {
        if (! $this->memberId) {
            return;
        }

        $member = TeamMember::query()
            ->with('translations')
            ->findOrFail($this->memberId);

        $preferredLocale = $this->form['locale'] ?: app()->getLocale();
        $translation = $member->translations->firstWhere('locale', $preferredLocale)
            ?? $member->translations->firstWhere('locale', config('app.locale', 'en'))
            ?? $member->translations->first();

        $this->form['code'] = (string) $member->code;
        $this->form['is_active'] = (bool) $member->is_active;
        $this->form['sort_order'] = (int) $member->sort_order;
        $this->form['payload_text'] = $member->payload
            ? json_encode($member->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
            : '';
        $this->form['email'] = (string) ($member->email ?? '');
        $this->form['mobile_phone'] = (string) ($member->mobile_phone ?? '');
        $this->form['facebook_url'] = (string) ($member->facebook_url ?? '');
        $this->form['twitter_url'] = (string) ($member->twitter_url ?? '');
        $this->form['linkedin_url'] = (string) ($member->linkedin_url ?? '');

        if ($translation) {
            $this->form['locale'] = (string) $translation->locale;
            $this->form['name'] = (string) $translation->name;
            $this->form['position'] = (string) ($translation->position ?? '');
            $this->form['departments'] = (string) ($translation->departments ?? '');
            $this->form['description_html'] = (string) ($translation->description_html ?? '');
        }

        $this->syncSelectedDepartmentsFromForm();
    }

    private function loadTranslationForLocale(): void
    {
        if (! $this->memberId) {
            $this->clearTranslationFields();

            return;
        }

        $translation = TeamMemberTranslation::query()
            ->where('team_member_id', $this->memberId)
            ->where('locale', $this->form['locale'])
            ->first();

        if (! $translation) {
            $this->clearTranslationFields();

            return;
        }

        $this->form['name'] = (string) $translation->name;
        $this->form['position'] = (string) ($translation->position ?? '');
        $this->form['departments'] = (string) ($translation->departments ?? '');
        $this->form['description_html'] = (string) ($translation->description_html ?? '');
        $this->syncSelectedDepartmentsFromForm();
    }

    private function clearTranslationFields(): void
    {
        $this->form['name'] = '';
        $this->form['position'] = '';
        $this->form['departments'] = '';
        $this->form['description_html'] = '';
        $this->selectedDepartments = [];
    }

    /**
     * @return array<mixed>|null|false
     */
    private function decodeJsonField(string $field): array|null|false
    {
        $value = trim((string) data_get($this, $field));
        if ($value === '') {
            return null;
        }

        $decoded = json_decode($value, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->addError($field, __('Invalid JSON payload.'));
            $this->dispatch('notify', type: 'danger', message: __('Invalid JSON payload.'));

            return false;
        }

        if (! is_array($decoded)) {
            $this->addError($field, __('JSON payload must decode to object/array.'));
            $this->dispatch('notify', type: 'danger', message: __('JSON payload must decode to object/array.'));

            return false;
        }

        return $decoded;
    }

    private function uniqueCodeFromBase(string $base): string
    {
        $cleanBase = trim($base) !== '' ? trim($base) : 'team-member';
        $code = $cleanBase;
        $suffix = 2;

        while (
            TeamMember::query()
                ->when($this->memberId, fn ($q) => $q->where('id', '!=', $this->memberId))
                ->where('code', $code)
                ->exists()
        ) {
            $code = $cleanBase.'-'.$suffix;
            $suffix++;
        }

        return $code;
    }

    private function nullableString(mixed $value): ?string
    {
        $text = trim((string) $value);

        return $text !== '' ? $text : null;
    }

    private function hydrateDepartmentOptions(): void
    {
        $departments = TeamMemberTranslation::query()
            ->pluck('departments')
            ->filter(fn ($value): bool => trim((string) $value) !== '')
            ->flatMap(fn ($value): array => $this->parseDepartments((string) $value))
            ->values()
            ->all();

        $this->existingDepartments = $this->mergeDepartmentOptions($departments);
    }

    private function syncSelectedDepartmentsFromForm(): void
    {
        $this->selectedDepartments = $this->parseDepartments((string) ($this->form['departments'] ?? ''));
        $this->existingDepartments = $this->mergeDepartmentOptions($this->selectedDepartments);
        $this->form['departments'] = $this->resolvedDepartments();
    }

    /**
     * @param  array<int, string>  $departments
     * @return array<int, string>
     */
    private function normalizeDepartments(array $departments): array
    {
        return collect($departments)
            ->map(fn ($department): string => trim((string) $department))
            ->filter()
            ->unique(fn (string $department): string => Str::lower($department))
            ->values()
            ->all();
    }

    /**
     * @return array<int, string>
     */
    private function parseDepartments(string $value): array
    {
        return $this->normalizeDepartments(
            preg_split('/[\r\n,]+/u', $value) ?: []
        );
    }

    /**
     * @param  array<int, string>  $departments
     * @return array<int, string>
     */
    private function mergeDepartmentOptions(array $departments): array
    {
        return collect([$this->existingDepartments, $departments])
            ->flatten(1)
            ->map(fn ($department): string => trim((string) $department))
            ->filter()
            ->unique(fn (string $department): string => Str::lower($department))
            ->sortBy(fn (string $department): string => Str::lower($department), SORT_NATURAL)
            ->values()
            ->all();
    }

    private function resolvedDepartments(): string
    {
        return implode("\n", $this->normalizeDepartments($this->selectedDepartments));
    }
}
