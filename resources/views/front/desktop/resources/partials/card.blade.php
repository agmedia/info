@php
    $variantClass = match ($item['group_code']) {
        'sector-analysis' => 'is-sector-analysis',
        'transaction-analysis' => 'is-transaction-analysis',
        default => 'is-download',
    };
    $iconClass = match ($item['group_code']) {
        'sector-analysis' => 'fa-chart-line-up',
        'transaction-analysis' => 'fa-arrow-right-arrow-left',
        default => 'fa-file-arrow-down',
    };
    $revealIndex = (int) ($revealIndex ?? 0);
@endphp

<article class="ac-resource-card content-reveal animation-index-{{ $revealIndex % 3 }}" data-image-reveal>
    <a href="{{ route('resources.show', ['slug' => $item['slug']]) }}" class="ac-resource-card-media {{ $variantClass }}" aria-label="{{ $item['title'] }}">
        @if ($item['cover_image_url'])
            <img src="{{ $item['cover_image_url'] }}" alt="{{ $item['title'] }}" loading="lazy" decoding="async">
        @else
            <span class="ac-resource-card-fallback">
                <i class="fa-duotone fa-thin {{ $iconClass }}" aria-hidden="true"></i>
                <span>{{ $item['group_label'] }}</span>
            </span>
        @endif
    </a>

    <div class="ac-resource-card-body">
        <p class="ac-resource-card-label">{{ $item['group_label'] }}</p>
        <h3><a href="{{ route('resources.show', ['slug' => $item['slug']]) }}">{{ $item['title'] }}</a></h3>
        @if ($item['excerpt'])
            <p class="ac-resource-card-excerpt">{{ $item['excerpt'] }}</p>
        @endif
        <a href="{{ route('resources.show', ['slug' => $item['slug']]) }}" class="ac-resource-card-link">
            <span>{{ __('resources.index.cta') }}</span>
            <i class="fa-duotone fa-thin fa-arrow-right" aria-hidden="true"></i>
        </a>
    </div>
</article>
