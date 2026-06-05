@php
    $serviceVideoSection = $serviceVideoSection ?? [];
    $serviceVideos = collect($serviceVideos ?? []);
    $serviceVideoSectionTitle = trim((string) ($serviceVideoSection['title'] ?? ''));
    $serviceVideoSectionIntro = trim((string) ($serviceVideoSection['intro'] ?? ''));
    $hasServiceVideoHead = $serviceVideoSectionTitle !== '' || $serviceVideoSectionIntro !== '';
    $serviceVideosLocale = (string) ($locale ?? app()->getLocale());
    $serviceVideoPlayLabel = str_starts_with(strtolower($serviceVideosLocale), 'hr') ? 'Pokreni video' : 'Play video';
@endphp

@if ($serviceVideos->isNotEmpty())
    <section
        class="ac-service-videos-section ac-support-story ac-home-blog ac-blog-related-section ac-family-blog-section"
        @if ($serviceVideoSectionTitle !== '')
            aria-labelledby="ac-service-videos-title"
        @else
            aria-label="{{ str_starts_with(strtolower($serviceVideosLocale), 'hr') ? 'Video sekcija usluga' : 'Service video section' }}"
        @endif
    >
        <div class="mx-auto w-full max-w-[1240px] px-6 lg:px-10">
            @if ($hasServiceVideoHead)
                <div class="ac-support-story-hero">
                    <div class="ac-support-story-shell">
                        <div class="ac-services-head ac-support-story-head">
                            @if ($serviceVideoSectionTitle !== '')
                                <h2 id="ac-service-videos-title">
                                    <span>{{ $serviceVideoSectionTitle }}</span>
                                </h2>
                            @endif

                            @if ($serviceVideoSectionIntro !== '')
                                <p class="ac-services-intro">{{ $serviceVideoSectionIntro }}</p>
                            @endif

                            <div class="ac-services-divider" aria-hidden="true">
                                <span class="ac-services-divider-line"></span>
                                <span class="ac-services-divider-glyph"></span>
                                <span class="ac-services-divider-line"></span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="ac-service-videos-carousel{{ $hasServiceVideoHead ? '' : ' ac-service-videos-carousel--flush' }}">
                <div class="splide ac-service-videos-splide" data-service-videos-splide>
                    <div class="splide__track">
                        <ul class="splide__list">
                            @foreach ($serviceVideos as $video)
                                <li class="splide__slide ac-service-video-slide">
                                    <article class="ac-service-video-card">
                                        <div class="ac-service-video-frame" data-service-video-frame>
                                            <iframe
                                                data-service-video-iframe
                                                data-base-src="{{ $video['embed_url'] }}"
                                                src="{{ $video['embed_url'] }}"
                                                title="{{ trim((string) ($video['title'] ?? '')) !== '' ? $video['title'] : ($serviceVideoSectionTitle !== '' ? $serviceVideoSectionTitle : 'Video') }}"
                                                loading="lazy"
                                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                                referrerpolicy="strict-origin-when-cross-origin"
                                                allowfullscreen
                                            ></iframe>

                                            @if (trim((string) ($video['poster_url'] ?? '')) !== '')
                                                <button
                                                    type="button"
                                                    class="ac-service-video-poster"
                                                    data-service-video-activate
                                                    aria-label="{{ $serviceVideoPlayLabel }}: {{ trim((string) ($video['title'] ?? '')) !== '' ? $video['title'] : ($serviceVideoSectionTitle !== '' ? $serviceVideoSectionTitle : 'Video') }}"
                                                >
                                                    <span class="ac-service-video-poster-media" aria-hidden="true">
                                                        <img src="{{ $video['poster_url'] }}" alt="" loading="lazy">
                                                    </span>
                                                    <span class="ac-service-video-poster-shade" aria-hidden="true"></span>
                                                    <span class="ac-service-video-poster-play" aria-hidden="true">
                                                        <svg viewBox="0 0 384 512" fill="currentColor" focusable="false" aria-hidden="true">
                                                            <path d="M73 39c-14.8-9.1-33.4-9.4-48.5-.9S0 62.5 0 80v352c0 17.5 9.4 33.8 24.5 42.9s33.7 8.2 48.5-.9l288-176c14.7-9 23-25 23-42.3s-8.3-33.4-23-42.3L73 39z"></path>
                                                        </svg>
                                                    </span>
                                                </button>
                                            @endif
                                        </div>

                                        @if (trim((string) ($video['title'] ?? '')) !== '')
                                            <div class="ac-service-video-card-body">
                                                <h3>{{ $video['title'] }}</h3>
                                            </div>
                                        @endif
                                    </article>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
@push('styles')
    <style>
        .ac-service-videos-section {
            padding-top: clamp(4.2rem, 6vw, 5.2rem);
            padding-bottom: clamp(4.3rem, 6.5vw, 5.4rem);
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.34), rgba(255, 255, 255, 0.12)),
                repeating-linear-gradient(90deg, rgba(15, 42, 67, 0.05) 0 1px, transparent 1px 24px),
                radial-gradient(48% 74% at 86% 16%, rgba(65, 122, 176, 0.12), transparent 62%),
                radial-gradient(34% 52% at 14% 84%, rgba(171, 141, 82, 0.06), transparent 68%),
                linear-gradient(120deg, #eef3f7 0%, #e8eff5 52%, #dfe9f2 100%);
        }

        .ac-service-videos-carousel {
            margin: 2rem auto 0;
        }

        .ac-service-videos-carousel--flush {
            margin-top: 0;
        }

        .ac-service-videos-splide .splide__track {
            overflow: hidden;
        }

        .ac-service-videos-splide .splide__list {
            align-items: stretch;
        }

        .ac-service-video-slide {
            display: flex;
            height: auto;
        }

        .ac-service-video-card {
            width: 100%;
            min-height: 100%;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: var(--front-card-radius);
            background: rgba(255, 255, 255, 0.92);
        }

        .ac-service-video-frame {
            position: relative;
            aspect-ratio: 16 / 9;
            overflow: hidden;
            background: #0f1725;
            border-bottom: 1px solid rgba(15, 27, 45, 0.08);
        }

        .ac-service-video-frame iframe {
            width: 100%;
            height: 100%;
            border: 0;
            display: block;
        }

        .ac-service-video-poster {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            border: 0;
            background: transparent;
            cursor: pointer;
        }

        .ac-service-video-poster-media,
        .ac-service-video-poster-media img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            display: block;
        }

        .ac-service-video-poster-media img {
            object-fit: cover;
        }

        .ac-service-video-poster-shade {
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(12, 20, 33, 0.12) 0%, rgba(12, 20, 33, 0.48) 100%);
        }

        .ac-service-video-poster-play {
            position: relative;
            z-index: 1;
            display: inline-flex;
            width: clamp(4.2rem, 7vw, 5.4rem);
            height: clamp(4.2rem, 7vw, 5.4rem);
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.96);
            color: #112033;
            box-shadow: 0 16px 36px rgba(15, 23, 42, 0.18);
            transition: transform 0.18s ease, box-shadow 0.18s ease;
        }

        .ac-service-video-poster-play svg {
            width: 1.05rem;
            height: 1.4rem;
            margin-left: 0.16rem;
        }

        .ac-service-video-poster:hover .ac-service-video-poster-play,
        .ac-service-video-poster:focus-visible .ac-service-video-poster-play {
            transform: scale(1.04);
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.22);
        }

        .ac-service-video-frame.is-active .ac-service-video-poster {
            opacity: 0;
            pointer-events: none;
        }

        .ac-service-video-card-body {
            padding: 1.15rem 1.2rem 1.3rem;
        }

        .ac-service-video-card-body h3 {
            margin: 0;
            font-size: 1rem;
            font-weight: 700;
            line-height: 1.5;
            color: #112033;
        }

        .ac-service-videos-splide .splide__pagination {
            bottom: -2.1rem;
        }

        .ac-service-videos-splide .splide__pagination__page {
            width: 0.48rem;
            height: 0.48rem;
            margin: 0 0.22rem;
            background: rgba(54, 90, 114, 0.22);
            opacity: 1;
        }

        .ac-service-videos-splide .splide__pagination__page.is-active {
            background: #365a72;
            transform: scale(1.15);
        }

        @media (max-width: 767px) {
            .ac-service-videos-section {
                padding-top: 3.8rem;
                padding-bottom: 4rem;
            }

            .ac-service-videos-carousel {
                margin-top: 1.2rem;
            }

            .ac-service-video-card-body {
                padding: 1rem 1rem 1.15rem;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        (function () {
            document.querySelectorAll('[data-service-video-frame]').forEach(function (frame) {
                if (frame.dataset.videoReady === '1') {
                    return;
                }

                frame.dataset.videoReady = '1';

                const button = frame.querySelector('[data-service-video-activate]');
                const iframe = frame.querySelector('[data-service-video-iframe]');

                if (!button || !iframe) {
                    return;
                }

                button.addEventListener('click', function () {
                    const baseSrc = iframe.dataset.baseSrc || iframe.getAttribute('src') || '';

                    try {
                        const url = new URL(baseSrc, window.location.origin);
                        url.searchParams.set('autoplay', '1');
                        url.searchParams.set('playsinline', '1');
                        iframe.src = url.toString();
                    } catch (error) {
                        iframe.src = baseSrc + (baseSrc.includes('?') ? '&' : '?') + 'autoplay=1&playsinline=1';
                    }

                    frame.classList.add('is-active');
                });
            });

            const mountServiceVideoSlider = function (el) {
                if (el.dataset.splideReady === '1') {
                    return;
                }

                el.dataset.splideReady = '1';

                const count = el.querySelectorAll('.splide__slide').length;
                const slider = new window.Splide(el, {
                    type: count > 2 ? 'loop' : 'slide',
                    perPage: Math.min(2, Math.max(1, count)),
                    perMove: 1,
                    gap: '1.4rem',
                    drag: count > 1,
                    snap: true,
                    pagination: count > 1,
                    arrows: false,
                    updateOnMove: true,
                    speed: 520,
                    breakpoints: {
                        760: { perPage: 1, gap: '1rem' },
                    },
                });

                slider.mount();
            };

            const initServiceVideoSliders = function () {
                if (typeof window.Splide !== 'function') {
                    return false;
                }

                document.querySelectorAll('[data-service-videos-splide]').forEach(function (el) {
                    mountServiceVideoSlider(el);
                });

                return true;
            };

            if (initServiceVideoSliders()) {
                return;
            }

            let attempts = 0;
            const timer = window.setInterval(function () {
                attempts += 1;
                if (initServiceVideoSliders() || attempts > 40) {
                    window.clearInterval(timer);
                }
            }, 120);
        }());
    </script>
@endpush
@endif
