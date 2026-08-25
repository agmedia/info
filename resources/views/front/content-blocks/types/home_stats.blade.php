@php
    $basePayload = is_array($block->payload ?? null) ? $block->payload : [];
    $translationPayload = is_array($translation?->payload ?? null) ? $translation->payload : [];
    $payload = array_merge($basePayload, $translationPayload);
    $stats = collect((array) ($payload['stats'] ?? []))
        ->map(static function ($stat): array {
            $stat = is_array($stat) ? $stat : [];

            return [
                'value' => trim((string) ($stat['value'] ?? '')),
                'suffix' => trim((string) ($stat['suffix'] ?? '')),
                'label' => trim((string) ($stat['label'] ?? '')),
            ];
        })
        ->filter(static fn (array $stat): bool => $stat['value'] !== '' || $stat['label'] !== '')
        ->values();

    if ($stats->isEmpty()) {
        $stats = collect([
            ['value' => '300', 'suffix' => '+', 'label' => 'Odrađenih projekata'],
            ['value' => '700', 'suffix' => '', 'label' => 'Redovnih klijenata'],
            ['value' => '75', 'suffix' => '', 'label' => 'Kvalificiranih stručnjaka'],
        ]);
    }
@endphp

<section class="front-hero-stats-card relative z-10" data-home-hero-stats>
    <div class="grid w-full grid-cols-2 md:grid-cols-3">
        @foreach ($stats->take(3) as $stat)
            @php
                $statValue = (string) ($stat['value'] ?? '');
                $statSuffix = (string) ($stat['suffix'] ?? '');
                $statLabel = (string) ($stat['label'] ?? '');
                $statCountTo = preg_replace('/[^0-9]/', '', $statValue);
                $statDisplayValue = $statValue.$statSuffix;
            @endphp
            <article class="front-hero-stat-card {{ $loop->last ? 'front-hero-stat-card--wide' : '' }} px-6 py-8 text-center" data-home-hero-stat style="--front-hero-stat-delay: {{ $loop->index * 320 }}ms;">
                <span class="front-hero-stat-icon mx-auto inline-flex h-11 w-11 items-center justify-center rounded-full" aria-hidden="true">
                    @if ($loop->first)
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8">
                            <path d="M4 19h16"/>
                            <rect x="6" y="11" width="2.8" height="6" rx="1"/>
                            <rect x="10.6" y="8" width="2.8" height="9" rx="1"/>
                            <rect x="15.2" y="5" width="2.8" height="12" rx="1"/>
                        </svg>
                    @elseif ($loop->iteration === 2)
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"/>
                            <circle cx="10" cy="7" r="4"/>
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    @else
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                    @endif
                </span>
                <div class="front-hero-stat-value-shell" data-home-hero-display data-home-hero-display-value="{{ $statDisplayValue }}">
                    <p class="front-hero-stat-value" data-home-hero-count data-count-to="{{ $statCountTo !== '' ? $statCountTo : $statValue }}" data-count-suffix="{{ $statSuffix }}">{{ $statValue }}</p>
                </div>
                <span class="front-hero-stat-accent" aria-hidden="true"></span>
                @if ($statLabel !== '')
                    <p class="front-hero-stat-label">{{ $statLabel }}</p>
                @endif
            </article>
        @endforeach
    </div>
</section>
