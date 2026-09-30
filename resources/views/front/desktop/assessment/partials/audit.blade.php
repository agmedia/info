<div class="ac-assessment-section">
    <div class="ac-assessment-section-head">
        <h3>{{ __('assessment.sections.audit_contact') }}</h3>
    </div>

    <div class="ac-assessment-grid">
        <div class="ac-assessment-field">
            <label for="audit-company-name">{{ __('assessment.form.audit_company_name') }}</label>
            <input id="audit-company-name" type="text" name="audit_company_name" value="{{ old('audit_company_name') }}" class="front-contact-input h-11 w-full text-sm" placeholder="{{ __('assessment.form.audit_company_name_placeholder') }}" required>
            @error('audit_company_name')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
        </div>
        <div class="ac-assessment-field">
            <label for="audit-company-oib">{{ __('assessment.form.audit_company_oib') }}</label>
            <input id="audit-company-oib" type="text" name="audit_company_oib" value="{{ old('audit_company_oib') }}" class="front-contact-input h-11 w-full text-sm" placeholder="{{ __('assessment.form.audit_company_oib_placeholder') }}" required>
            @error('audit_company_oib')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="ac-assessment-grid">
        <div class="ac-assessment-field">
            <label for="audit-contact-email">{{ __('assessment.form.audit_contact_email') }}</label>
            <input id="audit-contact-email" type="email" name="audit_contact_email" value="{{ old('audit_contact_email') }}" class="front-contact-input h-11 w-full text-sm" placeholder="{{ __('assessment.form.audit_contact_email_placeholder') }}" required>
            @error('audit_contact_email')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
        </div>
        <div class="ac-assessment-field">
            <label for="audit-contact-phone">{{ __('assessment.form.audit_contact_phone') }}</label>
            <input id="audit-contact-phone" type="tel" name="audit_contact_phone" value="{{ old('audit_contact_phone') }}" class="front-contact-input h-11 w-full text-sm" placeholder="{{ __('assessment.form.audit_contact_phone_placeholder') }}" required>
            @error('audit_contact_phone')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="ac-assessment-field ac-assessment-field--full">
        <label for="audit-message">{{ __('assessment.form.audit_message') }}</label>
        <textarea id="audit-message" name="audit_message" rows="6" class="front-contact-textarea ac-assessment-textarea ac-assessment-textarea--lg w-full text-sm" placeholder="{{ __('assessment.form.audit_message_placeholder') }}" required>{{ old('audit_message') }}</textarea>
        @error('audit_message')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
    </div>
</div>
