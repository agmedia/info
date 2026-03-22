<?php

namespace App\Livewire\Admin\Content\Glossary;

use App\Models\Content\Glossary\GlossaryTerm;
use App\Models\Content\Glossary\GlossaryTermTranslation;
use App\Services\Content\GlossaryImportService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Form extends Component
{
    private const CUSTOM_COLLECTION_OPTION = '__custom__';

    public ?int $termId = null;

    public array $existingCollectionCodes = [];

    public string $collectionCodeSelection = GlossaryImportService::DEFAULT_COLLECTION;

    public string $customCollectionCode = '';

    public array $form = [
        'code' => '',
        'collection_code' => GlossaryImportService::DEFAULT_COLLECTION,
        'is_active' => true,
        'sort_order' => 0,
        'payload_text' => '',
        'locale' => 'en',
        'title' => '',
        'slug' => '',
        'excerpt' => '',
        'body_html' => '',
        'meta_title' => '',
        'meta_description' => '',
        'translation_payload_text' => '',
    ];

    public function mount(?int $termId = null): void
    {
        $this->form['locale'] = (string) (request()->query('locale') ?: app()->getLocale() ?: config('admin_ui.locale.default', 'hr'));
        $this->hydrateCollectionOptions();

        if ($termId) {
            $this->termId = $termId;
            $this->loadTerm();
            return;
        }

        $this->syncCollectionSelectionFromForm();
    }

    public function updatedFormLocale(): void
    {
        $this->loadTranslationForLocale();
    }

    public function updatedCollectionCodeSelection(string $value): void
    {
        if ($value === self::CUSTOM_COLLECTION_OPTION) {
            $this->form['collection_code'] = $this->normalizeCollectionCode($this->customCollectionCode);
            return;
        }

        $this->form['collection_code'] = $this->normalizeCollectionCode($value);
        $this->customCollectionCode = '';
    }

    public function updatedCustomCollectionCode(string $value): void
    {
        $normalized = $this->normalizeCollectionCode($value);
        $this->customCollectionCode = $normalized;

        if ($this->collectionCodeSelection === self::CUSTOM_COLLECTION_OPTION) {
            $this->form['collection_code'] = $normalized;
        }
    }

    public function generateSlug(): void
    {
        $title = trim((string) $this->form['title']);
        if ($title !== '') {
            $this->form['slug'] = Str::slug($title);
        }
    }

    public function save()
    {
        $validated = $this->validate($this->rules());
        $wasEditing = (bool) $this->termId;

        $payload = $this->decodeJsonField('form.payload_text');
        if ($payload === false) {
            return null;
        }

        $translationPayload = $this->decodeJsonField('form.translation_payload_text');
        if ($translationPayload === false) {
            return null;
        }

        $userId = auth()->id();

        DB::transaction(function () use ($validated, $payload, $translationPayload, $userId, $wasEditing): void {
            $termData = [
                'code' => trim((string) $validated['form']['code']),
                'collection_code' => $this->resolvedCollectionCode(),
                'is_active' => (bool) $validated['form']['is_active'],
                'sort_order' => (int) $validated['form']['sort_order'],
                'payload' => $payload,
                'updated_by' => $userId,
            ];

            if ($this->termId) {
                $term = GlossaryTerm::query()->findOrFail($this->termId);
                $term->fill($termData)->save();
            } else {
                $term = GlossaryTerm::query()->create($termData + ['created_by' => $userId]);
                $this->termId = $term->id;
            }

            $term->translations()->updateOrCreate(
                ['locale' => $validated['form']['locale']],
                [
                    'title' => $validated['form']['title'],
                    'slug' => $validated['form']['slug'],
                    'excerpt' => $validated['form']['excerpt'] ?: null,
                    'body_html' => $validated['form']['body_html'] ?: null,
                    'meta_title' => $validated['form']['meta_title'] ?: null,
                    'meta_description' => $validated['form']['meta_description'] ?: null,
                    'payload' => $translationPayload,
                ]
            );

            activity('content_glossary')
                ->performedOn($term)
                ->causedBy(auth()->user())
                ->event($wasEditing ? 'updated' : 'created')
                ->withProperties([
                    'locale' => $validated['form']['locale'],
                    'slug' => $validated['form']['slug'],
                    'collection_code' => $termData['collection_code'],
                ])
                ->log('Glossary term saved');
        });

        $message = $wasEditing
            ? (string) __('admin.content.glossary.form.notify_updated')
            : (string) __('admin.content.glossary.form.notify_created');

        return redirect()
            ->route('admin.content.glossary.index', ['locale' => $this->form['locale']])
            ->with('notify', [
                'type' => 'success',
                'message' => $message,
            ]);
    }

    public function backToList()
    {
        return redirect()->route('admin.content.glossary.index', ['locale' => $this->form['locale']]);
    }

    public function render()
    {
        return view('livewire.admin.content.glossary.form', [
            'isEdit' => (bool) $this->termId,
            'glossaryCollectionOptions' => $this->existingCollectionCodes,
            'customCollectionOptionValue' => self::CUSTOM_COLLECTION_OPTION,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(): array
    {
        return [
            'form.code' => ['required', 'string', 'max:120', Rule::unique('content_glossary_terms', 'code')->ignore($this->termId)],
            'form.collection_code' => ['nullable', 'string', 'max:80'],
            'form.is_active' => ['boolean'],
            'form.sort_order' => ['nullable', 'integer', 'min:0'],
            'form.payload_text' => ['nullable', 'string'],
            'form.locale' => ['required', 'string', 'max:12'],
            'form.title' => ['required', 'string', 'max:255'],
            'form.slug' => [
                'required',
                'string',
                'max:191',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('content_glossary_term_translations', 'slug')
                    ->where(fn ($query) => $query->where('locale', $this->form['locale']))
                    ->ignore($this->termId, 'term_id'),
            ],
            'form.excerpt' => ['nullable', 'string'],
            'form.body_html' => ['nullable', 'string'],
            'form.meta_title' => ['nullable', 'string', 'max:255'],
            'form.meta_description' => ['nullable', 'string'],
            'form.translation_payload_text' => ['nullable', 'string'],
        ];
    }

    private function loadTerm(): void
    {
        if (! $this->termId) {
            return;
        }

        $term = GlossaryTerm::query()
            ->with('translations')
            ->findOrFail($this->termId);

        $preferredLocale = $this->form['locale'] ?: config('app.locale', 'en');
        $translation = $term->translations->firstWhere('locale', $preferredLocale)
            ?? $term->translations->firstWhere('locale', config('app.locale', 'en'))
            ?? $term->translations->first();

        $this->form['code'] = $term->code;
        $this->form['collection_code'] = $term->collection_code;
        $this->form['is_active'] = (bool) $term->is_active;
        $this->form['sort_order'] = (int) $term->sort_order;
        $this->form['payload_text'] = $term->payload
            ? json_encode($term->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
            : '';

        if ($translation) {
            $this->form['locale'] = $translation->locale;
            $this->form['title'] = $translation->title;
            $this->form['slug'] = $translation->slug;
            $this->form['excerpt'] = $translation->excerpt ?? '';
            $this->form['body_html'] = $translation->body_html ?? '';
            $this->form['meta_title'] = $translation->meta_title ?? '';
            $this->form['meta_description'] = $translation->meta_description ?? '';
            $this->form['translation_payload_text'] = $translation->payload
                ? json_encode($translation->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
                : '';
        }

        $this->hydrateCollectionOptions();
        $this->syncCollectionSelectionFromForm();
    }

    private function loadTranslationForLocale(): void
    {
        if (! $this->termId) {
            $this->clearTranslationFields();
            return;
        }

        $translation = GlossaryTermTranslation::query()
            ->where('term_id', $this->termId)
            ->where('locale', $this->form['locale'])
            ->first();

        if (! $translation) {
            $this->clearTranslationFields();
            return;
        }

        $this->form['title'] = $translation->title;
        $this->form['slug'] = $translation->slug;
        $this->form['excerpt'] = $translation->excerpt ?? '';
        $this->form['body_html'] = $translation->body_html ?? '';
        $this->form['meta_title'] = $translation->meta_title ?? '';
        $this->form['meta_description'] = $translation->meta_description ?? '';
        $this->form['translation_payload_text'] = $translation->payload
            ? json_encode($translation->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
            : '';
    }

    private function clearTranslationFields(): void
    {
        $this->form['title'] = '';
        $this->form['slug'] = '';
        $this->form['excerpt'] = '';
        $this->form['body_html'] = '';
        $this->form['meta_title'] = '';
        $this->form['meta_description'] = '';
        $this->form['translation_payload_text'] = '';
    }

    private function hydrateCollectionOptions(): void
    {
        $existingCollections = GlossaryTerm::query()
            ->select('collection_code')
            ->whereNotNull('collection_code')
            ->where('collection_code', '!=', '')
            ->distinct()
            ->orderBy('collection_code')
            ->pluck('collection_code')
            ->map(fn ($collectionCode): string => $this->normalizeCollectionCode((string) $collectionCode))
            ->filter(fn (string $collectionCode): bool => $collectionCode !== '')
            ->values()
            ->all();

        $currentCollectionCode = $this->normalizeCollectionCode((string) ($this->form['collection_code'] ?? ''));
        $fallbackCollections = [GlossaryImportService::DEFAULT_COLLECTION];

        $this->existingCollectionCodes = collect([...$fallbackCollections, ...$existingCollections, $currentCollectionCode])
            ->filter(fn (string $collectionCode): bool => $collectionCode !== '')
            ->unique()
            ->values()
            ->all();
    }

    private function syncCollectionSelectionFromForm(): void
    {
        $currentCollectionCode = $this->normalizeCollectionCode((string) ($this->form['collection_code'] ?? GlossaryImportService::DEFAULT_COLLECTION));
        $this->form['collection_code'] = $currentCollectionCode;

        if ($currentCollectionCode !== '' && in_array($currentCollectionCode, $this->existingCollectionCodes, true)) {
            $this->collectionCodeSelection = $currentCollectionCode;
            $this->customCollectionCode = '';
            return;
        }

        $this->collectionCodeSelection = self::CUSTOM_COLLECTION_OPTION;
        $this->customCollectionCode = $currentCollectionCode;
    }

    private function resolvedCollectionCode(): string
    {
        if ($this->collectionCodeSelection === self::CUSTOM_COLLECTION_OPTION) {
            return $this->normalizeCollectionCode($this->customCollectionCode) ?: GlossaryImportService::DEFAULT_COLLECTION;
        }

        return $this->normalizeCollectionCode($this->collectionCodeSelection) ?: GlossaryImportService::DEFAULT_COLLECTION;
    }

    private function normalizeCollectionCode(string $value): string
    {
        $normalized = Str::of($value)
            ->lower()
            ->ascii()
            ->replace('_', '-')
            ->replaceMatches('/[^a-z0-9\-]+/', '-')
            ->replaceMatches('/\-+/', '-')
            ->trim('-')
            ->value();

        return $normalized;
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
            $this->addError($field, (string) __('Invalid JSON payload.'));
            $this->dispatch('notify', type: 'danger', message: __('Invalid JSON payload.'));
            return false;
        }

        if (! is_array($decoded)) {
            $this->addError($field, (string) __('JSON payload must decode to object/array.'));
            $this->dispatch('notify', type: 'danger', message: __('JSON payload must decode to object/array.'));
            return false;
        }

        return $decoded;
    }
}
