@extends('front.desktop.layouts.store')

@section('title', __('ui.errors.not_found.title'))
@section('description', __('ui.errors.not_found.body'))
@section('robots', 'noindex,follow')
@section('canonical_policy', 'none')
@section('main_class', 'w-full px-0 py-0')
@section('hide_footer_newsletter', '1')

@section('content')
    <div class="ac-error-page">
        <section class="ac-error-hero" aria-labelledby="not-found-title">
            <div class="ac-error-grid">
                <div class="ac-error-visual" aria-hidden="true">
                    <img class="ac-error-compass" src="{{ asset('front-theme/images/branding/znak-ac.svg') }}" alt="" width="74" height="74">
                    <span class="ac-error-number">404</span>
                    <span class="ac-error-coordinate">45.8150° N · 15.9819° E</span>
                </div>

                <div class="ac-error-copy">
                    <p class="ac-error-eyebrow">{{ __('ui.errors.not_found.eyebrow') }}</p>
                    <h1 id="not-found-title">
                        {{ __('ui.errors.not_found.title_lead') }} <em>{{ __('ui.errors.not_found.title_accent') }}</em>
                    </h1>
                    <p class="ac-error-body">{{ __('ui.errors.not_found.body') }}</p>

                    <div class="ac-error-actions">
                        <a class="ac-error-button ac-error-button--primary" href="{{ route('home') }}">
                            {{ __('ui.errors.not_found.home') }}
                        </a>
                        <a class="ac-error-button ac-error-button--secondary" href="{{ \App\Support\Localization\FrontendRoute::url('search.index') }}">
                            {{ __('ui.errors.not_found.search') }}
                        </a>
                    </div>

                    <p class="ac-error-support">
                        {{ __('ui.errors.not_found.support') }}
                        <a href="{{ \App\Support\Localization\FrontendRoute::url('contact.create') }}">{{ __('ui.errors.not_found.contact') }}</a>
                    </p>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('front-theme/styles/pages/error-404.css') }}?v={{ filemtime(public_path('front-theme/styles/pages/error-404.css')) }}">
@endpush
