@php
    $navigationItems = collect($mainNavigation ?? [])->values();
    $hasNavigation = $navigationItems->isNotEmpty();
@endphp

@if ($hasNavigation)
    <div class="front-mobile-nav min-h-0 flex-1 overflow-y-auto border-t px-0 text-sm tracking-[0.03em]">
        @foreach ($navigationItems as $item)
            @php
                $target = !empty($item['open_in_new_tab']) ? '_blank' : null;
                $rel = !empty($item['open_in_new_tab']) ? 'noopener noreferrer' : null;
            @endphp

            <a href="{{ $item['url'] ?? '#' }}" class="front-mobile-nav-link flex min-h-[56px] items-center border-b px-4 py-3 text-[14px] font-semibold" @if($target) target="{{ $target }}" rel="{{ $rel }}" @endif>{{ $item['label'] }}</a>
        @endforeach
        <a href="{{ route('assessment.create') }}" class="front-mobile-nav-link flex min-h-[56px] items-center border-b px-4 py-3 text-[14px] font-semibold">Zatraži ponudu</a>
        @if ($showLeaseCalculatorLink ?? false)
            <a href="{{ route('lease-calculator.show') }}" class="front-mobile-nav-link flex min-h-[56px] items-center border-b px-4 py-3 text-[14px] font-semibold">MSFI 16 Kalkulator</a>
        @endif
    </div>
@else
    <nav class="front-mobile-nav min-h-0 flex-1 overflow-y-auto border-t px-0 text-sm tracking-[0.03em]">
        <a href="{{ route('blog.index') }}" class="front-mobile-nav-link flex min-h-[56px] items-center border-b px-4 py-3 text-[14px] font-semibold">{{ __('ui.front.desktop.nav.blog') }}</a>
        <a href="{{ route('faq.index') }}" class="front-mobile-nav-link flex min-h-[56px] items-center border-b px-4 py-3 text-[14px] font-semibold">{{ __('ui.front.desktop.nav.faq') }}</a>
        <a href="{{ route('contact.create') }}" class="front-mobile-nav-link flex min-h-[56px] items-center border-b px-4 py-3 text-[14px] font-semibold">{{ __('ui.front.desktop.nav.contact') }}</a>
        <a href="{{ route('assessment.create') }}" class="front-mobile-nav-link flex min-h-[56px] items-center border-b px-4 py-3 text-[14px] font-semibold">Zatraži ponudu</a>
        @if ($showLeaseCalculatorLink ?? false)
            <a href="{{ route('lease-calculator.show') }}" class="front-mobile-nav-link flex min-h-[56px] items-center border-b px-4 py-3 text-[14px] font-semibold">MSFI 16 Kalkulator</a>
        @endif
    </nav>
@endif
