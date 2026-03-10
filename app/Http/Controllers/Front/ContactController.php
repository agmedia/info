<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Front\Concerns\ResolvesFrontendView;
use App\Models\Content\Support\ContactMessage;
use App\Services\Front\StoreNotificationService;
use App\Services\Front\StoreSettingsService;
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
        private readonly StoreSettingsService $storeSettings
    ) {
    }

    public function create(Request $request): View
    {
        return view($this->frontendView($request, 'contact.create'));
    }

    public function store(Request $request): RedirectResponse
    {
        $captchaSettings = $this->storeSettings->captcha();
        $captchaEnabled = (bool) ($captchaSettings['recaptcha_v3_enabled'] ?? false)
            && trim((string) ($captchaSettings['recaptcha_v3_site_key'] ?? '')) !== ''
            && trim((string) ($captchaSettings['recaptcha_v3_secret_key'] ?? '')) !== '';
        $redirectTo = $this->safeRedirectTarget((string) $request->input('redirect_to', ''));

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
                'first_name' => app()->getLocale() === 'hr' ? 'Ime' : 'First name',
                'last_name' => app()->getLocale() === 'hr' ? 'Prezime' : 'Last name',
                'company' => app()->getLocale() === 'hr' ? 'Tvrtka' : 'Company',
                'email' => __('contact.form.email'),
                'phone' => __('contact.form.phone'),
                'subject' => __('contact.form.subject'),
                'message' => __('contact.form.message'),
                'accept_terms' => __('contact.form.accept_terms'),
                'recaptcha_token' => __('contact.validation.security_check'),
                'redirect_to' => 'redirect target',
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
                    'attribute' => app()->getLocale() === 'hr' ? 'Ime' : 'First name',
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
                'locale' => app()->getLocale(),
                'url' => $request->fullUrl(),
                'first_name' => trim((string) ($validated['first_name'] ?? '')) ?: null,
                'last_name' => trim((string) ($validated['last_name'] ?? '')) ?: null,
                'company' => trim((string) ($validated['company'] ?? '')) ?: null,
            ],
        ]);
        $this->notifications->sendContactNotification($message);

        if ($redirectTo !== null) {
            return redirect($redirectTo)->with('status', __('contact.sent_status'));
        }

        return redirect()->route('contact.create')->with('status', __('contact.sent_status'));
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

        if (! $success || $score < $minScore || ($action !== '' && $action !== $expectedAction)) {
            throw ValidationException::withMessages([
                'recaptcha_token' => __('contact.captcha_failed'),
            ]);
        }
    }

    private function safeRedirectTarget(string $target): ?string
    {
        $target = trim($target);

        if ($target === '') {
            return null;
        }

        if (str_starts_with($target, '/')) {
            return $target;
        }

        $targetHost = parse_url($target, PHP_URL_HOST);
        $appHost = parse_url(config('app.url'), PHP_URL_HOST);

        if ($targetHost !== null && $appHost !== null && strcasecmp((string) $targetHost, (string) $appHost) === 0) {
            $path = (string) parse_url($target, PHP_URL_PATH);
            $query = (string) parse_url($target, PHP_URL_QUERY);
            $fragment = (string) parse_url($target, PHP_URL_FRAGMENT);

            return $path
                .($query !== '' ? '?'.$query : '')
                .($fragment !== '' ? '#'.$fragment : '');
        }

        return null;
    }
}
