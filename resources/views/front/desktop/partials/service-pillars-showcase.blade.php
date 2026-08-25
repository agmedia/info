@php
    $sectionId = $sectionId ?? 'ac-service-pillars-showcase';
    $headingLevel = (int) ($headingLevel ?? 2);
    $headingTag = $headingLevel === 1 ? 'h1' : 'h2';
    $variant = (string) ($variant ?? 'text');
    $cards = collect($cards ?? [])->values();
    $titleLead = trim((string) ($titleLead ?? ''));
    $titleAccent = trim((string) ($titleAccent ?? ''));
    $intro = trim((string) ($intro ?? ''));
    $outro = collect($outro ?? [])->filter(fn ($item) => trim((string) $item) !== '')->values();
    $isImageVariant = $variant === 'image';
    $accessibleTitle = trim($titleLead.' '.$titleAccent);
    if ($accessibleTitle === '') {
        $accessibleTitle = $cards
            ->pluck('title')
            ->map(fn ($title) => trim((string) $title))
            ->filter()
            ->implode(', ');
    }
@endphp

<section id="{{ $sectionId }}" class="ac-service-pillars-showcase {{ $isImageVariant ? 'ac-service-pillars-showcase--image' : '' }}" @if ($accessibleTitle !== '') aria-labelledby="{{ $sectionId }}-title" @endif>
    <div class="ac-service-pillars-showcase-inner mx-auto w-full max-w-[1240px] px-5 lg:px-8">
        @if ($titleLead !== '' || $titleAccent !== '' || $intro !== '')
            <div class="ac-service-pillars-showcase-head">
                @if ($titleLead !== '' || $titleAccent !== '')
                    <{{ $headingTag }} id="{{ $sectionId }}-title">
                        @if ($titleLead !== '')
                            <span>{{ $titleLead }}</span>
                        @endif
                        @if ($titleAccent !== '')
                            <span class="ac-service-pillars-showcase-title-accent">{{ $titleAccent }}</span>
                        @endif
                    </{{ $headingTag }}>
                @elseif ($accessibleTitle !== '')
                    <{{ $headingTag }} id="{{ $sectionId }}-title" class="sr-only">{{ $accessibleTitle }}</{{ $headingTag }}>
                @endif

                @if ($intro !== '')
                    <p>{{ $intro }}</p>
                @endif
            </div>
        @elseif ($accessibleTitle !== '')
            <{{ $headingTag }} id="{{ $sectionId }}-title" class="sr-only">{{ $accessibleTitle }}</{{ $headingTag }}>
        @endif

        <div class="ac-service-pillars-showcase-grid {{ $isImageVariant ? 'ac-service-pillars-showcase-grid--image' : '' }}">
            @foreach ($cards as $card)
                @if ($isImageVariant)
                    <a href="{{ $card['url'] ?? \App\Support\Localization\FrontendRoute::url('services.index') }}" class="ac-service-pillar-image-card">
                        @if (!empty($card['image_url']))
                            <img src="{{ $card['image_url'] }}" alt="" aria-hidden="true" loading="{{ $loop->index < 3 ? 'eager' : 'lazy' }}" decoding="async">
                        @else
                            <span class="ac-service-pillar-image-card-placeholder" aria-hidden="true"></span>
                        @endif
                        <span class="ac-service-pillar-image-card-shade" aria-hidden="true"></span>
                        <span class="ac-service-pillar-image-card-content">
                            <span class="ac-service-pillar-text-card-title">{{ $card['title'] ?? '' }}</span>
                            @if (!empty($card['text']))
                                <span class="ac-service-pillar-image-card-text">{{ $card['text'] }}</span>
                            @endif
                            @if (trim((string) ($card['action_label'] ?? '')) !== '')
                                <span class="ac-service-pillar-image-card-action">
                                    <span>{{ $card['action_label'] }}</span>
                                    <span class="ac-service-pillar-image-card-arrow" aria-hidden="true"></span>
                                </span>
                            @endif
                        </span>
                    </a>
                @else
                    <a href="{{ $card['url'] ?? \App\Support\Localization\FrontendRoute::url('services.index') }}" class="ac-service-pillar-text-card">
                        <span class="ac-service-pillar-text-card-title">{{ $card['title'] ?? '' }}</span>
                        @if (!empty($card['subtitle']))
                            <span class="ac-service-pillar-text-card-subtitle">{{ $card['subtitle'] }}</span>
                        @endif
                        @if (!empty($card['bullets']))
                            <ul>
                                @foreach ($card['bullets'] as $bullet)
                                    <li>{{ $bullet }}</li>
                                @endforeach
                            </ul>
                        @elseif (!empty($card['text']))
                            <p>{{ $card['text'] }}</p>
                        @endif
                    </a>
                @endif
            @endforeach
        </div>

        @if ($outro->isNotEmpty())
            <div class="ac-service-pillars-showcase-copy">
                @foreach ($outro as $paragraph)
                    <p>{{ $paragraph }}</p>
                @endforeach
            </div>
        @endif
    </div>
</section>
