@extends('front.desktop.layouts.store')

@php
    $careerEmail = 'info@alphacapitalis.com';
    $careerUrl = 'mailto:'.$careerEmail.'?subject='.rawurlencode((string) __('ui.team.career_email_subject'));
    $isCroatian = str_starts_with(strtolower((string) $locale), 'hr');
    $teamTitleLead = 'ALPHA CAPITALIS';
    $teamTitleAccent = $isCroatian ? 'Tim' : 'Team';
    $careerBodyParts = preg_split('/(?<=\.)\s+/u', trim((string) __('ui.team.career_body')), 2) ?: [];
    $careerBodyLead = $careerBodyParts[0] ?? '';
    $careerBodyRest = $careerBodyParts[1] ?? '';
    $careerTitleWords = collect(preg_split('/\s+/u', trim((string) __('ui.team.career_title'))) ?: [])
        ->filter()
        ->values();
@endphp

@section('title', __('ui.team.page_title'))
@section('main_class', 'w-full px-0 py-0')

@section('content')
    <div class="ac-team-page">
        <section class="values-section services-index-intro ac-team-intro" aria-labelledby="ac-team-title">
            <div class="values-inner services-index-intro-layout">
                <div class="values-intro">
                    <h1 class="values-title services-index-intro-title" id="ac-team-title" data-words-slide-from-right aria-label="{{ $teamTitleLead }} {{ $teamTitleAccent }}">
                        <span class="values-word" style="--value-word-index: 0" aria-hidden="true">ALPHA</span>
                        <span class="values-word" style="--value-word-index: 1" aria-hidden="true">CAPITALIS</span>
                        <span class="values-word is-accent" style="--value-word-index: 2" aria-hidden="true">{{ $teamTitleAccent }}</span>
                    </h1>
                </div>

                <p class="values-copy services-index-intro-copy content-reveal" data-image-reveal>{{ __('ui.team.subtitle') }}</p>
            </div>
        </section>

        <section class="ac-team-section">
            <div class="ac-team-container">
                @if ($members->isEmpty())
                    <div class="ac-team-page-empty border border-dashed border-slate-300 bg-white/80 px-6 py-14 text-center shadow-[0_18px_54px_rgba(15,23,42,0.06)]">
                        <h2 class="text-2xl font-black tracking-tight text-slate-950">{{ __('ui.team.empty_title') }}</h2>
                        <p class="mx-auto mt-3 max-w-[34rem] text-sm leading-7 text-slate-600">{{ __('ui.team.empty') }}</p>
                    </div>
                @else
                    <div class="ac-team-member-list">
                        @foreach ($members as $member)
                            <article class="ac-team-member-card content-reveal" data-image-reveal style="--reveal-index: {{ $loop->index % 2 }}">
                                <div class="ac-team-member-layout grid gap-4 lg:grid-cols-[220px_minmax(0,1fr)] lg:items-start lg:gap-5">
                                    <div class="ac-team-member-media self-start overflow-hidden border border-slate-200 bg-white">
                                        <div class="relative overflow-hidden {{ $member['photo_url'] !== '' ? 'image-reveal-media' : '' }}">
                                            @if ($member['photo_url'] !== '')
                                                <img
                                                    src="{{ $member['photo_url'] }}"
                                                    alt="{{ $member['name'] }}"
                                                    class="ac-team-member-photo block h-auto w-full bg-white"
                                                    loading="lazy"
                                                    decoding="async"
                                            >
                                                <span class="image-reveal-curtain" aria-hidden="true"></span>
                                            @else
                                                <div class="ac-team-member-photo flex h-full items-center justify-center">
                                                    <span class="text-6xl font-black tracking-[0.18em] text-white/92">{{ $member['initials'] }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="ac-team-member-head pb-3.5">
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
                                                    <label for="team-bio-{{ $member['id'] }}" class="ac-team-bio-trigger services-index-inline-link">
                                                        <span class="ac-team-bio-more">{{ __('ui.team.read_more') }}</span>
                                                        <span class="ac-team-bio-less">{{ __('ui.team.read_less') }}</span>
                                                        <i class="fa-duotone fa-thin fa-arrow-right" aria-hidden="true"></i>
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
                                            <a href="mailto:{{ $member['email'] }}" title="{{ __('ui.team.social.email') }}" aria-label="{{ __('ui.team.social.email') }}" class="ac-team-social-link">
                                                <i class="fa-light fa-envelope" aria-hidden="true"></i>
                                            </a>
                                        @endif
                                        @if ($member['mobile_phone'] !== '' && $memberPhoneHref !== '')
                                            <a href="tel:{{ $memberPhoneHref }}" title="{{ __('ui.team.social.phone') }}" aria-label="{{ __('ui.team.social.phone') }}" class="ac-team-social-link">
                                                <i class="fa-light fa-mobile-screen-button" aria-hidden="true"></i>
                                            </a>
                                        @endif
                                        @if ($member['facebook_url'] !== '')
                                            <a href="{{ $member['facebook_url'] }}" target="_blank" rel="noopener noreferrer" title="{{ __('ui.team.social.facebook') }}" aria-label="{{ __('ui.team.social.facebook') }}" class="ac-team-social-link">
                                                <i class="fa-brands fa-facebook-f" aria-hidden="true"></i>
                                            </a>
                                        @endif
                                        @if ($member['twitter_url'] !== '')
                                            <a href="{{ $member['twitter_url'] }}" target="_blank" rel="noopener noreferrer" title="{{ __('ui.team.social.twitter') }}" aria-label="{{ __('ui.team.social.twitter') }}" class="ac-team-social-link">
                                                <i class="fa-brands fa-x-twitter" aria-hidden="true"></i>
                                            </a>
                                        @endif
                                        @if ($member['linkedin_url'] !== '')
                                            <a href="{{ $member['linkedin_url'] }}" target="_blank" rel="noopener noreferrer" title="{{ __('ui.team.social.linkedin') }}" aria-label="{{ __('ui.team.social.linkedin') }}" class="ac-team-social-link">
                                                <i class="fa-brands fa-linkedin-in" aria-hidden="true"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>

                @endif
            </div>
        </section>

        @if ($members->isNotEmpty())
            <section class="contact-cta ac-team-contact-cta" aria-labelledby="ac-team-career-title">
                <div class="contact-cta-shell">
                    <div class="contact-cta-copy">
                        <h2 class="contact-cta-title" id="ac-team-career-title" data-words-slide-from-right aria-label="{{ __('ui.team.career_title') }}">
                            @foreach ($careerTitleWords as $word)
                                <span class="contact-cta-title-word {{ $loop->remaining < 2 ? 'is-accent' : '' }}" style="--services-word-index: {{ $loop->index }}" aria-hidden="true">{{ $word }}</span>
                            @endforeach
                        </h2>
                    </div>

                    <div class="contact-cta-card" data-image-reveal>
                        <div class="contact-cta-card-heading"><span>{{ __('ui.team.eyebrow') }}</span></div>
                        <div class="ac-team-cta-expand">
                            <p class="ac-team-cta-body ac-team-cta-lead">{{ $careerBodyLead }}</p>
                            @if ($careerBodyRest !== '')
                                <input id="ac-team-career-copy" type="checkbox" class="ac-team-cta-toggle">
                                <div class="ac-team-cta-panel">
                                    <div class="ac-team-cta-panel-inner">
                                        <p class="ac-team-cta-body ac-team-cta-rest">{{ $careerBodyRest }}</p>
                                    </div>
                                </div>
                                <label for="ac-team-career-copy" class="ac-team-cta-trigger services-index-inline-link">
                                    <span class="ac-team-cta-more">{{ __('ui.team.read_more') }}</span>
                                    <span class="ac-team-cta-less">{{ __('ui.team.read_less') }}</span>
                                    <i class="fa-duotone fa-thin fa-arrow-right" aria-hidden="true"></i>
                                </label>
                            @endif
                        </div>
                        <a class="contact-cta-button" href="{{ $careerUrl }}">
                            <span>{{ __('ui.team.career_button') }}</span>
                            <i class="fa-duotone fa-thin fa-arrow-right" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
            </section>
        @endif
    </div>
@endsection

@push('styles')
    <style>
        .ac-team-page {
            --ac-team-paper: #f4f1ea;
            --ac-team-paper-light: #fbf9f5;
            --ac-team-navy: #041525;
            --ac-team-copy: rgba(3, 18, 31, 0.72);
            --ac-team-gold: #ad8437;
            --ac-team-line: rgba(3, 18, 31, 0.14);
            min-height: 100vh;
            background: var(--ac-team-paper);
            color: var(--ac-team-navy);
        }

        .ac-team-page p {
            margin: 0;
        }

        .ac-team-intro {
            border-top: 1px solid rgba(120, 96, 58, 0.05);
        }

        .ac-team-intro .services-index-intro-title {
            max-width: none;
            font-size: clamp(2.7rem, 3.2vw, 4.15rem);
            line-height: 1.03;
        }

        .ac-team-intro .services-index-intro-copy {
            display: flex;
            min-height: clamp(4.5rem, 7vw, 6rem);
            align-items: center;
            text-wrap: pretty;
        }

        .ac-team-container {
            width: min(1600px, calc(100% - 136px));
            margin-inline: auto;
        }

        .ac-team-section {
            padding: 0 0 clamp(5rem, 7vw, 7rem);
            background: var(--ac-team-paper);
        }

        .ac-team-member-list {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: clamp(3.5rem, 5vw, 5.75rem) clamp(2rem, 3vw, 3.5rem);
        }

        .ac-team-member-card {
            position: relative;
            min-width: 0;
            padding-top: 1.25rem;
            border-top: 1px solid var(--ac-team-line);
            border-radius: 0 !important;
            background: transparent;
        }

        .ac-team-member-card::before {
            position: absolute;
            top: -1px;
            left: 0;
            width: 4.5rem;
            height: 1px;
            background: var(--ac-team-gold);
            content: "";
        }

        .ac-team-member-layout {
            display: grid;
            grid-template-columns: 200px minmax(0, 1fr) !important;
            align-items: start;
            column-gap: clamp(1.25rem, 2vw, 1.75rem) !important;
            row-gap: 1rem !important;
        }

        .ac-team-member-media {
            grid-column: 1;
            grid-row: 1 / span 3;
            width: 100%;
            max-width: none;
            aspect-ratio: 0.78 / 1;
            border: 1px solid rgba(3, 18, 31, 0.12) !important;
            background: var(--ac-team-paper-light) !important;
        }

        .ac-team-member-card:nth-child(4n + 1) .ac-team-member-media,
        .ac-team-member-card:nth-child(4n + 3) .ac-team-member-media {
            border-radius: clamp(26px, 2.2vw, 40px) 5px clamp(26px, 2.2vw, 40px) 5px;
        }

        .ac-team-member-card:nth-child(4n + 2) .ac-team-member-media,
        .ac-team-member-card:nth-child(4n + 4) .ac-team-member-media {
            border-radius: 5px clamp(26px, 2.2vw, 40px) 5px clamp(26px, 2.2vw, 40px);
        }

        .ac-team-member-media > div {
            width: 100%;
            height: 100%;
        }

        .ac-team-member-photo {
            width: 100%;
            height: 100% !important;
            object-fit: cover;
            object-position: center top;
            filter: saturate(0.84) contrast(1.02);
            transition: transform 700ms cubic-bezier(0.16, 1, 0.3, 1);
        }

        .ac-team-member-media .image-reveal-media {
            --image-reveal-curtain-color: var(--ac-team-paper);
        }

        .ac-team-member-photo.flex {
            background: var(--ac-team-paper-light);
        }

        .ac-team-member-card:hover .ac-team-member-photo:not(.flex) {
            transform: scale(1.035);
        }

        .ac-team-member-head {
            grid-column: 2;
            grid-row: 1;
            min-width: 0;
            padding-bottom: 1rem !important;
            border-bottom: 0 !important;
        }

        .ac-team-member-name {
            margin: 0;
            color: var(--ac-team-navy) !important;
            font-family: "Bodoni Moda Variable", "Times New Roman", serif;
            font-size: clamp(1.35rem, 1.55vw, 1.7rem) !important;
            font-weight: 500 !important;
            line-height: 1.13 !important;
            letter-spacing: -0.022em !important;
            text-wrap: balance;
        }

        .ac-team-role {
            margin-top: 0.65rem !important;
            color: #7b5b22 !important;
            font-size: 0.68rem !important;
            font-weight: 750 !important;
            letter-spacing: 0.11em !important;
            line-height: 1.45;
        }

        .ac-team-member-bio {
            grid-column: 2;
            grid-row: 2;
            min-width: 0;
        }

        .ac-team-member-bio .ac-team-bio,
        .ac-team-member-bio .content-richtext.mt-4 {
            margin-top: 0 !important;
        }

        .ac-team-member-card .ac-team-bio-excerpt,
        .ac-team-member-card .ac-team-bio-content,
        .ac-team-member-card .content-richtext {
            color: var(--ac-team-copy) !important;
            font-size: 0.88rem !important;
            line-height: 1.7 !important;
        }

        .ac-team-member-card .content-richtext p + p {
            margin-top: 0.85rem;
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
            display: -webkit-box;
            max-height: 10.5rem;
            overflow: hidden;
            opacity: 1;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 7;
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
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            margin-top: 0.9rem;
            padding-bottom: 0.3rem;
            color: #6f4d12;
            cursor: pointer;
            font-size: 0.7rem;
            font-weight: 750;
            letter-spacing: 0.11em;
            text-transform: uppercase;
        }

        .ac-team-bio-trigger i {
            font-size: 0.95rem;
            font-weight: 400;
            line-height: 1;
            transition: transform 220ms ease;
        }

        .ac-team-bio-trigger:hover i {
            transform: translate(2px, -2px);
        }

        .ac-team-bio-toggle:checked ~ .ac-team-bio-excerpt {
            display: block;
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

        .ac-team-bio-toggle:checked ~ .ac-team-bio-trigger .ac-team-bio-more,
        .ac-team-bio-toggle:not(:checked) ~ .ac-team-bio-trigger .ac-team-bio-less {
            display: none;
        }

        .ac-team-member-actions {
            grid-column: 2;
            grid-row: 3;
            display: flex;
            flex-wrap: wrap;
            gap: 0.55rem !important;
            margin-top: 0 !important;
            align-self: end;
        }

        .ac-team-social-link {
            display: inline-flex;
            width: 2.75rem;
            height: 2.75rem;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(173, 132, 55, 0.34) !important;
            border-radius: 14px 3px 14px 3px !important;
            background: transparent !important;
            color: var(--ac-team-navy) !important;
            font-size: 0.98rem;
            --fa-primary-color: currentColor;
            --fa-primary-opacity: 1;
            --fa-secondary-color: currentColor;
            --fa-secondary-opacity: 0.56;
            transition: border-color 240ms ease, background-color 240ms ease, color 240ms ease;
        }

        .ac-team-social-link:hover {
            border-color: var(--gold-light) !important;
            background: var(--gold-light) !important;
            color: var(--ac-team-navy) !important;
        }

        .ac-team-page-empty {
            padding: clamp(2.5rem, 5vw, 4rem);
            border: 1px solid rgba(3, 18, 31, 0.14) !important;
            border-radius: 0;
            background: var(--ac-team-paper-light) !important;
            box-shadow: none !important;
        }

        .ac-team-contact-cta .contact-cta-shell {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .ac-team-contact-cta .contact-cta-card {
            padding: clamp(30px, 3vw, 48px);
        }

        .ac-team-contact-cta .contact-cta-card-heading {
            margin-bottom: clamp(20px, 2vw, 30px);
        }

        .ac-team-cta-expand {
            position: relative;
            margin-bottom: clamp(26px, 3vw, 38px);
        }

        .ac-team-cta-toggle {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .ac-team-contact-cta .ac-team-cta-body {
            max-width: 540px;
            margin: 0;
            color: rgba(247, 247, 245, 0.64);
            font-size: 0.97rem;
            line-height: 1.65;
        }

        .ac-team-cta-panel {
            display: grid;
            grid-template-rows: 0fr;
            transition: grid-template-rows 620ms cubic-bezier(0.16, 1, 0.3, 1);
        }

        .ac-team-cta-panel-inner {
            min-height: 0;
            overflow: hidden;
        }

        .ac-team-contact-cta .ac-team-cta-rest {
            padding-top: 0.85rem;
        }

        .ac-team-cta-trigger {
            display: inline-flex;
            align-items: center;
            gap: 0.65rem;
            margin-top: 1rem;
            padding-bottom: 0.3rem;
            color: var(--gold-light);
            cursor: pointer;
            font-size: 0.7rem;
            font-weight: 750;
            letter-spacing: 0.11em;
            text-transform: uppercase;
        }

        .front-desktop-shell:not(.front-route-home) .front-content-shell .ac-team-contact-cta .ac-team-cta-trigger,
        .front-desktop-shell:not(.front-route-home) .front-content-shell .ac-team-contact-cta .ac-team-cta-trigger:hover,
        .front-desktop-shell:not(.front-route-home) .front-content-shell .ac-team-contact-cta .ac-team-cta-trigger:focus-visible {
            color: var(--gold-light) !important;
        }

        .ac-team-cta-trigger i {
            font-size: 0.92rem;
            transform: none !important;
            transition: none;
        }

        .ac-team-cta-toggle:checked ~ .ac-team-cta-panel {
            grid-template-rows: 1fr;
        }

        .ac-team-cta-toggle:checked ~ .ac-team-cta-trigger .ac-team-cta-more,
        .ac-team-cta-toggle:not(:checked) ~ .ac-team-cta-trigger .ac-team-cta-less {
            display: none;
        }

        .front-desktop-shell:not(.front-route-home) .front-content-shell .ac-team-contact-cta .contact-cta-title {
            max-width: 760px;
            font-size: clamp(3rem, 3.75vw, 4.75rem);
            letter-spacing: -0.018em;
            line-height: 1.04;
        }

        .front-desktop-shell:not(.front-route-home) .front-content-shell .ac-team-contact-cta .contact-cta-button {
            color: var(--navy-deep);
        }

        .front-desktop-shell:not(.front-route-home) .front-content-shell .ac-team-contact-cta .contact-cta-button:hover,
        .front-desktop-shell:not(.front-route-home) .front-content-shell .ac-team-contact-cta .contact-cta-button:focus-visible {
            color: var(--white);
        }

        @media (max-width: 1180px) {
            .ac-team-container {
                width: min(100% - 80px, 960px);
            }

            .ac-team-member-list {
                grid-template-columns: 1fr;
                gap: 4rem;
            }

            .ac-team-member-layout {
                grid-template-columns: 240px minmax(0, 1fr) !important;
            }
        }

        @media (max-width: 720px) {
            .ac-team-intro .services-index-intro-title {
                font-size: clamp(2.2rem, 10.6vw, 3.15rem);
                line-height: 1.04;
            }

            .ac-team-container {
                width: calc(100% - 40px);
            }

            .ac-team-member-list {
                gap: 3.5rem;
            }

            .ac-team-member-layout {
                grid-template-columns: 124px minmax(0, 1fr) !important;
                column-gap: 1rem !important;
                row-gap: 1.1rem !important;
            }

            .ac-team-member-media {
                grid-column: 1;
                grid-row: 1;
                width: 124px;
            }

            .ac-team-member-head {
                grid-column: 2;
                grid-row: 1;
                align-self: center;
                padding-bottom: 0.8rem !important;
            }

            .ac-team-member-name {
                font-size: clamp(1.08rem, 5.2vw, 1.35rem) !important;
            }

            .ac-team-role {
                font-size: 0.61rem !important;
                letter-spacing: 0.09em !important;
            }

            .ac-team-member-bio,
            .ac-team-member-actions {
                grid-column: 1 / -1;
            }

            .ac-team-member-bio {
                grid-row: 2;
            }

            .ac-team-member-actions {
                grid-row: 3;
            }

            .ac-team-member-card .ac-team-bio-excerpt,
            .ac-team-member-card .ac-team-bio-content,
            .ac-team-member-card .content-richtext {
                font-size: 0.92rem !important;
                line-height: 1.76 !important;
            }

            .ac-team-bio-excerpt {
                max-height: 12rem;
                -webkit-line-clamp: 7;
            }

        }
    </style>
@endpush

@push('scripts')
    <script>
        (() => {
            const initTeamReveals = () => {
                const cards = Array.from(document.querySelectorAll('.ac-team-member-card[data-image-reveal]'));

                if (cards.length === 0) {
                    return;
                }

                const reveal = (card) => {
                    card.querySelectorAll('.image-reveal-media').forEach((media) => {
                        const image = media.querySelector('img');

                        if (image?.complete && image.naturalWidth > 0) {
                            media.classList.add('is-loaded');
                        } else {
                            image?.addEventListener('load', () => media.classList.add('is-loaded'), { once: true });
                        }
                    });

                    card.classList.add('is-image-revealed');
                };

                if (window.matchMedia('(prefers-reduced-motion: reduce)').matches || !('IntersectionObserver' in window)) {
                    cards.forEach(reveal);
                    return;
                }

                const observer = new IntersectionObserver((entries) => {
                    entries.forEach((entry) => {
                        if (!entry.isIntersecting) {
                            return;
                        }

                        reveal(entry.target);
                        observer.unobserve(entry.target);
                    });
                }, {
                    root: null,
                    rootMargin: '0px 0px -18% 0px',
                    threshold: 0.05,
                });

                cards.forEach((card) => observer.observe(card));
            };

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initTeamReveals, { once: true });
            } else {
                initTeamReveals();
            }
        })();
    </script>
@endpush
