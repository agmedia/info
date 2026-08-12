@extends('front.desktop.layouts.store')

@php
    $careerEmail = 'info@alphacapitalis.com';
    $careerUrl = 'mailto:'.$careerEmail.'?subject='.rawurlencode((string) __('ui.team.career_email_subject'));
    $pageTitleBreadcrumbs = [
        ['label' => __('ui.front.desktop.footer.home'), 'url' => route('home')],
        ['label' => __('ui.team.page_title'), 'current' => true],
    ];
@endphp

@section('title', __('ui.team.page_title'))
@section('main_class', 'w-full px-0 py-0')

@section('content')
    <div class="ac-team-page">
        <x-front.page-title-band
            :breadcrumbs="$pageTitleBreadcrumbs"
            section-class="ac-team-title-band"
            breadcrumb-class="ac-team-title-breadcrumb"
        >
            <div class="ac-page-title-copy">
                <h1>{{ __('ui.team.title') }}</h1>
                <p>{{ __('ui.team.subtitle') }}</p>
            </div>
        </x-front.page-title-band>

        <section class="ac-team-section">
            <div class="ac-team-container">
                @if ($members->isEmpty())
                    <div class="ac-team-page-empty border border-dashed border-slate-300 bg-white/80 px-6 py-14 text-center shadow-[0_18px_54px_rgba(15,23,42,0.06)]">
                        <h2 class="text-2xl font-black tracking-tight text-slate-950">{{ __('ui.team.empty_title') }}</h2>
                        <p class="mx-auto mt-3 max-w-[34rem] text-sm leading-7 text-slate-600">{{ __('ui.team.empty') }}</p>
                    </div>
                @else
                    <div class="ac-team-member-list space-y-6">
                        @foreach ($members as $member)
                            <article class="ac-team-member-card overflow-hidden border border-slate-200 bg-white p-4 sm:p-4 lg:p-5">
                                <div class="ac-team-member-layout grid gap-4 lg:grid-cols-[220px_minmax(0,1fr)] lg:items-start lg:gap-5">
                                    <div class="ac-team-member-media self-start overflow-hidden border border-slate-200 bg-white">
                                        <div class="relative overflow-hidden">
                                            @if ($member['photo_url'] !== '')
                                                <img
                                                    src="{{ $member['photo_url'] }}"
                                                    alt="{{ $member['name'] }}"
                                                    class="ac-team-member-photo block h-auto w-full bg-white"
                                                    loading="lazy"
                                                    decoding="async"
                                                >
                                            @else
                                                <div class="ac-team-member-photo flex h-full items-center justify-center">
                                                    <span class="text-6xl font-black tracking-[0.18em] text-white/92">{{ $member['initials'] }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="ac-team-member-head border-b border-slate-100 pb-3.5">
                                        <h3 class="ac-team-member-name text-[1.2rem] font-black leading-tight tracking-tight text-slate-950 sm:text-[1.38rem]">{{ $member['name'] }}</h3>
                                        @if ($member['position'] !== '')
                                            <p class="ac-team-role mt-2 text-[0.8rem] font-semibold uppercase text-sky-800">
                                                {{ $member['position'] }}
                                            </p>
                                        @endif
                                    </div>

                                    @if ($member['description_html'] !== '')
                                        <div class="ac-team-member-bio">
                                            @if ($member['has_long_description'])
                                                <div class="ac-team-bio mt-4">
                                                    <input id="team-bio-{{ $member['id'] }}" type="checkbox" class="ac-team-bio-toggle">
                                                    <p class="ac-team-bio-excerpt text-[0.9rem] leading-7 text-slate-600">
                                                        {{ $member['description_excerpt'] }}
                                                    </p>
                                                    <div class="ac-team-bio-panel">
                                                        <div class="ac-team-bio-panel-inner">
                                                            <div class="content-richtext ac-team-bio-content text-[0.9rem] leading-7 text-slate-600">
                                                                {!! $member['description_html'] !!}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <label for="team-bio-{{ $member['id'] }}" class="ac-team-bio-trigger">
                                                        <span class="ac-team-bio-more">{{ __('ui.team.read_more') }}</span>
                                                        <span class="ac-team-bio-less">{{ __('ui.team.read_less') }}</span>
                                                    </label>
                                                </div>
                                            @else
                                                <div class="content-richtext mt-4 text-[0.9rem] leading-7 text-slate-600">
                                                    {!! $member['description_html'] !!}
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                    @php
                                        $memberPhoneHref = preg_replace('/[^0-9+]/', '', $member['mobile_phone']);
                                    @endphp

                                    <div class="ac-team-member-actions mt-4 flex flex-wrap gap-2.5">
                                        @if ($member['email'] !== '')
                                            <a href="mailto:{{ $member['email'] }}" title="{{ __('ui.team.social.email') }}" aria-label="{{ __('ui.team.social.email') }}" class="ac-team-social-link inline-flex h-10 w-10 items-center justify-center border border-slate-200 bg-slate-50 text-slate-700 transition hover:border-sky-200 hover:bg-white hover:text-sky-900">
                                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                    <path d="M3 5.75h14v8.5a1.25 1.25 0 0 1-1.25 1.25H4.25A1.25 1.25 0 0 1 3 14.25v-8.5Z"></path>
                                                    <path d="m4 6.5 6 4.75 6-4.75"></path>
                                                </svg>
                                            </a>
                                        @endif
                                        @if ($member['mobile_phone'] !== '' && $memberPhoneHref !== '')
                                            <a href="tel:{{ $memberPhoneHref }}" title="{{ __('ui.team.social.phone') }}" aria-label="{{ __('ui.team.social.phone') }}" class="ac-team-social-link inline-flex h-10 w-10 items-center justify-center border border-slate-200 bg-slate-50 text-slate-700 transition hover:border-sky-200 hover:bg-white hover:text-sky-900">
                                                <svg class="h-4 w-4" viewBox="0 0 384 512" fill="currentColor" aria-hidden="true">
                                                    <path d="M16 64C16 28.7 44.7 0 80 0L304 0c35.3 0 64 28.7 64 64l0 384c0 35.3-28.7 64-64 64L80 512c-35.3 0-64-28.7-64-64L16 64zM128 440c0 13.3 10.7 24 24 24l80 0c13.3 0 24-10.7 24-24s-10.7-24-24-24l-80 0c-13.3 0-24 10.7-24 24zM304 64l-224 0 0 304 224 0 0-304z"></path>
                                                </svg>
                                            </a>
                                        @endif
                                        @if ($member['facebook_url'] !== '')
                                            <a href="{{ $member['facebook_url'] }}" target="_blank" rel="noopener noreferrer" title="{{ __('ui.team.social.facebook') }}" aria-label="{{ __('ui.team.social.facebook') }}" class="ac-team-social-link inline-flex h-10 w-10 items-center justify-center border border-slate-200 bg-slate-50 text-slate-700 transition hover:border-sky-200 hover:bg-white hover:text-sky-900">
                                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path d="M11.167 17v-6.091h2.042l.306-2.373h-2.348V7.02c0-.686.19-1.153 1.173-1.153H13.6V3.744c-.218-.03-.967-.094-1.839-.094-1.82 0-3.067 1.11-3.067 3.149v1.737H6.636v2.373h2.058V17h2.473Z"></path>
                                                </svg>
                                            </a>
                                        @endif
                                        @if ($member['twitter_url'] !== '')
                                            <a href="{{ $member['twitter_url'] }}" target="_blank" rel="noopener noreferrer" title="{{ __('ui.team.social.twitter') }}" aria-label="{{ __('ui.team.social.twitter') }}" class="ac-team-social-link inline-flex h-10 w-10 items-center justify-center border border-slate-200 bg-slate-50 text-slate-700 transition hover:border-sky-200 hover:bg-white hover:text-sky-900">
                                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path d="M4.36 4h3.04l3 4.215L14.09 4H16l-4.775 5.452L16.5 16h-3.04l-3.244-4.556L6.216 16H4.31l5.01-5.72L4.36 4Zm2.1 1.42h-.73l7.81 9.16h.73l-7.81-9.16Z"></path>
                                                </svg>
                                            </a>
                                        @endif
                                        @if ($member['linkedin_url'] !== '')
                                            <a href="{{ $member['linkedin_url'] }}" target="_blank" rel="noopener noreferrer" title="{{ __('ui.team.social.linkedin') }}" aria-label="{{ __('ui.team.social.linkedin') }}" class="ac-team-social-link inline-flex h-10 w-10 items-center justify-center border border-slate-200 bg-slate-50 text-slate-700 transition hover:border-sky-200 hover:bg-white hover:text-sky-900">
                                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path d="M5.057 7.3H2.6V17h2.457V7.3Zm.16-3.156A1.43 1.43 0 0 0 3.793 2.7a1.43 1.43 0 0 0-1.424 1.444c0 .793.63 1.438 1.406 1.438H3.8c.794 0 1.417-.645 1.417-1.438ZM17 11.104C17 8.179 15.438 6.82 13.354 6.82c-1.682 0-2.435.926-2.856 1.576V7.3H8.042c.032.728 0 9.7 0 9.7h2.456v-5.418c0-.29.021-.58.107-.787.235-.58.77-1.18 1.67-1.18 1.177 0 1.648.89 1.648 2.197V17H17v-5.896Z"></path>
                                                </svg>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <section class="ac-team-career-card mt-12 border border-slate-200 bg-white p-6 sm:p-7 lg:p-8">
                        <div class="max-w-[70rem]">
                            <h2 class="text-[1.65rem] font-black leading-tight text-slate-950 sm:text-[1.9rem]">
                                {{ __('ui.team.career_title') }}
                            </h2>
                            <p class="mt-4 text-[0.96rem] leading-8 text-slate-600">
                                {{ __('ui.team.career_body') }}
                            </p>
                            <a href="{{ $careerUrl }}" class="front-action-cta ac-team-cta-dark mt-6">
                                <span>{{ __('ui.team.career_button') }}</span>
                                <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M4 12L12 4"></path>
                                    <path d="M6 4h6v6"></path>
                                </svg>
                            </a>
                        </div>
                    </section>
                @endif
            </div>
        </section>
    </div>
@endsection

@push('styles')
    <style>
        .ac-team-page {
            --ac-team-bg-warm: #f6f1e7;
            --ac-team-bg-light: #fbf6ed;
            --ac-team-blue: #10213a;
            --ac-team-gold: #7c653b;
            --ac-team-line: rgba(120, 96, 58, 0.05);
            min-height: 100vh;
            background: var(--ac-team-bg-warm);
            color: #101820;
        }

        .ac-team-page p {
            margin: 0;
        }

        .ac-team-container {
            width: min(100% - 2rem, 1320px);
            margin: 0 auto;
        }

        @media (min-width: 640px) {
            .ac-team-container {
                width: min(100% - 3rem, 1272px);
            }
        }

        @media (min-width: 1024px) {
            .ac-team-container {
                width: min(100% - 4rem, 1256px);
            }
        }

        .ac-team-title-band {
            margin-bottom: 0;
            background: var(--ac-team-bg-warm);
            border-top-color: transparent;
            border-bottom-color: var(--ac-team-line);
        }

        .ac-team-title-band .ac-page-title-copy h1 {
            color: #101820;
            font-size: 2.65rem;
            font-weight: 700;
            line-height: 1.1;
            letter-spacing: 0;
        }

        .ac-team-title-band .ac-page-title-copy > p,
        .ac-team-title-band .front-scroll-breadcrumb-link,
        .ac-team-title-band .front-scroll-breadcrumb-current,
        .ac-team-title-band .front-scroll-breadcrumb-separator {
            color: #4f4a43;
        }

        .ac-team-title-band .ac-page-title-breadcrumb::before,
        .ac-team-title-band .ac-page-title-breadcrumb::after {
            background: rgba(120, 96, 58, 0.07);
        }

        .ac-team-section {
            padding: clamp(3rem, 5.8vw, 5.5rem) 0 clamp(5rem, 7vw, 7rem);
            background: var(--ac-team-bg-warm);
        }

        .ac-team-intro-grid {
            margin-bottom: clamp(2.2rem, 4vw, 3rem);
        }

        .ac-team-page-panel,
        .ac-team-page-empty,
        .ac-team-member-card,
        .ac-team-career-card {
            border: 1px solid rgba(171, 141, 82, 0.12) !important;
            border-radius: 8px;
            background: #fff !important;
            box-shadow: none !important;
        }

        .ac-team-support-panel {
            color: #101820 !important;
        }

        .ac-team-support-panel h2,
        .ac-team-career-card h2,
        .ac-team-list-head h2 {
            color: #101820 !important;
            font-family: "Instrument Sans Variable", Arial, sans-serif;
            font-weight: 700 !important;
            letter-spacing: 0 !important;
        }

        .ac-team-support-panel p,
        .ac-team-intro-panel p:not(.ac-team-kicker),
        .ac-team-list-head p:not(.ac-team-kicker),
        .ac-team-career-card p {
            color: #403a34 !important;
        }

        .ac-team-list-head {
            display: grid;
            justify-items: center;
            margin-left: auto;
            margin-right: auto;
            text-align: center;
        }

        .ac-team-list-head .ac-team-kicker {
            color: var(--ac-team-gold) !important;
        }

        .ac-team-list-head h2 {
            max-width: 28ch;
        }

        .ac-team-list-head p:not(.ac-team-kicker) {
            max-width: 58rem;
            text-wrap: pretty;
        }

        .ac-team-intro-lead {
            font-family: "Instrument Sans Variable", Arial, sans-serif;
            font-size: clamp(1.5rem, 2vw, 1.95rem);
            font-weight: 700;
            line-height: 1.2;
            letter-spacing: 0;
            color: #101820 !important;
        }

        .ac-team-kicker {
            color: var(--ac-team-gold) !important;
            font-size: 0.76rem !important;
            font-weight: 700 !important;
            letter-spacing: 0.18em;
        }

        .ac-team-role {
            color: var(--ac-team-gold) !important;
            letter-spacing: 0.08em;
        }

        .ac-team-cta-light.front-action-cta {
            min-width: 176px;
            border-color: var(--ac-team-blue);
            background: var(--ac-team-blue);
            color: #ffffff !important;
            box-shadow: none;
            border-radius: var(--front-button-radius);
        }

        .ac-team-cta-light.front-action-cta:hover {
            background: #173b5d;
            border-color: #173b5d;
            color: #ffffff !important;
        }

        .ac-team-cta-dark.front-action-cta {
            min-width: 220px;
            border-color: var(--ac-team-blue);
            background: var(--ac-team-blue);
            color: #ffffff !important;
            box-shadow: none;
            border-radius: var(--front-button-radius);
        }

        .ac-team-cta-dark.front-action-cta:hover {
            background: #173b5d;
            border-color: #173b5d;
            color: #ffffff !important;
        }

        .ac-team-member-card {
            padding: 1rem !important;
        }

        .ac-team-member-media {
            border-color: rgba(120, 96, 58, 0.05) !important;
            border-radius: 8px;
            background: #fff !important;
        }

        .ac-team-member-media > div {
            height: 100%;
        }

        .ac-team-member-photo {
            width: 100%;
            height: 100% !important;
            aspect-ratio: auto;
            object-fit: cover;
            object-position: center top;
        }

        .ac-team-member-photo.flex {
            background: var(--ac-team-blue);
        }

        .ac-team-member-head {
            border-bottom-color: rgba(15, 42, 67, 0.06) !important;
        }

        .ac-team-member-name {
            color: #101820 !important;
            font-family: "Instrument Sans Variable", Arial, sans-serif;
            font-weight: 700 !important;
            letter-spacing: 0 !important;
        }

        .ac-team-member-card .ac-team-bio-excerpt,
        .ac-team-member-card .ac-team-bio-content,
        .ac-team-member-card .content-richtext {
            color: #403a34 !important;
        }

        .ac-team-social-link {
            border-color: rgba(15, 42, 67, 0.1) !important;
            border-radius: 8px;
            background: #fff !important;
            color: var(--ac-team-blue) !important;
        }

        .ac-team-social-link:hover {
            border-color: rgba(120, 96, 58, 0.32) !important;
            background: #fbf6ed !important;
            color: var(--ac-team-blue) !important;
        }

        .ac-team-bio {
            position: relative;
        }

        .ac-team-bio-toggle {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .ac-team-bio-excerpt {
            max-height: 18rem;
            overflow: hidden;
            opacity: 1;
            transition: max-height 0.32s ease, opacity 0.24s ease, margin 0.32s ease;
        }

        .ac-team-bio-panel {
            display: grid;
            grid-template-rows: 0fr;
            transition: grid-template-rows 0.36s ease;
        }

        .ac-team-bio-panel-inner {
            overflow: hidden;
        }

        .ac-team-bio-content {
            opacity: 0;
            transform: translateY(-8px);
            transition: opacity 0.24s ease, transform 0.3s ease;
        }

        .ac-team-bio-trigger {
            margin-top: 1rem;
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            cursor: pointer;
            color: #334155;
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .ac-team-bio-toggle:checked ~ .ac-team-bio-excerpt {
            max-height: 0;
            margin-top: 0;
            opacity: 0;
        }

        .ac-team-bio-toggle:checked ~ .ac-team-bio-panel {
            grid-template-rows: 1fr;
        }

        .ac-team-bio-toggle:checked ~ .ac-team-bio-panel .ac-team-bio-content {
            opacity: 1;
            transform: translateY(0);
        }

        .ac-team-bio-toggle:checked ~ .ac-team-bio-trigger .ac-team-bio-more {
            display: none;
        }

        .ac-team-bio-toggle:not(:checked) ~ .ac-team-bio-trigger .ac-team-bio-less {
            display: none;
        }

        @media (min-width: 641px) {
            .ac-team-member-layout {
                grid-template-columns: 240px minmax(0, 1fr);
                align-items: start;
                column-gap: 1.25rem;
                row-gap: 1rem;
            }

            .ac-team-member-media {
                grid-column: 1;
                grid-row: 1 / span 3;
                width: 100%;
                max-width: 240px;
                aspect-ratio: 4 / 5.25;
                margin-left: 0;
                margin-right: 0;
            }

            .ac-team-member-head {
                grid-column: 2;
                grid-row: 1;
            }

            .ac-team-member-bio {
                grid-column: 2;
                grid-row: 2;
            }

            .ac-team-member-actions {
                grid-column: 2;
                grid-row: 3;
                margin-top: 0;
            }
        }

        @media (max-width: 720px) {
            .ac-team-container {
                width: min(100% - 1.35rem, 1320px);
            }

            .ac-team-title-band .ac-page-title-copy h1 {
                font-size: 2.1rem;
            }
        }

        @media (max-width: 640px) {
            .ac-team-member-layout {
                grid-template-columns: 108px minmax(0, 1fr);
                align-items: start;
                column-gap: 0.95rem;
                row-gap: 0.85rem;
            }

            .ac-team-member-media {
                width: 108px;
                max-width: 108px;
                aspect-ratio: 4 / 5.15;
                margin-left: 0;
                margin-right: 0;
            }

            .ac-team-member-photo {
                object-fit: cover;
                object-position: center top;
            }

            .ac-team-member-head {
                min-width: 0;
                align-self: center;
                padding-bottom: 0.85rem;
            }

            .ac-team-member-name {
                font-size: 1.14rem;
                line-height: 1.04;
            }

            .ac-team-role {
                margin-top: 0.55rem;
                font-size: 0.72rem;
                letter-spacing: 0.08em;
            }

            .ac-team-member-bio,
            .ac-team-member-actions {
                grid-column: 1 / -1;
            }

            .ac-team-member-bio .ac-team-bio {
                margin-top: 0;
            }

            .ac-team-member-card .ac-team-bio-excerpt,
            .ac-team-member-card .ac-team-bio-content,
            .ac-team-member-card .content-richtext {
                font-size: 0.94rem !important;
                line-height: 1.78 !important;
            }

            .ac-team-member-card .ac-team-bio-excerpt {
                max-height: 13.2rem;
            }

            .ac-team-member-card .ac-team-bio-trigger {
                margin-top: 0.85rem;
                font-size: 0.74rem;
                letter-spacing: 0.06em;
            }

            .ac-team-member-actions {
                margin-top: 0.2rem;
                gap: 0.55rem;
            }

            .ac-team-member-actions a {
                width: 2.35rem;
                height: 2.35rem;
            }

            .ac-team-member-actions a svg {
                width: 0.92rem;
                height: 0.92rem;
            }
        }
    </style>
@endpush
