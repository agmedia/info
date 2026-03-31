<?php

namespace App\Livewire\Admin\Settings\System;

use App\Models\Settings\Local\Language;
use App\Services\Content\EuFundsCallImportService;
use App\Services\Settings\SystemSettingsService;
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

    /** @var array<string, mixed>|null */
    public ?array $result = null;

    public ?string $errorMessage = null;

    public function mount(): void
    {
        $this->authorizeAccess();
        $this->locale = $this->resolveDefaultLocale();
        $this->loadStoredImportState();
    }

    public function import(EuFundsCallImportService $importer): void
    {
        $this->authorizeAccess();
        $this->resetValidation();
        $this->errorMessage = null;
        $this->result = null;

        $this->validate($this->rules());

        try {
            $filePath = $this->persistUploadedXml();
            $this->executeImport($importer, $filePath);
        } catch (\Throwable $exception) {
            $this->errorMessage = $exception->getMessage();
            $this->dispatch('notify', type: 'danger', message: $this->errorMessage);
        }
    }

    public function reimport(EuFundsCallImportService $importer): void
    {
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
            $this->executeImport($importer, Storage::disk('local')->path($this->storedXmlPath));
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

    private function executeImport(EuFundsCallImportService $importer, string $filePath): void
    {
        $this->result = $importer->import($filePath, $this->buildImportOptions());

        $this->dispatch(
            'notify',
            type: 'success',
            message: __('Imported :count EU funds call item(s).', ['count' => count((array) ($this->result['imported'] ?? []))])
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
            $user && (Bouncer::is($user)->an('superadmin') || $user->can('settings.system.store.manage')),
            403
        );
    }
}
