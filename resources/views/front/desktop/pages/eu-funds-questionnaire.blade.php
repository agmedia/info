@extends('front.desktop.layouts.store')

@section('title', __('eu_funds_questionnaire.page_title'))
@section('main_class', 'w-full px-0 py-0')
@section('hide_footer_newsletter', '1')

@section('content')
    @php
        $captchaSiteKey = trim((string) ($storeSettings['captcha']['recaptcha_v3_site_key'] ?? ''));
        $captchaEnabled = (bool) ($storeSettings['captcha']['recaptcha_v3_enabled'] ?? false) && $captchaSiteKey !== '';
        $contactEmail = trim((string) ($storeSettings['footer']['email_support'] ?? ''));
        $contactPhone = trim((string) ($storeSettings['footer']['phone'] ?? ''));
        $contactPhoneHref = preg_replace('/\s+/', '', $contactPhone);
        $headingWords = static fn (string $title): array => preg_split('/\s+/u', trim($title), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $employeeOptions = [
            '0' => __('eu_funds_questionnaire.options.employee_count.0'),
            '1_9' => __('eu_funds_questionnaire.options.employee_count.1_9'),
            '10_49' => __('eu_funds_questionnaire.options.employee_count.10_49'),
            '50_249' => __('eu_funds_questionnaire.options.employee_count.50_249'),
            '250_plus' => __('eu_funds_questionnaire.options.employee_count.250_plus'),
        ];
        $relatedCompanyOptions = [
            'yes' => __('eu_funds_questionnaire.options.related_companies.yes'),
            'no' => __('eu_funds_questionnaire.options.related_companies.no'),
        ];
        $projectSectorOptions = [
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
        $plannedCostOptions = [
            'construction' => __('eu_funds_questionnaire.options.planned_costs.construction'),
            'equipment' => __('eu_funds_questionnaire.options.planned_costs.equipment'),
            'innovation_research' => __('eu_funds_questionnaire.options.planned_costs.innovation_research'),
            'energy_efficiency' => __('eu_funds_questionnaire.options.planned_costs.energy_efficiency'),
            'digitalization' => __('eu_funds_questionnaire.options.planned_costs.digitalization'),
        ];
        $investmentAmountOptions = [
            'up_to_100k' => __('eu_funds_questionnaire.options.investment_amount.up_to_100k'),
            '100k_500k' => __('eu_funds_questionnaire.options.investment_amount.100k_500k'),
            '500k_1000k' => __('eu_funds_questionnaire.options.investment_amount.500k_1000k'),
            '1000k_2000k' => __('eu_funds_questionnaire.options.investment_amount.1000k_2000k'),
            'over_2000k' => __('eu_funds_questionnaire.options.investment_amount.over_2000k'),
        ];
        $interestedServiceOptions = [
            'loans' => __('eu_funds_questionnaire.options.interested_services.loans'),
            'investment_incentives' => __('eu_funds_questionnaire.options.interested_services.investment_incentives'),
            'r_and_d_support' => __('eu_funds_questionnaire.options.interested_services.r_and_d_support'),
            'none' => __('eu_funds_questionnaire.options.interested_services.none'),
        ];
        $selectedProjectSectors = collect((array) old('project_sectors', []))->map(fn ($value) => (string) $value)->all();
        $selectedPlannedCosts = collect((array) old('planned_costs', []))->map(fn ($value) => (string) $value)->all();
        $selectedInterestedServices = collect((array) old('interested_services', []))->map(fn ($value) => (string) $value)->all();
        $showAdditionalNotes = old('related_companies') === 'yes' || trim((string) old('additional_notes')) !== '';
        $showProjectSectorOther = in_array('other', $selectedProjectSectors, true) || trim((string) old('project_sector_other')) !== '';
    @endphp

    <div class="front-contact-page ac-assessment-page ac-eu-questionnaire-page">
        <section class="ac-tool-intro" aria-labelledby="ac-eu-questionnaire-title">
            <div class="ac-tool-container ac-tool-intro-layout">
                <div class="ac-tool-intro-heading">
                    @php($questionnaireHeadingWords = $headingWords(__('eu_funds_questionnaire.heading')))
                    <h1 class="values-title services-index-intro-title ac-tool-display-title" id="ac-eu-questionnaire-title" data-words-slide-from-right aria-label="{{ __('eu_funds_questionnaire.heading') }}">
                        @foreach ($questionnaireHeadingWords as $word)
                            <span class="values-word animation-index-{{ $loop->index }} {{ $loop->last && count($questionnaireHeadingWords) > 1 ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                        @endforeach
                    </h1>
                </div>

                <div class="ac-tool-intro-copy content-reveal animation-index-1" data-image-reveal>
                    <p>{{ __('eu_funds_questionnaire.subheading') }}</p>
                </div>
            </div>
        </section>

        <section class="front-contact-content-shell ac-tool-content-section" aria-labelledby="ac-eu-questionnaire-form-title">
            <div class="ac-tool-container">
                <div class="front-contact-layout ac-assessment-layout">
                    <form
                        method="POST"
                        action="{{ \App\Support\Localization\FrontendRoute::url('eu-funds.questionnaire.store') }}"
                        class="front-contact-form ac-assessment-form ac-eu-questionnaire-form content-reveal animation-index-0"
                        novalidate
                        data-eu-funds-questionnaire-form
                        data-image-reveal
                        @if($captchaEnabled) data-recaptcha-form data-recaptcha-site-key="{{ $captchaSiteKey }}" data-recaptcha-action="eu_funds_questionnaire_form" @endif
                    >
                        @csrf
                        <input type="hidden" name="recaptcha_token" value="" data-recaptcha-token>

                        <div class="front-contact-form-head">
                            <p class="front-contact-section-kicker">{{ __('eu_funds_questionnaire.form.kicker') }}</p>
                            <h2 id="ac-eu-questionnaire-form-title">{{ __('eu_funds_questionnaire.form.title') }}</h2>
                            <p>{{ __('eu_funds_questionnaire.form.intro') }}</p>
                        </div>

                        @if (session('status'))
                            <div class="front-contact-status" role="status">
                                <i class="fa-light fa-circle-check" aria-hidden="true"></i>
                                <span>{{ session('status') }}</span>
                            </div>
                        @endif

                        <div class="ac-assessment-section">
                            <div class="ac-assessment-section-head">
                                <h3>{{ __('eu_funds_questionnaire.sections.company') }}</h3>
                            </div>

                            <div class="ac-assessment-grid">
                                <div class="ac-assessment-field">
                                    <label for="eu-questionnaire-company-name" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('eu_funds_questionnaire.form.company_name') }} *</label>
                                    <input id="eu-questionnaire-company-name" type="text" name="company_name" value="{{ old('company_name') }}" class="front-contact-input h-11 w-full text-sm" autocomplete="organization" required>
                                    @error('company_name')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                                </div>
                                <div class="ac-assessment-field">
                                    <label for="eu-questionnaire-company-oib" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('eu_funds_questionnaire.form.company_oib') }} *</label>
                                    <input id="eu-questionnaire-company-oib" type="text" name="company_oib" value="{{ old('company_oib') }}" class="front-contact-input h-11 w-full text-sm" inputmode="numeric" required>
                                    @error('company_oib')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            <div class="ac-assessment-field ac-assessment-field--full">
                                <label for="eu-questionnaire-company-activity" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('eu_funds_questionnaire.form.company_activity') }} *</label>
                                <input id="eu-questionnaire-company-activity" type="text" name="company_activity" value="{{ old('company_activity') }}" class="front-contact-input h-11 w-full text-sm" required>
                                @error('company_activity')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                            </div>

                            <fieldset class="ac-assessment-field ac-assessment-field--full">
                                <legend class="ac-eu-questionnaire-legend">{{ __('eu_funds_questionnaire.form.employee_count') }} *</legend>
                                <div class="ac-eu-questionnaire-option-grid ac-eu-questionnaire-option-grid--compact">
                                    @foreach ($employeeOptions as $value => $label)
                                        <label class="ac-eu-questionnaire-option">
                                            <input type="radio" name="employee_count" value="{{ $value }}" @checked(old('employee_count') === $value) required>
                                            <span>{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('employee_count')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                            </fieldset>

                            <fieldset class="ac-assessment-field ac-assessment-field--full" data-conditional-root="related_companies">
                                <legend class="ac-eu-questionnaire-legend">{{ __('eu_funds_questionnaire.form.related_companies') }} *</legend>
                                <div class="ac-eu-questionnaire-option-grid ac-eu-questionnaire-option-grid--binary">
                                    @foreach ($relatedCompanyOptions as $value => $label)
                                        <label class="ac-eu-questionnaire-option">
                                            <input type="radio" name="related_companies" value="{{ $value }}" @checked(old('related_companies') === $value) required data-conditional-toggle="related_companies">
                                            <span>{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('related_companies')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror

                                <div class="ac-eu-questionnaire-conditional {{ $showAdditionalNotes ? '' : 'hidden' }}" data-conditional-target="related_companies">
                                    <label for="eu-questionnaire-additional-notes" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('eu_funds_questionnaire.form.additional_notes') }} *</label>
                                    <textarea id="eu-questionnaire-additional-notes" name="additional_notes" rows="4" class="front-contact-textarea ac-assessment-textarea w-full text-sm" placeholder="{{ __('eu_funds_questionnaire.form.additional_notes_placeholder') }}">{{ old('additional_notes') }}</textarea>
                                    @error('additional_notes')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                                </div>
                            </fieldset>
                        </div>

                        <div class="ac-assessment-section">
                            <div class="ac-assessment-section-head">
                                <h3>{{ __('eu_funds_questionnaire.sections.investment') }}</h3>
                            </div>

                            <fieldset class="ac-assessment-field ac-assessment-field--full" data-conditional-root="project_sector_other">
                                <legend class="ac-eu-questionnaire-legend">{{ __('eu_funds_questionnaire.form.project_sectors') }} *</legend>
                                <div class="ac-eu-questionnaire-option-grid">
                                    @foreach ($projectSectorOptions as $value => $label)
                                        <label class="ac-eu-questionnaire-option">
                                            <input
                                                type="checkbox"
                                                name="project_sectors[]"
                                                value="{{ $value }}"
                                                @checked(in_array($value, $selectedProjectSectors, true))
                                                @if ($value === 'other') data-conditional-checkbox="project_sector_other" @endif
                                            >
                                            <span>{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('project_sectors')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                                @error('project_sectors.*')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror

                                <div class="ac-eu-questionnaire-conditional {{ $showProjectSectorOther ? '' : 'hidden' }}" data-conditional-target="project_sector_other">
                                    <label for="eu-questionnaire-project-sector-other" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('eu_funds_questionnaire.form.project_sector_other') }}</label>
                                    <input id="eu-questionnaire-project-sector-other" type="text" name="project_sector_other" value="{{ old('project_sector_other') }}" class="front-contact-input h-11 w-full text-sm" placeholder="{{ __('eu_funds_questionnaire.form.project_sector_other_placeholder') }}">
                                    @error('project_sector_other')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                                </div>
                            </fieldset>

                            <div class="ac-assessment-field ac-assessment-field--full">
                                <label for="eu-questionnaire-investment-location" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('eu_funds_questionnaire.form.investment_location') }} *</label>
                                <input id="eu-questionnaire-investment-location" type="text" name="investment_location" value="{{ old('investment_location') }}" class="front-contact-input h-11 w-full text-sm" autocomplete="address-level2" required>
                                @error('investment_location')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                            </div>

                            <fieldset class="ac-assessment-field ac-assessment-field--full">
                                <legend class="ac-eu-questionnaire-legend">{{ __('eu_funds_questionnaire.form.planned_costs') }} *</legend>
                                <div class="ac-eu-questionnaire-option-grid">
                                    @foreach ($plannedCostOptions as $value => $label)
                                        <label class="ac-eu-questionnaire-option">
                                            <input type="checkbox" name="planned_costs[]" value="{{ $value }}" @checked(in_array($value, $selectedPlannedCosts, true))>
                                            <span>{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('planned_costs')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                                @error('planned_costs.*')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                            </fieldset>

                            <fieldset class="ac-assessment-field ac-assessment-field--full">
                                <legend class="ac-eu-questionnaire-legend">{{ __('eu_funds_questionnaire.form.investment_amount') }} *</legend>
                                <div class="ac-eu-questionnaire-option-grid">
                                    @foreach ($investmentAmountOptions as $value => $label)
                                        <label class="ac-eu-questionnaire-option">
                                            <input type="radio" name="investment_amount" value="{{ $value }}" @checked(old('investment_amount') === $value) required>
                                            <span>{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('investment_amount')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                            </fieldset>
                        </div>

                        <div class="ac-assessment-section">
                            <div class="ac-assessment-section-head">
                                <h3>{{ __('eu_funds_questionnaire.sections.services') }}</h3>
                            </div>

                            <fieldset class="ac-assessment-field ac-assessment-field--full">
                                <legend class="ac-eu-questionnaire-legend">{{ __('eu_funds_questionnaire.form.interested_services') }} *</legend>
                                <div class="ac-eu-questionnaire-option-grid">
                                    @foreach ($interestedServiceOptions as $value => $label)
                                        <label class="ac-eu-questionnaire-option">
                                            <input type="checkbox" name="interested_services[]" value="{{ $value }}" @checked(in_array($value, $selectedInterestedServices, true))>
                                            <span>{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('interested_services')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                                @error('interested_services.*')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                            </fieldset>

                            <div class="ac-assessment-grid">
                                <div class="ac-assessment-field">
                                    <label for="eu-questionnaire-contact-name" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('eu_funds_questionnaire.form.contact_name') }} *</label>
                                    <input id="eu-questionnaire-contact-name" type="text" name="contact_name" value="{{ old('contact_name') }}" class="front-contact-input h-11 w-full text-sm" autocomplete="name" required>
                                    @error('contact_name')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                                </div>
                                <div class="ac-assessment-field">
                                    <label for="eu-questionnaire-email" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('eu_funds_questionnaire.form.email') }} *</label>
                                    <input id="eu-questionnaire-email" type="email" name="email" value="{{ old('email', auth()->user()?->email) }}" class="front-contact-input h-11 w-full text-sm" autocomplete="email" required>
                                    @error('email')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            <div class="ac-assessment-field ac-assessment-field--full">
                                <label for="eu-questionnaire-contact-phone" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('eu_funds_questionnaire.form.contact_phone') }} *</label>
                                <input id="eu-questionnaire-contact-phone" type="tel" name="contact_phone" value="{{ old('contact_phone') }}" class="front-contact-input h-11 w-full text-sm" autocomplete="tel" required>
                                @error('contact_phone')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="front-contact-consent-wrap">
                            <label class="front-contact-consent" for="eu-questionnaire-accept-terms">
                                <input id="eu-questionnaire-accept-terms" type="checkbox" name="accept_terms" value="1" class="front-contact-checkbox mt-0.5 h-4 w-4 border-slate-300 text-slate-900 focus:ring-0" @checked((bool) old('accept_terms'))>
                                <span>{{ __('eu_funds_questionnaire.form.accept_terms_label') }}</span>
                            </label>
                            @error('accept_terms')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                            @error('recaptcha_token')<p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                        </div>

                        <p class="ac-eu-questionnaire-privacy">{{ __('eu_funds_questionnaire.privacy_note') }}</p>

                        <div class="front-contact-form-actions">
                            <button type="submit" class="editorial-dark-button ac-tool-submit">
                                <span>{{ __('eu_funds_questionnaire.form.submit') }}</span>
                                <i class="fa-light fa-arrow-up-right" aria-hidden="true"></i>
                            </button>
                        </div>
                    </form>

                    <aside class="front-contact-sidebar content-reveal animation-index-1" data-image-reveal aria-label="{{ __('eu_funds_questionnaire.sidebar.title') }}">
                        <div class="front-contact-panel front-contact-panel--direct">
                            <h2>{{ __('eu_funds_questionnaire.sidebar.title') }}</h2>
                            <p class="front-contact-panel-intro">{{ __('eu_funds_questionnaire.sidebar.body') }}</p>

                            <ul class="front-contact-direct-list">
                                <li>
                                    <i class="fa-light fa-magnifying-glass-chart" aria-hidden="true"></i>
                                    <span>
                                        <small>{{ __('eu_funds_questionnaire.sidebar.point_1_label') }}</small>
                                        <strong>{{ __('eu_funds_questionnaire.sidebar.point_1') }}</strong>
                                    </span>
                                </li>
                                <li>
                                    <i class="fa-light fa-file-check" aria-hidden="true"></i>
                                    <span>
                                        <small>{{ __('eu_funds_questionnaire.sidebar.point_2_label') }}</small>
                                        <strong>{{ __('eu_funds_questionnaire.sidebar.point_2') }}</strong>
                                    </span>
                                </li>
                                @if ($contactEmail !== '')
                                    <li>
                                        <i class="fa-light fa-envelope" aria-hidden="true"></i>
                                        <span>
                                            <small>{{ __('contact.direct.email') }}</small>
                                            <a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a>
                                        </span>
                                    </li>
                                @endif
                                @if ($contactPhone !== '')
                                    <li>
                                        <i class="fa-light fa-phone" aria-hidden="true"></i>
                                        <span>
                                            <small>{{ __('contact.direct.phone') }}</small>
                                            <a href="tel:{{ $contactPhoneHref }}">{{ $contactPhone }}</a>
                                        </span>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </aside>
                </div>
            </div>
        </section>
    </div>

@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('front-theme/styles/pages/tool-pages.css') }}?v={{ filemtime(public_path('front-theme/styles/pages/tool-pages.css')) }}">
    <link rel="stylesheet" href="{{ asset('front-theme/styles/pages/eu-funds-questionnaire.css') }}?v={{ filemtime(public_path('front-theme/styles/pages/eu-funds-questionnaire.css')) }}">
@endpush

@if ($captchaEnabled)
    @push('scripts')
        <script src="https://www.google.com/recaptcha/api.js?render={{ $captchaSiteKey }}"></script>
    @endpush
@endif

@push('scripts')
    <script src="{{ asset('front-theme/scripts/eu-funds-questionnaire.js') }}?v={{ filemtime(public_path('front-theme/scripts/eu-funds-questionnaire.js')) }}"></script>
@endpush
