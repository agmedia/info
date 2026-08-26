<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Front\Concerns\ResolvesFrontendView;
use App\Models\Content\Support\ContactMessage;
use App\Services\Content\ContentBlockResolver;
use App\Services\Front\StoreNotificationService;
use App\Services\Front\StoreSettingsService;
use App\Support\Localization\FrontendLocalePolicy;
use App\Support\Localization\FrontendRoute;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ContactController extends Controller
{
    use ResolvesFrontendView;

    public function __construct(
        private readonly StoreNotificationService $notifications,
        private readonly StoreSettingsService $storeSettings,
        private readonly ContentBlockResolver $contentBlockResolver,
    ) {}

    public function create(Request $request): View
    {
        $payload = $this->resolveContactPayload($request);
        $this->abortIfStrictContactTranslationIsMissing($payload);

        return view($this->frontendView($request, 'contact.create'), [
            'contactPageContent' => (array) ($payload['contact_page'] ?? []),
            'contactLocationsContent' => (array) ($payload['locations'] ?? []),
            'contactLocationStats' => collect((array) ($payload['contact_stats'] ?? [])),
        ]);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $contactPayload = $this->resolveContactPayload($request);
        $this->abortIfStrictContactTranslationIsMissing($contactPayload);
        $captchaSettings = $this->storeSettings->captcha();
        $captchaEnabled = (bool) ($captchaSettings['recaptcha_v3_enabled'] ?? false)
            && trim((string) ($captchaSettings['recaptcha_v3_site_key'] ?? '')) !== ''
            && trim((string) ($captchaSettings['recaptcha_v3_secret_key'] ?? '')) !== '';
        $redirectTo = $this->safeRedirectTarget((string) $request->input('redirect_to', ''));

        if ($this->honeypotWasFilled($request)) {
            return $this->successfulSubmissionResponse($request, $contactPayload, $redirectTo);
        }

        $validated = $request->validate(
            [
                'name' => ['nullable', 'string', 'max:191'],
                'first_name' => ['required_without:name', 'nullable', 'string', 'max:80'],
                'last_name' => ['nullable', 'string', 'max:120'],
                'company' => ['nullable', 'string', 'max:191'],
                'email' => ['required', 'email', 'max:191'],
                'phone' => ['nullable', 'string', 'max:80'],
                'subject' => ['nullable', 'string', 'max:191'],
                'message' => ['required', 'string', 'min:10', 'max:8000'],
                'accept_terms' => ['accepted'],
                'recaptcha_token' => [$captchaEnabled ? 'required' : 'nullable', 'string', 'max:4096'],
                'redirect_to' => ['nullable', 'string', 'max:2048'],
            ],
            [
                'required' => __('contact.validation.required'),
                'email' => __('contact.validation.email'),
                'accepted' => __('contact.validation.accepted'),
                'min.string' => __('contact.validation.min_string'),
                'max.string' => __('contact.validation.max_string'),
            ],
            [
                'name' => __('contact.form.name'),
                'first_name' => __('contact.validation.attributes.first_name'),
                'last_name' => __('contact.validation.attributes.last_name'),
                'company' => __('contact.validation.attributes.company'),
                'email' => __('contact.form.email'),
                'phone' => __('contact.form.phone'),
                'subject' => __('contact.form.subject'),
                'message' => __('contact.form.message'),
                'accept_terms' => __('contact.form.accept_terms'),
                'recaptcha_token' => __('contact.validation.security_check'),
                'redirect_to' => __('contact.validation.attributes.redirect_to'),
            ]
        );

        $resolvedName = trim((string) ($validated['name'] ?? ''));

        if ($resolvedName === '') {
            $resolvedName = trim(implode(' ', array_filter([
                trim((string) ($validated['first_name'] ?? '')),
                trim((string) ($validated['last_name'] ?? '')),
            ])));
        }

        if ($resolvedName === '') {
            throw ValidationException::withMessages([
                'first_name' => __('contact.validation.required', [
                    'attribute' => __('contact.validation.attributes.first_name'),
                ]),
            ]);
        }

        if ($captchaEnabled) {
            $this->assertRecaptchaIsValid(
                token: (string) ($validated['recaptcha_token'] ?? ''),
                secret: (string) $captchaSettings['recaptcha_v3_secret_key'],
                minScore: (float) ($captchaSettings['recaptcha_v3_min_score'] ?? 0.5),
                expectedAction: 'contact_form',
                ip: (string) $request->ip()
            );
        }

        $sourcePage = $this->resolveSourcePage($redirectTo);

        $message = ContactMessage::query()->create([
            'user_id' => $request->user()?->id,
            'name' => $resolvedName,
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'subject' => trim((string) ($validated['subject'] ?? '')) !== ''
                ? (string) $validated['subject']
                : __('contact.form.default_subject'),
            'message' => $validated['message'],
            'status' => ContactMessage::STATUS_NEW,
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'payload' => [
                'form_type' => in_array($sourcePage, ['/kontakt', '/contact'], true)
                    ? ContactMessage::FORM_TYPE_CONTACT
                    : ContactMessage::FORM_TYPE_SERVICE_CONTACT,
                'locale' => app()->getLocale(),
                'url' => $request->fullUrl(),
                'source_page' => $sourcePage,
                'redirect_to' => $redirectTo,
                'first_name' => trim((string) ($validated['first_name'] ?? '')) ?: null,
                'last_name' => trim((string) ($validated['last_name'] ?? '')) ?: null,
                'company' => trim((string) ($validated['company'] ?? '')) ?: null,
            ],
        ]);
        $this->notifications->sendContactNotification($message);

        return $this->successfulSubmissionResponse($request, $contactPayload, $redirectTo);
    }

    /**
     * Return the normal success response for both accepted submissions and honeypot decoys.
     *
     * @param  array<string, mixed>  $contactPayload
     */
    private function successfulSubmissionResponse(
        Request $request,
        array $contactPayload,
        ?string $redirectTo,
    ): JsonResponse|RedirectResponse {
        $sentStatus = trim((string) data_get(
            $contactPayload,
            'contact_page.sent_status',
            '',
        )) ?: __('contact.sent_status');

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'message' => $sentStatus,
            ]);
        }

        if ($redirectTo !== null) {
            $response = redirect($redirectTo);

            return $sentStatus !== '' ? $response->with('status', $sentStatus) : $response;
        }

        $response = redirect()->to(FrontendRoute::url('contact.create'));

        return $sentStatus !== '' ? $response->with('status', $sentStatus) : $response;
    }

    private function honeypotWasFilled(Request $request): bool
    {
        $website = $request->input('website');

        return is_string($website)
            ? trim($website) !== ''
            : $website !== null;
    }

    /**
     * Resolve contact copy from the exact active-locale CMS translation only.
     *
     * @return array<string, mixed>
     */
    private function resolveContactPayload(Request $request): array
    {
        $locale = strtolower(trim((string) app()->getLocale()));
        $variant = (string) $request->attributes->get('frontend_variant', 'desktop');
        $statsItem = $this->contentBlockResolver
            ->forPlacement('home.stats', $locale, null, null, $variant)
            ->first(static fn (array $item): bool => (string) (($item['block'] ?? null)?->type ?? '') === 'home_stats');
        $translation = $statsItem['translation'] ?? null;

        if (strtolower(trim((string) ($translation?->locale ?? ''))) !== $locale) {
            return [];
        }

        return is_array($translation?->payload ?? null) ? $translation->payload : [];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function abortIfStrictContactTranslationIsMissing(array $payload): void
    {
        if (! FrontendLocalePolicy::requiresExactTranslation((string) app()->getLocale())) {
            return;
        }

        $contactPage = (array) ($payload['contact_page'] ?? []);
        abort_if(
            ! collect($contactPage)->contains(fn (mixed $value): bool => trim((string) $value) !== ''),
            404
        );
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
                'recaptcha_token' => __('contact.captcha_failed'),
            ]);
        }

        if (! $response->ok()) {
            throw ValidationException::withMessages([
                'recaptcha_token' => __('contact.captcha_failed'),
            ]);
        }

        $json = $response->json();
        $success = (bool) ($json['success'] ?? false);
        $score = (float) ($json['score'] ?? 0.0);
        $action = (string) ($json['action'] ?? '');

        if (! $success || $score < $minScore || $action !== $expectedAction) {
            throw ValidationException::withMessages([
                'recaptcha_token' => __('contact.captcha_failed'),
            ]);
        }
    }

    private function safeRedirectTarget(string $target): ?string
    {
        $target = trim($target);

        if ($target === '' || preg_match('/[\x00-\x1F\x7F]/', $target) === 1) {
            return null;
        }

        if ($this->isSafeLocalRedirect($target)) {
            return $target;
        }

        $targetHost = parse_url($target, PHP_URL_HOST);
        $appHost = parse_url(config('app.url'), PHP_URL_HOST);

        if ($targetHost !== null && $appHost !== null && strcasecmp((string) $targetHost, (string) $appHost) === 0) {
            $path = (string) parse_url($target, PHP_URL_PATH);
            $query = (string) parse_url($target, PHP_URL_QUERY);
            $fragment = (string) parse_url($target, PHP_URL_FRAGMENT);

            $localTarget = ($path !== '' ? $path : '/')
                .($query !== '' ? '?'.$query : '')
                .($fragment !== '' ? '#'.$fragment : '');

            return $this->isSafeLocalRedirect($localTarget) ? $localTarget : null;
        }

        return null;
    }

    private function isSafeLocalRedirect(string $target): bool
    {
        return preg_match('~^/(?![/\\\\])~', $target) === 1;
    }

    private function resolveSourcePage(?string $redirectTo): string
    {
        if ($redirectTo === null) {
            return $this->localizedContactPath();
        }

        $path = (string) parse_url($redirectTo, PHP_URL_PATH);

        return $path !== '' ? $path : $this->localizedContactPath();
    }

    private function localizedContactPath(): string
    {
        return (string) parse_url(FrontendRoute::url('contact.create'), PHP_URL_PATH);
    }
}
