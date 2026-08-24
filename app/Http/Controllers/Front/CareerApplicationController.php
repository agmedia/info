<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Content\Support\CareerApplication;
use App\Services\Front\StoreSettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class CareerApplicationController extends Controller
{
    public function __construct(
        private readonly StoreSettingsService $storeSettings
    ) {
    }

    public function store(Request $request): RedirectResponse
    {
        $captchaSettings = $this->storeSettings->captcha();
        $captchaEnabled = (bool) ($captchaSettings['recaptcha_v3_enabled'] ?? false)
            && trim((string) ($captchaSettings['recaptcha_v3_site_key'] ?? '')) !== ''
            && trim((string) ($captchaSettings['recaptcha_v3_secret_key'] ?? '')) !== '';

        $validated = $request->validate(
            [
                'first_name' => ['required', 'string', 'max:80'],
                'last_name' => ['required', 'string', 'max:120'],
                'email' => ['required', 'email', 'max:191'],
                'message' => ['nullable', 'string', 'max:8000'],
                'cv' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
                'accept_terms' => ['accepted'],
                'recaptcha_token' => [$captchaEnabled ? 'required' : 'nullable', 'string', 'max:4096'],
            ],
            [
                'required' => __('career.validation.required'),
                'email' => __('career.validation.email'),
                'accepted' => __('career.validation.accepted'),
                'mimes' => __('career.validation.mimes'),
                'max.file' => __('career.validation.max_file'),
                'max.string' => __('career.validation.max_string'),
            ],
            [
                'first_name' => __('career.form.first_name'),
                'last_name' => __('career.form.last_name'),
                'email' => __('career.form.email'),
                'message' => __('career.form.message'),
                'cv' => __('career.form.cv'),
                'accept_terms' => __('career.form.accept_terms'),
                'recaptcha_token' => __('career.validation.security_check'),
            ]
        );

        if ($captchaEnabled) {
            $this->assertRecaptchaIsValid(
                token: (string) ($validated['recaptcha_token'] ?? ''),
                secret: (string) $captchaSettings['recaptcha_v3_secret_key'],
                minScore: (float) ($captchaSettings['recaptcha_v3_min_score'] ?? 0.5),
                expectedAction: 'career_application_form',
                ip: (string) $request->ip()
            );
        }

        $upload = $request->file('cv');
        if (! $upload) {
            throw ValidationException::withMessages([
                'cv' => __('career.validation.required', ['attribute' => __('career.form.cv')]),
            ]);
        }

        $storedPath = $upload->store('career-applications/cv', CareerApplication::CV_DISK);

        CareerApplication::query()->create([
            'user_id' => $request->user()?->id,
            'first_name' => trim((string) $validated['first_name']),
            'last_name' => trim((string) $validated['last_name']),
            'email' => trim((string) $validated['email']),
            'message' => trim((string) ($validated['message'] ?? '')) ?: null,
            'cv_path' => $storedPath,
            'cv_disk' => CareerApplication::CV_DISK,
            'cv_original_name' => (string) $upload->getClientOriginalName(),
            'cv_mime_type' => (string) ($upload->getClientMimeType() ?? ''),
            'cv_size' => (int) ($upload->getSize() ?? 0),
            'status' => CareerApplication::STATUS_NEW,
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
        ]);

        return redirect()
            ->to(route('pages.show', ['slug' => 'karijera']).'#career-cta')
            ->with('status', __('career.sent_status'));
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
                'recaptcha_token' => __('career.captcha_failed'),
            ]);
        }

        if (! $response->ok()) {
            throw ValidationException::withMessages([
                'recaptcha_token' => __('career.captcha_failed'),
            ]);
        }

        $json = $response->json();
        $success = (bool) ($json['success'] ?? false);
        $score = (float) ($json['score'] ?? 0.0);
        $action = (string) ($json['action'] ?? '');

        if (! $success || $score < $minScore || $action !== $expectedAction) {
            throw ValidationException::withMessages([
                'recaptcha_token' => __('career.captcha_failed'),
            ]);
        }
    }
}
