@extends('front.desktop.layouts.store')

@php
    use Illuminate\Support\Str;

    $isCroatian = str_starts_with(strtolower((string) app()->getLocale()), 'hr');
    $pageTitleBreadcrumbs = [
        ['label' => __('ui.front.desktop.footer.home'), 'url' => route('home')],
        ['label' => __('resources.page_title'), 'url' => route('resources.index')],
        [
            'label' => Str::limit($document['title'], 72, '...'),
            'current' => true,
            'current_class' => 'ac-resources-breadcrumb-current',
            'title' => $document['title'],
        ],
    ];
    $variantClass = match ($document['group_code']) {
        'sector-analysis' => 'is-sector-analysis',
        'transaction-analysis' => 'is-transaction-analysis',
        default => 'is-download',
    };
    $iconClass = match ($document['group_code']) {
        'sector-analysis' => 'fa-chart-line-up',
        'transaction-analysis' => 'fa-arrow-right-arrow-left',
        default => 'fa-file-arrow-down',
    };
    $relatedTitle = trim((string) __('resources.detail.related_title'));
    $headingWords = preg_split('/\s+/u', $relatedTitle, -1, PREG_SPLIT_NO_EMPTY) ?: [];
@endphp

@section('title', $document['title'])
@section('main_class', 'w-full px-0 py-0')

@section('content')
    <div class="ac-resources-page ac-resource-detail-page">
        <x-front.page-title-band
            :breadcrumbs="$pageTitleBreadcrumbs"
            section-class="ac-resource-detail-intro"
            container-class="ac-resources-container"
            hero-class="ac-resource-detail-intro-hero"
            panel-class="ac-resource-detail-intro-grid"
            breadcrumb-class="ac-resources-breadcrumb"
        >
            <p class="ac-resources-kicker content-reveal" data-image-reveal>{{ $document['group_label'] }}</p>
            <h1 class="ac-resource-detail-title content-reveal animation-index-1" data-image-reveal>{{ $document['title'] }}</h1>
        </x-front.page-title-band>

        <section id="resource-request-form" class="ac-resource-detail-section" aria-labelledby="ac-resource-form-title">
            <div class="ac-resource-detail-shell">
                <div class="ac-resource-detail-preview-wrap content-reveal" data-image-reveal>
                    <article class="ac-resource-preview-card">
                        @if ($document['cover_image_url'])
                            <div class="ac-resource-preview-media">
                                <img src="{{ $document['cover_image_url'] }}" alt="{{ $document['title'] }}" class="ac-resource-preview-image" loading="eager" decoding="async">
                            </div>
                        @else
                            <div class="ac-resource-preview-media ac-resource-preview-media--fallback {{ $variantClass }}">
                                <i class="fa-duotone fa-thin {{ $iconClass }}" aria-hidden="true"></i>
                                <span>{{ $document['group_label'] }}</span>
                            </div>
                        @endif

                        <div class="ac-resource-preview-copy">
                            <p class="ac-resources-kicker">{{ __('resources.download_delivery') }}</p>
                            @if ($document['excerpt'])
                                <p>{{ $document['excerpt'] }}</p>
                            @endif
                        </div>
                    </article>
                </div>

                <div class="ac-resource-form-wrap content-reveal animation-index-1" data-image-reveal>
                    <div class="ac-resource-form-panel">
                        <div class="ac-resource-form-head">
                            <p class="ac-resources-kicker">{{ __('resources.form.title') }}</p>
                            <h2 id="ac-resource-form-title">{{ __('resources.detail.title') }}</h2>
                            <p>{{ __('resources.detail.intro') }}</p>
                        </div>

                        <form
                            method="POST"
                            action="{{ route('resources.request', ['slug' => $document['slug']]) }}"
                            class="ac-resource-form"
                            novalidate
                            data-resource-request-form
                            data-should-focus-form="{{ ($errors->any() || session('status')) ? 'true' : 'false' }}"
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
                                    <span>{{ __('resources.form.submit') }}</span>
                                    <i class="fa-duotone fa-thin fa-arrow-right" aria-hidden="true"></i>
                                </button>
                                <p class="ac-resource-form-error {{ $errors->has('recaptcha_token') ? '' : 'hidden' }}" data-field-error="recaptcha_token">@error('recaptcha_token'){{ $message }}@enderror</p>
                                @error('resource')
                                    <p class="ac-resource-form-error">{{ $message }}</p>
                                @enderror
                            </div>
                            @if ($captchaEnabled)
                                @include('front.desktop.partials.recaptcha-disclosure')
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </section>

        @if ($relatedDocuments->isNotEmpty())
            <section class="ac-resources-related" aria-labelledby="ac-resources-related-title">
                <div class="ac-resources-container">
                    <header class="ac-resources-related-head">
                        <h2 id="ac-resources-related-title" data-words-slide-from-right aria-label="{{ $relatedTitle }}">
                            @foreach ($headingWords as $word)
                                <span class="animation-index-{{ $loop->index }} {{ $loop->last ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
                            @endforeach
                        </h2>
                        <p>{{ __('resources.detail.related_intro') }}</p>
                    </header>

                    <div class="ac-resources-grid">
                        @foreach ($relatedDocuments as $item)
                            @include('front.desktop.resources.partials.card', [
                                'item' => $item,
                                'revealIndex' => $loop->index,
                            ])
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('front-theme/styles/pages/resources.css') }}?v={{ filemtime(public_path('front-theme/styles/pages/resources.css')) }}">
@endpush

@if ($captchaEnabled)
    @push('scripts')
        <script src="https://www.google.com/recaptcha/api.js?render={{ $captchaSiteKey }}"></script>
    @endpush
@endif

@push('scripts')
    <script defer src="{{ asset('front-theme/scripts/resources.js') }}?v={{ filemtime(public_path('front-theme/scripts/resources.js')) }}"></script>
@endpush
