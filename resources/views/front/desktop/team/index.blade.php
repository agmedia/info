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
    $photoMembers = $members->filter(static fn (array $member): bool => trim((string) ($member['photo_url'] ?? '')) !== '')->values();
    $photoOpenLabel = $isCroatian ? 'Otvori fotografiju' : 'Open photo';
    $lightboxCloseLabel = $isCroatian ? 'Zatvori galeriju' : 'Close gallery';
    $lightboxPreviousLabel = $isCroatian ? 'Prethodna fotografija' : 'Previous photo';
    $lightboxNextLabel = $isCroatian ? 'Sljedeća fotografija' : 'Next photo';
@endphp

@section('title', __('ui.team.page_title'))
@section('main_class', 'w-full px-0 py-0')

@section('content')
    <div class="ac-team-page">
        <section class="values-section services-index-intro ac-team-intro" aria-labelledby="ac-team-title">
            <div class="values-inner services-index-intro-layout">
                <div class="values-intro">
                    <h1 class="values-title services-index-intro-title" id="ac-team-title" data-words-slide-from-right aria-label="{{ $teamTitleLead }} {{ $teamTitleAccent }}">
                        <span class="values-word animation-index-0" aria-hidden="true">ALPHA</span>
                        <span class="values-word animation-index-1" aria-hidden="true">CAPITALIS</span>
                        <span class="values-word animation-index-2 is-accent" aria-hidden="true">{{ $teamTitleAccent }}</span>
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
                            <article class="ac-team-member-card content-reveal animation-index-{{ $loop->index % 2 }}" data-image-reveal>
                                <div class="ac-team-member-layout grid gap-4 lg:grid-cols-[220px_minmax(0,1fr)] lg:items-start lg:gap-5">
                                    <div class="ac-team-member-media self-start overflow-hidden border border-slate-200 bg-white">
                                        @if ($member['photo_url'] !== '')
                                            <button
                                                type="button"
                                                class="ac-team-member-photo-trigger relative overflow-hidden image-reveal-media"
                                                data-team-lightbox-trigger
                                                data-team-lightbox-src="{{ $member['photo_url'] }}"
                                                data-team-lightbox-alt="{{ $member['name'] }}"
                                                data-team-lightbox-role="{{ $member['position'] }}"
                                                aria-label="{{ $photoOpenLabel }}: {{ $member['name'] }}"
                                            >
                                                <img
                                                    src="{{ $member['photo_url'] }}"
                                                    alt="{{ $member['name'] }}"
                                                    class="ac-team-member-photo block h-auto w-full bg-white"
                                                    loading="lazy"
                                                    decoding="async"
                                                >
                                                <span class="image-reveal-curtain" aria-hidden="true"></span>
                                                <span class="ac-team-member-photo-zoom" aria-hidden="true">
                                                    <i class="fa-light fa-magnifying-glass-plus"></i>
                                                </span>
                                            </button>
                                        @else
                                            <div class="relative overflow-hidden">
                                                <div class="ac-team-member-photo flex h-full items-center justify-center">
                                                    <span class="text-6xl font-black tracking-[0.18em] text-white/92">{{ $member['initials'] }}</span>
                                                </div>
                                            </div>
                                        @endif
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

        @if ($photoMembers->isNotEmpty())
            <div
                class="ac-team-lightbox"
                data-team-lightbox
                role="dialog"
                aria-modal="true"
                aria-hidden="true"
                aria-labelledby="ac-team-lightbox-caption"
                hidden
            >
                <div class="ac-team-lightbox-backdrop" data-team-lightbox-close aria-hidden="true"></div>

                <div class="ac-team-lightbox-dialog">
                    <button type="button" class="ac-team-lightbox-close" data-team-lightbox-close aria-label="{{ $lightboxCloseLabel }}">
                        <i class="fa-light fa-xmark" aria-hidden="true"></i>
                    </button>

                    @if ($photoMembers->count() > 1)
                        <button type="button" class="ac-team-lightbox-nav is-previous" data-team-lightbox-previous aria-label="{{ $lightboxPreviousLabel }}">
                            <i class="fa-duotone fa-thin fa-arrow-left" aria-hidden="true"></i>
                        </button>
                    @endif

                    <figure class="ac-team-lightbox-figure">
                        <div class="ac-team-lightbox-media">
                            <img src="" alt="" data-team-lightbox-image>
                        </div>
                        <figcaption class="ac-team-lightbox-meta">
                            <span class="ac-team-lightbox-person">
                                <span id="ac-team-lightbox-caption" data-team-lightbox-caption></span>
                                <span class="ac-team-lightbox-role" data-team-lightbox-role></span>
                            </span>
                            <span class="ac-team-lightbox-position" data-team-lightbox-position aria-live="polite"></span>
                        </figcaption>
                    </figure>

                    @if ($photoMembers->count() > 1)
                        <button type="button" class="ac-team-lightbox-nav is-next" data-team-lightbox-next aria-label="{{ $lightboxNextLabel }}">
                            <i class="fa-duotone fa-thin fa-arrow-right" aria-hidden="true"></i>
                        </button>
                    @endif
                </div>
            </div>
        @endif

        @if ($members->isNotEmpty())
            <section class="contact-cta ac-team-contact-cta" aria-labelledby="ac-team-career-title">
                <div class="contact-cta-shell">
                    <div class="contact-cta-copy">
                        <h2 class="contact-cta-title" id="ac-team-career-title" data-words-slide-from-right aria-label="{{ __('ui.team.career_title') }}">
                            @foreach ($careerTitleWords as $word)
                                <span class="contact-cta-title-word animation-index-{{ $loop->index }} {{ $loop->remaining < 2 ? 'is-accent' : '' }}" aria-hidden="true">{{ $word }}</span>
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
    <link rel="stylesheet" href="{{ asset('front-theme/styles/pages/team.css') }}?v={{ filemtime(public_path('front-theme/styles/pages/team.css')) }}">
@endpush

@push('scripts')
    <script defer src="{{ asset('front-theme/scripts/team.js') }}?v={{ filemtime(public_path('front-theme/scripts/team.js')) }}"></script>
@endpush
