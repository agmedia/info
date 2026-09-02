<?php

namespace App\Livewire\Admin\Settings\System;

use App\Models\Settings\Local\Language;
use App\Services\Content\EuFundsCallImportService;
use App\Services\Content\WordPressBlogImportService;
use App\Services\Settings\SystemSettingsService;
use App\Support\Content\EuFundsLinkedBlogPostRegistry;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Silber\Bouncer\BouncerFacade as Bouncer;

class WordPressCallImport extends Component
{
    use WithFileUploads;

    private const SETTING_LAST_XML_PATH = 'content_call_import_last_xml_path';

    private const SETTING_LAST_XML_NAME = 'content_call_import_last_xml_name';

    public ?TemporaryUploadedFile $xmlUpload = null;

    public ?string $storedXmlPath = null;

    public ?string $storedXmlName = null;

    public string $locale = 'hr';

    public string $limit = '0';

    public string $offset = '0';

    public bool $force = false;

    public bool $importLinkedBlogPosts = true;

    public int $linkedBlogPostTargetCount = 0;

    /** @var array<string, mixed>|null */
    public ?array $result = null;

    public ?string $errorMessage = null;

    public function mount(): void
    {
        $this->authorizeAccess();
        $this->locale = $this->resolveDefaultLocale();
        $this->linkedBlogPostTargetCount = count(EuFundsLinkedBlogPostRegistry::slugs('hr'));
        $this->loadStoredImportState();
    }

    public function import(
        EuFundsCallImportService $callImporter,
        WordPressBlogImportService $blogImporter
    ): void {
        $this->authorizeAccess();
        $this->resetValidation();
        $this->errorMessage = null;
        $this->result = null;

        $this->validate($this->rules());

        try {
            $filePath = $this->persistUploadedXml();
            $this->executeImport($callImporter, $blogImporter, $filePath);
        } catch (\Throwable $exception) {
            $this->errorMessage = $exception->getMessage();
            $this->dispatch('notify', type: 'danger', message: $this->errorMessage);
        }
    }

    public function reimport(
        EuFundsCallImportService $callImporter,
        WordPressBlogImportService $blogImporter
    ): void {
        $this->authorizeAccess();
        $this->resetValidation();
        $this->errorMessage = null;
        $this->result = null;

        if (! is_string($this->storedXmlPath) || trim($this->storedXmlPath) === '' || ! Storage::disk('local')->exists($this->storedXmlPath)) {
            $this->clearStoredImportState();
            $this->errorMessage = __('Upload XML first, then you can reimport/update existing call posts.');
            $this->dispatch('notify', type: 'warning', message: $this->errorMessage);

            return;
        }

        try {
            $this->executeImport(
                $callImporter,
                $blogImporter,
                Storage::disk('local')->path($this->storedXmlPath)
            );
        } catch (\Throwable $exception) {
            $this->errorMessage = $exception->getMessage();
            $this->dispatch('notify', type: 'danger', message: $this->errorMessage);
        }
    }

    public function render()
    {
        return view('livewire.admin.settings.system.word-press-call-import');
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'xmlUpload' => ['required', 'file', 'max:51200'],
            'locale' => ['required', 'string', 'max:12'],
            'limit' => ['required', 'integer', 'min:0', 'max:500'],
            'offset' => ['required', 'integer', 'min:0', 'max:500'],
            'force' => ['boolean'],
            'importLinkedBlogPosts' => ['boolean'],
        ];
    }

    /**
     * @return array{
     *     locale:string,
     *     limit:int,
     *     offset:int,
     *     force:bool,
     *     user_id:int|null
     * }
     */
    private function buildImportOptions(): array
    {
        return [
            'locale' => $this->normalizeLocale($this->locale),
            'limit' => (int) $this->limit,
            'offset' => (int) $this->offset,
            'force' => (bool) $this->force,
            'user_id' => auth()->id(),
        ];
    }

    private function executeImport(
        EuFundsCallImportService $callImporter,
        WordPressBlogImportService $blogImporter,
        string $filePath
    ): void {
        $options = $this->buildImportOptions();
        $linkedResult = $this->importLinkedBlogPosts
            ? $blogImporter->importEuFundsLinkedPosts($filePath, [
                'locale' => $options['locale'],
                'user_id' => $options['user_id'],
            ])
            : null;
        $this->result = $callImporter->import($filePath, $options);
        $this->result['linked_blog_posts'] = [
            'enabled' => $this->importLinkedBlogPosts,
            'target_count' => (int) ($linkedResult['requested_slug_count'] ?? $this->linkedBlogPostTargetCount),
            'imported_count' => count((array) ($linkedResult['imported'] ?? [])),
            'skipped_existing_count' => (int) ($linkedResult['skipped_existing_count'] ?? 0),
        ];

        $callCount = count((array) ($this->result['imported'] ?? []));
        $notification = $this->importLinkedBlogPosts
            ? __('Imported :count EU funds call item(s). Linked blog posts: :imported new, :existing already present.', [
                'count' => $callCount,
                'imported' => $this->result['linked_blog_posts']['imported_count'],
                'existing' => $this->result['linked_blog_posts']['skipped_existing_count'],
            ])
            : __('Imported :count EU funds call item(s). Linked blog posts were skipped.', ['count' => $callCount]);

        $this->dispatch(
            'notify',
            type: 'success',
            message: $notification
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
            $user && (Bouncer::is($user)->an('superadmin') || $user->can('settings.system.imports.manage')),
            403
        );
    }
}
