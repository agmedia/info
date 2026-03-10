@props([
    'breadcrumbs' => [],
    'sectionClass' => '',
    'containerClass' => 'mx-auto w-full max-w-[1320px] px-4 sm:px-6 lg:px-8',
    'heroClass' => '',
    'panelClass' => '',
    'breadcrumbClass' => '',
    'trackClass' => '',
    'centerBreadcrumbs' => true,
])

@php
    $bandClass = trim('ac-page-title-band '.$sectionClass);
    $heroClassName = trim('ac-page-title-hero '.$heroClass);
    $panelClassName = trim('ac-page-title-panel '.$panelClass);
    $breadcrumbClassName = trim('front-scroll-breadcrumb ac-page-title-breadcrumb '.$breadcrumbClass);
    $trackClassName = trim('front-scroll-breadcrumb-track '.($centerBreadcrumbs ? 'is-centered ' : '').$trackClass);
@endphp

<section class="{{ $bandClass }}">
    <div class="{{ $containerClass }}">
        <section class="{{ $heroClassName }}">
            @if ($breadcrumbs !== [])
                <nav aria-label="Breadcrumb" class="{{ $breadcrumbClassName }}">
                    <ol class="{{ $trackClassName }}">
                        @foreach ($breadcrumbs as $breadcrumb)
                            @php
                                $label = trim((string) ($breadcrumb['label'] ?? ''));
                                $url = trim((string) ($breadcrumb['url'] ?? ''));
                                $current = (bool) ($breadcrumb['current'] ?? false);
                                $linkClass = trim((string) ($breadcrumb['link_class'] ?? ''));
                                $currentClass = trim((string) ($breadcrumb['current_class'] ?? ''));
                                $title = trim((string) ($breadcrumb['title'] ?? ''));
                            @endphp

                            @continue($label === '')

                            @if (! $loop->first)
                                <li class="front-scroll-breadcrumb-separator" aria-hidden="true">/</li>
                            @endif

                            <li>
                                @if ($url !== '' && ! $current)
                                    <a href="{{ $url }}" class="{{ trim('front-scroll-breadcrumb-link '.$linkClass) }}">{{ $label }}</a>
                                @else
                                    <span
                                        class="{{ trim('front-scroll-breadcrumb-current '.$currentClass) }}"
                                        @if ($title !== '') title="{{ $title }}" @endif
                                    >
                                        {{ $label }}
                                    </span>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                </nav>
            @endif

            <div class="{{ $panelClassName }}">
                {{ $slot }}
            </div>
        </section>
    </div>
</section>
