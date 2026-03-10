@php
    $children = collect($child['children'] ?? []);
    $padding = 1.25 + ($level * 0.9);
    $textSizeClass = match (true) {
        $level >= 2 => 'text-[12px]',
        $level === 1 => 'text-[12.5px]',
        default => 'text-[13px]',
    };
    $labelWeightClass = $level === 0 ? 'font-semibold' : ($level === 1 ? 'font-medium' : 'font-normal');
    $leafWeightClass = $level === 0 ? 'font-medium' : ($level === 1 ? 'font-normal' : 'font-light');
    $target = !empty($child['open_in_new_tab']) ? '_blank' : null;
    $rel = !empty($child['open_in_new_tab']) ? 'noopener noreferrer' : null;
@endphp

<li class="border-b">
@if ($children->isNotEmpty())
        <details class="group/subnav">
            <summary class="front-mobile-subnav-summary flex min-h-[52px] cursor-pointer list-none items-center justify-between gap-3 py-3 pr-3 {{ $textSizeClass }}" style="padding-left: {{ $padding }}rem;">
                <span class="min-w-0 truncate pr-2 {{ $labelWeightClass }}">{{ $child['label'] ?? '' }}</span>
                <span class="front-mobile-nav-toggle inline-flex h-7 w-7 items-center justify-center text-[18px] font-light leading-none group-open/subnav:hidden">+</span>
                <span class="front-mobile-nav-toggle hidden h-7 w-7 items-center justify-center text-[18px] font-light leading-none group-open/subnav:inline-flex">-</span>
            </summary>
            <ul class="bg-white/[0.02]">
                @foreach ($children as $nestedChild)
                    @include('front.desktop.partials.main-nav-mobile-child', ['child' => $nestedChild, 'level' => $level + 1])
                @endforeach
            </ul>
        </details>
    @else
        <a href="{{ $child['url'] ?? '#' }}" class="front-mobile-subnav-link flex min-h-[52px] items-center py-3 {{ $textSizeClass }} {{ $leafWeightClass }}" style="padding-left: {{ $padding }}rem;" @if($target) target="{{ $target }}" rel="{{ $rel }}" @endif>
            {{ $child['label'] ?? '' }}
        </a>
    @endif
</li>
