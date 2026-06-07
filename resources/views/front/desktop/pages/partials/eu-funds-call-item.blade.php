@php
    $resolvedLink = $item['resolved_link'] ?? ['url' => ''];
    $itemUrl = trim((string) ($resolvedLink['url'] ?? ''));
    $dateLabel = trim((string) ($item['date_label'] ?? ''));
    $dateValue = trim((string) ($item['date_value'] ?? ''));
@endphp

<li class="{{ $itemUrl !== '' ? 'is-linked' : 'is-static' }}">
    @if ($itemUrl !== '')
        <a
            href="{{ $itemUrl }}"
            @if($resolvedLink['open_in_new_tab'] ?? false) target="_blank" rel="{{ $resolvedLink['rel'] ?? 'noopener noreferrer' }}" @endif
        >
            <span class="ac-eu-call-item-title">{{ $item['title'] ?? '' }}</span>
            @if ($dateLabel !== '' && $dateValue !== '')
                <span class="ac-eu-call-item-date">{{ $dateLabel }}: {{ $dateValue }}</span>
            @endif
        </a>
    @else
        <div class="ac-eu-call-item-row">
            <span class="ac-eu-call-item-title">{{ $item['title'] ?? '' }}</span>
            @if ($dateLabel !== '' && $dateValue !== '')
                <span class="ac-eu-call-item-date">{{ $dateLabel }}: {{ $dateValue }}</span>
            @endif
        </div>
    @endif
</li>
