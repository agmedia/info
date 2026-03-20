@extends('front.desktop.layouts.store')

@php
    $captchaSiteKey = trim((string) ($storeSettings['captcha']['recaptcha_v3_site_key'] ?? ''));
    $captchaEnabled = (bool) ($storeSettings['captcha']['recaptcha_v3_enabled'] ?? false) && $captchaSiteKey !== '';
    $contactEmail = trim((string) ($storeSettings['footer']['email_support'] ?? '')) ?: 'info@alphacapitalis.com';
    $contactPhone = trim((string) ($storeSettings['footer']['phone'] ?? '')) ?: '+385 (1) 580 6656';
    $contactPhoneHref = preg_replace('/\s+/', '', $contactPhone);
@endphp

@section('title', $servicePageMetaTitle !== '' ? $servicePageMetaTitle : ($servicePageTitle ?? 'Obiteljski biznis'))
@section('main_class', 'w-full px-0 py-0')

@section('content')
    <div class="ac-family-business-page">
        <section class="ac-family-hero">
            <div class="ac-family-hero-media" aria-hidden="true" style="background-image: url('{{ $heroBackgroundUrl }}');"></div>
            <div class="ac-family-hero-overlay"></div>

            <div class="mx-auto w-full max-w-[1240px] px-5 lg:px-8">
                <div class="ac-family-hero-content">
                    <div class="ac-family-hero-shell">
                        <div class="ac-family-hero-copy">
                            <h1 class="ac-family-hero-title">
                                <span class="is-brand">{{ $heroSection['brand_title'] ?? 'ALPHA CAPITALIS' }}</span>
                                <span class="is-subtitle">
                                    <span class="is-subtitle-lead">{{ $heroSection['subtitle_lead'] ?? 'Savjetnici za' }}</span>
                                    <span class="is-subtitle-accent">{{ $heroSection['subtitle_accent'] ?? 'obiteljski biznis' }}</span>
                                </span>
                            </h1>

                            <p class="ac-family-hero-intro">{{ $heroSection['intro'] ?? '' }}</p>

                            <div class="ac-family-hero-actions">
                                <a href="{{ $heroSection['cta_url'] ?? '#family-business-publika' }}" class="front-action-cta">
                                    <span>{{ $heroSection['cta_label'] ?? 'Pružamo vam podršku' }}</span>
                                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M12 5v14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"></path>
                                        <path d="m6 13 6 6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="mx-auto w-full max-w-[1240px] px-5 lg:px-8">
            <section id="family-business-publika" class="ac-family-section ac-family-section--intro">
                <div class="ac-family-foundation-head">
                    <h2>{{ $audienceSection['headline'] }}</h2>
                </div>

                <div class="ac-family-foundation-grid">
                    @foreach ($audienceSection['cards'] as $card)
                        <article class="ac-family-foundation-item">
                            <div class="ac-family-foundation-item-head">
                                <span class="ac-family-foundation-icon" aria-hidden="true">
                                    @switch($card['icon'] ?? null)
                                        @case('founders')
                                            <svg viewBox="0 0 24 24" fill="none">
                                                <path d="M7 19h10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                                <path d="M8 19V9l4-3 4 3v10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M10 12h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                            </svg>
                                            @break
                                        @case('successors')
                                            <svg viewBox="0 0 24 24" fill="none">
                                                <path d="M5 17 11 11l3 3 5-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M15 8h4v4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                            @break
                                        @case('family')
                                            <svg viewBox="0 0 24 24" fill="none">
                                                <circle cx="9" cy="8" r="3" stroke="currentColor" stroke-width="1.8"/>
                                                <circle cx="16.5" cy="9.5" r="2.5" stroke="currentColor" stroke-width="1.8"/>
                                                <path d="M4.5 18a4.5 4.5 0 0 1 9 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                                <path d="M14.5 18a3.5 3.5 0 0 1 7 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                            </svg>
                                            @break
                                        @default
                                            <svg viewBox="0 0 24 24" fill="none">
                                                <rect x="4" y="7" width="16" height="11" rx="2" stroke="currentColor" stroke-width="1.8"/>
                                                <path d="M9 7V6a3 3 0 0 1 6 0v1" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                                <path d="M10 12h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                            </svg>
                                    @endswitch
                                </span>
                                <h3>{{ $card['title'] }}</h3>
                            </div>
                            <p>{{ $card['text'] }}</p>
                        </article>
                    @endforeach
                </div>

                <div class="ac-family-ffi-banner">
                    <div class="ac-family-ffi-banner-copy">
                        <h3>{{ $ffiSection['title'] }}</h3>
                        @foreach ($ffiSection['body'] as $paragraph)
                            <p>{{ $paragraph }}</p>
                        @endforeach
                    </div>

                    <div class="ac-family-ffi-logo-wrap">
                        <img src="{{ $ffiSection['logo_url'] }}" alt="{{ $ffiSection['logo_alt'] }}" class="ac-family-ffi-logo">
                    </div>
                </div>
            </section>

            <section class="ac-family-section ac-family-faq-section" aria-labelledby="ac-family-faq-title">
                <div class="ac-family-faq-head">
                    <div class="ac-family-faq-title-col">
                        <p class="ac-family-section-kicker">{{ $whatWeDoSection['kicker'] }}</p>
                        <h2 id="ac-family-faq-title">{{ $whatWeDoSection['title'] }}</h2>
                    </div>
                    <div class="ac-family-faq-intro-col">
                        <p>{{ $whatWeDoSection['intro'] }}</p>
                    </div>
                </div>

                @if (($familyBusinessFaqs ?? collect())->isNotEmpty())
                    <div class="ac-family-faq-list">
                        @foreach ($familyBusinessFaqs as $faq)
                            @php
                                $translation = $faq->translations->firstWhere('locale', $locale)
                                    ?? $faq->translations->firstWhere('locale', $fallbackLocale)
                                    ?? $faq->translations->first();
                            @endphp
                            @if ($translation)
                                <details class="faq-accordion-item ac-family-faq-item group">
                                    <summary class="faq-accordion-summary ac-family-faq-summary">
                                        <span class="ac-family-faq-question">{{ $translation->question }}</span>
                                        <span class="ac-family-faq-plus" aria-hidden="true">+</span>
                                    </summary>
                                    <div class="ac-family-faq-answer-wrap">
                                        <div class="content-richtext ac-family-faq-answer">
                                            {!! $translation->answer_html ?: '<p>—</p>' !!}
                                        </div>
                                    </div>
                                </details>
                            @endif
                        @endforeach
                    </div>
                @endif
            </section>
        </div>

        <section class="ac-support-story ac-blog-related-section ac-family-section !py-[clamp(2.6rem,4vw,3.3rem)] border-y border-[#d8c4a0]/30" aria-labelledby="ac-family-capabilities-title">
            <div class="mx-auto w-full max-w-[1240px] px-6 lg:px-10">
                <div class="space-y-12 lg:space-y-14">
                    @foreach ($capabilitySections as $sectionIndex => $capabilitySection)
                        <article class="{{ $sectionIndex > 0 ? 'border-t border-[#d8c4a0]/70 pt-12 lg:pt-14' : '' }}">
                            <div class="grid gap-6 lg:grid-cols-[auto_minmax(0,0.78fr)_minmax(0,1fr)] lg:items-start lg:gap-8">
                                <div class="inline-flex h-16 w-16 flex-none items-center justify-center rounded-[20px] bg-[linear-gradient(180deg,#0f1b2d_0%,#123250_100%)] text-white">
                                    @switch($capabilitySection['icon'])
                                        @case('governance')
                                            <svg viewBox="0 0 24 24" class="h-8 w-8" fill="none" aria-hidden="true">
                                                <path d="M5 10.5 12 6l7 4.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M6.5 10.5V18M12 10.5V18M17.5 10.5V18" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                                                <path d="M4.5 18h15" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                                            </svg>
                                            @break
                                        @case('transition')
                                            <svg viewBox="0 0 24 24" class="h-8 w-8" fill="none" aria-hidden="true">
                                                <path d="M6 17 11 12l3 3 4-6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M15 9h3v3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M5 5v14" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                                            </svg>
                                            @break
                                        @default
                                            <svg viewBox="0 0 24 24" class="h-8 w-8" fill="none" aria-hidden="true">
                                                <circle cx="8" cy="8" r="2.5" stroke="currentColor" stroke-width="1.7"/>
                                                <circle cx="16" cy="8" r="2.5" stroke="currentColor" stroke-width="1.7"/>
                                                <path d="M4.5 18a3.5 3.5 0 0 1 7 0" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                                                <path d="M12.5 18a3.5 3.5 0 0 1 7 0" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                                            </svg>
                                    @endswitch
                                </div>

                                <div class="max-w-[24rem]">
                                    <h2 @if($sectionIndex === 0) id="ac-family-capabilities-title" @endif class="font-['Playfair_Display'] text-[clamp(1.8rem,2.4vw,2.7rem)] font-semibold leading-[1.08] text-slate-950">
                                        {{ $capabilitySection['title'] }}
                                    </h2>
                                </div>

                                <div class="max-w-[40rem] text-[0.98rem] leading-8 text-slate-700">
                                    <p>{{ $capabilitySection['intro'] }}</p>
                                </div>
                            </div>

                            <div class="mt-8 grid gap-6 md:grid-cols-3 lg:mt-9">
                                @foreach ($capabilitySection['items'] as $item)
                                    <article class="border-l-2 border-[#d8c4a0] pl-4">
                                        <h3 class="text-[1.05rem] font-semibold leading-7 text-slate-950">{{ $item['title'] }}</h3>
                                        <p class="mt-2 text-[0.96rem] leading-7 text-slate-700">{{ $item['text'] }}</p>
                                    </article>
                                @endforeach
                            </div>

                            <div class="mt-8 flex flex-col gap-4 border-t border-slate-200/80 pt-6 lg:flex-row lg:items-end lg:justify-between">
                                <div class="max-w-[48rem]">
                                    <p class="ac-family-section-kicker">KAKO VAM MOŽEMO POMOĆI</p>
                                    <p class="mt-3 text-[0.98rem] leading-7 text-slate-700">{{ $capabilitySection['help'] }}</p>
                                </div>

                                <a href="#family-business-sastanak" class="front-contact-submit inline-flex h-11 items-center justify-center gap-2 px-6 text-sm font-semibold !text-white transition">
                                    <span>Zatražite konzultacije</span>
                                    <svg viewBox="0 0 24 24" class="h-4 w-4 flex-none" fill="none" aria-hidden="true">
                                        <path d="M12 5v14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"></path>
                                        <path d="m6 13 6 6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg>
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <div class="mx-auto w-full max-w-[1240px] px-5 lg:px-8">
            <section class="mt-12 lg:mt-14" aria-labelledby="ac-family-advisory-title">
                <div class="ac-family-faq-head">
                    <div class="ac-family-faq-title-col">
                        <p class="ac-family-section-kicker">{{ $advisoryApproachSection['kicker'] }}</p>
                        <h2 id="ac-family-advisory-title" class="mt-4 font-['Playfair_Display'] text-[clamp(1.6rem,2.2vw,2.25rem)] font-semibold leading-[1.16] text-slate-950">
                            {{ $advisoryApproachSection['title'] }}
                        </h2>
                    </div>

                    <div class="max-w-[36rem] pt-1 text-[0.98rem] leading-[1.66] text-[#2c3948]">
                        <p>{{ $advisoryApproachSection['intro'] }}</p>
                    </div>
                </div>

                <div class="mt-10 lg:mt-12">
                    <p class="ac-family-section-kicker">{{ $advisoryApproachSection['box_title'] }}</p>

                    <div class="mt-6 grid gap-x-10 gap-y-7 border-t border-[#d8c4a0] pt-6 lg:grid-cols-2 lg:pt-8">
                        @foreach ($advisoryApproachSection['items'] as $item)
                            <article class="flex gap-4 border-b border-slate-200/80 pb-6">
                                <span class="inline-flex h-9 w-9 flex-none items-center justify-center rounded-full border border-[#d8c4a0] bg-[#f8f3e8] text-[0.78rem] font-semibold tracking-[0.08em] text-[#9a773d]" aria-hidden="true">
                                    {{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}
                                </span>
                                <div class="min-w-0">
                                    <h3 class="text-[1rem] font-semibold leading-7 text-slate-950">{{ $item['lead'] }}</h3>
                                    <p class="mt-1.5 text-[0.98rem] leading-7 text-slate-700">{{ $item['body'] }}</p>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    @if ($brochureUrl)
                        <div class="mt-8 flex justify-center lg:mt-10">
                            <a
                                href="{{ $brochureUrl }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex min-w-[232px] h-12 items-center justify-center gap-3 whitespace-nowrap rounded-[14px] border border-white/10 bg-[#0f1b2d] px-6 text-[0.78rem] font-semibold uppercase tracking-[0.16em] !text-white transition hover:bg-[#123250]"
                            >
                                <svg viewBox="0 0 24 24" class="h-[1.05rem] w-[1.05rem] flex-none" fill="none" aria-hidden="true">
                                    <path d="M8 3.75h6.5L19 8.25V18a2 2 0 0 1-2 2H8A2 2 0 0 1 6 18V5.75a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                                    <path d="M14 3.75V8.5h4.75" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M8.9 12.25h6.2M8.9 15.25h6.2" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                                </svg>
                                <span>Preuzmite brošuru</span>
                                <svg viewBox="0 0 24 24" class="h-[0.95rem] w-[0.95rem] flex-none" fill="none" aria-hidden="true">
                                    <path d="M5 12h13" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"></path>
                                    <path d="m12 5 7 7-7 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                            </a>
                        </div>
                    @endif
                </div>
            </section>

            @if (($familyBusinessTeam ?? collect())->isNotEmpty())
                <section class="ac-family-section ac-family-team-showcase-section" aria-labelledby="ac-family-team-title">
                    <div class="ac-family-team-showcase-head">
                        <p class="ac-family-section-kicker">{{ $teamSection['kicker'] }}</p>
                        <h2 id="ac-family-team-title">{{ $teamSection['title'] }}</h2>
                        <p>{{ $teamSection['intro'] }}</p>
                    </div>

                    <div class="space-y-6 mt-10">
                        @foreach ($familyBusinessTeam as $member)
                            <article class="ac-team-member-card overflow-hidden rounded-[32px] border border-slate-200 bg-white p-4 sm:p-4 lg:p-5">
                                <div class="ac-team-member-layout grid gap-4 lg:grid-cols-[220px_minmax(0,1fr)] lg:items-start lg:gap-5">
                                    <div class="ac-team-member-media self-start overflow-hidden rounded-[24px] border border-slate-200 bg-white">
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
                                                <div class="ac-team-member-photo flex aspect-[4/5] items-center justify-center bg-[linear-gradient(180deg,#0d233b_0%,#123151_100%)]">
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
                                                    <input id="family-team-bio-{{ $member['id'] }}" type="checkbox" class="ac-team-bio-toggle">
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
                                                    <label for="family-team-bio-{{ $member['id'] }}" class="ac-team-bio-trigger">
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
                                                <a href="mailto:{{ $member['email'] }}" title="{{ __('ui.team.social.email') }}" aria-label="{{ __('ui.team.social.email') }}" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-slate-50 text-slate-700 transition hover:border-sky-200 hover:bg-white hover:text-sky-900">
                                                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                        <path d="M3 5.75h14v8.5a1.25 1.25 0 0 1-1.25 1.25H4.25A1.25 1.25 0 0 1 3 14.25v-8.5Z"></path>
                                                        <path d="m4 6.5 6 4.75 6-4.75"></path>
                                                    </svg>
                                                </a>
                                            @endif
                                            @if ($member['mobile_phone'] !== '' && $memberPhoneHref !== '')
                                                <a href="tel:{{ $memberPhoneHref }}" title="{{ __('ui.team.social.phone') }}" aria-label="{{ __('ui.team.social.phone') }}" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-slate-50 text-slate-700 transition hover:border-sky-200 hover:bg-white hover:text-sky-900">
                                                    <svg class="h-4 w-4" viewBox="0 0 384 512" fill="currentColor" aria-hidden="true">
                                                        <path d="M16 64C16 28.7 44.7 0 80 0L304 0c35.3 0 64 28.7 64 64l0 384c0 35.3-28.7 64-64 64L80 512c-35.3 0-64-28.7-64-64L16 64zM128 440c0 13.3 10.7 24 24 24l80 0c13.3 0 24-10.7 24-24s-10.7-24-24-24l-80 0c-13.3 0-24 10.7-24 24zM304 64l-224 0 0 304 224 0 0-304z"></path>
                                                    </svg>
                                                </a>
                                            @endif
                                            @if ($member['facebook_url'] !== '')
                                                <a href="{{ $member['facebook_url'] }}" target="_blank" rel="noopener noreferrer" title="{{ __('ui.team.social.facebook') }}" aria-label="{{ __('ui.team.social.facebook') }}" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-slate-50 text-slate-700 transition hover:border-sky-200 hover:bg-white hover:text-sky-900">
                                                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                        <path d="M11.167 17v-6.091h2.042l.306-2.373h-2.348V7.02c0-.686.19-1.153 1.173-1.153H13.6V3.744c-.218-.03-.967-.094-1.839-.094-1.82 0-3.067 1.11-3.067 3.149v1.737H6.636v2.373h2.058V17h2.473Z"></path>
                                                    </svg>
                                                </a>
                                            @endif
                                            @if ($member['twitter_url'] !== '')
                                                <a href="{{ $member['twitter_url'] }}" target="_blank" rel="noopener noreferrer" title="{{ __('ui.team.social.twitter') }}" aria-label="{{ __('ui.team.social.twitter') }}" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-slate-50 text-slate-700 transition hover:border-sky-200 hover:bg-white hover:text-sky-900">
                                                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                        <path d="M4.36 4h3.04l3 4.215L14.09 4H16l-4.775 5.452L16.5 16h-3.04l-3.244-4.556L6.216 16H4.31l5.01-5.72L4.36 4Zm2.1 1.42h-.73l7.81 9.16h.73l-7.81-9.16Z"></path>
                                                    </svg>
                                                </a>
                                            @endif
                                            @if ($member['linkedin_url'] !== '')
                                                <a href="{{ $member['linkedin_url'] }}" target="_blank" rel="noopener noreferrer" title="{{ __('ui.team.social.linkedin') }}" aria-label="{{ __('ui.team.social.linkedin') }}" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-slate-50 text-slate-700 transition hover:border-sky-200 hover:bg-white hover:text-sky-900">
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
                </section>
            @endif

                <section id="family-business-sastanak" class="ac-family-section pb-16 md:pb-24" aria-labelledby="ac-family-meeting-title">
                <div class="ac-family-team-showcase-head">
                    <p class="ac-family-section-kicker">{{ $meetingSection['kicker'] ?? 'SASTANAK' }}</p>
                    <h2 id="ac-family-meeting-title">{{ $meetingSection['title'] }}</h2>
                    <p>{{ $meetingSection['intro'] }}</p>
                </div>

                <div class="mt-10 grid gap-5 lg:grid-cols-[300px_minmax(0,1fr)] lg:items-start">
                    <aside class="front-contact-sidebar">
                        <div class="front-contact-panel front-contact-panel--direct">
                            <h2>{{ $meetingSection['visit_title'] }}</h2>
                            <div class="mt-4 space-y-1 text-[0.89rem] leading-6 text-slate-700">
                                <p style="white-space: nowrap;">{{ $meetingSection['visit_lines'][0] }}</p>
                                <p>{{ $meetingSection['visit_lines'][1] }}</p>
                            </div>
                        </div>

                        <div class="front-contact-panel front-contact-panel--direct">
                            <h2>{{ $meetingSection['contact_title'] }}</h2>
                            <ul class="front-contact-direct-list">
                                <li>
                                    <span>Telefon</span>
                                    <a href="tel:{{ $contactPhoneHref }}">{{ $contactPhone }}</a>
                                </li>
                                <li>
                                    <span>Email</span>
                                    <a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a>
                                </li>
                            </ul>
                        </div>
                    </aside>

                    <form
                        method="POST"
                        action="{{ route('contact.store') }}"
                        class="front-contact-form"
                        novalidate
                        data-contact-form
                        data-msg-name-required="{{ __('contact.validation.inline.name_required') }}"
                        data-msg-email-required="{{ __('contact.validation.inline.email_required') }}"
                        data-msg-email-invalid="{{ __('contact.validation.inline.email_invalid') }}"
                        data-msg-message-required="{{ __('contact.validation.inline.message_required') }}"
                        data-msg-message-min="{{ __('contact.validation.inline.message_min') }}"
                        data-msg-accept-terms="{{ __('contact.validation.inline.accept_terms') }}"
                        @if($captchaEnabled) data-recaptcha-form data-recaptcha-site-key="{{ $captchaSiteKey }}" data-recaptcha-action="contact_form" @endif
                    >
                        @csrf
                        <input type="hidden" name="recaptcha_token" value="" data-recaptcha-token>
                        <input type="hidden" name="redirect_to" value="{{ route('family-business.show') }}#family-business-sastanak">

                        @if (session('status'))
                            <div class="front-contact-status" role="status">
                                {{ session('status') }}
                            </div>
                        @endif

                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="family-business-first-name">Ime</label>
                                <input id="family-business-first-name" type="text" name="first_name" value="{{ old('first_name') }}" class="front-contact-input h-11 w-full text-sm" required>
                                <p class="mt-2 text-xs font-semibold text-rose-600 {{ $errors->has('first_name') ? '' : 'hidden' }}" data-field-error="first_name">@error('first_name'){{ $message }}@enderror</p>
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="family-business-last-name">Prezime</label>
                                <input id="family-business-last-name" type="text" name="last_name" value="{{ old('last_name') }}" class="front-contact-input h-11 w-full text-sm">
                                <p class="mt-2 text-xs font-semibold text-rose-600 {{ $errors->has('last_name') ? '' : 'hidden' }}" data-field-error="last_name">@error('last_name'){{ $message }}@enderror</p>
                            </div>
                        </div>

                        <div class="mt-4 grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="family-business-company">Tvrtka</label>
                                <input id="family-business-company" type="text" name="company" value="{{ old('company') }}" class="front-contact-input h-11 w-full text-sm">
                                <p class="mt-2 text-xs font-semibold text-rose-600 {{ $errors->has('company') ? '' : 'hidden' }}" data-field-error="company">@error('company'){{ $message }}@enderror</p>
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="family-business-phone">Broj telefona</label>
                                <input id="family-business-phone" type="text" name="phone" value="{{ old('phone') }}" class="front-contact-input h-11 w-full text-sm">
                                <p class="mt-2 text-xs font-semibold text-rose-600 {{ $errors->has('phone') ? '' : 'hidden' }}" data-field-error="phone">@error('phone'){{ $message }}@enderror</p>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="family-business-email">Email</label>
                            <input id="family-business-email" type="email" name="email" value="{{ old('email', auth()->user()?->email) }}" class="front-contact-input h-11 w-full text-sm" required>
                            <p class="mt-2 text-xs font-semibold text-rose-600 {{ $errors->has('email') ? '' : 'hidden' }}" data-field-error="email">@error('email'){{ $message }}@enderror</p>
                        </div>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="family-business-subject">Naslov poruke</label>
                            <input id="family-business-subject" type="text" name="subject" value="{{ old('subject') }}" class="front-contact-input h-11 w-full text-sm">
                            <p class="mt-2 text-xs font-semibold text-rose-600 {{ $errors->has('subject') ? '' : 'hidden' }}" data-field-error="subject">@error('subject'){{ $message }}@enderror</p>
                        </div>

                        <div class="mt-4">
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500" for="family-business-message">Poruka</label>
                            <textarea id="family-business-message" name="message" rows="8" class="front-contact-textarea w-full text-sm" required>{{ old('message') }}</textarea>
                            <p class="mt-2 text-xs font-semibold text-rose-600 {{ $errors->has('message') ? '' : 'hidden' }}" data-field-error="message">@error('message'){{ $message }}@enderror</p>
                        </div>

                        <div class="front-contact-consent-wrap">
                            <label class="front-contact-consent">
                                <input type="checkbox" name="accept_terms" value="1" class="front-contact-checkbox mt-0.5 h-4 w-4 border-slate-300 text-slate-900 focus:ring-0" @checked((bool) old('accept_terms'))>
                                <span>{{ __('contact.form.accept_terms') }}</span>
                            </label>
                            <p class="mt-2 text-xs font-semibold text-rose-600 {{ $errors->has('accept_terms') ? '' : 'hidden' }}" data-field-error="accept_terms">@error('accept_terms'){{ $message }}@enderror</p>
                        </div>

                        <div class="front-contact-form-actions">
                            <button type="submit" class="front-contact-submit inline-flex h-11 items-center justify-center px-6 text-sm font-semibold text-white transition">
                                {{ $meetingSection['submit'] }}
                            </button>
                            <p class="text-xs font-semibold text-rose-600 {{ $errors->has('recaptcha_token') ? '' : 'hidden' }}" data-field-error="recaptcha_token">@error('recaptcha_token'){{ $message }}@enderror</p>
                        </div>
                    </form>
                </div>
            </section>
        </div>

        @if (($familyBusinessPosts ?? collect())->isNotEmpty())
            <section class="ac-support-story ac-home-blog ac-blog-related-section ac-family-blog-section" aria-labelledby="ac-family-blog-title">
                <div class="mx-auto w-full max-w-[1240px] px-6 lg:px-10">
                    <div class="ac-support-story-hero">
                        <div class="ac-support-story-shell">
                            <div class="ac-services-head ac-support-story-head">
                                <h2 id="ac-family-blog-title">
                                    <span>{{ $blogSection['title'] }}</span>
                                </h2>
                                <p class="ac-services-intro">{{ $blogSection['intro'] }}</p>
                                <div class="ac-services-divider" aria-hidden="true">
                                    <span class="ac-services-divider-line"></span>
                                    <span class="ac-services-divider-glyph"></span>
                                    <span class="ac-services-divider-line"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="ac-home-blog-carousel ac-blog-related-content">
                        <div class="ac-blog-grid ac-blog-grid-related">
                            @foreach ($familyBusinessPosts as $post)
                                @include('front.desktop.blog.partials.card', [
                                    'post' => $post,
                                    'locale' => $locale,
                                    'fallbackLocale' => $fallbackLocale,
                                ])
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>
        @endif
    </div>
@endsection

@push('styles')
    <style>
        @media (min-width: 641px) {
            .ac-team-member-layout {
                grid-template-columns: 220px minmax(0, 1fr);
                align-items: start;
                column-gap: 1.25rem;
                row-gap: 1rem;
            }

            .ac-team-member-media {
                grid-column: 1;
                grid-row: 1 / span 3;
                width: 100%;
                max-width: 220px;
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

        @media (max-width: 640px) {
            .ac-team-member-card {
                padding: 1rem;
                border-radius: 28px;
            }

            .ac-team-member-layout {
                grid-template-columns: 108px minmax(0, 1fr);
                align-items: start;
                column-gap: 0.95rem;
                row-gap: 0.85rem;
            }

            .ac-team-member-media {
                width: 108px;
                max-width: 108px;
                margin-left: 0;
                margin-right: 0;
                border-radius: 22px;
            }

            .ac-team-member-photo {
                aspect-ratio: 0.78;
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

@include('front.desktop.contact.partials.form-script', [
    'captchaEnabled' => $captchaEnabled,
    'captchaSiteKey' => $captchaSiteKey,
])

@push('scripts')
    <script>
        (function () {
            const shouldFocusSection = {{ ($errors->any() || session('status')) ? 'true' : 'false' }};
            const section = document.getElementById('family-business-sastanak');
            const faqItems = Array.from(document.querySelectorAll('.ac-family-faq-item'));

            const syncFaqItem = function (item) {
                const content = item.querySelector('.ac-family-faq-answer-wrap');
                if (!content) {
                    return;
                }

                if (item.hasAttribute('open')) {
                    content.style.height = 'auto';
                    return;
                }

                content.style.height = '0px';
            };

            const animateFaqItem = function (item, expand) {
                const content = item.querySelector('.ac-family-faq-answer-wrap');
                if (!content) {
                    return;
                }

                if (content.__faqTransitionHandler) {
                    content.removeEventListener('transitionend', content.__faqTransitionHandler);
                    content.__faqTransitionHandler = null;
                }

                if (content.__faqFallbackTimer) {
                    window.clearTimeout(content.__faqFallbackTimer);
                    content.__faqFallbackTimer = null;
                }

                const startHeight = content.offsetHeight;
                if (expand) {
                    item.setAttribute('open', '');
                }

                const endHeight = expand ? content.scrollHeight : 0;
                item.classList.remove('is-opening', 'is-closing');
                item.classList.add(expand ? 'is-opening' : 'is-closing');
                content.style.height = startHeight + 'px';

                if (!expand) {
                    item.removeAttribute('open');
                }

                requestAnimationFrame(function () {
                    content.style.height = endHeight + 'px';
                });

                const finalizeAnimation = function () {
                    if (content.__faqTransitionHandler) {
                        content.removeEventListener('transitionend', content.__faqTransitionHandler);
                        content.__faqTransitionHandler = null;
                    }

                    if (content.__faqFallbackTimer) {
                        window.clearTimeout(content.__faqFallbackTimer);
                        content.__faqFallbackTimer = null;
                    }

                    item.classList.remove('is-opening', 'is-closing');

                    if (expand) {
                        content.style.height = 'auto';
                        return;
                    }

                    item.removeAttribute('open');
                    content.style.height = '0px';
                };

                const onTransitionEnd = function (event) {
                    if (event.propertyName !== 'height') {
                        return;
                    }

                    finalizeAnimation();
                };

                content.__faqTransitionHandler = onTransitionEnd;
                content.addEventListener('transitionend', onTransitionEnd);
                content.__faqFallbackTimer = window.setTimeout(finalizeAnimation, 520);
            };

            const closeOtherFaqItems = function (activeItem) {
                faqItems.forEach(function (item) {
                    if (item === activeItem || !item.hasAttribute('open')) {
                        return;
                    }

                    animateFaqItem(item, false);
                });
            };

            faqItems.forEach(function (item) {
                const summary = item.querySelector('.ac-family-faq-summary');
                if (!summary) {
                    return;
                }

                syncFaqItem(item);

                summary.addEventListener('click', function (event) {
                    event.preventDefault();

                    if (item.classList.contains('is-opening') || item.classList.contains('is-closing')) {
                        return;
                    }

                    const shouldExpand = !item.hasAttribute('open');
                    if (shouldExpand) {
                        closeOtherFaqItems(item);
                    }

                    animateFaqItem(item, shouldExpand);
                });
            });

            if (shouldFocusSection && section) {
                requestAnimationFrame(function () {
                    section.scrollIntoView({ behavior: 'smooth', block: 'start' });
                });
            }
        }());
    </script>
@endpush
