<?php

namespace App\Support;

final class AssessmentQuestionnaire
{
    public const SERVICE_ACCOUNTING = 'accounting';

    public const SERVICE_AUDIT = 'audit';

    public const SERVICE_ADVISORY = 'advisory';

    /**
     * @return array<string, string>
     */
    public static function services(): array
    {
        return [
            self::SERVICE_ACCOUNTING => __('assessment.services.accounting'),
            self::SERVICE_AUDIT => __('assessment.services.audit'),
            self::SERVICE_ADVISORY => __('assessment.services.advisory'),
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function serviceKeys(): array
    {
        return array_keys(self::services());
    }

    public static function normalizeService(?string $service): string
    {
        $service = trim((string) $service);

        return in_array($service, self::serviceKeys(), true)
            ? $service
            : self::SERVICE_ACCOUNTING;
    }

    public static function serviceLabel(?string $service): string
    {
        $service = self::normalizeService($service);

        return self::services()[$service];
    }

    /**
     * @return array<int, string>
     */
    public static function answerFields(string $service): array
    {
        return match (self::normalizeService($service)) {
            self::SERVICE_AUDIT => [
                'audit_company_name',
                'audit_company_oib',
                'audit_contact_email',
                'audit_contact_phone',
                'audit_message',
            ],
            self::SERVICE_ADVISORY => [
                'advisory_contact_person',
                'advisory_contact_email',
                'advisory_contact_phone',
                'advisory_quote_company_name',
                'advisory_quote_company_activity',
                'advisory_quote_company_revenue',
                'advisory_quote_company_employees',
                'advisory_service',
                'advisory_reason',
                'advisory_deadline',
                'advisory_additional_information',
                'advisory_sale_share',
                'advisory_sale_buyer_identified',
                'advisory_sale_closing_timeline',
                'advisory_purchase_target_identified',
                'advisory_purchase_target_activity',
                'advisory_purchase_financing',
                'advisory_dd_side',
                'advisory_dd_period',
                'advisory_dd_loi_signed',
                'advisory_review_client',
                'advisory_review_period',
                'advisory_valuation_purpose',
                'advisory_valuation_date',
                'advisory_financing_amount',
                'advisory_financing_purpose',
                'advisory_financing_existing_debt',
                'advisory_target_relation',
                'advisory_target_company_name',
                'advisory_target_company_oib',
                'advisory_target_company_activity',
                'advisory_target_company_revenue',
                'advisory_target_company_employees',
                'advisory_target_related_companies',
                'advisory_target_consolidated_review',
            ],
            default => [
                'company_name',
                'company_oib',
                'activity',
                'contact_email',
                'contact_phone',
                'incoming_invoices_monthly',
                'outgoing_invoices_monthly',
                'bank_accounts_monthly',
                'payroll_calculations_monthly',
                'other_calculations_monthly',
                'incoming_invoice_payments',
                'inventory_bookkeeping',
                'travel_orders_monthly',
                'cost_centers_tracking',
                'intrastat_obligation',
                'audit_obligation',
                'monthly_reporting',
                'vat_status',
                'accounting_software',
                'tax_issues',
                'document_delivery',
                'additional_requirements',
                'potential_start_date',
            ],
        };
    }

    /**
     * @return array<string, string>
     */
    public static function fieldLabels(string $service): array
    {
        $labels = [];

        foreach (self::answerFields($service) as $field) {
            $labels[$field] = (string) __('assessment.form.'.$field);
        }

        return $labels;
    }

    public static function formatAnswer(string $field, mixed $value): string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return '';
        }

        $group = match ($field) {
            'inventory_bookkeeping',
            'cost_centers_tracking',
            'monthly_reporting',
            'advisory_sale_buyer_identified',
            'advisory_purchase_target_identified',
            'advisory_dd_loi_signed',
            'advisory_financing_existing_debt',
            'advisory_target_related_companies',
            'advisory_target_consolidated_review' => 'yes_no',
            'advisory_quote_company_revenue',
            'advisory_target_company_revenue' => 'revenue',
            'advisory_quote_company_employees',
            'advisory_target_company_employees' => 'employees',
            'advisory_service' => 'advisory_services',
            'advisory_deadline' => 'deadlines',
            'advisory_dd_side' => 'dd_sides',
            'advisory_review_client' => 'review_clients',
            'advisory_valuation_purpose' => 'valuation_purposes',
            'advisory_financing_purpose' => 'financing_purposes',
            'advisory_target_relation' => 'target_relations',
            default => null,
        };

        if ($group === null) {
            return $value;
        }

        $translated = __('assessment.values.'.$group.'.'.$value);

        return is_string($translated) && $translated !== 'assessment.values.'.$group.'.'.$value
            ? $translated
            : $value;
    }

    /**
     * @param  array<string, mixed>  $answers
     */
    public static function companyName(array $answers, string $service): string
    {
        return trim((string) match (self::normalizeService($service)) {
            self::SERVICE_AUDIT => $answers['audit_company_name'] ?? '',
            self::SERVICE_ADVISORY => $answers['advisory_quote_company_name'] ?? '',
            default => $answers['company_name'] ?? '',
        });
    }

    /**
     * @param  array<string, mixed>  $answers
     */
    public static function companyOib(array $answers, string $service): string
    {
        return trim((string) match (self::normalizeService($service)) {
            self::SERVICE_AUDIT => $answers['audit_company_oib'] ?? '',
            self::SERVICE_ADVISORY => '',
            default => $answers['company_oib'] ?? '',
        });
    }

    /**
     * @param  array<string, mixed>  $answers
     */
    public static function companyActivity(array $answers, string $service): string
    {
        return trim((string) match (self::normalizeService($service)) {
            self::SERVICE_ADVISORY => $answers['advisory_quote_company_activity'] ?? '',
            self::SERVICE_AUDIT => '',
            default => $answers['activity'] ?? '',
        });
    }
}
