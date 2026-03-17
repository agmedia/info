@php
    $navigationItems = collect($mainNavigation ?? [])->reject(function ($item) {
        $label = trim((string) ($item['label'] ?? ''));
        $url = rtrim((string) ($item['url'] ?? ''), '/');
        $homeUrl = rtrim(route('home'), '/');

        return $label === 'Početna' || $url === $homeUrl;
    })->values();
    $hasNavigation = $navigationItems->isNotEmpty();
@endphp

@if ($hasNavigation)
    @foreach ($navigationItems as $item)
        @php
            $children = collect($item['children'] ?? []);
            $hasChildren = $children->isNotEmpty();
            $href = (string) ($item['url'] ?? '#');
            $target = !empty($item['open_in_new_tab']) ? '_blank' : null;
            $rel = !empty($item['open_in_new_tab']) ? 'noopener noreferrer' : null;
        @endphp

        @if ($hasChildren)
            <div class="group/nav relative">
                <a href="{{ $href }}" class="front-nav-link inline-flex items-center gap-1 py-6 transition" @if($target) target="{{ $target }}" rel="{{ $rel }}" @endif>
                    <span class="front-nav-link-label border-b pb-0.5 transition">{{ $item['label'] }}</span>
                    <span class="front-nav-caret" aria-hidden="true">▼</span>
                </a>

                <div class="front-nav-dropdown invisible pointer-events-none absolute left-1/2 top-full z-50 min-w-[19rem] -translate-x-1/2 p-2 opacity-0 transition-all duration-150 group-hover/nav:visible group-hover/nav:pointer-events-auto group-hover/nav:opacity-100">
                    <ul class="front-nav-dropdown-list space-y-1">
                        @foreach ($children as $child)
                            @include('front.desktop.partials.main-nav-child', ['child' => $child, 'level' => 0])
                        @endforeach
                    </ul>
                </div>
            </div>
        @else
            <a href="{{ $href }}" class="front-nav-link inline-flex items-center py-6 transition" @if($target) target="{{ $target }}" rel="{{ $rel }}" @endif>
                <span class="front-nav-link-label border-b pb-0.5 transition">{{ $item['label'] }}</span>
            </a>
        @endif
    @endforeach
@else
    <a href="{{ route('blog.index') }}" class="front-nav-link inline-flex items-center py-6"><span class="front-nav-link-label border-b pb-0.5 transition">{{ __('ui.front.desktop.nav.blog') }}</span></a>
    <a href="{{ route('faq.index') }}" class="front-nav-link inline-flex items-center py-6"><span class="front-nav-link-label border-b pb-0.5 transition">{{ __('ui.front.desktop.nav.faq') }}</span></a>
    <a href="{{ route('contact.create') }}" class="front-nav-link inline-flex items-center py-6"><span class="front-nav-link-label border-b pb-0.5 transition">{{ __('ui.front.desktop.nav.contact') }}</span></a>
@endif
