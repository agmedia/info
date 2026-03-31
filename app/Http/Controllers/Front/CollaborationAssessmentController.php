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

class CollaborationAssessmentController extends Controller
{
    use ResolvesFrontendView;

    public function __construct(
        private readonly StoreNotificationService $notifications,
        private readonly StoreSettingsService $storeSettings
    ) {
    }

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

        $validated = $request->validate(
            [
                'company_name' => ['required', 'string', 'max:191'],
                'company_oib' => ['required', 'string', 'max:50'],
                'activity' => ['required', 'string', 'max:191'],
                'contact_email' => ['required', 'email', 'max:191'],
                'contact_phone' => ['required', 'string', 'max:80'],
                'incoming_invoices_monthly' => ['required', 'string', 'max:80'],
                'outgoing_invoices_monthly' => ['required', 'string', 'max:80'],
                'bank_accounts_monthly' => ['required', 'string', 'max:80'],
                'payroll_calculations_monthly' => ['required', 'string', 'max:80'],
                'other_calculations_monthly' => ['nullable', 'string', 'max:500'],
                'incoming_invoice_payments' => ['nullable', 'string', 'max:500'],
                'inventory_bookkeeping' => ['nullable', 'string', 'in:yes,no'],
                'travel_orders_monthly' => ['nullable', 'string', 'max:80'],
                'cost_centers_tracking' => ['nullable', 'string', 'in:yes,no'],
                'intrastat_obligation' => ['nullable', 'string', 'max:500'],
                'audit_obligation' => ['nullable', 'string', 'max:500'],
                'monthly_reporting' => ['nullable', 'string', 'in:yes,no'],
                'vat_status' => ['nullable', 'string', 'max:500'],
                'accounting_software' => ['nullable', 'string', 'max:191'],
                'tax_issues' => ['nullable', 'string', 'max:1000'],
                'document_delivery' => ['nullable', 'string', 'max:500'],
                'additional_requirements' => ['nullable', 'string', 'max:1500'],
                'potential_start_date' => ['nullable', 'date'],
                'accept_terms' => ['accepted'],
                'recaptcha_token' => [$captchaEnabled ? 'required' : 'nullable', 'string', 'max:4096'],
            ],
            [
                'required' => __('assessment.validation.required'),
                'email' => __('assessment.validation.email'),
                'accepted' => __('assessment.validation.accepted'),
                'max.string' => __('assessment.validation.max_string'),
                'date' => __('assessment.validation.date'),
                'in' => __('assessment.validation.in'),
            ],
            [
                'company_name' => __('assessment.form.company_name'),
                'company_oib' => __('assessment.form.company_oib'),
                'activity' => __('assessment.form.activity'),
                'contact_email' => __('assessment.form.contact_email'),
                'contact_phone' => __('assessment.form.contact_phone'),
                'incoming_invoices_monthly' => __('assessment.form.incoming_invoices_monthly'),
                'outgoing_invoices_monthly' => __('assessment.form.outgoing_invoices_monthly'),
                'bank_accounts_monthly' => __('assessment.form.bank_accounts_monthly'),
                'payroll_calculations_monthly' => __('assessment.form.payroll_calculations_monthly'),
                'other_calculations_monthly' => __('assessment.form.other_calculations_monthly'),
                'incoming_invoice_payments' => __('assessment.form.incoming_invoice_payments'),
                'inventory_bookkeeping' => __('assessment.form.inventory_bookkeeping'),
                'travel_orders_monthly' => __('assessment.form.travel_orders_monthly'),
                'cost_centers_tracking' => __('assessment.form.cost_centers_tracking'),
                'intrastat_obligation' => __('assessment.form.intrastat_obligation'),
                'audit_obligation' => __('assessment.form.audit_obligation'),
                'monthly_reporting' => __('assessment.form.monthly_reporting'),
                'vat_status' => __('assessment.form.vat_status'),
                'accounting_software' => __('assessment.form.accounting_software'),
                'tax_issues' => __('assessment.form.tax_issues'),
                'document_delivery' => __('assessment.form.document_delivery'),
                'additional_requirements' => __('assessment.form.additional_requirements'),
                'potential_start_date' => __('assessment.form.potential_start_date'),
                'accept_terms' => __('assessment.form.accept_terms'),
                'recaptcha_token' => __('assessment.validation.security_check'),
            ]
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

        $payload = [
            'form_type' => ContactMessage::FORM_TYPE_COLLABORATION_ASSESSMENT,
            'locale' => app()->getLocale(),
            'url' => $request->fullUrl(),
            'answers' => [
                'company_name' => $validated['company_name'],
                'company_oib' => $validated['company_oib'],
                'activity' => $validated['activity'],
                'contact_email' => $validated['contact_email'],
                'contact_phone' => $validated['contact_phone'],
                'incoming_invoices_monthly' => $validated['incoming_invoices_monthly'],
                'outgoing_invoices_monthly' => $validated['outgoing_invoices_monthly'],
                'bank_accounts_monthly' => $validated['bank_accounts_monthly'],
                'payroll_calculations_monthly' => $validated['payroll_calculations_monthly'],
                'other_calculations_monthly' => $validated['other_calculations_monthly'] ?? null,
                'incoming_invoice_payments' => $validated['incoming_invoice_payments'] ?? null,
                'inventory_bookkeeping' => $validated['inventory_bookkeeping'] ?? null,
                'travel_orders_monthly' => $validated['travel_orders_monthly'] ?? null,
                'cost_centers_tracking' => $validated['cost_centers_tracking'] ?? null,
                'intrastat_obligation' => $validated['intrastat_obligation'] ?? null,
                'audit_obligation' => $validated['audit_obligation'] ?? null,
                'monthly_reporting' => $validated['monthly_reporting'] ?? null,
                'vat_status' => $validated['vat_status'] ?? null,
                'accounting_software' => $validated['accounting_software'] ?? null,
                'tax_issues' => $validated['tax_issues'] ?? null,
                'document_delivery' => $validated['document_delivery'] ?? null,
                'additional_requirements' => $validated['additional_requirements'] ?? null,
                'potential_start_date' => $validated['potential_start_date'] ?? null,
            ],
        ];

        $message = ContactMessage::query()->create([
            'user_id' => $request->user()?->id,
            'name' => $validated['company_name'],
            'email' => $validated['contact_email'],
            'phone' => $validated['contact_phone'],
            'subject' => __('assessment.form.default_subject'),
            'message' => $this->buildSummary($payload['answers']),
            'status' => ContactMessage::STATUS_NEW,
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'payload' => $payload,
        ]);

        $this->notifications->sendContactNotification($message);

        return redirect()
            ->route('assessment.create')
            ->with('status', __('assessment.sent_status'));
    }

    /**
     * @param array<string, string|null> $answers
     */
    private function buildSummary(array $answers): string
    {
        $labels = [
            'company_name' => __('assessment.form.company_name'),
            'company_oib' => __('assessment.form.company_oib'),
            'activity' => __('assessment.form.activity'),
            'contact_email' => __('assessment.form.contact_email'),
            'contact_phone' => __('assessment.form.contact_phone'),
            'incoming_invoices_monthly' => __('assessment.form.incoming_invoices_monthly'),
            'outgoing_invoices_monthly' => __('assessment.form.outgoing_invoices_monthly'),
            'bank_accounts_monthly' => __('assessment.form.bank_accounts_monthly'),
            'payroll_calculations_monthly' => __('assessment.form.payroll_calculations_monthly'),
            'other_calculations_monthly' => __('assessment.form.other_calculations_monthly'),
            'incoming_invoice_payments' => __('assessment.form.incoming_invoice_payments'),
            'inventory_bookkeeping' => __('assessment.form.inventory_bookkeeping'),
            'travel_orders_monthly' => __('assessment.form.travel_orders_monthly'),
            'cost_centers_tracking' => __('assessment.form.cost_centers_tracking'),
            'intrastat_obligation' => __('assessment.form.intrastat_obligation'),
            'audit_obligation' => __('assessment.form.audit_obligation'),
            'monthly_reporting' => __('assessment.form.monthly_reporting'),
            'vat_status' => __('assessment.form.vat_status'),
            'accounting_software' => __('assessment.form.accounting_software'),
            'tax_issues' => __('assessment.form.tax_issues'),
            'document_delivery' => __('assessment.form.document_delivery'),
            'additional_requirements' => __('assessment.form.additional_requirements'),
            'potential_start_date' => __('assessment.form.potential_start_date'),
        ];

        $booleanMap = [
            'yes' => __('assessment.options.yes'),
            'no' => __('assessment.options.no'),
        ];

        $lines = [];

        foreach ($labels as $key => $label) {
            $value = trim((string) ($answers[$key] ?? ''));
            if ($value === '') {
                continue;
            }

            $lines[] = $label.': '.($booleanMap[$value] ?? $value);
        }

        return implode("\n", $lines);
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

        if (! $success || $score < $minScore || ($action !== '' && $action !== $expectedAction)) {
            throw ValidationException::withMessages([
                'recaptcha_token' => __('assessment.captcha_failed'),
            ]);
        }
    }
}
