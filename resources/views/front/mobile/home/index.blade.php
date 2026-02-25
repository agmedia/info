@extends('front.mobile.layouts.store')

@section('title', (string) ($storeSettings['branding']['store_name'] ?? config('app.name', 'AG Info')).' Mobile')
@section('header_title', (string) ($storeSettings['branding']['store_name'] ?? config('app.name', 'AG Info')))
@section('page_title', __('Home'))

@section('content')
    @php
        $resolver = app(\App\Services\Content\ContentBlockResolver::class);
        $locale = app()->getLocale();

        $homeHeroBlocks = $resolver->forPlacement('home.hero', $locale, null, null, 'mobile', true);
        $homeHeroBenefitsBlocks = $resolver->forPlacement('home.hero_benefits', $locale, null, null, 'mobile', true);
        $homeCategoriesBlocks = $resolver->forPlacement('home.categories', $locale, null, null, 'mobile', true);
        $homeBottomBlocks = $resolver->forPlacement('home.bottom', $locale, null, null, 'mobile', true);
    @endphp

    @if ($homeHeroBlocks->isNotEmpty())
        @include('components.content-placement', ['items' => $homeHeroBlocks])
    @endif

    @if ($homeHeroBenefitsBlocks->isNotEmpty())
        <div class="mt-2">
            @include('components.content-placement', ['items' => $homeHeroBenefitsBlocks])
        </div>
    @endif

    @if ($homeCategoriesBlocks->isNotEmpty())
        <div class="mt-2">
            @include('components.content-placement', ['items' => $homeCategoriesBlocks])
        </div>
    @endif

    @if ($homeBottomBlocks->isNotEmpty())
        <div class="mt-2">
            @include('components.content-placement', ['items' => $homeBottomBlocks])
        </div>
    @endif

    @if ($homeHeroBlocks->isEmpty() && $homeHeroBenefitsBlocks->isEmpty() && $homeCategoriesBlocks->isEmpty() && $homeBottomBlocks->isEmpty())
        <div class="card card-style">
            <div class="content">
                <h3 class="mb-2">{{ __('Home') }}</h3>
                <p class="opacity-70 mb-0">{{ __('No active mobile content blocks yet. Create blocks and assign them to mobile placements in Admin > Content > Blocks/Slots.') }}</p>
            </div>
        </div>
    @endif
@endsection
