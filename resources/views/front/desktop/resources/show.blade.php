@extends('front.desktop.layouts.store')

@section('title', $document['title'])
@section('main_class', 'w-full px-0 py-0 pb-0')

@section('content')
    @php
        $pageTitleBreadcrumbs = [
            ['label' => __('ui.front.desktop.footer.home'), 'url' => route('home')],
            ['label' => __('resources.page_title'), 'url' => route('resources.index')],
            ['label' => $document['title'], 'current' => true],
        ];
        $accentClasses = match ($document['group_code']) {
            'sector-analysis' => 'from-[#0f172a] via-[#1d4ed8] to-[#f59e0b]',
            'transaction-analysis' => 'from-[#102542] via-[#0f766e] to-[#fbbf24]',
            default => 'from-[#111827] via-[#334155] to-[#f8b84e]',
        };
    @endphp

    <x-front.page-title-band :breadcrumbs="$pageTitleBreadcrumbs">
        <div class="ac-page-title-copy">
            <h1>{{ $document['title'] }}</h1>
        </div>
    </x-front.page-title-band>

    <section id="resource-request-form" class="ac-resource-detail-section">
        <div class="ac-resource-detail-shell">
            <div class="ac-resource-detail-copy-wrap">
                <div class="ac-resource-detail-copy">
                    <article class="ac-resource-preview-card">
                        @if ($document['cover_image_url'])
                            <div class="ac-resource-preview-media">
                                <img src="{{ $document['cover_image_url'] }}" alt="{{ $document['title'] }}" class="ac-resource-preview-image">
                            </div>
                        @else
                            <div class="ac-resource-preview-media ac-resource-preview-media--fallback bg-gradient-to-br {{ $accentClasses }}">
                                <div class="ac-resource-preview-fallback"></div>
                            </div>
                        @endif
                    </article>
                </div>
            </div>

            <div class="ac-resource-form-wrap">
                <div class="ac-resource-form-panel">
                    <div class="ac-resource-form-head">
                        <p class="ac-resource-form-kicker">{{ __('resources.form.title') }}</p>
                        <h2>{{ __('resources.detail.title') }}</h2>
                        <p>{{ __('resources.detail.intro') }}</p>
                    </div>

                    <form
                        method="POST"
                        action="{{ route('resources.request', ['slug' => $document['slug']]) }}"
                        class="ac-resource-form"
                        novalidate
                        data-resource-request-form
                        data-msg-name-required="{{ __('resources.validation.inline.name_required') }}"
                        data-msg-email-required="{{ __('resources.validation.inline.email_required') }}"
                        data-msg-email-invalid="{{ __('resources.validation.inline.email_invalid') }}"
                        data-msg-accept-terms="{{ __('resources.validation.inline.accept_terms') }}"
                        @if($captchaEnabled) data-recaptcha-form data-recaptcha-site-key="{{ $captchaSiteKey }}" data-recaptcha-action="resource_download_request" @endif
                    >
                        @csrf
                        <input type="hidden" name="recaptcha_token" value="" data-recaptcha-token>

                        @if (session('status'))
                            <div class="ac-resource-status" role="status">
                                {{ session('status') }}
                            </div>
                        @endif

                        <div class="ac-resource-form-field" data-resource-field>
                            <label class="ac-resource-form-label" for="resource-name">{{ __('resources.form.name') }}</label>
                            <input id="resource-name" type="text" name="name" value="{{ old('name', auth()->user()?->name) }}" class="ac-resource-form-input" required>
                            <p class="ac-resource-form-error {{ $errors->has('name') ? '' : 'hidden' }}" data-field-error="name">@error('name'){{ $message }}@enderror</p>
                        </div>

                        <div class="ac-resource-form-field" data-resource-field>
                            <label class="ac-resource-form-label" for="resource-company">{{ __('resources.form.company') }}</label>
                            <input id="resource-company" type="text" name="company" value="{{ old('company') }}" class="ac-resource-form-input">
                            <p class="ac-resource-form-error {{ $errors->has('company') ? '' : 'hidden' }}" data-field-error="company">@error('company'){{ $message }}@enderror</p>
                        </div>

                        <div class="ac-resource-form-field" data-resource-field>
                            <label class="ac-resource-form-label" for="resource-email">{{ __('resources.form.email') }}</label>
                            <input id="resource-email" type="email" name="email" value="{{ old('email', auth()->user()?->email) }}" class="ac-resource-form-input" required>
                            <p class="ac-resource-form-error {{ $errors->has('email') ? '' : 'hidden' }}" data-field-error="email">@error('email'){{ $message }}@enderror</p>
                        </div>

                        <div class="ac-resource-form-field" data-resource-field>
                            <label class="ac-resource-form-label" for="resource-phone">{{ __('resources.form.phone') }}</label>
                            <input id="resource-phone" type="text" name="phone" value="{{ old('phone') }}" class="ac-resource-form-input">
                            <p class="ac-resource-form-error {{ $errors->has('phone') ? '' : 'hidden' }}" data-field-error="phone">@error('phone'){{ $message }}@enderror</p>
                        </div>

                        <div class="ac-resource-form-consent-wrap" data-resource-consent-field>
                            <label class="ac-resource-form-consent">
                                <input type="checkbox" name="accept_terms" value="1" class="ac-resource-form-checkbox" @checked((bool) old('accept_terms'))>
                                <span>{{ __('resources.form.accept_terms') }}</span>
                            </label>
                            <p class="ac-resource-form-error {{ $errors->has('accept_terms') ? '' : 'hidden' }}" data-field-error="accept_terms">@error('accept_terms'){{ $message }}@enderror</p>
                        </div>

                        <div class="ac-resource-form-actions">
                            <button type="submit" class="ac-resource-submit-button">
                                {{ __('resources.form.submit') }}
                            </button>
                            <p class="ac-resource-form-error {{ $errors->has('recaptcha_token') ? '' : 'hidden' }}" data-field-error="recaptcha_token">@error('recaptcha_token'){{ $message }}@enderror</p>
                            @error('resource')
                                <p class="ac-resource-form-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        .ac-resource-detail-section {
            --ac-resource-preview-radius: 0;
        }

        .ac-resource-detail-section {
            position: relative;
            margin-inline: calc(50% - 50vw);
            background:
                linear-gradient(90deg, #f7f3ec 0%, #f7f3ec 50%, #f7fafc 50%, #f7fafc 100%);
            border-top: 1px solid rgba(120, 96, 58, 0.05);
            border-bottom: 1px solid rgba(120, 96, 58, 0.05);
            overflow: hidden;
        }

        .ac-resource-detail-section::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 50%;
            background:
                radial-gradient(92% 128% at 100% 0%, rgba(148, 163, 184, 0.12), transparent 46%),
                linear-gradient(90deg, rgba(15, 23, 42, 0.04) 0 1px, transparent 1px 100%),
                linear-gradient(180deg, rgba(15, 23, 42, 0.025) 0 1px, transparent 1px 100%),
                linear-gradient(180deg, #fcfdfe 0%, #f5f8fb 100%);
            background-size: auto, 36px 36px, 36px 36px, auto;
            opacity: 0.9;
            pointer-events: none;
        }

        .ac-resource-detail-shell {
            position: relative;
            z-index: 1;
            display: grid;
            align-items: center;
            width: min(100%, 96rem);
            margin: 0 auto;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            gap: clamp(2rem, 3vw, 3rem);
            min-height: 46rem;
        }

        .ac-resource-detail-copy-wrap,
        .ac-resource-form-wrap {
            position: relative;
            padding: clamp(2rem, 3vw, 3rem);
        }

        .ac-resource-detail-copy-wrap::before,
        .ac-resource-form-wrap::before {
            content: '';
            position: absolute;
            inset: 0;
            opacity: 0.45;
            pointer-events: none;
        }

        .ac-resource-detail-copy,
        .ac-resource-form-panel {
            position: relative;
            z-index: 1;
        }

        .ac-resource-detail-copy {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .ac-resource-preview-card {
            width: 100%;
            max-width: 100%;
            height: min(100%, 40.625rem);
            max-height: 40.625rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: var(--ac-resource-preview-radius);
            overflow: hidden;
        }

        .ac-resource-preview-media {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: inherit;
            overflow: hidden;
        }

        .ac-resource-preview-image {
            display: block;
            width: auto;
            height: 100%;
            max-height: 40.625rem;
            max-width: 100%;
            object-fit: contain;
            object-position: center center;
            border-radius: inherit;
        }

        .ac-resource-preview-media--fallback {
            border-radius: inherit;
        }

        .ac-resource-detail-copy-wrap::before {
            background-image: linear-gradient(90deg, rgba(16, 33, 58, 0.06) 0 1px, transparent 1px 100%);
            background-size: 42px 42px;
        }

        .ac-resource-form-wrap {
            display: flex;
            align-items: stretch;
            justify-content: flex-start;
            padding: clamp(2.8rem, 4.8vw, 4.4rem) clamp(1.5rem, 3.5vw, 3rem) clamp(4rem, 7vw, 6rem);
            overflow: hidden;
        }

        .ac-resource-form-wrap::before {
            display: none;
        }

        .ac-resource-form-panel {
            width: 100%;
            max-width: 34rem;
            margin-right: auto;
        }

        .ac-resource-form-head {
            display: grid;
            gap: 0.55rem;
            margin-bottom: 1.8rem;
        }

        .ac-resource-form-kicker {
            color: #7b8797;
            font-size: 0.76rem;
            font-weight: 700;
            letter-spacing: 0.18em;
            text-transform: uppercase;
        }

        .ac-resource-form-head h2 {
            margin: 0;
            color: #10213a;
            font-family: var(--front-font-family, "Instrument Sans Variable", Arial, sans-serif);
            font-size: clamp(1.9rem, 2.5vw, 2.45rem);
            font-weight: 600;
            line-height: 1.04;
        }

        .ac-resource-form-head p:last-child {
            max-width: 31rem;
            margin: 0;
            color: #54667d;
            font-size: 0.96rem;
            line-height: 1.78;
        }

        .ac-resource-form {
            display: grid;
            gap: 1.3rem;
        }

        .ac-resource-form-field {
            display: grid;
        }

        .ac-resource-form-field.is-active .ac-resource-form-label,
        .ac-resource-form-field.is-filled .ac-resource-form-label {
            color: #44566e;
        }

        .ac-resource-form-label {
            display: block;
            margin-bottom: 0.35rem;
            color: #6a7c92;
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            transition: color 0.18s ease;
        }

        .ac-resource-form-input {
            width: 100%;
            border: 0;
            border-bottom: 1px solid rgba(15, 23, 42, 0.1);
            background: transparent;
            min-height: 3.15rem;
            padding: 0.8rem 0 0.9rem;
            color: #0f172a;
            font-size: 0.98rem;
            line-height: 1.6;
            outline: none;
            transition: border-color 0.18s ease, box-shadow 0.18s ease, background-color 0.18s ease;
        }

        .ac-resource-form-input:focus {
            border-bottom-color: #173b5d;
            box-shadow: none;
            background: transparent;
        }

        .ac-resource-form-input:-webkit-autofill,
        .ac-resource-form-input:-webkit-autofill:hover,
        .ac-resource-form-input:-webkit-autofill:focus {
            -webkit-text-fill-color: #0f172a;
            -webkit-box-shadow: 0 0 0 1000px transparent inset;
            transition: background-color 9999s ease-out 0s;
        }

        .ac-resource-form-error {
            margin-top: 0.45rem;
            color: #b42318;
            font-size: 0.78rem;
            font-weight: 700;
            line-height: 1.45;
        }

        .ac-resource-status {
            border-radius: 1rem;
            background: rgba(22, 163, 74, 0.1);
            padding: 0.85rem 1rem;
            color: #166534;
            font-size: 0.92rem;
            font-weight: 600;
            line-height: 1.6;
        }

        .ac-resource-form-consent-wrap {
            display: grid;
            gap: 0.4rem;
            padding-top: 0.75rem;
            border-top: 1px solid rgba(120, 96, 58, 0.05);
        }

        .ac-resource-form-consent-wrap.is-selected .ac-resource-form-consent {
            color: #10213a;
        }

        .ac-resource-form-consent {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            color: #475569;
            font-size: 0.88rem;
            line-height: 1.6;
            transition: color 0.18s ease;
        }

        .ac-resource-form-checkbox {
            flex: 0 0 auto;
            width: 1rem;
            height: 1rem;
            margin-top: 0.18rem;
            accent-color: #10213a;
        }

        .ac-resource-form-actions {
            display: grid;
            gap: 0.55rem;
            padding-top: 0.4rem;
        }

        .ac-resource-submit-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            min-height: 3.2rem;
            padding: 0.95rem 1.35rem;
            border: 1px solid #0f2a43;
            border-radius: var(--front-button-radius);
            background: #0f2a43;
            color: #fff;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            cursor: pointer;
            transition: background-color 0.18s ease, border-color 0.18s ease, transform 0.18s ease;
        }

        .ac-resource-submit-button:hover {
            background: #173b5d;
            border-color: #173b5d;
            transform: translateY(-1px);
        }

        @media (max-width: 1180px) {
            .ac-resource-detail-shell {
                grid-template-columns: 1fr;
                gap: 0;
                min-height: 0;
            }

            .ac-resource-detail-section {
                background:
                    linear-gradient(180deg, #f7f3ec 0%, #f7f3ec 50%, #f7fafc 50%, #f7fafc 100%);
            }

            .ac-resource-detail-section::after {
                top: 50%;
                right: 0;
                bottom: 0;
                left: 0;
            }

            .ac-resource-detail-copy {
                max-width: none;
                height: auto;
            }

            .ac-resource-detail-copy-wrap,
            .ac-resource-form-wrap {
                padding: 1.5rem 1rem;
            }

            .ac-resource-preview-card {
                height: auto;
            }

            .ac-resource-preview-media {
                aspect-ratio: 16 / 11;
                height: auto;
                align-items: center;
            }

            .ac-resource-preview-image {
                width: 100%;
                height: 100%;
                object-fit: contain;
            }

            .ac-resource-form-wrap {
                padding: 2.4rem 1.4rem 3rem;
            }
        }

        @media (max-width: 767px) {
            .ac-resource-detail-copy-wrap,
            .ac-resource-form-wrap {
                padding: 1.3rem 1rem;
            }

            .ac-resource-preview-card {
                margin-top: 1.15rem;
                border-radius: 0;
            }

            .ac-resource-preview-body {
                padding: 1.2rem 1.1rem 1.35rem;
            }

            .ac-resource-form-wrap {
                padding: 2rem 1.15rem 2.4rem;
            }

            .ac-resource-form-head h2 {
                font-size: clamp(1.7rem, 7vw, 2.05rem);
            }
        }
    </style>
@endpush

@if ($captchaEnabled)
    @push('scripts')
        <script src="https://www.google.com/recaptcha/api.js?render={{ $captchaSiteKey }}"></script>
    @endpush
@endif

@push('scripts')
    <script>
        (function () {
            const form = document.querySelector('[data-resource-request-form]');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const shouldFocusForm = {{ ($errors->any() || session('status')) ? 'true' : 'false' }};

            if (!form) {
                return;
            }

            const clearError = function (field) {
                const errorNode = form.querySelector('[data-field-error="' + field + '"]');
                if (!errorNode) {
                    return;
                }

                errorNode.textContent = '';
                errorNode.classList.add('hidden');
            };

            const setError = function (field, message) {
                const errorNode = form.querySelector('[data-field-error="' + field + '"]');
                if (!errorNode) {
                    return;
                }

                errorNode.textContent = message;
                errorNode.classList.remove('hidden');
            };

            const getHeaderOffset = function () {
                const stickyHeader = document.querySelector('[data-front-sticky-header]');
                if (!(stickyHeader instanceof HTMLElement)) {
                    return 18;
                }

                return Math.round(stickyHeader.getBoundingClientRect().height) + 18;
            };

            const scrollToForm = function () {
                const target = document.getElementById('resource-request-form') || form;
                if (!(target instanceof HTMLElement)) {
                    return;
                }

                const targetTop = window.pageYOffset + target.getBoundingClientRect().top - getHeaderOffset();

                if (typeof window.__frontAnimateScrollTo === 'function') {
                    window.__frontAnimateScrollTo(targetTop);
                    return;
                }

                window.scrollTo(0, Math.max(0, targetTop));
            };

            const syncFieldState = function (field) {
                if (!(field instanceof HTMLElement)) {
                    return;
                }

                const checkbox = field.querySelector('input[type="checkbox"]');
                if (checkbox instanceof HTMLInputElement) {
                    field.classList.toggle('is-selected', checkbox.checked);
                    return;
                }

                const input = field.querySelector('input, textarea, select');
                if (!(input instanceof HTMLInputElement || input instanceof HTMLTextAreaElement || input instanceof HTMLSelectElement)) {
                    return;
                }

                field.classList.toggle('is-filled', input.value.trim() !== '');
            };

            const bindFieldState = function (field) {
                if (!(field instanceof HTMLElement)) {
                    return;
                }

                const checkbox = field.querySelector('input[type="checkbox"]');
                if (checkbox instanceof HTMLInputElement) {
                    checkbox.addEventListener('change', function () {
                        syncFieldState(field);
                    });
                    syncFieldState(field);
                    return;
                }

                const input = field.querySelector('input, textarea, select');
                if (!(input instanceof HTMLInputElement || input instanceof HTMLTextAreaElement || input instanceof HTMLSelectElement)) {
                    return;
                }

                input.addEventListener('focus', function () {
                    field.classList.add('is-active');
                });

                input.addEventListener('blur', function () {
                    field.classList.remove('is-active');
                    syncFieldState(field);
                });

                ['input', 'change'].forEach(function (eventName) {
                    input.addEventListener(eventName, function () {
                        syncFieldState(field);
                    });
                });

                syncFieldState(field);
            };

            form.querySelectorAll('[data-resource-field], [data-resource-consent-field]').forEach(bindFieldState);

            if (shouldFocusForm) {
                window.requestAnimationFrame(scrollToForm);
            }

            form.addEventListener('submit', function (event) {
                event.preventDefault();

                ['name', 'email', 'accept_terms', 'recaptcha_token'].forEach(clearError);

                const nameField = form.querySelector('[name="name"]');
                const emailField = form.querySelector('[name="email"]');
                const acceptTerms = form.querySelector('[name="accept_terms"]');
                let valid = true;

                if (!nameField || nameField.value.trim() === '') {
                    setError('name', form.dataset.msgNameRequired || '');
                    valid = false;
                }

                const emailValue = emailField ? emailField.value.trim() : '';
                if (emailValue === '') {
                    setError('email', form.dataset.msgEmailRequired || '');
                    valid = false;
                } else if (!emailRegex.test(emailValue)) {
                    setError('email', form.dataset.msgEmailInvalid || '');
                    valid = false;
                }

                if (!acceptTerms || !acceptTerms.checked) {
                    setError('accept_terms', form.dataset.msgAcceptTerms || '');
                    valid = false;
                }

                if (!valid) {
                    scrollToForm();
                    return;
                }

                const tokenInput = form.querySelector('[data-recaptcha-token]');
                const siteKey = form.dataset.recaptchaSiteKey;
                const action = form.dataset.recaptchaAction || 'resource_download_request';
                if (!tokenInput || !window.grecaptcha || !siteKey) {
                    form.submit();
                    return;
                }

                grecaptcha.ready(function () {
                    grecaptcha.execute(siteKey, { action: action }).then(function (token) {
                        tokenInput.value = token || '';
                        form.submit();
                    });
                });
            });
        }());
    </script>
@endpush
