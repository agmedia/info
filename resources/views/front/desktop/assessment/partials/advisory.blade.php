@php
    $advisoryService = trim((string) old('advisory_service', ''));
    $advisoryServices = __('assessment.values.advisory_services');
    $revenueOptions = __('assessment.values.revenue');
    $employeeOptions = __('assessment.values.employees');
    $deadlineOptions = __('assessment.values.deadlines');
    $targetRelationOptions = __('assessment.values.target_relations');
    $targetRelation = trim((string) old('advisory_target_relation', 'same'));
    $relatedCompanies = trim((string) old('advisory_target_related_companies', ''));
@endphp

<div class="ac-assessment-section">
    <div class="ac-assessment-section-head">
        <h3>{{ __('assessment.sections.advisory_requester') }}</h3>
    </div>

    <div class="ac-assessment-field ac-assessment-field--full">
        <label for="advisory-contact-person">{{ __('assessment.form.advisory_contact_person') }}</label>
        <input id="advisory-contact-person" type="text" name="advisory_contact_person" value="{{ old('advisory_contact_person') }}" class="front-contact-input h-11 w-full text-sm" required>
        @error('advisory_contact_person')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div class="ac-assessment-grid">
        <div class="ac-assessment-field">
            <label for="advisory-contact-email">{{ __('assessment.form.advisory_contact_email') }}</label>
            <input id="advisory-contact-email" type="email" name="advisory_contact_email" value="{{ old('advisory_contact_email') }}" class="front-contact-input h-11 w-full text-sm" required>
            @error('advisory_contact_email')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
        </div>
        <div class="ac-assessment-field">
            <label for="advisory-contact-phone">{{ __('assessment.form.advisory_contact_phone') }}</label>
            <input id="advisory-contact-phone" type="tel" name="advisory_contact_phone" value="{{ old('advisory_contact_phone') }}" class="front-contact-input h-11 w-full text-sm" required>
            @error('advisory_contact_phone')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
        </div>
    </div>
</div>

<div class="ac-assessment-section">
    <div class="ac-assessment-section-head">
        <h3>{{ __('assessment.sections.advisory_quote_company') }}</h3>
    </div>

    <div class="ac-assessment-grid">
        <div class="ac-assessment-field">
            <label for="advisory-quote-company-name">{{ __('assessment.form.advisory_quote_company_name') }}</label>
            <input id="advisory-quote-company-name" type="text" name="advisory_quote_company_name" value="{{ old('advisory_quote_company_name') }}" class="front-contact-input h-11 w-full text-sm" required>
            @error('advisory_quote_company_name')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
        </div>
        <div class="ac-assessment-field">
            <label for="advisory-quote-company-activity">{{ __('assessment.form.advisory_quote_company_activity') }}</label>
            <input id="advisory-quote-company-activity" type="text" name="advisory_quote_company_activity" value="{{ old('advisory_quote_company_activity') }}" class="front-contact-input h-11 w-full text-sm" required>
            @error('advisory_quote_company_activity')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="ac-assessment-grid">
        <div class="ac-assessment-field">
            <label for="advisory-quote-company-revenue">{{ __('assessment.form.advisory_quote_company_revenue') }}</label>
            <select id="advisory-quote-company-revenue" name="advisory_quote_company_revenue" class="front-contact-input h-11 w-full text-sm" required>
                <option value="">{{ __('assessment.options.choose') }}</option>
                @foreach ($revenueOptions as $value => $label)
                    <option value="{{ $value }}" @selected(old('advisory_quote_company_revenue') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            @error('advisory_quote_company_revenue')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
        </div>
        <div class="ac-assessment-field">
            <label for="advisory-quote-company-employees">{{ __('assessment.form.advisory_quote_company_employees') }}</label>
            <select id="advisory-quote-company-employees" name="advisory_quote_company_employees" class="front-contact-input h-11 w-full text-sm" required>
                <option value="">{{ __('assessment.options.choose') }}</option>
                @foreach ($employeeOptions as $value => $label)
                    <option value="{{ $value }}" @selected(old('advisory_quote_company_employees') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            @error('advisory_quote_company_employees')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
        </div>
    </div>
</div>

<div class="ac-assessment-section">
    <div class="ac-assessment-section-head">
        <h3>{{ __('assessment.sections.advisory_service') }}</h3>
    </div>

    <div class="ac-assessment-field ac-assessment-field--full">
        <label for="advisory-service">{{ __('assessment.form.advisory_service') }}</label>
        <select id="advisory-service" name="advisory_service" class="front-contact-input h-11 w-full text-sm" data-advisory-service-select required>
            <option value="">{{ __('assessment.options.choose') }}</option>
            @foreach ($advisoryServices as $value => $label)
                <option value="{{ $value }}" @selected($advisoryService === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('advisory_service')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div class="ac-assessment-field ac-assessment-field--full">
        <label for="advisory-reason">{{ __('assessment.form.advisory_reason') }}</label>
        <textarea id="advisory-reason" name="advisory_reason" rows="4" class="front-contact-textarea ac-assessment-textarea w-full text-sm" required>{{ old('advisory_reason') }}</textarea>
        @error('advisory_reason')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div class="ac-assessment-field ac-assessment-field--full">
        <label for="advisory-deadline">{{ __('assessment.form.advisory_deadline') }}</label>
        <select id="advisory-deadline" name="advisory_deadline" class="front-contact-input h-11 w-full text-sm" required>
            <option value="">{{ __('assessment.options.choose') }}</option>
            @foreach ($deadlineOptions as $value => $label)
                <option value="{{ $value }}" @selected(old('advisory_deadline') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('advisory_deadline')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div class="ac-assessment-field ac-assessment-field--full">
        <label for="advisory-additional-information">{{ __('assessment.form.advisory_additional_information') }}</label>
        <textarea id="advisory-additional-information" name="advisory_additional_information" rows="5" class="front-contact-textarea ac-assessment-textarea ac-assessment-textarea--lg w-full text-sm">{{ old('advisory_additional_information') }}</textarea>
        @error('advisory_additional_information')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div class="ac-assessment-field ac-assessment-field--full">
        <label for="advisory-attachments">{{ __('assessment.form.advisory_attachments') }}</label>
        <input
            id="advisory-attachments"
            type="file"
            name="advisory_attachments[]"
            class="front-contact-input ac-assessment-file-input w-full text-sm"
            accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png"
            data-assessment-file-input
            multiple
        >
        <p class="ac-assessment-field-help">{{ __('assessment.form.advisory_attachments_help') }}</p>
        <p class="ac-assessment-file-summary" data-assessment-file-summary aria-live="polite"></p>
        @error('advisory_attachments')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
        @foreach ($errors->get('advisory_attachments.*') as $attachmentErrors)
            @foreach ($attachmentErrors as $attachmentError)
                <p class="mt-2 text-xs font-semibold text-rose-600">{{ $attachmentError }}</p>
            @endforeach
        @endforeach
    </div>
</div>

<div
    class="ac-assessment-section ac-assessment-conditional-section"
    data-advisory-conditional-section
    @if (!array_key_exists($advisoryService, $advisoryServices)) hidden @endif
>
    <div class="ac-assessment-section-head">
        <h3>{{ __('assessment.sections.advisory_conditions') }}</h3>
    </div>

    <div data-advisory-conditional="company_sale" @if ($advisoryService !== 'company_sale') hidden @endif>
        <div class="ac-assessment-grid">
            <div class="ac-assessment-field">
                <label for="advisory-sale-share">{{ __('assessment.form.advisory_sale_share') }}</label>
                <input id="advisory-sale-share" type="number" min="0" max="100" step="0.01" name="advisory_sale_share" value="{{ old('advisory_sale_share') }}" class="front-contact-input h-11 w-full text-sm" data-required-when-active>
                @error('advisory_sale_share')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div class="ac-assessment-field">
                <label for="advisory-sale-buyer-identified">{{ __('assessment.form.advisory_sale_buyer_identified') }}</label>
                <select id="advisory-sale-buyer-identified" name="advisory_sale_buyer_identified" class="front-contact-input h-11 w-full text-sm" data-required-when-active>
                    <option value="">{{ __('assessment.options.choose') }}</option>
                    <option value="yes" @selected(old('advisory_sale_buyer_identified') === 'yes')>{{ __('assessment.options.yes') }}</option>
                    <option value="no" @selected(old('advisory_sale_buyer_identified') === 'no')>{{ __('assessment.options.no') }}</option>
                </select>
                @error('advisory_sale_buyer_identified')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
            </div>
        </div>
        <div class="ac-assessment-field ac-assessment-field--full">
            <label for="advisory-sale-closing-timeline">{{ __('assessment.form.advisory_sale_closing_timeline') }}</label>
            <input id="advisory-sale-closing-timeline" type="text" name="advisory_sale_closing_timeline" value="{{ old('advisory_sale_closing_timeline') }}" class="front-contact-input h-11 w-full text-sm" data-required-when-active>
            @error('advisory_sale_closing_timeline')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
        </div>
    </div>

    <div data-advisory-conditional="company_purchase" @if ($advisoryService !== 'company_purchase') hidden @endif>
        <div class="ac-assessment-grid">
            <div class="ac-assessment-field">
                <label for="advisory-purchase-target-identified">{{ __('assessment.form.advisory_purchase_target_identified') }}</label>
                <select id="advisory-purchase-target-identified" name="advisory_purchase_target_identified" class="front-contact-input h-11 w-full text-sm" data-required-when-active>
                    <option value="">{{ __('assessment.options.choose') }}</option>
                    <option value="yes" @selected(old('advisory_purchase_target_identified') === 'yes')>{{ __('assessment.options.yes') }}</option>
                    <option value="no" @selected(old('advisory_purchase_target_identified') === 'no')>{{ __('assessment.options.no') }}</option>
                </select>
                @error('advisory_purchase_target_identified')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div class="ac-assessment-field">
                <label for="advisory-purchase-target-activity">{{ __('assessment.form.advisory_purchase_target_activity') }}</label>
                <input id="advisory-purchase-target-activity" type="text" name="advisory_purchase_target_activity" value="{{ old('advisory_purchase_target_activity') }}" class="front-contact-input h-11 w-full text-sm" data-required-when-active>
                @error('advisory_purchase_target_activity')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
            </div>
        </div>
        <div class="ac-assessment-field ac-assessment-field--full">
            <label for="advisory-purchase-financing">{{ __('assessment.form.advisory_purchase_financing') }}</label>
            <textarea id="advisory-purchase-financing" name="advisory_purchase_financing" rows="4" class="front-contact-textarea ac-assessment-textarea w-full text-sm" data-required-when-active>{{ old('advisory_purchase_financing') }}</textarea>
            @error('advisory_purchase_financing')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
        </div>
    </div>

    <div data-advisory-conditional="financial_due_diligence" @if ($advisoryService !== 'financial_due_diligence') hidden @endif>
        <div class="ac-assessment-grid">
            <div class="ac-assessment-field">
                <label for="advisory-dd-side">{{ __('assessment.form.advisory_dd_side') }}</label>
                <select id="advisory-dd-side" name="advisory_dd_side" class="front-contact-input h-11 w-full text-sm" data-required-when-active>
                    <option value="">{{ __('assessment.options.choose') }}</option>
                    @foreach (__('assessment.values.dd_sides') as $value => $label)
                        <option value="{{ $value }}" @selected(old('advisory_dd_side') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('advisory_dd_side')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div class="ac-assessment-field">
                <label for="advisory-dd-period">{{ __('assessment.form.advisory_dd_period') }}</label>
                <input id="advisory-dd-period" type="text" name="advisory_dd_period" value="{{ old('advisory_dd_period') }}" class="front-contact-input h-11 w-full text-sm" data-required-when-active>
                @error('advisory_dd_period')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
            </div>
        </div>
        <div class="ac-assessment-field ac-assessment-field--full">
            <label for="advisory-dd-loi-signed">{{ __('assessment.form.advisory_dd_loi_signed') }}</label>
            <select id="advisory-dd-loi-signed" name="advisory_dd_loi_signed" class="front-contact-input h-11 w-full text-sm" data-required-when-active>
                <option value="">{{ __('assessment.options.choose') }}</option>
                <option value="yes" @selected(old('advisory_dd_loi_signed') === 'yes')>{{ __('assessment.options.yes') }}</option>
                <option value="no" @selected(old('advisory_dd_loi_signed') === 'no')>{{ __('assessment.options.no') }}</option>
            </select>
            @error('advisory_dd_loi_signed')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
        </div>
    </div>

    <div data-advisory-conditional="independent_review" @if ($advisoryService !== 'independent_review') hidden @endif>
        <div class="ac-assessment-grid">
            <div class="ac-assessment-field">
                <label for="advisory-review-client">{{ __('assessment.form.advisory_review_client') }}</label>
                <select id="advisory-review-client" name="advisory_review_client" class="front-contact-input h-11 w-full text-sm" data-required-when-active>
                    <option value="">{{ __('assessment.options.choose') }}</option>
                    @foreach (__('assessment.values.review_clients') as $value => $label)
                        <option value="{{ $value }}" @selected(old('advisory_review_client') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('advisory_review_client')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div class="ac-assessment-field">
                <label for="advisory-review-period">{{ __('assessment.form.advisory_review_period') }}</label>
                <input id="advisory-review-period" type="text" name="advisory_review_period" value="{{ old('advisory_review_period') }}" class="front-contact-input h-11 w-full text-sm" data-required-when-active>
                @error('advisory_review_period')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    <div data-advisory-conditional="valuation" @if ($advisoryService !== 'valuation') hidden @endif>
        <div class="ac-assessment-grid">
            <div class="ac-assessment-field">
                <label for="advisory-valuation-purpose">{{ __('assessment.form.advisory_valuation_purpose') }}</label>
                <select id="advisory-valuation-purpose" name="advisory_valuation_purpose" class="front-contact-input h-11 w-full text-sm" data-required-when-active>
                    <option value="">{{ __('assessment.options.choose') }}</option>
                    @foreach (__('assessment.values.valuation_purposes') as $value => $label)
                        <option value="{{ $value }}" @selected(old('advisory_valuation_purpose') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('advisory_valuation_purpose')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div class="ac-assessment-field">
                <label for="advisory-valuation-date">{{ __('assessment.form.advisory_valuation_date') }}</label>
                <input id="advisory-valuation-date" type="date" name="advisory_valuation_date" value="{{ old('advisory_valuation_date') }}" class="front-contact-input h-11 w-full text-sm" data-required-when-active>
                @error('advisory_valuation_date')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    <div data-advisory-conditional="financing" @if ($advisoryService !== 'financing') hidden @endif>
        <div class="ac-assessment-grid">
            <div class="ac-assessment-field">
                <label for="advisory-financing-amount">{{ __('assessment.form.advisory_financing_amount') }}</label>
                <input id="advisory-financing-amount" type="text" name="advisory_financing_amount" value="{{ old('advisory_financing_amount') }}" class="front-contact-input h-11 w-full text-sm" data-required-when-active>
                @error('advisory_financing_amount')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div class="ac-assessment-field">
                <label for="advisory-financing-purpose">{{ __('assessment.form.advisory_financing_purpose') }}</label>
                <select id="advisory-financing-purpose" name="advisory_financing_purpose" class="front-contact-input h-11 w-full text-sm" data-required-when-active>
                    <option value="">{{ __('assessment.options.choose') }}</option>
                    @foreach (__('assessment.values.financing_purposes') as $value => $label)
                        <option value="{{ $value }}" @selected(old('advisory_financing_purpose') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('advisory_financing_purpose')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
            </div>
        </div>
        <div class="ac-assessment-field ac-assessment-field--full">
            <label for="advisory-financing-existing-debt">{{ __('assessment.form.advisory_financing_existing_debt') }}</label>
            <select id="advisory-financing-existing-debt" name="advisory_financing_existing_debt" class="front-contact-input h-11 w-full text-sm" data-required-when-active>
                <option value="">{{ __('assessment.options.choose') }}</option>
                <option value="yes" @selected(old('advisory_financing_existing_debt') === 'yes')>{{ __('assessment.options.yes') }}</option>
                <option value="no" @selected(old('advisory_financing_existing_debt') === 'no')>{{ __('assessment.options.no') }}</option>
            </select>
            @error('advisory_financing_existing_debt')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
        </div>
    </div>
</div>

<div class="ac-assessment-section">
    <div class="ac-assessment-section-head">
        <h3>{{ __('assessment.sections.advisory_target_company') }}</h3>
    </div>

    <div class="ac-assessment-field ac-assessment-field--full">
        <label for="advisory-target-relation">{{ __('assessment.form.advisory_target_relation') }}</label>
        <select id="advisory-target-relation" name="advisory_target_relation" class="front-contact-input h-11 w-full text-sm" data-advisory-target-relation required>
            @foreach ($targetRelationOptions as $value => $label)
                <option value="{{ $value }}" @selected($targetRelation === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('advisory_target_relation')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div class="ac-assessment-dependent-fields" data-advisory-target-fields @if ($targetRelation !== 'different') hidden @endif>
        <div class="ac-assessment-grid">
            <div class="ac-assessment-field">
                <label for="advisory-target-company-name">{{ __('assessment.form.advisory_target_company_name') }}</label>
                <input id="advisory-target-company-name" type="text" name="advisory_target_company_name" value="{{ old('advisory_target_company_name') }}" class="front-contact-input h-11 w-full text-sm" data-required-when-active>
                @error('advisory_target_company_name')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div class="ac-assessment-field">
                <label for="advisory-target-company-oib">{{ __('assessment.form.advisory_target_company_oib') }}</label>
                <input id="advisory-target-company-oib" type="text" name="advisory_target_company_oib" value="{{ old('advisory_target_company_oib') }}" class="front-contact-input h-11 w-full text-sm" data-required-when-active>
                @error('advisory_target_company_oib')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="ac-assessment-field ac-assessment-field--full">
            <label for="advisory-target-company-activity">{{ __('assessment.form.advisory_target_company_activity') }}</label>
            <input id="advisory-target-company-activity" type="text" name="advisory_target_company_activity" value="{{ old('advisory_target_company_activity') }}" class="front-contact-input h-11 w-full text-sm" data-required-when-active>
            @error('advisory_target_company_activity')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
        </div>

        <div class="ac-assessment-grid">
            <div class="ac-assessment-field">
                <label for="advisory-target-company-revenue">{{ __('assessment.form.advisory_target_company_revenue') }}</label>
                <select id="advisory-target-company-revenue" name="advisory_target_company_revenue" class="front-contact-input h-11 w-full text-sm" data-required-when-active>
                    <option value="">{{ __('assessment.options.choose') }}</option>
                    @foreach ($revenueOptions as $value => $label)
                        <option value="{{ $value }}" @selected(old('advisory_target_company_revenue') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('advisory_target_company_revenue')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div class="ac-assessment-field">
                <label for="advisory-target-company-employees">{{ __('assessment.form.advisory_target_company_employees') }}</label>
                <select id="advisory-target-company-employees" name="advisory_target_company_employees" class="front-contact-input h-11 w-full text-sm" data-required-when-active>
                    <option value="">{{ __('assessment.options.choose') }}</option>
                    @foreach ($employeeOptions as $value => $label)
                        <option value="{{ $value }}" @selected(old('advisory_target_company_employees') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('advisory_target_company_employees')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="ac-assessment-field ac-assessment-field--full">
            <label for="advisory-target-related-companies">{{ __('assessment.form.advisory_target_related_companies') }}</label>
            <select id="advisory-target-related-companies" name="advisory_target_related_companies" class="front-contact-input h-11 w-full text-sm" data-advisory-related-companies data-required-when-active>
                <option value="">{{ __('assessment.options.choose') }}</option>
                <option value="yes" @selected($relatedCompanies === 'yes')>{{ __('assessment.options.yes') }}</option>
                <option value="no" @selected($relatedCompanies === 'no')>{{ __('assessment.options.no') }}</option>
            </select>
            @error('advisory_target_related_companies')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
        </div>

        <div class="ac-assessment-field ac-assessment-field--full" data-advisory-consolidated-field @if ($relatedCompanies !== 'yes') hidden @endif>
            <label for="advisory-target-consolidated-review">{{ __('assessment.form.advisory_target_consolidated_review') }}</label>
            <select id="advisory-target-consolidated-review" name="advisory_target_consolidated_review" class="front-contact-input h-11 w-full text-sm" data-required-when-active>
                <option value="">{{ __('assessment.options.choose') }}</option>
                <option value="yes" @selected(old('advisory_target_consolidated_review') === 'yes')>{{ __('assessment.options.yes') }}</option>
                <option value="no" @selected(old('advisory_target_consolidated_review') === 'no')>{{ __('assessment.options.no') }}</option>
            </select>
            @error('advisory_target_consolidated_review')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
        </div>
    </div>
</div>
