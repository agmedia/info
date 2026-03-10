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
    <div class="bg-[linear-gradient(180deg,#f4f7fb_0%,#ffffff_22%,#f8fbfd_100%)]">
        <x-front.page-title-band :breadcrumbs="$pageTitleBreadcrumbs">
            <div class="ac-page-title-copy">
                <h1>{{ __('ui.team.title') }}</h1>
                <p>{{ __('ui.team.subtitle') }}</p>
            </div>
        </x-front.page-title-band>

        <section class="pb-16 pt-8 lg:pb-24 lg:pt-12">
            <div class="mx-auto w-full max-w-[1320px] px-4 sm:px-6 lg:px-8">
                <div class="mb-12 grid gap-6 lg:grid-cols-[minmax(0,1.35fr)_360px] lg:items-stretch">
                    <article class="overflow-hidden rounded-[34px] border border-slate-200 bg-white p-7 sm:p-8 lg:p-9">
                        <div class="max-w-[54rem]">
                            <p class="ac-team-kicker text-xs font-semibold uppercase text-sky-800">{{ __('ui.team.eyebrow') }}</p>
                            <h2 class="ac-team-intro-lead mt-4 max-w-[42rem] text-slate-950">
                                {{ __('ui.team.intro_lead') }}
                            </h2>
                            <p class="mt-5 max-w-[46rem] text-[0.98rem] leading-8 text-slate-600">
                                {{ __('ui.team.intro_body') }}
                            </p>
                        </div>
                    </article>

                    <aside class="overflow-hidden rounded-[34px] border border-slate-200 bg-[#103a63] p-7 text-white sm:p-8">
                        <div>
                            <p class="ac-team-kicker text-xs font-semibold uppercase text-sky-100/80">{{ __('ui.team.support_label') }}</p>
                            <h2 class="mt-4 text-[1.65rem] font-black leading-[1.18] text-white">
                                {{ __('ui.team.support_title') }}
                            </h2>
                            <p class="mt-4 text-[0.95rem] leading-8 text-sky-50/88">
                                {{ __('ui.team.support_text') }}
                            </p>

                            <a href="{{ route('contact.create') }}" class="front-action-cta ac-team-cta-light mt-7">
                                <span>{{ __('ui.team.contact_button') }}</span>
                                <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M4 12L12 4"></path>
                                    <path d="M6 4h6v6"></path>
                                </svg>
                            </a>
                        </div>
                    </aside>
                </div>

                @if ($members->isEmpty())
                    <div class="rounded-[32px] border border-dashed border-slate-300 bg-white/80 px-6 py-14 text-center shadow-[0_18px_54px_rgba(15,23,42,0.06)]">
                        <h2 class="text-2xl font-black tracking-tight text-slate-950">{{ __('ui.team.empty_title') }}</h2>
                        <p class="mx-auto mt-3 max-w-[34rem] text-sm leading-7 text-slate-600">{{ __('ui.team.empty') }}</p>
                    </div>
                @else
                    <div class="mb-8 max-w-[52rem]">
                        <p class="ac-team-kicker text-xs font-semibold uppercase text-slate-500">{{ __('ui.team.section_label') }}</p>
                        <h2 class="mt-3 text-[2rem] font-black tracking-tight text-slate-950">{{ __('ui.team.section_title') }}</h2>
                        <p class="mt-3 text-[0.95rem] leading-7 text-slate-600">{{ __('ui.team.section_intro') }}</p>
                    </div>

                    <div class="space-y-6">
                        @foreach ($members as $member)
                            <article class="overflow-hidden rounded-[32px] border border-slate-200 bg-white p-4 sm:p-4 lg:p-5">
                                <div class="grid gap-4 lg:grid-cols-[220px_minmax(0,1fr)] lg:items-start lg:gap-5">
                                    <div class="self-start mx-auto w-full max-w-[220px] overflow-hidden rounded-[24px] border border-slate-200 bg-white lg:mx-0">
                                        <div class="relative overflow-hidden">
                                            @if ($member['photo_url'] !== '')
                                                <img
                                                    src="{{ $member['photo_url'] }}"
                                                    alt="{{ $member['name'] }}"
                                                    class="block h-auto w-full bg-white"
                                                    loading="lazy"
                                                    decoding="async"
                                                >
                                            @else
                                                <div class="flex h-full items-center justify-center">
                                                    <span class="text-6xl font-black tracking-[0.18em] text-white/92">{{ $member['initials'] }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex min-w-0 flex-col justify-center">
                                        <div class="border-b border-slate-100 pb-3.5">
                                            <h3 class="text-[1.2rem] font-black leading-tight tracking-tight text-slate-950 sm:text-[1.38rem]">{{ $member['name'] }}</h3>
                                            @if ($member['position'] !== '')
                                                <p class="ac-team-role mt-2 text-[0.8rem] font-semibold uppercase text-sky-800">
                                                    {{ $member['position'] }}
                                                </p>
                                            @endif
                                        </div>

                                        @if ($member['description_html'] !== '')
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
                                        @endif

                                        @php
                                            $memberPhoneHref = preg_replace('/[^0-9+]/', '', $member['mobile_phone']);
                                        @endphp

                                        <div class="mt-4 flex flex-wrap gap-2.5">
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
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <section class="mt-12 rounded-[36px] border border-slate-200 bg-white p-6 sm:p-7 lg:p-8">
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
        .ac-team-intro-lead {
            font-family: "Playfair Display", Sans-serif;
            font-size: clamp(1.5rem, 2vw, 1.95rem);
            font-weight: 600;
            line-height: 1.18;
            letter-spacing: 0;
        }

        .ac-team-kicker {
            letter-spacing: 0.08em;
        }

        .ac-team-role {
            letter-spacing: 0.06em;
        }

        .ac-team-cta-light.front-action-cta {
            min-width: 176px;
            border-color: rgba(255, 255, 255, 0.22);
            background: #ffffff;
            color: #0f2e4b !important;
            box-shadow: none;
            border-radius: 999px;
        }

        .ac-team-cta-light.front-action-cta:hover {
            background: #f4ede0;
            border-color: rgba(171, 141, 82, 0.68);
            color: #0f2e4b !important;
        }

        .ac-team-cta-dark.front-action-cta {
            min-width: 220px;
            border-color: rgba(11, 44, 73, 0.92);
            background: linear-gradient(90deg, #08111a 0%, #0b2c49 100%);
            color: #ffffff !important;
            box-shadow: none;
            border-radius: 999px;
        }

        .ac-team-cta-dark.front-action-cta:hover {
            background: linear-gradient(90deg, #0d1b29 0%, #143a5c 100%);
            border-color: rgba(171, 141, 82, 0.68);
            color: #ffffff !important;
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
    </style>
@endpush
