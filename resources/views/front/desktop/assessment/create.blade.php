@extends('front.desktop.layouts.store')

@section('title', __('assessment.page_title'))
@section('main_class', 'w-full px-0 py-0')
@section('hide_footer_newsletter', '1')

@section('content')
    @php
        $captchaSiteKey = trim((string) ($storeSettings['captcha']['recaptcha_v3_site_key'] ?? ''));
        $captchaEnabled = (bool) ($storeSettings['captcha']['recaptcha_v3_enabled'] ?? false) && $captchaSiteKey !== '';
        $contactEmail = trim((string) ($storeSettings['footer']['email_support'] ?? '')) ?: 'info@alphacapitalis.com';
        $contactPhone = trim((string) ($storeSettings['footer']['phone'] ?? '')) ?: '+385 (1) 580 6656';
        $contactPhoneHref = preg_replace('/\s+/', '', $contactPhone);
        $headingWords = static fn (string $title): array => preg_split('/\s+/u', trim($title), -1, PREG_SPLIT_NO_EMPTY) ?: [];
    @endphp

    <div class="front-contact-page ac-assessment-page" data-assessment-form-root data-locale="{{ app()->getLocale() }}">
        <section class="ac-tool-intro" aria-labelledby="ac-assessment-title">
            <div class="ac-tool-container ac-tool-intro-layout">
                <div class="ac-tool-intro-heading">
                    @php($assessmentHeadingWords = $headingWords(__('assessment.heading')))
                    <h1 class="values-title services-index-intro-title ac-tool-display-title" id="ac-assessment-title" data-words-slide-from-right aria-label="{{ __('assessment.heading') }}">
                        @foreach ($assessmentHeadingWords as $word)
                            <span class="values-word animation-index-{{ $loop->index }} {{ $loop->last && count($assessmentHeadingWords) > 1 ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                        @endforeach
                    </h1>
                </div>

                <div class="ac-tool-intro-copy content-reveal animation-index-1" data-image-reveal>
                    <p>{{ __('assessment.subheading') }}</p>
                </div>
            </div>
        </section>

        <section class="front-contact-content-shell ac-tool-content-section" aria-labelledby="ac-assessment-form-title">
            <div class="ac-tool-container">
                <div class="front-contact-layout ac-assessment-layout">
                    <form
                        method="POST"
                        action="{{ route('assessment.store') }}"
                        class="front-contact-form ac-assessment-form content-reveal animation-index-0"
                        novalidate
                        data-image-reveal
                        @if($captchaEnabled) data-recaptcha-form data-recaptcha-site-key="{{ $captchaSiteKey }}" data-recaptcha-action="collaboration_assessment_form" @endif
                    >
                        @csrf
                        <input type="hidden" name="recaptcha_token" value="" data-recaptcha-token>

                        <div class="front-contact-form-head">
                            <p class="front-contact-section-kicker">{{ __('assessment.form.kicker') }}</p>
                            <h2 id="ac-assessment-form-title">{{ __('assessment.form.title') }}</h2>
                            <p>{{ __('assessment.form.intro') }}</p>
                        </div>

                        @if (session('status'))
                            <div class="front-contact-status" role="status">
                                <i class="fa-light fa-circle-check" aria-hidden="true"></i>
                                <span>{{ session('status') }}</span>
                            </div>
                        @endif

                        <div class="ac-assessment-section">
                            <div class="ac-assessment-section-head">
                                <h3>{{ __('assessment.sections.company') }}</h3>
                            </div>

                            <div class="ac-assessment-grid">
                                <div class="ac-assessment-field">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('assessment.form.company_name') }}</label>
                                    <input type="text" name="company_name" value="{{ old('company_name') }}" class="front-contact-input h-11 w-full text-sm" required>
                                    @error('company_name')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                                </div>
                                <div class="ac-assessment-field">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('assessment.form.company_oib') }}</label>
                                    <input type="text" name="company_oib" value="{{ old('company_oib') }}" class="front-contact-input h-11 w-full text-sm" required>
                                    @error('company_oib')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            <div class="ac-assessment-grid">
                                <div class="ac-assessment-field">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('assessment.form.activity') }}</label>
                                    <input type="text" name="activity" value="{{ old('activity') }}" class="front-contact-input h-11 w-full text-sm" required>
                                    @error('activity')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                                </div>
                                <div class="ac-assessment-field">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('assessment.form.potential_start_date') }}</label>
                                    <div class="ac-assessment-date-field" data-assessment-date-field="assessment-start-date">
                                        <span class="ac-lease-date-display is-placeholder" data-assessment-date-display="assessment-start-date">{{ __('assessment.form.date_placeholder') }}</span>
                                        <input
                                            id="assessment-start-date"
                                            type="date"
                                            name="potential_start_date"
                                            value="{{ old('potential_start_date') }}"
                                            class="ac-lease-date-input"
                                        >
                                        <button
                                            type="button"
                                            class="ac-lease-date-trigger"
                                            data-assessment-date-trigger="assessment-start-date"
                                            aria-expanded="false"
                                            aria-label="{{ __('assessment.form.open_calendar') }}: {{ __('assessment.form.potential_start_date') }}"
                                        >
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" aria-hidden="true">
                                                <rect x="3" y="5" width="18" height="16" rx="2"></rect>
                                                <path d="M16 3v4M8 3v4M3 10h18"></path>
                                            </svg>
                                        </button>
                                        <div class="ac-lease-calendar" data-assessment-calendar-panel hidden>
                                            <div class="ac-lease-calendar-head">
                                                <button type="button" class="ac-lease-calendar-nav" data-assessment-calendar-prev aria-label="{{ __('assessment.form.previous_month') }}">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" aria-hidden="true">
                                                        <path d="M15 18l-6-6 6-6"></path>
                                                    </svg>
                                                </button>
                                                <div class="ac-lease-calendar-title" data-assessment-calendar-title></div>
                                                <label class="sr-only" for="assessment-start-year">{{ __('assessment.form.select_year') }}</label>
                                                <select id="assessment-start-year" class="ac-lease-calendar-year" data-assessment-calendar-year></select>
                                                <button type="button" class="ac-lease-calendar-nav" data-assessment-calendar-next aria-label="{{ __('assessment.form.next_month') }}">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" aria-hidden="true">
                                                        <path d="M9 18l6-6-6-6"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                            <div class="ac-lease-calendar-weekdays" data-assessment-calendar-weekdays></div>
                                            <div class="ac-lease-calendar-grid" data-assessment-calendar-grid></div>
                                        </div>
                                    </div>
                                    @error('potential_start_date')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            <div class="ac-assessment-grid">
                                <div class="ac-assessment-field">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('assessment.form.contact_email') }}</label>
                                    <input type="email" name="contact_email" value="{{ old('contact_email') }}" class="front-contact-input h-11 w-full text-sm" required>
                                    @error('contact_email')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                                </div>
                                <div class="ac-assessment-field">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('assessment.form.contact_phone') }}</label>
                                    <input type="text" name="contact_phone" value="{{ old('contact_phone') }}" class="front-contact-input h-11 w-full text-sm" required>
                                    @error('contact_phone')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="ac-assessment-section">
                            <div class="ac-assessment-section-head">
                                <h3>{{ __('assessment.sections.volume') }}</h3>
                            </div>

                            <div class="ac-assessment-grid">
                                <div class="ac-assessment-field">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('assessment.form.incoming_invoices_monthly') }}</label>
                                    <input type="text" name="incoming_invoices_monthly" value="{{ old('incoming_invoices_monthly') }}" class="front-contact-input h-11 w-full text-sm" required>
                                    @error('incoming_invoices_monthly')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                                </div>
                                <div class="ac-assessment-field">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('assessment.form.outgoing_invoices_monthly') }}</label>
                                    <input type="text" name="outgoing_invoices_monthly" value="{{ old('outgoing_invoices_monthly') }}" class="front-contact-input h-11 w-full text-sm" required>
                                    @error('outgoing_invoices_monthly')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            <div class="ac-assessment-grid">
                                <div class="ac-assessment-field">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('assessment.form.bank_accounts_monthly') }}</label>
                                    <input type="text" name="bank_accounts_monthly" value="{{ old('bank_accounts_monthly') }}" class="front-contact-input h-11 w-full text-sm" required>
                                    @error('bank_accounts_monthly')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                                </div>
                                <div class="ac-assessment-field">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('assessment.form.payroll_calculations_monthly') }}</label>
                                    <input type="text" name="payroll_calculations_monthly" value="{{ old('payroll_calculations_monthly') }}" class="front-contact-input h-11 w-full text-sm" required>
                                    @error('payroll_calculations_monthly')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            <div class="ac-assessment-field ac-assessment-field--full">
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('assessment.form.other_calculations_monthly') }}</label>
                                <textarea name="other_calculations_monthly" rows="3" class="front-contact-textarea ac-assessment-textarea w-full text-sm">{{ old('other_calculations_monthly') }}</textarea>
                                @error('other_calculations_monthly')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="ac-assessment-section">
                            <div class="ac-assessment-section-head">
                                <h3>{{ __('assessment.sections.process') }}</h3>
                            </div>

                            <div class="ac-assessment-grid">
                                <div class="ac-assessment-field">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('assessment.form.inventory_bookkeeping') }}</label>
                                    <select name="inventory_bookkeeping" class="front-contact-input h-11 w-full text-sm">
                                        <option value="">{{ __('assessment.options.choose') }}</option>
                                        <option value="yes" @selected(old('inventory_bookkeeping') === 'yes')>{{ __('assessment.options.yes') }}</option>
                                        <option value="no" @selected(old('inventory_bookkeeping') === 'no')>{{ __('assessment.options.no') }}</option>
                                    </select>
                                    @error('inventory_bookkeeping')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                                </div>
                                <div class="ac-assessment-field">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('assessment.form.cost_centers_tracking') }}</label>
                                    <select name="cost_centers_tracking" class="front-contact-input h-11 w-full text-sm">
                                        <option value="">{{ __('assessment.options.choose') }}</option>
                                        <option value="yes" @selected(old('cost_centers_tracking') === 'yes')>{{ __('assessment.options.yes') }}</option>
                                        <option value="no" @selected(old('cost_centers_tracking') === 'no')>{{ __('assessment.options.no') }}</option>
                                    </select>
                                    @error('cost_centers_tracking')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            <div class="ac-assessment-grid">
                                <div class="ac-assessment-field">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('assessment.form.monthly_reporting') }}</label>
                                    <select name="monthly_reporting" class="front-contact-input h-11 w-full text-sm">
                                        <option value="">{{ __('assessment.options.choose') }}</option>
                                        <option value="yes" @selected(old('monthly_reporting') === 'yes')>{{ __('assessment.options.yes') }}</option>
                                        <option value="no" @selected(old('monthly_reporting') === 'no')>{{ __('assessment.options.no') }}</option>
                                    </select>
                                    @error('monthly_reporting')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                                </div>
                                <div class="ac-assessment-field">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('assessment.form.travel_orders_monthly') }}</label>
                                    <input type="text" name="travel_orders_monthly" value="{{ old('travel_orders_monthly') }}" class="front-contact-input h-11 w-full text-sm">
                                    @error('travel_orders_monthly')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            <div class="ac-assessment-field ac-assessment-field--full">
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('assessment.form.incoming_invoice_payments') }}</label>
                                <textarea name="incoming_invoice_payments" rows="3" class="front-contact-textarea ac-assessment-textarea w-full text-sm">{{ old('incoming_invoice_payments') }}</textarea>
                                @error('incoming_invoice_payments')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="ac-assessment-section">
                            <div class="ac-assessment-section-head">
                                <h3>{{ __('assessment.sections.special') }}</h3>
                            </div>

                            <div class="ac-assessment-grid">
                                <div class="ac-assessment-field">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('assessment.form.intrastat_obligation') }}</label>
                                    <textarea name="intrastat_obligation" rows="3" class="front-contact-textarea ac-assessment-textarea w-full text-sm">{{ old('intrastat_obligation') }}</textarea>
                                    @error('intrastat_obligation')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                                </div>
                                <div class="ac-assessment-field">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('assessment.form.audit_obligation') }}</label>
                                    <textarea name="audit_obligation" rows="3" class="front-contact-textarea ac-assessment-textarea w-full text-sm">{{ old('audit_obligation') }}</textarea>
                                    @error('audit_obligation')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            <div class="ac-assessment-grid">
                                <div class="ac-assessment-field">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('assessment.form.vat_status') }}</label>
                                    <textarea name="vat_status" rows="3" class="front-contact-textarea ac-assessment-textarea w-full text-sm">{{ old('vat_status') }}</textarea>
                                    @error('vat_status')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                                </div>
                                <div class="ac-assessment-field ac-assessment-field--tall-control">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('assessment.form.accounting_software') }}</label>
                                    <input type="text" name="accounting_software" value="{{ old('accounting_software') }}" class="front-contact-input h-11 w-full text-sm">
                                    @error('accounting_software')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            <div class="ac-assessment-grid">
                                <div class="ac-assessment-field">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('assessment.form.tax_issues') }}</label>
                                    <textarea name="tax_issues" rows="3" class="front-contact-textarea ac-assessment-textarea w-full text-sm">{{ old('tax_issues') }}</textarea>
                                    @error('tax_issues')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                                </div>
                                <div class="ac-assessment-field">
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('assessment.form.document_delivery') }}</label>
                                    <textarea name="document_delivery" rows="3" class="front-contact-textarea ac-assessment-textarea w-full text-sm">{{ old('document_delivery') }}</textarea>
                                    @error('document_delivery')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="ac-assessment-section">
                            <div class="ac-assessment-section-head">
                                <h3>{{ __('assessment.sections.additional') }}</h3>
                            </div>

                            <div class="ac-assessment-field ac-assessment-field--full">
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('assessment.form.additional_requirements') }}</label>
                                <textarea name="additional_requirements" rows="5" class="front-contact-textarea ac-assessment-textarea ac-assessment-textarea--lg w-full text-sm">{{ old('additional_requirements') }}</textarea>
                                @error('additional_requirements')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="front-contact-consent-wrap">
                            <label class="front-contact-consent">
                                <input type="checkbox" name="accept_terms" value="1" class="front-contact-checkbox mt-0.5 h-4 w-4 border-slate-300 text-slate-900 focus:ring-0" @checked((bool) old('accept_terms'))>
                                <span>{{ __('assessment.form.accept_terms') }}</span>
                            </label>
                            @error('accept_terms')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                            @error('recaptcha_token')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                        </div>

                        <div class="front-contact-form-actions">
                            <button type="submit" class="editorial-dark-button ac-tool-submit">
                                <span>{{ __('assessment.form.submit') }}</span>
                                <i class="fa-light fa-arrow-up-right" aria-hidden="true"></i>
                            </button>
                        </div>
                    </form>

                    <aside class="front-contact-sidebar content-reveal animation-index-1" data-image-reveal aria-label="{{ __('assessment.sidebar.title') }}">
                        <div class="front-contact-panel front-contact-panel--direct">
                            <h2>{{ __('assessment.sidebar.title') }}</h2>
                            <p class="front-contact-panel-intro">{{ __('assessment.sidebar.body') }}</p>

                            <ul class="front-contact-direct-list">
                                <li>
                                    <i class="fa-light fa-chart-mixed" aria-hidden="true"></i>
                                    <span>
                                        <small>{{ __('assessment.sidebar.point_1_label') }}</small>
                                        <strong>{{ __('assessment.sidebar.point_1') }}</strong>
                                    </span>
                                </li>
                                <li>
                                    <i class="fa-light fa-file-lines" aria-hidden="true"></i>
                                    <span>
                                        <small>{{ __('assessment.sidebar.point_2_label') }}</small>
                                        <strong>{{ __('assessment.sidebar.point_2') }}</strong>
                                    </span>
                                </li>
                                <li>
                                    <i class="fa-light fa-envelope" aria-hidden="true"></i>
                                    <span>
                                        <small>{{ __('contact.direct.email') }}</small>
                                        <a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a>
                                    </span>
                                </li>
                                <li>
                                    <i class="fa-light fa-phone" aria-hidden="true"></i>
                                    <span>
                                        <small>{{ __('contact.direct.phone') }}</small>
                                        <a href="tel:{{ $contactPhoneHref }}">{{ $contactPhone }}</a>
                                    </span>
                                </li>
                            </ul>
                        </div>

                        <div class="front-contact-help">
                            <span class="front-contact-help-icon" aria-hidden="true">
                                <i class="fa-light fa-circle-info"></i>
                            </span>
                            <div>
                                <h3>{{ __('assessment.help.title') }}</h3>
                                <p>{{ __('assessment.help.body') }}</p>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </section>
    </div>

    @if ($captchaEnabled)
        @push('scripts')
            <script src="https://www.google.com/recaptcha/api.js?render={{ $captchaSiteKey }}"></script>
        @endpush
    @endif

    @push('scripts')
        <script>
            (function () {
                const root = document.querySelector('[data-assessment-form-root]');
                if (!root) {
                    return;
                }

                const locale = root.dataset.locale === 'hr' ? 'hr-HR' : 'en-US';
                const datePlaceholder = locale === 'hr-HR' ? 'dd.mm.gggg' : 'mm/dd/yyyy';
                const calendarWeekdays = locale === 'hr-HR'
                    ? ['Pon', 'Uto', 'Sri', 'Čet', 'Pet', 'Sub', 'Ned']
                    : ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                const calendarMonthFormatter = new Intl.DateTimeFormat(locale, { month: 'long' });
                const dateFields = Array.from(root.querySelectorAll('[data-assessment-date-field]'));
                const dateDisplays = Array.from(root.querySelectorAll('[data-assessment-date-display]'));
                const dateTriggers = Array.from(root.querySelectorAll('[data-assessment-date-trigger]'));

                const capitalize = function (value) {
                    return value.charAt(0).toUpperCase() + value.slice(1);
                };

                const formatInputDate = function (date) {
                    const day = String(date.getDate()).padStart(2, '0');
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const year = String(date.getFullYear());

                    return year + '-' + month + '-' + day;
                };

                const formatDateValue = function (date) {
                    const day = String(date.getDate()).padStart(2, '0');
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const year = String(date.getFullYear());

                    if (locale === 'hr-HR') {
                        return day + '.' + month + '.' + year + '.';
                    }

                    return month + '/' + day + '/' + year;
                };

                const parseDate = function (value) {
                    const match = /^(\d{4})-(\d{2})-(\d{2})$/.exec(String(value || '').trim());
                    if (!match) {
                        return null;
                    }

                    const date = new Date(Number(match[1]), Number(match[2]) - 1, Number(match[3]));
                    return Number.isNaN(date.getTime()) ? null : date;
                };

                const setCalendarMonth = function (field, monthDate) {
                    field.dataset.calendarMonth = String(monthDate.getFullYear()) + '-' + String(monthDate.getMonth());
                };

                const getCalendarMonth = function (field, fallbackDate) {
                    const storedValue = String(field.dataset.calendarMonth || '');
                    const parts = storedValue.split('-');

                    if (parts.length === 2) {
                        const year = Number(parts[0]);
                        const month = Number(parts[1]);
                        if (Number.isInteger(year) && Number.isInteger(month)) {
                            return new Date(year, month, 1);
                        }
                    }

                    return new Date(fallbackDate.getFullYear(), fallbackDate.getMonth(), 1);
                };

                const closeCalendars = function () {
                    dateFields.forEach(function (field) {
                        const panel = field.querySelector('[data-assessment-calendar-panel]');
                        const trigger = field.querySelector('[data-assessment-date-trigger]');

                        field.classList.remove('is-open');

                        if (panel) {
                            panel.hidden = true;
                        }

                        if (trigger instanceof HTMLButtonElement) {
                            trigger.setAttribute('aria-expanded', 'false');
                        }
                    });
                };

                const syncDateDisplays = function () {
                    dateDisplays.forEach(function (display) {
                        const targetId = display.getAttribute('data-assessment-date-display');
                        if (!targetId) {
                            return;
                        }

                        const input = root.querySelector('#' + targetId);
                        if (!(input instanceof HTMLInputElement)) {
                            return;
                        }

                        const parsedDate = parseDate(input.value);
                        const hasValue = parsedDate instanceof Date;

                        display.textContent = hasValue ? formatDateValue(parsedDate) : datePlaceholder;
                        display.classList.toggle('is-placeholder', !hasValue);
                    });
                };

                const renderCalendar = function (field) {
                    const input = field.querySelector('.ac-lease-date-input');
                    const title = field.querySelector('[data-assessment-calendar-title]');
                    const yearSelect = field.querySelector('[data-assessment-calendar-year]');
                    const weekdays = field.querySelector('[data-assessment-calendar-weekdays]');
                    const grid = field.querySelector('[data-assessment-calendar-grid]');

                    if (!(input instanceof HTMLInputElement) || !title || !weekdays || !grid) {
                        return;
                    }

                    const selectedDate = parseDate(input.value);
                    const fallbackDate = selectedDate || new Date();
                    const monthDate = getCalendarMonth(field, fallbackDate);
                    const today = new Date();
                    const currentYear = today.getFullYear();
                    const daysInMonth = new Date(monthDate.getFullYear(), monthDate.getMonth() + 1, 0).getDate();
                    const firstWeekday = (new Date(monthDate.getFullYear(), monthDate.getMonth(), 1).getDay() + 6) % 7;
                    const cells = [];

                    title.textContent = capitalize(calendarMonthFormatter.format(monthDate));

                    if (yearSelect instanceof HTMLSelectElement) {
                        const minYear = Math.min(currentYear - 20, monthDate.getFullYear() - 5);
                        const maxYear = Math.max(currentYear + 30, monthDate.getFullYear() + 5);
                        const options = [];

                        for (let year = minYear; year <= maxYear; year += 1) {
                            options.push('<option value="' + year + '"' + (year === monthDate.getFullYear() ? ' selected' : '') + '>' + year + '</option>');
                        }

                        yearSelect.innerHTML = options.join('');
                        yearSelect.value = String(monthDate.getFullYear());
                    }

                    weekdays.innerHTML = calendarWeekdays.map(function (label) {
                        return '<span>' + label + '</span>';
                    }).join('');

                    for (let index = 0; index < firstWeekday; index += 1) {
                        cells.push('<span class="ac-lease-calendar-cell is-empty" aria-hidden="true"></span>');
                    }

                    for (let day = 1; day <= daysInMonth; day += 1) {
                        const cellDate = new Date(monthDate.getFullYear(), monthDate.getMonth(), day);
                        const isoDate = formatInputDate(cellDate);
                        const isSelected = selectedDate && formatInputDate(selectedDate) === isoDate;
                        const isToday = formatInputDate(today) === isoDate;

                        cells.push(
                            '<button type="button" class="ac-lease-calendar-day' +
                                (isSelected ? ' is-selected' : '') +
                                (isToday ? ' is-today' : '') +
                            '" data-assessment-calendar-day="' + isoDate + '">' + day + '</button>'
                        );
                    }

                    while (cells.length % 7 !== 0) {
                        cells.push('<span class="ac-lease-calendar-cell is-empty" aria-hidden="true"></span>');
                    }

                    grid.innerHTML = cells.join('');

                    grid.querySelectorAll('[data-assessment-calendar-day]').forEach(function (button) {
                        button.addEventListener('click', function () {
                            const isoDate = button.getAttribute('data-assessment-calendar-day');
                            if (!isoDate) {
                                return;
                            }

                            input.value = isoDate;
                            syncDateDisplays();
                            closeCalendars();
                            input.dispatchEvent(new Event('input', { bubbles: true }));
                            input.dispatchEvent(new Event('change', { bubbles: true }));
                        });
                    });
                };

                const openCalendar = function (field) {
                    const input = field.querySelector('.ac-lease-date-input');
                    const panel = field.querySelector('[data-assessment-calendar-panel]');
                    const trigger = field.querySelector('[data-assessment-date-trigger]');
                    const selectedDate = input instanceof HTMLInputElement ? parseDate(input.value) : null;
                    const baseDate = selectedDate || new Date();

                    if (window.matchMedia('(max-width: 760px)').matches) {
                        if (input instanceof HTMLInputElement) {
                            if (typeof input.showPicker === 'function') {
                                input.showPicker();
                            } else {
                                input.focus();
                                input.click();
                            }
                        }
                        return;
                    }

                    closeCalendars();
                    setCalendarMonth(field, new Date(baseDate.getFullYear(), baseDate.getMonth(), 1));
                    renderCalendar(field);

                    field.classList.add('is-open');

                    if (panel) {
                        panel.hidden = false;
                    }

                    if (trigger instanceof HTMLButtonElement) {
                        trigger.setAttribute('aria-expanded', 'true');
                    }
                };

                dateTriggers.forEach(function (trigger) {
                    trigger.addEventListener('click', function () {
                        const targetId = trigger.getAttribute('data-assessment-date-trigger');
                        if (!targetId) {
                            return;
                        }

                        const input = root.querySelector('#' + targetId);
                        if (!(input instanceof HTMLInputElement)) {
                            return;
                        }

                        const field = input.closest('[data-assessment-date-field]');
                        if (!field) {
                            return;
                        }

                        openCalendar(field);
                    });
                });

                dateFields.forEach(function (field) {
                    const display = field.querySelector('[data-assessment-date-display]');
                    const prevButton = field.querySelector('[data-assessment-calendar-prev]');
                    const nextButton = field.querySelector('[data-assessment-calendar-next]');
                    const yearSelect = field.querySelector('[data-assessment-calendar-year]');

                    if (display) {
                        display.addEventListener('click', function () {
                            openCalendar(field);
                        });
                    }

                    if (prevButton instanceof HTMLButtonElement) {
                        prevButton.addEventListener('click', function () {
                            const currentMonth = getCalendarMonth(field, new Date());
                            setCalendarMonth(field, new Date(currentMonth.getFullYear(), currentMonth.getMonth() - 1, 1));
                            renderCalendar(field);
                        });
                    }

                    if (nextButton instanceof HTMLButtonElement) {
                        nextButton.addEventListener('click', function () {
                            const currentMonth = getCalendarMonth(field, new Date());
                            setCalendarMonth(field, new Date(currentMonth.getFullYear(), currentMonth.getMonth() + 1, 1));
                            renderCalendar(field);
                        });
                    }

                    if (yearSelect instanceof HTMLSelectElement) {
                        yearSelect.addEventListener('change', function () {
                            const currentMonth = getCalendarMonth(field, new Date());
                            const nextYear = Number(yearSelect.value);

                            if (!Number.isInteger(nextYear)) {
                                return;
                            }

                            setCalendarMonth(field, new Date(nextYear, currentMonth.getMonth(), 1));
                            renderCalendar(field);
                        });
                    }
                });

                document.addEventListener('click', function (event) {
                    if (event.target.closest('[data-assessment-date-field]')) {
                        return;
                    }

                    closeCalendars();
                });

                document.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape') {
                        closeCalendars();
                    }
                });

                syncDateDisplays();
            }());

            (function () {
                const forms = document.querySelectorAll('[data-recaptcha-form]');

                forms.forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        const tokenInput = form.querySelector('[data-recaptcha-token]');
                        const siteKey = form.dataset.recaptchaSiteKey;
                        const action = form.dataset.recaptchaAction || 'collaboration_assessment_form';

                        if (!tokenInput || !window.grecaptcha || !siteKey) {
                            return;
                        }

                        event.preventDefault();

                        grecaptcha.ready(function () {
                            grecaptcha.execute(siteKey, { action: action }).then(function (token) {
                                tokenInput.value = token || '';
                                form.submit();
                            });
                        });
                    });
                });
            }());
        </script>
    @endpush
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('front-theme/styles/pages/tool-pages.css') }}?v={{ filemtime(public_path('front-theme/styles/pages/tool-pages.css')) }}">
@endpush
