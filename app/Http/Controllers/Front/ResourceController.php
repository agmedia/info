<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Front\Concerns\ResolvesFrontendView;
use App\Models\Content\Resource\ResourceDocument;
use App\Models\Content\Resource\ResourceDocumentTranslation;
use App\Models\Content\Resource\ResourceDownloadRequest;
use App\Services\Front\StoreNotificationService;
use App\Services\Front\StoreSettingsService;
use App\Support\Content\ResourceDocumentGroupRegistry;
use App\Support\Localization\FrontendLocalePolicy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ResourceController extends Controller
{
    use ResolvesFrontendView;

    public function __construct(
        private readonly StoreNotificationService $notifications,
        private readonly StoreSettingsService $storeSettings
    ) {}

    public function index(Request $request): View
    {
        $locale = app()->getLocale();
        $fallbackLocale = $this->fallbackLocale((string) $locale);
        $requiresExactTranslation = FrontendLocalePolicy::requiresExactTranslation((string) $locale);

        if ($requiresExactTranslation) {
            $hasLocalizedDocuments = $this->baseDocumentQuery()
                ->whereHas('translations', fn ($query) => $query->where('locale', $locale))
                ->exists();

            abort_unless($hasLocalizedDocuments, 404);
        }

        $documents = $this->baseDocumentQuery()
            ->when(
                $requiresExactTranslation,
                fn ($query) => $query->whereHas('translations', fn ($translationQuery) => $translationQuery->where('locale', $locale))
            )
            ->with(['translations' => fn ($query) => $query->whereIn('locale', [$locale, $fallbackLocale])])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (ResourceDocument $document): ?array => $this->mapDocument($document, $locale, $fallbackLocale))
            ->filter()
            ->values();

        $groups = collect(ResourceDocumentGroupRegistry::labels())
            ->map(function (string $label, string $groupCode) use ($documents): ?array {
                $items = $documents->where('group_code', $groupCode)->values();

                if ($items->isEmpty()) {
                    return null;
                }

                return [
                    'code' => $groupCode,
                    'label' => $label,
                    'description' => __('resources.groups.'.$groupCode.'.description'),
                    'items' => $items,
                ];
            })
            ->filter()
            ->sortBy(fn (array $group): string => mb_strtolower((string) ($group['label'] ?? '')))
            ->values();

        return view($this->frontendView($request, 'resources.index'), [
            'documents' => $documents,
            'groups' => $groups,
        ]);
    }

    public function show(Request $request, string $slug): View
    {
        $locale = app()->getLocale();
        $fallbackLocale = $this->fallbackLocale((string) $locale);
        $document = $this->findDocumentBySlug($slug, $locale, $fallbackLocale);
        abort_if(! $document, 404);

        $documentData = $this->mapDocument($document, $locale, $fallbackLocale, $slug);
        abort_if($documentData === null, 404);

        $related = $this->baseDocumentQuery()
            ->whereKeyNot($document->getKey())
            ->where('group_code', $document->group_code)
            ->when(
                FrontendLocalePolicy::requiresExactTranslation((string) $locale),
                fn ($query) => $query->whereHas('translations', fn ($translationQuery) => $translationQuery->where('locale', $locale))
            )
            ->with(['translations' => fn ($query) => $query->whereIn('locale', [$locale, $fallbackLocale])])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->limit(3)
            ->get()
            ->map(fn (ResourceDocument $row): ?array => $this->mapDocument($row, $locale, $fallbackLocale))
            ->filter()
            ->values();

        return view($this->frontendView($request, 'resources.show'), [
            'document' => $documentData,
            'relatedDocuments' => $related,
            'captchaEnabled' => $this->captchaEnabled(),
            'captchaSiteKey' => trim((string) ($this->storeSettings->captcha()['recaptcha_v3_site_key'] ?? '')),
        ]);
    }

    public function store(Request $request, string $slug): RedirectResponse
    {
        $locale = app()->getLocale();
        $fallbackLocale = $this->fallbackLocale((string) $locale);
        $document = $this->findDocumentBySlug($slug, $locale, $fallbackLocale);
        abort_if(! $document, 404);

        $documentData = $this->mapDocument($document, $locale, $fallbackLocale, $slug);
        abort_if($documentData === null, 404);

        if (! $documentData['download_available']) {
            throw ValidationException::withMessages([
                'resource' => __('resources.form.download_unavailable'),
            ]);
        }

        $captchaSettings = $this->storeSettings->captcha();
        $captchaEnabled = $this->captchaEnabled();

        $validated = $request->validate(
            [
                'name' => ['required', 'string', 'max:191'],
                'company' => ['nullable', 'string', 'max:191'],
                'email' => ['required', 'email', 'max:191'],
                'phone' => ['nullable', 'string', 'max:80'],
                'accept_terms' => ['accepted'],
                'recaptcha_token' => [$captchaEnabled ? 'required' : 'nullable', 'string', 'max:4096'],
            ],
            [
                'required' => __('resources.validation.required'),
                'email' => __('resources.validation.email'),
                'accepted' => __('resources.validation.accepted'),
                'max.string' => __('resources.validation.max_string'),
            ],
            [
                'name' => __('resources.form.name'),
                'company' => __('resources.form.company'),
                'email' => __('resources.form.email'),
                'phone' => __('resources.form.phone'),
                'accept_terms' => __('resources.form.accept_terms'),
                'recaptcha_token' => __('resources.validation.security_check'),
            ]
        );

        if ($captchaEnabled) {
            $this->assertRecaptchaIsValid(
                token: (string) ($validated['recaptcha_token'] ?? ''),
                secret: (string) $captchaSettings['recaptcha_v3_secret_key'],
                minScore: (float) ($captchaSettings['recaptcha_v3_min_score'] ?? 0.5),
                expectedAction: 'resource_download_request',
                ip: (string) $request->ip()
            );
        }

        $downloadRequest = ResourceDownloadRequest::query()->create([
            'user_id' => $request->user()?->id,
            'document_id' => $document->getKey(),
            'document_code' => $documentData['code'],
            'document_title' => $documentData['title'],
            'document_slug' => $documentData['slug'],
            'document_group_code' => $documentData['group_code'],
            'document_download_url' => $documentData['download_url'],
            'name' => trim((string) $validated['name']),
            'email' => trim((string) $validated['email']),
            'phone' => trim((string) ($validated['phone'] ?? '')) ?: null,
            'company' => trim((string) ($validated['company'] ?? '')) ?: null,
            'status' => ResourceDownloadRequest::STATUS_NEW,
            'locale' => $locale,
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'payload' => [
                'source_url' => $request->fullUrl(),
            ],
        ]);

        $this->notifications->sendResourceDownloadLink($downloadRequest);

        return redirect()
            ->to(route('resources.show', ['slug' => $documentData['slug']]).'#resource-request-form')
            ->with('status', __('resources.form.sent_status', ['title' => $documentData['title']]));
    }

    private function baseDocumentQuery()
    {
        return ResourceDocument::query()
            ->where('is_active', true)
            ->where(function ($query): void {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }

    private function findDocumentBySlug(string $slug, string $locale, string $fallbackLocale): ?ResourceDocument
    {
        return $this->baseDocumentQuery()
            ->whereHas('translations', function ($query) use ($slug, $locale, $fallbackLocale): void {
                $query->where('slug', $slug)
                    ->whereIn('locale', array_values(array_unique([$locale, $fallbackLocale])));
            })
            ->with(['translations' => fn ($query) => $query->whereIn('locale', [$locale, $fallbackLocale])])
            ->get()
            ->sortBy(fn (ResourceDocument $document): int => $this->matchScore($document, $slug, $locale, $fallbackLocale))
            ->first();
    }

    private function fallbackLocale(string $locale): string
    {
        return FrontendLocalePolicy::fallbackLocale(
            $locale,
            (string) config('app.fallback_locale', config('app.locale', 'en'))
        );
    }

    private function mapDocument(
        ResourceDocument $document,
        string $locale,
        string $fallbackLocale,
        ?string $slug = null
    ): ?array {
        $translation = $this->pickTranslation($document, $locale, $fallbackLocale, $slug);
        if (! $translation) {
            return null;
        }

        return [
            'id' => (int) $document->getKey(),
            'code' => (string) $document->code,
            'group_code' => (string) $document->group_code,
            'group_label' => ResourceDocumentGroupRegistry::label((string) $document->group_code),
            'title' => trim((string) $translation->title),
            'slug' => trim((string) $translation->slug),
            'excerpt' => trim((string) ($translation->excerpt ?? '')) ?: __('resources.groups.'.(string) $document->group_code.'.item_fallback'),
            'cover_image_url' => trim((string) ($document->cover_image_url ?? '')) ?: null,
            'download_url' => trim((string) ($document->download_url ?? '')) ?: null,
            'download_available' => trim((string) ($document->download_url ?? '')) !== '',
            'source_url' => trim((string) ($document->source_url ?? '')) ?: null,
            'published_at' => $document->published_at,
        ];
    }

    private function pickTranslation(
        ResourceDocument $document,
        string $locale,
        string $fallbackLocale,
        ?string $slug = null
    ): ?ResourceDocumentTranslation {
        /** @var Collection<int, ResourceDocumentTranslation> $translations */
        $translations = $document->translations;

        return $translations
            ->sortBy(function (ResourceDocumentTranslation $translation) use ($locale, $fallbackLocale, $slug): int {
                $translationLocale = (string) ($translation->locale ?? '');
                $translationSlug = (string) ($translation->slug ?? '');

                return match (true) {
                    $slug !== null && $translationLocale === $locale && $translationSlug === $slug => 0,
                    $slug !== null && $translationLocale === $fallbackLocale && $translationSlug === $slug => 1,
                    $translationLocale === $locale => 2,
                    $translationLocale === $fallbackLocale => 3,
                    $slug !== null && $translationSlug === $slug => 4,
                    default => 10,
                };
            })
            ->first();
    }

    private function matchScore(ResourceDocument $document, string $slug, string $locale, string $fallbackLocale): int
    {
        $translation = $this->pickTranslation($document, $locale, $fallbackLocale, $slug);
        if (! $translation) {
            return 99;
        }

        $translationLocale = (string) ($translation->locale ?? '');
        $translationSlug = (string) ($translation->slug ?? '');

        return match (true) {
            $translationLocale === $locale && $translationSlug === $slug => 0,
            $translationLocale === $fallbackLocale && $translationSlug === $slug => 1,
            $translationLocale === $locale => 2,
            $translationLocale === $fallbackLocale => 3,
            $translationSlug === $slug => 4,
            default => 10,
        };
    }

    private function captchaEnabled(): bool
    {
        $captchaSettings = $this->storeSettings->captcha();

        return (bool) ($captchaSettings['recaptcha_v3_enabled'] ?? false)
            && trim((string) ($captchaSettings['recaptcha_v3_site_key'] ?? '')) !== ''
            && trim((string) ($captchaSettings['recaptcha_v3_secret_key'] ?? '')) !== '';
    }

    private function assertRecaptchaIsValid(
        string $token,
        string $secret,
        float $minScore,
        string $expectedAction,
        string $ip
    ): void {
        $minScore = max(0.0, min(1.0, $minScore));

        try {
            $response = Http::asForm()
                ->timeout(8)
                ->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => $secret,
                    'response' => $token,
                    'remoteip' => $ip,
                ]);
        } catch (\Throwable) {
            throw ValidationException::withMessages([
                'recaptcha_token' => __('resources.captcha_failed'),
            ]);
        }

        if (! $response->ok()) {
            throw ValidationException::withMessages([
                'recaptcha_token' => __('resources.captcha_failed'),
            ]);
        }

        $json = $response->json();
        $success = (bool) ($json['success'] ?? false);
        $score = (float) ($json['score'] ?? 0.0);
        $action = (string) ($json['action'] ?? '');

        if (! $success || $score < $minScore || $action !== $expectedAction) {
            throw ValidationException::withMessages([
                'recaptcha_token' => __('resources.captcha_failed'),
            ]);
        }
    }
}
