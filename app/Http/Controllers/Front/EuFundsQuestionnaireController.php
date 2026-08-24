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
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class EuFundsQuestionnaireController extends Controller
{
    use ResolvesFrontendView;

    public function __construct(
        private readonly StoreNotificationService $notifications,
        private readonly StoreSettingsService $storeSettings
    ) {
    }

    public function create(Request $request): View
    {
        return view($this->frontendView($request, 'pages.eu-funds-questionnaire'));
    }

    public function store(Request $request): RedirectResponse
    {
        $captchaSettings = $this->storeSettings->captcha();
        $captchaEnabled = (bool) ($captchaSettings['recaptcha_v3_enabled'] ?? false)
            && trim((string) ($captchaSettings['recaptcha_v3_site_key'] ?? '')) !== ''
            && trim((string) ($captchaSettings['recaptcha_v3_secret_key'] ?? '')) !== '';

        $employeeOptions = $this->employeeOptions();
        $relatedCompanyOptions = $this->relatedCompanyOptions();
        $projectSectorOptions = $this->projectSectorOptions();
        $plannedCostOptions = $this->plannedCostOptions();
        $investmentAmountOptions = $this->investmentAmountOptions();
        $interestedServiceOptions = $this->interestedServiceOptions();

        $validated = $request->validate(
            [
                'company_name' => ['required', 'string', 'max:191'],
                'company_oib' => ['required', 'string', 'max:50'],
                'company_activity' => ['required', 'string', 'max:255'],
                'employee_count' => ['required', Rule::in(array_keys($employeeOptions))],
                'related_companies' => ['required', Rule::in(array_keys($relatedCompanyOptions))],
                'project_sectors' => ['required', 'array', 'min:1'],
                'project_sectors.*' => [Rule::in(array_keys($projectSectorOptions))],
                'project_sector_other' => [
                    Rule::requiredIf(static fn () => in_array('other', (array) $request->input('project_sectors', []), true)),
                    'nullable',
                    'string',
                    'max:191',
                ],
                'investment_location' => ['required', 'string', 'max:255'],
                'planned_costs' => ['required', 'array', 'min:1'],
                'planned_costs.*' => [Rule::in(array_keys($plannedCostOptions))],
                'investment_amount' => ['required', Rule::in(array_keys($investmentAmountOptions))],
                'interested_services' => ['required', 'array', 'min:1'],
                'interested_services.*' => [Rule::in(array_keys($interestedServiceOptions))],
                'additional_notes' => [
                    Rule::requiredIf(static fn () => (string) $request->input('related_companies') === 'yes'),
                    'nullable',
                    'string',
                    'max:2000',
                ],
                'contact_name' => ['required', 'string', 'max:191'],
                'email' => ['required', 'email', 'max:191'],
                'contact_phone' => ['required', 'string', 'max:80'],
                'accept_terms' => ['accepted'],
                'recaptcha_token' => [$captchaEnabled ? 'required' : 'nullable', 'string', 'max:4096'],
            ],
            [
                'required' => __('eu_funds_questionnaire.validation.required'),
                'required_if' => __('eu_funds_questionnaire.validation.required_if'),
                'accepted' => __('eu_funds_questionnaire.validation.accepted'),
                'email' => __('eu_funds_questionnaire.validation.email'),
                'max.string' => __('eu_funds_questionnaire.validation.max_string'),
                'min.array' => __('eu_funds_questionnaire.validation.min_array'),
                'in' => __('eu_funds_questionnaire.validation.in'),
            ],
            [
                'company_name' => __('eu_funds_questionnaire.form.company_name'),
                'company_oib' => __('eu_funds_questionnaire.form.company_oib'),
                'company_activity' => __('eu_funds_questionnaire.form.company_activity'),
                'employee_count' => __('eu_funds_questionnaire.form.employee_count'),
                'related_companies' => __('eu_funds_questionnaire.form.related_companies'),
                'project_sectors' => __('eu_funds_questionnaire.form.project_sectors'),
                'project_sector_other' => __('eu_funds_questionnaire.form.project_sector_other'),
                'investment_location' => __('eu_funds_questionnaire.form.investment_location'),
                'planned_costs' => __('eu_funds_questionnaire.form.planned_costs'),
                'investment_amount' => __('eu_funds_questionnaire.form.investment_amount'),
                'interested_services' => __('eu_funds_questionnaire.form.interested_services'),
                'additional_notes' => __('eu_funds_questionnaire.form.additional_notes'),
                'contact_name' => __('eu_funds_questionnaire.form.contact_name'),
                'email' => __('eu_funds_questionnaire.form.email'),
                'contact_phone' => __('eu_funds_questionnaire.form.contact_phone'),
                'accept_terms' => __('eu_funds_questionnaire.form.accept_terms'),
                'recaptcha_token' => __('eu_funds_questionnaire.validation.security_check'),
            ]
        );

        if ($captchaEnabled) {
            $this->assertRecaptchaIsValid(
                token: (string) ($validated['recaptcha_token'] ?? ''),
                secret: (string) $captchaSettings['recaptcha_v3_secret_key'],
                minScore: (float) ($captchaSettings['recaptcha_v3_min_score'] ?? 0.5),
                expectedAction: 'eu_funds_questionnaire_form',
                ip: (string) $request->ip()
            );
        }

        $projectSectors = collect((array) ($validated['project_sectors'] ?? []))
            ->map(fn (string $value): string => (string) ($projectSectorOptions[$value] ?? $value))
            ->values()
            ->all();
        $plannedCosts = collect((array) ($validated['planned_costs'] ?? []))
            ->map(fn (string $value): string => (string) ($plannedCostOptions[$value] ?? $value))
            ->values()
            ->all();
        $interestedServices = collect((array) ($validated['interested_services'] ?? []))
            ->map(fn (string $value): string => (string) ($interestedServiceOptions[$value] ?? $value))
            ->values()
            ->all();

        $answers = [
            'company_name' => $validated['company_name'],
            'company_oib' => $validated['company_oib'],
            'company_activity' => $validated['company_activity'],
            'employee_count' => (string) $employeeOptions[$validated['employee_count']],
            'related_companies' => (string) $relatedCompanyOptions[$validated['related_companies']],
            'project_sectors' => $projectSectors,
            'project_sector_other' => trim((string) ($validated['project_sector_other'] ?? '')) ?: null,
            'investment_location' => $validated['investment_location'],
            'planned_costs' => $plannedCosts,
            'investment_amount' => (string) $investmentAmountOptions[$validated['investment_amount']],
            'interested_services' => $interestedServices,
            'additional_notes' => trim((string) ($validated['additional_notes'] ?? '')) ?: null,
            'contact_name' => $validated['contact_name'],
            'email' => $validated['email'],
            'contact_phone' => $validated['contact_phone'],
        ];

        $message = ContactMessage::query()->create([
            'user_id' => $request->user()?->id,
            'name' => $answers['contact_name'],
            'email' => $answers['email'],
            'phone' => $answers['contact_phone'],
            'subject' => ContactMessage::SUBJECT_EU_FUNDS_QUESTIONNAIRE,
            'message' => $this->buildSummary($answers),
            'status' => ContactMessage::STATUS_NEW,
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'payload' => [
                'form_type' => ContactMessage::FORM_TYPE_EU_FUNDS_QUESTIONNAIRE,
                'locale' => app()->getLocale(),
                'url' => $request->fullUrl(),
                'answers' => $answers,
            ],
        ]);

        $this->notifications->sendContactNotification($message);

        return redirect()
            ->route('eu-funds.questionnaire.create')
            ->with('status', __('eu_funds_questionnaire.sent_status'));
    }

    /**
     * @return array<string, string>
     */
    private function employeeOptions(): array
    {
        return [
            '0' => __('eu_funds_questionnaire.options.employee_count.0'),
            '1_9' => __('eu_funds_questionnaire.options.employee_count.1_9'),
            '10_49' => __('eu_funds_questionnaire.options.employee_count.10_49'),
            '50_249' => __('eu_funds_questionnaire.options.employee_count.50_249'),
            '250_plus' => __('eu_funds_questionnaire.options.employee_count.250_plus'),
        ];
    }

    /**
     * @return array<string, string>
     */
    private function relatedCompanyOptions(): array
    {
        return [
            'yes' => __('eu_funds_questionnaire.options.related_companies.yes'),
            'no' => __('eu_funds_questionnaire.options.related_companies.no'),
        ];
    }

    /**
     * @return array<string, string>
     */
    private function projectSectorOptions(): array
    {
        return [
            'manufacturing' => __('eu_funds_questionnaire.options.project_sectors.manufacturing'),
            'ict' => __('eu_funds_questionnaire.options.project_sectors.ict'),
            'creative_industries' => __('eu_funds_questionnaire.options.project_sectors.creative_industries'),
            'tourism' => __('eu_funds_questionnaire.options.project_sectors.tourism'),
            'agriculture' => __('eu_funds_questionnaire.options.project_sectors.agriculture'),
            'education' => __('eu_funds_questionnaire.options.project_sectors.education'),
            'construction' => __('eu_funds_questionnaire.options.project_sectors.construction'),
            'trade' => __('eu_funds_questionnaire.options.project_sectors.trade'),
            'transport_logistics' => __('eu_funds_questionnaire.options.project_sectors.transport_logistics'),
            'other' => __('eu_funds_questionnaire.options.project_sectors.other'),
        ];
    }

    /**
     * @return array<string, string>
     */
    private function plannedCostOptions(): array
    {
        return [
            'construction' => __('eu_funds_questionnaire.options.planned_costs.construction'),
            'equipment' => __('eu_funds_questionnaire.options.planned_costs.equipment'),
            'innovation_research' => __('eu_funds_questionnaire.options.planned_costs.innovation_research'),
            'energy_efficiency' => __('eu_funds_questionnaire.options.planned_costs.energy_efficiency'),
            'digitalization' => __('eu_funds_questionnaire.options.planned_costs.digitalization'),
        ];
    }

    /**
     * @return array<string, string>
     */
    private function investmentAmountOptions(): array
    {
        return [
            'up_to_100k' => __('eu_funds_questionnaire.options.investment_amount.up_to_100k'),
            '100k_500k' => __('eu_funds_questionnaire.options.investment_amount.100k_500k'),
            '500k_1000k' => __('eu_funds_questionnaire.options.investment_amount.500k_1000k'),
            '1000k_2000k' => __('eu_funds_questionnaire.options.investment_amount.1000k_2000k'),
            'over_2000k' => __('eu_funds_questionnaire.options.investment_amount.over_2000k'),
        ];
    }

    /**
     * @return array<string, string>
     */
    private function interestedServiceOptions(): array
    {
        return [
            'loans' => __('eu_funds_questionnaire.options.interested_services.loans'),
            'investment_incentives' => __('eu_funds_questionnaire.options.interested_services.investment_incentives'),
            'r_and_d_support' => __('eu_funds_questionnaire.options.interested_services.r_and_d_support'),
            'none' => __('eu_funds_questionnaire.options.interested_services.none'),
        ];
    }

    /**
     * @param array<string, mixed> $answers
     */
    private function buildSummary(array $answers): string
    {
        $labels = [
            'company_name' => __('eu_funds_questionnaire.form.company_name'),
            'company_oib' => __('eu_funds_questionnaire.form.company_oib'),
            'company_activity' => __('eu_funds_questionnaire.form.company_activity'),
            'employee_count' => __('eu_funds_questionnaire.form.employee_count'),
            'related_companies' => __('eu_funds_questionnaire.form.related_companies'),
            'project_sectors' => __('eu_funds_questionnaire.form.project_sectors'),
            'investment_location' => __('eu_funds_questionnaire.form.investment_location'),
            'planned_costs' => __('eu_funds_questionnaire.form.planned_costs'),
            'investment_amount' => __('eu_funds_questionnaire.form.investment_amount'),
            'interested_services' => __('eu_funds_questionnaire.form.interested_services'),
            'additional_notes' => __('eu_funds_questionnaire.form.additional_notes'),
            'contact_name' => __('eu_funds_questionnaire.form.contact_name'),
            'email' => __('eu_funds_questionnaire.form.email'),
            'contact_phone' => __('eu_funds_questionnaire.form.contact_phone'),
        ];

        $lines = [];

        foreach ($labels as $key => $label) {
            $value = $answers[$key] ?? null;

            if (is_array($value)) {
                $value = implode(', ', array_values(array_filter(array_map(
                    static fn ($item): string => trim((string) $item),
                    $value
                ))));
            } else {
                $value = trim((string) $value);
            }

            if ($value === '') {
                continue;
            }

            if ($key === 'project_sectors' && trim((string) ($answers['project_sector_other'] ?? '')) !== '') {
                $value .= ' | '.__('eu_funds_questionnaire.form.project_sector_other').': '.trim((string) $answers['project_sector_other']);
            }

            $lines[] = $label.': '.$value;
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
                'recaptcha_token' => __('eu_funds_questionnaire.captcha_failed'),
            ]);
        }

        if (! $response->ok()) {
            throw ValidationException::withMessages([
                'recaptcha_token' => __('eu_funds_questionnaire.captcha_failed'),
            ]);
        }

        $json = $response->json();
        $success = (bool) ($json['success'] ?? false);
        $score = (float) ($json['score'] ?? 0.0);
        $action = (string) ($json['action'] ?? '');

        if (! $success || $score < $minScore || $action !== $expectedAction) {
            throw ValidationException::withMessages([
                'recaptcha_token' => __('eu_funds_questionnaire.captcha_failed'),
            ]);
        }
    }
}
