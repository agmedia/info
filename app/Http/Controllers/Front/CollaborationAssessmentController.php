<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Front\Concerns\ResolvesFrontendView;
use App\Models\Content\Support\ContactMessage;
use App\Services\Front\StoreNotificationService;
use App\Services\Front\StoreSettingsService;
use App\Support\AssessmentQuestionnaire;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CollaborationAssessmentController extends Controller
{
    use ResolvesFrontendView;

    public function __construct(
        private readonly StoreNotificationService $notifications,
        private readonly StoreSettingsService $storeSettings
    ) {}

    public function create(Request $request): View
    {
        return view($this->frontendView($request, 'assessment.create'));
    }

    public function store(Request $request): RedirectResponse
    {
        $captchaSettings = $this->storeSettings->captcha();
        $captchaEnabled = (bool) ($captchaSettings['recaptcha_v3_enabled'] ?? false)
            && trim((string) ($captchaSettings['recaptcha_v3_site_key'] ?? '')) !== ''
            && trim((string) ($captchaSettings['recaptcha_v3_secret_key'] ?? '')) !== '';

        $submittedService = trim((string) $request->input('service', ''));
        if ($submittedService === '') {
            $submittedService = AssessmentQuestionnaire::SERVICE_ACCOUNTING;
            $request->merge(['service' => $submittedService]);
        }

        $validated = $request->validate(
            $this->validationRules($request, $submittedService, $captchaEnabled),
            [
                'required' => __('assessment.validation.required'),
                'email' => __('assessment.validation.email'),
                'accepted' => __('assessment.validation.accepted'),
                'max.string' => __('assessment.validation.max_string'),
                'max.file' => __('assessment.validation.max_file'),
                'max.array' => __('assessment.validation.max_array'),
                'mimes' => __('assessment.validation.mimes'),
                'date' => __('assessment.validation.date'),
                'numeric' => __('assessment.validation.numeric'),
                'between.numeric' => __('assessment.validation.between_numeric'),
                'in' => __('assessment.validation.in'),
            ],
            $this->validationAttributes()
        );

        if ($captchaEnabled) {
            $this->assertRecaptchaIsValid(
                token: (string) ($validated['recaptcha_token'] ?? ''),
                secret: (string) $captchaSettings['recaptcha_v3_secret_key'],
                minScore: (float) ($captchaSettings['recaptcha_v3_min_score'] ?? 0.5),
                expectedAction: 'collaboration_assessment_form',
                ip: (string) $request->ip()
            );
        }

        $service = AssessmentQuestionnaire::normalizeService((string) $validated['service']);
        $answers = ['service' => $service];

        foreach (AssessmentQuestionnaire::answerFields($service) as $field) {
            if (! array_key_exists($field, $validated)) {
                continue;
            }

            $value = $validated[$field];
            $answers[$field] = is_string($value) ? trim($value) : $value;
        }

        $attachments = $service === AssessmentQuestionnaire::SERVICE_ADVISORY
            ? $this->storeAttachments($request)
            : [];
        $identity = $this->contactIdentity($answers, $service);
        $company = AssessmentQuestionnaire::companyName($answers, $service);
        $payload = [
            'form_type' => ContactMessage::FORM_TYPE_COLLABORATION_ASSESSMENT,
            'service' => $service,
            'company' => $company,
            'locale' => app()->getLocale(),
            'url' => $request->fullUrl(),
            'answers' => $answers,
            'attachments' => $attachments,
        ];

        try {
            $message = ContactMessage::query()->create([
                'user_id' => $request->user()?->id,
                'name' => $identity['name'],
                'email' => $identity['email'],
                'phone' => $identity['phone'],
                'subject' => __('assessment.form.default_subject'),
                'message' => $this->buildSummary($answers, $attachments),
                'status' => ContactMessage::STATUS_NEW,
                'ip_address' => $request->ip(),
                'user_agent' => (string) $request->userAgent(),
                'payload' => $payload,
            ]);
        } catch (\Throwable $exception) {
            $this->deleteAttachments($attachments);

            throw $exception;
        }

        $this->notifications->sendContactNotification($message);

        $routeParameters = $service === AssessmentQuestionnaire::SERVICE_ACCOUNTING
            ? []
            : ['service' => $service];

        return redirect()
            ->route('assessment.create', $routeParameters)
            ->with('status', __('assessment.sent_status'));
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function validationRules(Request $request, string $service, bool $captchaEnabled): array
    {
        $accounting = $service === AssessmentQuestionnaire::SERVICE_ACCOUNTING;
        $audit = $service === AssessmentQuestionnaire::SERVICE_AUDIT;
        $advisory = $service === AssessmentQuestionnaire::SERVICE_ADVISORY;
        $advisoryService = trim((string) $request->input('advisory_service', ''));
        $differentTarget = $advisory && $request->input('advisory_target_relation') === 'different';
        $relatedCompanies = $differentTarget && $request->input('advisory_target_related_companies') === 'yes';

        return [
            'service' => ['required', Rule::in(AssessmentQuestionnaire::serviceKeys())],

            'company_name' => $this->scopedRules($accounting, ['required', 'string', 'max:191']),
            'company_oib' => $this->scopedRules($accounting, ['required', 'string', 'max:50']),
            'activity' => $this->scopedRules($accounting, ['required', 'string', 'max:191']),
            'contact_email' => $this->scopedRules($accounting, ['required', 'email', 'max:191']),
            'contact_phone' => $this->scopedRules($accounting, ['required', 'string', 'max:80']),
            'incoming_invoices_monthly' => $this->scopedRules($accounting, ['required', 'string', 'max:80']),
            'outgoing_invoices_monthly' => $this->scopedRules($accounting, ['required', 'string', 'max:80']),
            'bank_accounts_monthly' => $this->scopedRules($accounting, ['required', 'string', 'max:80']),
            'payroll_calculations_monthly' => $this->scopedRules($accounting, ['required', 'string', 'max:80']),
            'other_calculations_monthly' => $this->scopedRules($accounting, ['nullable', 'string', 'max:500']),
            'incoming_invoice_payments' => $this->scopedRules($accounting, ['nullable', 'string', 'max:500']),
            'inventory_bookkeeping' => $this->scopedRules($accounting, ['nullable', 'string', 'in:yes,no']),
            'travel_orders_monthly' => $this->scopedRules($accounting, ['nullable', 'string', 'max:80']),
            'cost_centers_tracking' => $this->scopedRules($accounting, ['nullable', 'string', 'in:yes,no']),
            'intrastat_obligation' => $this->scopedRules($accounting, ['nullable', 'string', 'max:500']),
            'audit_obligation' => $this->scopedRules($accounting, ['nullable', 'string', 'max:500']),
            'monthly_reporting' => $this->scopedRules($accounting, ['nullable', 'string', 'in:yes,no']),
            'vat_status' => $this->scopedRules($accounting, ['nullable', 'string', 'max:500']),
            'accounting_software' => $this->scopedRules($accounting, ['nullable', 'string', 'max:191']),
            'tax_issues' => $this->scopedRules($accounting, ['nullable', 'string', 'max:1000']),
            'document_delivery' => $this->scopedRules($accounting, ['nullable', 'string', 'max:500']),
            'additional_requirements' => $this->scopedRules($accounting, ['nullable', 'string', 'max:1500']),
            'potential_start_date' => $this->scopedRules($accounting, ['nullable', 'date']),

            'audit_company_name' => $this->scopedRules($audit, ['required', 'string', 'max:191']),
            'audit_company_oib' => $this->scopedRules($audit, ['required', 'string', 'max:50']),
            'audit_contact_email' => $this->scopedRules($audit, ['required', 'email', 'max:191']),
            'audit_contact_phone' => $this->scopedRules($audit, ['required', 'string', 'max:80']),
            'audit_message' => $this->scopedRules($audit, ['required', 'string', 'max:4000']),

            'advisory_contact_person' => $this->scopedRules($advisory, ['required', 'string', 'max:191']),
            'advisory_contact_email' => $this->scopedRules($advisory, ['required', 'email', 'max:191']),
            'advisory_contact_phone' => $this->scopedRules($advisory, ['required', 'string', 'max:80']),
            'advisory_quote_company_name' => $this->scopedRules($advisory, ['required', 'string', 'max:191']),
            'advisory_quote_company_activity' => $this->scopedRules($advisory, ['required', 'string', 'max:191']),
            'advisory_quote_company_revenue' => $this->scopedRules($advisory, ['required', Rule::in($this->optionKeys('revenue'))]),
            'advisory_quote_company_employees' => $this->scopedRules($advisory, ['required', Rule::in($this->optionKeys('employees'))]),
            'advisory_service' => $this->scopedRules($advisory, ['required', Rule::in($this->optionKeys('advisory_services'))]),
            'advisory_reason' => $this->scopedRules($advisory, ['required', 'string', 'max:2000']),
            'advisory_deadline' => $this->scopedRules($advisory, ['required', Rule::in($this->optionKeys('deadlines'))]),
            'advisory_additional_information' => $this->scopedRules($advisory, ['nullable', 'string', 'max:4000']),
            'advisory_attachments' => $this->scopedRules($advisory, ['nullable', 'array', 'max:5']),
            'advisory_attachments.*' => $this->scopedRules($advisory, ['file', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png', 'max:10240']),

            'advisory_sale_share' => $this->scopedRules($advisory && $advisoryService === 'company_sale', ['required', 'numeric', 'between:0,100']),
            'advisory_sale_buyer_identified' => $this->scopedRules($advisory && $advisoryService === 'company_sale', ['required', 'in:yes,no']),
            'advisory_sale_closing_timeline' => $this->scopedRules($advisory && $advisoryService === 'company_sale', ['required', 'string', 'max:191']),
            'advisory_purchase_target_identified' => $this->scopedRules($advisory && $advisoryService === 'company_purchase', ['required', 'in:yes,no']),
            'advisory_purchase_target_activity' => $this->scopedRules($advisory && $advisoryService === 'company_purchase', ['required', 'string', 'max:191']),
            'advisory_purchase_financing' => $this->scopedRules($advisory && $advisoryService === 'company_purchase', ['required', 'string', 'max:1000']),
            'advisory_dd_side' => $this->scopedRules($advisory && $advisoryService === 'financial_due_diligence', ['required', Rule::in($this->optionKeys('dd_sides'))]),
            'advisory_dd_period' => $this->scopedRules($advisory && $advisoryService === 'financial_due_diligence', ['required', 'string', 'max:191']),
            'advisory_dd_loi_signed' => $this->scopedRules($advisory && $advisoryService === 'financial_due_diligence', ['required', 'in:yes,no']),
            'advisory_review_client' => $this->scopedRules($advisory && $advisoryService === 'independent_review', ['required', Rule::in($this->optionKeys('review_clients'))]),
            'advisory_review_period' => $this->scopedRules($advisory && $advisoryService === 'independent_review', ['required', 'string', 'max:191']),
            'advisory_valuation_purpose' => $this->scopedRules($advisory && $advisoryService === 'valuation', ['required', Rule::in($this->optionKeys('valuation_purposes'))]),
            'advisory_valuation_date' => $this->scopedRules($advisory && $advisoryService === 'valuation', ['required', 'date']),
            'advisory_financing_amount' => $this->scopedRules($advisory && $advisoryService === 'financing', ['required', 'string', 'max:191']),
            'advisory_financing_purpose' => $this->scopedRules($advisory && $advisoryService === 'financing', ['required', Rule::in($this->optionKeys('financing_purposes'))]),
            'advisory_financing_existing_debt' => $this->scopedRules($advisory && $advisoryService === 'financing', ['required', 'in:yes,no']),

            'advisory_target_relation' => $this->scopedRules($advisory, ['required', Rule::in($this->optionKeys('target_relations'))]),
            'advisory_target_company_name' => $this->scopedRules($differentTarget, ['required', 'string', 'max:191']),
            'advisory_target_company_oib' => $this->scopedRules($differentTarget, ['required', 'string', 'max:50']),
            'advisory_target_company_activity' => $this->scopedRules($differentTarget, ['required', 'string', 'max:191']),
            'advisory_target_company_revenue' => $this->scopedRules($differentTarget, ['required', Rule::in($this->optionKeys('revenue'))]),
            'advisory_target_company_employees' => $this->scopedRules($differentTarget, ['required', Rule::in($this->optionKeys('employees'))]),
            'advisory_target_related_companies' => $this->scopedRules($differentTarget, ['required', 'in:yes,no']),
            'advisory_target_consolidated_review' => $this->scopedRules($relatedCompanies, ['required', 'in:yes,no']),

            'accept_terms' => ['accepted'],
            'recaptcha_token' => [$captchaEnabled ? 'required' : 'nullable', 'string', 'max:4096'],
        ];
    }

    /**
     * @param  array<int, mixed>  $rules
     * @return array<int, mixed>
     */
    private function scopedRules(bool $active, array $rules): array
    {
        return [Rule::excludeIf(! $active), ...$rules];
    }

    /**
     * @return array<int, string>
     */
    private function optionKeys(string $group): array
    {
        $options = __('assessment.values.'.$group);

        return is_array($options) ? array_keys($options) : [];
    }

    /**
     * @return array<string, string>
     */
    private function validationAttributes(): array
    {
        $attributes = [
            'service' => __('assessment.form.service'),
            'accept_terms' => __('assessment.form.accept_terms'),
            'recaptcha_token' => __('assessment.validation.security_check'),
            'advisory_attachments' => __('assessment.form.advisory_attachments'),
            'advisory_attachments.*' => __('assessment.form.advisory_attachments'),
        ];

        foreach (AssessmentQuestionnaire::serviceKeys() as $service) {
            $attributes = [...$attributes, ...AssessmentQuestionnaire::fieldLabels($service)];
        }

        return $attributes;
    }

    /**
     * @param  array<string, mixed>  $answers
     * @param  array<int, array<string, mixed>>  $attachments
     */
    private function buildSummary(array $answers, array $attachments): string
    {
        $service = AssessmentQuestionnaire::normalizeService((string) ($answers['service'] ?? ''));
        $lines = [
            __('assessment.form.service').': '.AssessmentQuestionnaire::serviceLabel($service),
        ];

        foreach (AssessmentQuestionnaire::fieldLabels($service) as $key => $label) {
            $value = AssessmentQuestionnaire::formatAnswer($key, $answers[$key] ?? null);

            if ($value !== '') {
                $lines[] = $label.': '.$value;
            }
        }

        if ($attachments !== []) {
            $names = array_filter(array_map(
                static fn (array $attachment): string => trim((string) ($attachment['name'] ?? '')),
                $attachments
            ));

            if ($names !== []) {
                $lines[] = __('assessment.form.advisory_attachments').': '.implode(', ', $names);
            }
        }

        return implode("\n", $lines);
    }

    /**
     * @param  array<string, mixed>  $answers
     * @return array{name: string, email: string, phone: string}
     */
    private function contactIdentity(array $answers, string $service): array
    {
        return match ($service) {
            AssessmentQuestionnaire::SERVICE_AUDIT => [
                'name' => trim((string) ($answers['audit_company_name'] ?? '')),
                'email' => trim((string) ($answers['audit_contact_email'] ?? '')),
                'phone' => trim((string) ($answers['audit_contact_phone'] ?? '')),
            ],
            AssessmentQuestionnaire::SERVICE_ADVISORY => [
                'name' => trim((string) ($answers['advisory_contact_person'] ?? '')),
                'email' => trim((string) ($answers['advisory_contact_email'] ?? '')),
                'phone' => trim((string) ($answers['advisory_contact_phone'] ?? '')),
            ],
            default => [
                'name' => trim((string) ($answers['company_name'] ?? '')),
                'email' => trim((string) ($answers['contact_email'] ?? '')),
                'phone' => trim((string) ($answers['contact_phone'] ?? '')),
            ],
        };
    }

    /**
     * @return array<int, array{disk: string, path: string, name: string, mime: string, size: int}>
     */
    private function storeAttachments(Request $request): array
    {
        $files = $request->file('advisory_attachments', []);
        if ($files instanceof UploadedFile) {
            $files = [$files];
        }

        if (! is_array($files)) {
            return [];
        }

        $attachments = [];

        try {
            foreach ($files as $file) {
                if (! $file instanceof UploadedFile) {
                    continue;
                }

                $extension = strtolower((string) $file->getClientOriginalExtension());
                $storedName = (string) Str::uuid().($extension !== '' ? '.'.$extension : '');
                $path = $file->storeAs(
                    'contact-message-attachments/'.now()->format('Y/m'),
                    $storedName,
                    'local'
                );

                if (! is_string($path) || $path === '') {
                    throw new \RuntimeException('Unable to store proposal request attachment.');
                }

                $attachments[] = [
                    'disk' => 'local',
                    'path' => $path,
                    'name' => Str::limit(basename((string) $file->getClientOriginalName()), 240, ''),
                    'mime' => (string) ($file->getMimeType() ?: 'application/octet-stream'),
                    'size' => (int) ($file->getSize() ?? 0),
                ];
            }
        } catch (\Throwable $exception) {
            $this->deleteAttachments($attachments);

            throw $exception;
        }

        return $attachments;
    }

    /**
     * @param  array<int, array<string, mixed>>  $attachments
     */
    private function deleteAttachments(array $attachments): void
    {
        foreach ($attachments as $attachment) {
            $disk = trim((string) ($attachment['disk'] ?? 'local')) ?: 'local';
            $path = trim((string) ($attachment['path'] ?? ''));

            if ($path !== '') {
                Storage::disk($disk)->delete($path);
            }
        }
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
                'recaptcha_token' => __('assessment.captcha_failed'),
            ]);
        }

        if (! $response->ok()) {
            throw ValidationException::withMessages([
                'recaptcha_token' => __('assessment.captcha_failed'),
            ]);
        }

        $json = $response->json();
        $success = (bool) ($json['success'] ?? false);
        $score = (float) ($json['score'] ?? 0.0);
        $action = (string) ($json['action'] ?? '');

        if (! $success || $score < $minScore || $action !== $expectedAction) {
            throw ValidationException::withMessages([
                'recaptcha_token' => __('assessment.captcha_failed'),
            ]);
        }
    }
}
