<?php

namespace App\Livewire\Admin\Content\Resource;

use App\Models\Content\Resource\ResourceDocument;
use App\Models\Content\Resource\ResourceDocumentTranslation;
use App\Support\Content\ResourceDocumentGroupRegistry;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class Form extends Component
{
    use WithFileUploads;

    private const TAB_OPTIONS = ['content', 'media', 'seo'];

    public ?int $documentId = null;

    public string $activeTab = 'content';

    public ?TemporaryUploadedFile $downloadUpload = null;

    public ?TemporaryUploadedFile $coverImageUpload = null;

    public array $form = [
        'code' => '',
        'group_code' => ResourceDocumentGroupRegistry::DOWNLOADS,
        'is_active' => true,
        'published_at' => '',
        'sort_order' => 0,
        'download_url' => '',
        'cover_image_url' => '',
        'source_url' => '',
        'payload_text' => '',
        'locale' => 'hr',
        'title' => '',
        'slug' => '',
        'excerpt' => '',
        'meta_title' => '',
        'meta_description' => '',
        'translation_payload_text' => '',
    ];

    public function mount(?int $documentId = null): void
    {
        $this->form['locale'] = (string) (request()->query('locale') ?: app()->getLocale() ?: config('admin_ui.locale.default', 'hr'));

        if ($documentId) {
            $this->documentId = $documentId;
            $this->loadDocument();
        }
    }

    public function updatedFormLocale(): void
    {
        $this->loadTranslationForLocale();
    }

    public function generateSlug(): void
    {
        $title = trim((string) $this->form['title']);
        if ($title !== '') {
            $this->form['slug'] = Str::slug($title);
        }
    }

    public function setTab(string $tab): void
    {
        if (! in_array($tab, self::TAB_OPTIONS, true)) {
            return;
        }

        $this->activeTab = $tab;
    }

    public function save()
    {
        $validated = $this->validate($this->rules());
        $wasEditing = (bool) $this->documentId;

        $pagePayload = $this->decodeJsonField('form.payload_text');
        if ($pagePayload === false) {
            return null;
        }

        $translationPayload = $this->decodeJsonField('form.translation_payload_text');
        if ($translationPayload === false) {
            return null;
        }

        $downloadUrl = trim((string) ($validated['form']['download_url'] ?? '')) ?: null;
        if ($this->downloadUpload) {
            $downloadUrl = Storage::disk('public')->url(
                $this->downloadUpload->store('resource-documents/downloads', 'public')
            );
        }

        $coverImageUrl = trim((string) ($validated['form']['cover_image_url'] ?? '')) ?: null;
        if ($this->coverImageUpload) {
            $coverImageUrl = Storage::disk('public')->url(
                $this->coverImageUpload->store('resource-documents/covers', 'public')
            );
        }

        $userId = auth()->id();

        DB::transaction(function () use (
            $validated,
            $pagePayload,
            $translationPayload,
            $downloadUrl,
            $coverImageUrl,
            $userId,
            $wasEditing
        ): void {
            $documentData = [
                'code' => trim((string) $validated['form']['code']),
                'group_code' => trim((string) $validated['form']['group_code']),
                'is_active' => (bool) $validated['form']['is_active'],
                'published_at' => $validated['form']['published_at'] ?: null,
                'sort_order' => (int) $validated['form']['sort_order'],
                'download_url' => $downloadUrl,
                'cover_image_url' => $coverImageUrl,
                'source_url' => trim((string) ($validated['form']['source_url'] ?? '')) ?: null,
                'payload' => $pagePayload,
                'updated_by' => $userId,
            ];

            if ($this->documentId) {
                $document = ResourceDocument::query()->findOrFail($this->documentId);
                $document->fill($documentData)->save();
            } else {
                $document = ResourceDocument::query()->create($documentData + ['created_by' => $userId]);
                $this->documentId = (int) $document->id;
            }

            $document->translations()->updateOrCreate(
                ['locale' => $validated['form']['locale']],
                [
                    'title' => trim((string) $validated['form']['title']),
                    'slug' => trim((string) $validated['form']['slug']),
                    'excerpt' => trim((string) ($validated['form']['excerpt'] ?? '')) ?: null,
                    'meta_title' => trim((string) ($validated['form']['meta_title'] ?? '')) ?: null,
                    'meta_description' => trim((string) ($validated['form']['meta_description'] ?? '')) ?: null,
                    'payload' => $translationPayload,
                ]
            );

            activity('content_resource_documents')
                ->performedOn($document)
                ->causedBy(auth()->user())
                ->event($wasEditing ? 'updated' : 'created')
                ->withProperties([
                    'locale' => $validated['form']['locale'],
                    'slug' => $validated['form']['slug'],
                    'group_code' => $validated['form']['group_code'],
                ])
                ->log('Resource document saved');
        });

        $this->downloadUpload = null;
        $this->coverImageUpload = null;

        $message = $wasEditing
            ? __('admin.content.resources.form.notify_updated')
            : __('admin.content.resources.form.notify_created');

        return redirect()
            ->route('admin.content.resources.index', ['locale' => $this->form['locale']])
            ->with('notify', [
                'type' => 'success',
                'message' => $message,
            ]);
    }

    public function backToList()
    {
        return redirect()->route('admin.content.resources.index', ['locale' => $this->form['locale']]);
    }

    public function render()
    {
        return view('livewire.admin.content.resource.form', [
            'isEdit' => (bool) $this->documentId,
            'groupOptions' => ResourceDocumentGroupRegistry::labels(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(): array
    {
        return [
            'form.code' => ['required', 'string', 'max:120', Rule::unique('content_resource_documents', 'code')->ignore($this->documentId)],
            'form.group_code' => ['required', 'string', Rule::in(array_keys(ResourceDocumentGroupRegistry::labels()))],
            'form.is_active' => ['boolean'],
            'form.published_at' => ['nullable', 'date'],
            'form.sort_order' => ['nullable', 'integer', 'min:0'],
            'form.download_url' => ['nullable', 'string', 'max:2048'],
            'form.cover_image_url' => ['nullable', 'string', 'max:2048'],
            'form.source_url' => ['nullable', 'string', 'max:2048'],
            'form.payload_text' => ['nullable', 'string'],

            'form.locale' => ['required', 'string', 'max:12'],
            'form.title' => ['required', 'string', 'max:255'],
            'form.slug' => [
                'required',
                'string',
                'max:191',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('content_resource_document_translations', 'slug')
                    ->where(fn ($query) => $query->where('locale', $this->form['locale']))
                    ->ignore($this->documentId, 'document_id'),
            ],
            'form.excerpt' => ['nullable', 'string'],
            'form.meta_title' => ['nullable', 'string', 'max:255'],
            'form.meta_description' => ['nullable', 'string'],
            'form.translation_payload_text' => ['nullable', 'string'],

            'downloadUpload' => ['nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx', 'max:20480'],
            'coverImageUpload' => ['nullable', 'image', 'max:5120'],
        ];
    }

    private function loadDocument(): void
    {
        if (! $this->documentId) {
            return;
        }

        $document = ResourceDocument::query()
            ->with('translations')
            ->findOrFail($this->documentId);

        $preferredLocale = $this->form['locale'] ?: config('app.locale', 'hr');
        $fallbackLocale = (string) config('app.fallback_locale', $preferredLocale);

        $translation = $document->translations->firstWhere('locale', $preferredLocale)
            ?? $document->translations->firstWhere('locale', $fallbackLocale)
            ?? $document->translations->first();

        $this->form['code'] = $document->code;
        $this->form['group_code'] = $document->group_code;
        $this->form['is_active'] = (bool) $document->is_active;
        $this->form['published_at'] = $document->published_at?->format('Y-m-d\TH:i') ?? '';
        $this->form['sort_order'] = (int) $document->sort_order;
        $this->form['download_url'] = (string) ($document->download_url ?? '');
        $this->form['cover_image_url'] = (string) ($document->cover_image_url ?? '');
        $this->form['source_url'] = (string) ($document->source_url ?? '');
        $this->form['payload_text'] = $document->payload
            ? json_encode($document->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
            : '';

        if ($translation) {
            $this->form['locale'] = $translation->locale;
            $this->form['title'] = $translation->title;
            $this->form['slug'] = $translation->slug;
            $this->form['excerpt'] = $translation->excerpt ?? '';
            $this->form['meta_title'] = $translation->meta_title ?? '';
            $this->form['meta_description'] = $translation->meta_description ?? '';
            $this->form['translation_payload_text'] = $translation->payload
                ? json_encode($translation->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
                : '';
        }
    }

    private function loadTranslationForLocale(): void
    {
        if (! $this->documentId) {
            $this->clearTranslationFields();

            return;
        }

        $translation = ResourceDocumentTranslation::query()
            ->where('document_id', $this->documentId)
            ->where('locale', $this->form['locale'])
            ->first();

        if (! $translation) {
            $this->clearTranslationFields();

            return;
        }

        $this->form['title'] = $translation->title;
        $this->form['slug'] = $translation->slug;
        $this->form['excerpt'] = $translation->excerpt ?? '';
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
        $this->form['meta_title'] = '';
        $this->form['meta_description'] = '';
        $this->form['translation_payload_text'] = '';
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
