@php
    $hasNavigation = !empty($mainNavigation ?? []);
@endphp

@if ($hasNavigation)
    @foreach ($mainNavigation as $item)
        @php
            $children = collect($item['children'] ?? []);
            $hasChildren = $children->isNotEmpty();
            $href = (string) ($item['url'] ?? '#');
            $target = !empty($item['open_in_new_tab']) ? '_blank' : null;
            $rel = !empty($item['open_in_new_tab']) ? 'noopener noreferrer' : null;
        @endphp

        @if ($hasChildren)
            <div class="group/nav relative">
                <a href="{{ $href }}" class="front-nav-link inline-flex items-center py-6 text-[14px] font-semibold uppercase tracking-[0.12em] transition" @if($target) target="{{ $target }}" rel="{{ $rel }}" @endif>
                    <span class="front-nav-link-label border-b pb-0.5 transition">{{ $item['label'] }}</span>
                </a>

                <div class="front-nav-dropdown invisible absolute left-1/2 top-full z-50 w-[340px] -translate-x-1/2 p-2 opacity-0 transition-all duration-150 group-hover/nav:visible group-hover/nav:opacity-100">
                    @foreach ($children as $child)
                        @php
                            $childHref = (string) ($child['url'] ?? '#');
                            $childTarget = !empty($child['open_in_new_tab']) ? '_blank' : null;
                            $childRel = !empty($child['open_in_new_tab']) ? 'noopener noreferrer' : null;
                        @endphp
                        <a href="{{ $childHref }}" class="front-nav-dropdown-link block px-3 py-2 text-sm" @if($childTarget) target="{{ $childTarget }}" rel="{{ $childRel }}" @endif>
                            {{ (string) ($child['label'] ?? 'Link') }}
                        </a>
                    @endforeach
                </div>
            </div>
        @else
            <a href="{{ $href }}" class="front-nav-link inline-flex items-center py-6 text-[14px] font-semibold uppercase tracking-[0.12em] transition" @if($target) target="{{ $target }}" rel="{{ $rel }}" @endif>
                <span class="front-nav-link-label border-b pb-0.5 transition">{{ $item['label'] }}</span>
            </a>
        @endif
    @endforeach
@else
    <a href="{{ route('home') }}" class="front-nav-link inline-flex items-center py-6"><span class="front-nav-link-label border-b pb-0.5 transition">Home</span></a>
    <a href="{{ route('blog.index') }}" class="front-nav-link inline-flex items-center py-6"><span class="front-nav-link-label border-b pb-0.5 transition">{{ __('ui.front.desktop.nav.blog') }}</span></a>
    <a href="{{ route('faq.index') }}" class="front-nav-link inline-flex items-center py-6"><span class="front-nav-link-label border-b pb-0.5 transition">{{ __('ui.front.desktop.nav.faq') }}</span></a>
    <a href="{{ route('contact.create') }}" class="front-nav-link inline-flex items-center py-6"><span class="front-nav-link-label border-b pb-0.5 transition">{{ __('ui.front.desktop.nav.contact') }}</span></a>
@endif
