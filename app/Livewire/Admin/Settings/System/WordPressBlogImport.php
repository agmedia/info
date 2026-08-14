<?php

namespace App\Livewire\Admin\Settings\System;

use App\Models\Catalog\Category\Category;
use App\Models\Settings\Local\Language;
use App\Services\Content\WordPressBlogImportService;
use App\Services\Settings\SystemSettingsService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Silber\Bouncer\BouncerFacade as Bouncer;

class WordPressBlogImport extends Component
{
    use WithFileUploads;

    private const SETTING_LAST_XML_PATH = 'content_blog_import_last_xml_path';

    private const SETTING_LAST_XML_NAME = 'content_blog_import_last_xml_name';

    public ?TemporaryUploadedFile $xmlUpload = null;

    public ?string $storedXmlPath = null;

    public ?string $storedXmlName = null;

    public string $locale = 'hr';

    public string $categoryMode = 'single';

    public string $selectedCategoryId = '';

    public string $categoryName = 'Novosti';

    public string $categorySlug = 'novosti';

    public string $limit = '3';

    public string $offset = '0';

    public string $slugsText = '';

    /** @var array<int, array{id:int,code:string,name:string,slug:string}> */
    public array $categoryOptions = [];

    /** @var array<string, mixed>|null */
    public ?array $result = null;

    public ?string $errorMessage = null;

    public bool $categorySlugManuallyChanged = false;

    public function mount(): void
    {
        $this->authorizeAccess();

        $this->locale = $this->resolveDefaultLocale();
        $this->loadStoredImportState();
        $this->loadCategoryOptions();
        $this->applyDefaultCategorySelection();
    }

    public function updatedLocale(): void
    {
        $this->locale = $this->normalizeLocale($this->locale);
        $this->loadCategoryOptions();

        if ($this->selectedCategoryId !== '') {
            $this->applySelectedCategory($this->selectedCategoryId);
        }
    }

    public function updatedSelectedCategoryId(string $value): void
    {
        if ($value === '') {
            $this->categorySlugManuallyChanged = false;

            return;
        }

        $this->applySelectedCategory($value);
    }

    public function updatedCategoryName(string $value): void
    {
        if ($this->selectedCategoryId !== '' || $this->categorySlugManuallyChanged) {
            return;
        }

        $this->categorySlug = Str::slug($value);
    }

    public function updatedCategorySlug(string $value): void
    {
        $this->categorySlugManuallyChanged = trim($value) !== '';
    }

    public function import(WordPressBlogImportService $importer): void
    {
        $this->authorizeAccess();
        $this->resetValidation();
        $this->errorMessage = null;
        $this->result = null;

        $this->validate($this->rules());

        try {
            $filePath = $this->persistUploadedXml();
            $this->executeImport($importer, $filePath, true);
        } catch (\Throwable $exception) {
            $this->errorMessage = $exception->getMessage();
            $this->dispatch('notify', type: 'danger', message: $this->errorMessage);

            return;
        }
    }

    public function reimport(WordPressBlogImportService $importer): void
    {
        $this->authorizeAccess();
        $this->resetValidation();
        $this->errorMessage = null;
        $this->result = null;

        if (! is_string($this->storedXmlPath) || trim($this->storedXmlPath) === '' || ! Storage::disk('local')->exists($this->storedXmlPath)) {
            $this->clearStoredImportState();
            $this->errorMessage = __('Upload XML first, then you can reimport/update existing posts.');
            $this->dispatch('notify', type: 'warning', message: $this->errorMessage);

            return;
        }

        try {
            $this->executeImport($importer, Storage::disk('local')->path($this->storedXmlPath), false);
        } catch (\Throwable $exception) {
            $this->errorMessage = $exception->getMessage();
            $this->dispatch('notify', type: 'danger', message: $this->errorMessage);
        }
    }

    public function render()
    {
        return view('livewire.admin.settings.system.word-press-blog-import');
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'xmlUpload' => ['required', 'file', 'max:51200'],
            'locale' => ['required', 'string', 'max:12'],
            'categoryMode' => ['required', Rule::in(['single', 'source'])],
            'selectedCategoryId' => [
                'nullable',
                Rule::exists('categories', 'id')->where(fn ($query) => $query->where('scope', Category::SCOPE_BLOG)),
            ],
            'categoryName' => ['nullable', 'string', 'max:120'],
            'categorySlug' => ['nullable', 'string', 'max:120'],
            'limit' => ['required', 'integer', 'min:0', 'max:5000'],
            'offset' => ['required', 'integer', 'min:0', 'max:50000'],
            'slugsText' => ['nullable', 'string', 'max:5000'],
        ];
    }

    /**
     * @return array{
     *     limit:int,
     *     offset:int,
     *     locale:string,
     *     category_mode:string,
     *     category_name:string,
     *     category_slug:string,
     *     only_missing:bool,
     *     slugs:array<int,string>,
     *     user_id:int|null
     * }
     */
    private function buildImportOptions(bool $onlyMissing): array
    {
        $selectedCategory = $this->findCategoryOption($this->selectedCategoryId);
        $categoryName = trim((string) ($selectedCategory['name'] ?? $this->categoryName));
        $categorySlug = trim((string) ($selectedCategory['slug'] ?? $this->categorySlug));

        if ($categoryName === '') {
            $categoryName = 'Novosti';
        }

        if ($categorySlug === '') {
            $categorySlug = Str::slug($categoryName);
        }

        $slugs = collect(preg_split('/[\s,]+/', $this->slugsText) ?: [])
            ->map(static fn (string $slug): string => trim($slug))
            ->filter()
            ->values()
            ->all();

        return [
            'limit' => (int) $this->limit,
            'offset' => (int) $this->offset,
            'locale' => $this->normalizeLocale($this->locale),
            'category_mode' => $this->categoryMode,
            'category_name' => $categoryName,
            'category_slug' => $categorySlug !== '' ? $categorySlug : 'novosti',
            'only_missing' => $onlyMissing,
            'slugs' => $slugs,
            'user_id' => auth()->id(),
        ];
    }

    private function executeImport(WordPressBlogImportService $importer, string $filePath, bool $onlyMissing): void
    {
        $this->result = $importer->import($filePath, $this->buildImportOptions($onlyMissing));
        $this->loadCategoryOptions();

        if (($this->result['categories'][0]['id'] ?? null) !== null && $this->categoryMode === 'single') {
            $this->applySelectedCategory((string) $this->result['categories'][0]['id']);
        }

        $this->dispatch(
            'notify',
            type: 'success',
            message: __('Imported :count WordPress post(s); skipped :existing existing and :uncategorized Uncategorized-only.', [
                'count' => count((array) ($this->result['imported'] ?? [])),
                'existing' => (int) ($this->result['skipped_existing_count'] ?? 0),
                'uncategorized' => (int) ($this->result['skipped_uncategorized_count'] ?? 0),
            ])
        );
    }

    private function persistUploadedXml(): string
    {
        $filePath = $this->xmlUpload?->getRealPath() ?: $this->xmlUpload?->getPathname() ?: '';
        if ($filePath === '') {
            throw new \RuntimeException('XML upload is missing.');
        }

        $extension = $this->xmlUpload?->getClientOriginalExtension() ?: 'xml';
        $fileName = sprintf(
            '%s-%s.%s',
            now()->format('Ymd-His'),
            Str::random(10),
            trim($extension) !== '' ? $extension : 'xml'
        );

        $storedPath = $this->xmlUpload?->storeAs('wp-imports', $fileName, 'local');
        if (! is_string($storedPath) || trim($storedPath) === '') {
            throw new \RuntimeException('XML file could not be persisted for import.');
        }

        $this->storedXmlPath = $storedPath;
        $this->storedXmlName = $this->xmlUpload?->getClientOriginalName() ?: basename($storedPath);
        $this->persistStoredImportState();

        return Storage::disk('local')->path($storedPath);
    }

    private function loadStoredImportState(): void
    {
        $settings = app(SystemSettingsService::class);
        $storedPath = trim((string) $settings->get(self::SETTING_LAST_XML_PATH, ''));
        $storedName = trim((string) $settings->get(self::SETTING_LAST_XML_NAME, ''));

        if ($storedPath !== '' && Storage::disk('local')->exists($storedPath)) {
            $this->storedXmlPath = $storedPath;
            $this->storedXmlName = $storedName !== '' ? $storedName : basename($storedPath);

            return;
        }

        $this->storedXmlPath = null;
        $this->storedXmlName = null;

        if ($storedPath !== '' || $storedName !== '') {
            $this->clearStoredImportState();
        }
    }

    private function persistStoredImportState(): void
    {
        app(SystemSettingsService::class)->putMany([
            self::SETTING_LAST_XML_PATH => $this->storedXmlPath,
            self::SETTING_LAST_XML_NAME => $this->storedXmlName,
        ]);
    }

    private function clearStoredImportState(): void
    {
        $this->storedXmlPath = null;
        $this->storedXmlName = null;

        app(SystemSettingsService::class)->putMany([
            self::SETTING_LAST_XML_PATH => null,
            self::SETTING_LAST_XML_NAME => null,
        ]);
    }

    private function applyDefaultCategorySelection(): void
    {
        $preferred = collect($this->categoryOptions)->first(
            static fn (array $category): bool => in_array($category['slug'], ['novosti'], true) || $category['code'] === 'novosti'
        );

        if ($preferred === null) {
            return;
        }

        $this->applySelectedCategory((string) $preferred['id']);
    }

    private function applySelectedCategory(string $id): void
    {
        $category = $this->findCategoryOption($id);
        if ($category === null) {
            return;
        }

        $this->selectedCategoryId = (string) $category['id'];
        $this->categoryName = (string) $category['name'];
        $this->categorySlug = (string) $category['slug'];
        $this->categorySlugManuallyChanged = true;
    }

    private function findCategoryOption(string $id): ?array
    {
        /** @var array{id:int,code:string,name:string,slug:string}|null $category */
        $category = collect($this->categoryOptions)
            ->first(static fn (array $item): bool => (string) $item['id'] === $id);

        return $category;
    }

    private function loadCategoryOptions(): void
    {
        $locale = $this->normalizeLocale($this->locale);

        $this->categoryOptions = Category::query()
            ->where('scope', Category::SCOPE_BLOG)
            ->with('translations')
            ->orderBy('code')
            ->get()
            ->map(function (Category $category) use ($locale): array {
                $translations = $category->translations
                    ->where('scope', Category::SCOPE_BLOG)
                    ->values();

                $translation = $translations->firstWhere('locale', $locale) ?? $translations->first();

                return [
                    'id' => (int) $category->id,
                    'code' => (string) $category->code,
                    'name' => (string) ($translation?->name ?? Str::headline((string) $category->code)),
                    'slug' => (string) ($translation?->slug ?? $category->code),
                ];
            })
            ->values()
            ->all();
    }

    private function resolveDefaultLocale(): string
    {
        try {
            $language = Language::query()
                ->where('is_active', true)
                ->orderByDesc('is_default')
                ->orderBy('sort_order')
                ->value('code');

            if (is_string($language) && trim($language) !== '') {
                return $this->normalizeLocale($language);
            }
        } catch (\Throwable) {
            // Fall back to app locale if local language table is not ready.
        }

        return $this->normalizeLocale((string) config('app.locale', 'hr'));
    }

    private function normalizeLocale(string $locale): string
    {
        $normalized = Str::lower(trim($locale));

        if ($normalized === '') {
            return 'hr';
        }

        foreach (['_', '-'] as $separator) {
            if (str_contains($normalized, $separator)) {
                $normalized = (string) explode($separator, $normalized)[0];
            }
        }

        return $normalized !== '' ? $normalized : 'hr';
    }

    private function authorizeAccess(): void
    {
        $user = auth()->user();

        abort_unless(
            $user && (Bouncer::is($user)->an('superadmin') || $user->can('settings.system.store.manage')),
            403
        );
    }
}
