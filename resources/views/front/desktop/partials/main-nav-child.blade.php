@php
    $children = collect($child['children'] ?? []);
    $hasChildren = $children->isNotEmpty();
    $childHref = (string) ($child['url'] ?? '#');
    $childTarget = !empty($child['open_in_new_tab']) ? '_blank' : null;
    $childRel = !empty($child['open_in_new_tab']) ? 'noopener noreferrer' : null;
@endphp
<li class="front-nav-dropdown-item {{ $hasChildren ? 'group/subnav relative' : '' }}">
    <a href="{{ $childHref }}" class="front-nav-dropdown-link {{ $hasChildren ? 'front-nav-dropdown-link--branch' : '' }}" @if($childTarget) target="{{ $childTarget }}" rel="{{ $childRel }}" @endif>
        <span class="min-w-0 truncate">{{ $child['label'] ?? '' }}</span>
        @if ($hasChildren)
            <span class="front-nav-dropdown-caret" aria-hidden="true">›</span>
        @endif
    </a>

    @if ($hasChildren)
        <div class="front-nav-submenu invisible pointer-events-none absolute left-full top-0 z-20 w-[19rem] pl-2 opacity-0 transition-all duration-150 group-hover/subnav:visible group-hover/subnav:pointer-events-auto group-hover/subnav:opacity-100">
            <div class="front-nav-submenu-panel p-2">
                <ul class="front-nav-dropdown-list space-y-1">
            @foreach ($children as $nestedChild)
                @include('front.desktop.partials.main-nav-child', ['child' => $nestedChild, 'level' => $level + 1])
            @endforeach
                </ul>
            </div>
        </div>
    @endif
</li>
