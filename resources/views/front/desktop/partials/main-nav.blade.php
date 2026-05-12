@php
    $navigationItems = collect($mainNavigation ?? [])->values();
    $hasNavigation = $navigationItems->isNotEmpty();
    $currentUrl = rtrim(url()->current(), '/');
@endphp

@if ($hasNavigation)
    @foreach ($navigationItems as $item)
        @php
            $href = (string) ($item['url'] ?? '#');
            $normalizedHref = rtrim($href, '/');
            $target = !empty($item['open_in_new_tab']) ? '_blank' : null;
            $rel = !empty($item['open_in_new_tab']) ? 'noopener noreferrer' : null;
            $isActive = $normalizedHref !== '' && $normalizedHref !== '#' && $normalizedHref === $currentUrl;
        @endphp

        <a href="{{ $href }}" class="front-nav-link {{ $isActive ? 'is-active' : '' }} inline-flex items-center py-6 transition" @if($target) target="{{ $target }}" rel="{{ $rel }}" @endif>
            <span class="front-nav-link-label border-b pb-0.5 transition">{{ $item['label'] }}</span>
        </a>
    @endforeach
@else
    <a href="{{ route('blog.index') }}" class="front-nav-link inline-flex items-center py-6"><span class="front-nav-link-label border-b pb-0.5 transition">{{ __('ui.front.desktop.nav.blog') }}</span></a>
    <a href="{{ route('faq.index') }}" class="front-nav-link inline-flex items-center py-6"><span class="front-nav-link-label border-b pb-0.5 transition">{{ __('ui.front.desktop.nav.faq') }}</span></a>
    <a href="{{ route('contact.create') }}" class="front-nav-link inline-flex items-center py-6"><span class="front-nav-link-label border-b pb-0.5 transition">{{ __('ui.front.desktop.nav.contact') }}</span></a>
@endif
