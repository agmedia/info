@php
    $hasNavigation = !empty($mainNavigation ?? []);
@endphp

@if ($hasNavigation)
    <div class="front-mobile-nav min-h-0 flex-1 overflow-y-auto border-t px-0 text-sm tracking-[0.03em]">
        @foreach ($mainNavigation as $item)
            @php
                $children = collect($item['children'] ?? []);
                $hasChildren = $children->isNotEmpty();
                $target = !empty($item['open_in_new_tab']) ? '_blank' : null;
                $rel = !empty($item['open_in_new_tab']) ? 'noopener noreferrer' : null;
            @endphp

            @if ($hasChildren)
                <details class="group/nav border-b">
                    <summary class="front-mobile-nav-summary flex min-h-[56px] cursor-pointer list-none items-center justify-between px-4 py-3">
                        <span class="min-w-0 truncate pr-3 text-[14px] font-semibold">{{ $item['label'] }}</span>
                        <span class="front-mobile-nav-toggle inline-flex h-8 w-8 items-center justify-center text-[21px] font-light leading-none group-open/nav:hidden">+</span>
                        <span class="front-mobile-nav-toggle hidden h-8 w-8 items-center justify-center text-[21px] font-light leading-none group-open/nav:inline-flex">-</span>
                    </summary>
                    <ul class="px-0 pb-0 text-[13px]">
                        @foreach ($children as $child)
                            @include('front.desktop.partials.main-nav-mobile-child', ['child' => $child, 'level' => 0])
                        @endforeach
                    </ul>
                </details>
            @else
                <a href="{{ $item['url'] ?? '#' }}" class="front-mobile-nav-link flex min-h-[56px] items-center border-b px-4 py-3 text-[14px] font-semibold" @if($target) target="{{ $target }}" rel="{{ $rel }}" @endif>{{ $item['label'] }}</a>
            @endif
        @endforeach
    </div>
@else
    <nav class="front-mobile-nav min-h-0 flex-1 overflow-y-auto border-t px-0 text-sm tracking-[0.03em]">
        <a href="{{ route('home') }}" class="front-mobile-nav-link flex min-h-[56px] items-center border-b px-4 py-3 text-[14px] font-semibold">Home</a>
        <a href="{{ route('blog.index') }}" class="front-mobile-nav-link flex min-h-[56px] items-center border-b px-4 py-3 text-[14px] font-semibold">{{ __('ui.front.desktop.nav.blog') }}</a>
        <a href="{{ route('faq.index') }}" class="front-mobile-nav-link flex min-h-[56px] items-center border-b px-4 py-3 text-[14px] font-semibold">{{ __('ui.front.desktop.nav.faq') }}</a>
        <a href="{{ route('contact.create') }}" class="front-mobile-nav-link flex min-h-[56px] items-center border-b px-4 py-3 text-[14px] font-semibold">{{ __('ui.front.desktop.nav.contact') }}</a>
    </nav>
@endif
