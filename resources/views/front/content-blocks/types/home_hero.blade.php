@php
    $basePayload = is_array($block->payload ?? null) ? $block->payload : [];
    $translationPayload = is_array($translation?->payload ?? null) ? $translation->payload : [];
    $payload = array_merge($basePayload, $translationPayload);

    $title = trim((string) ($translation?->title ?? '')) ?: 'ALPHA CAPITALIS';
    $subtitle = trim((string) ($translation?->subtitle ?? '')) ?: 'VAŠ KOMPAS KROZ SVIJET FINANCIJA';
    $primaryLabel = trim((string) ($translation?->cta_label ?? '')) ?: 'Naše usluge';
    $primaryUrl = trim((string) ($translation?->cta_url ?? '')) ?: route('services.index');
    $secondaryLabel = trim((string) ($payload['secondary_cta_label'] ?? '')) ?: 'Ugovori sastanak';
    $secondaryUrl = trim((string) ($payload['secondary_cta_url'] ?? '')) ?: route('contact.create');
    $kicker = trim((string) ($payload['kicker'] ?? ''));
    $imageUrl = $block->getFirstMediaUrl('block_background', 'hero_1440x480');
    if ($imageUrl === '') {
        $imageUrl = $block->getFirstMediaUrl('block_background');
    }
    if ($imageUrl === '') {
        $imageUrl = asset('assets/images/Naslovna.png');
    }
@endphp

<section id="video-sadrzaj" class="front-hero-video-section w-full border-b border-black/20 bg-black">
    <div class="front-hero-video-wrap relative w-full overflow-hidden">
        <div class="front-hero-image absolute inset-0" style="background-image: url('{{ $imageUrl }}');"></div>
        <div class="front-hero-video-overlay absolute inset-0"></div>
        <div class="front-hero-video-content absolute inset-0 flex items-center justify-center px-6 text-center">
            <div>
                @if ($kicker !== '')
                    <p class="front-kicker mb-4 justify-center text-white/80">{{ $kicker }}</p>
                @endif
                <h1 class="front-hero-video-title text-white">{{ $title }}</h1>
                <p class="front-hero-video-subtitle mt-5 text-white/90">{{ $subtitle }}</p>
                <div class="front-hero-cta-row mt-8 flex flex-wrap items-center justify-center gap-3">
                    @if ($primaryLabel !== '' && $primaryUrl !== '')
                        <a href="{{ $primaryUrl }}" class="front-hero-cta front-hero-cta-primary inline-flex items-center justify-center px-6 py-3 text-[11px] font-semibold uppercase tracking-[0.12em]">
                            {{ $primaryLabel }}
                        </a>
                    @endif
                    @if ($secondaryLabel !== '' && $secondaryUrl !== '')
                        <a href="{{ $secondaryUrl }}" class="front-hero-cta front-hero-cta-secondary inline-flex items-center justify-center px-6 py-3 text-[11px] font-semibold uppercase tracking-[0.12em]">
                            {{ $secondaryLabel }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
