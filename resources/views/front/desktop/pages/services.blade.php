@extends('front.desktop.layouts.store')

@section('title', $servicePageTitle ?? 'Usluge')
@section('main_class', 'w-full px-0 py-0')

@section('content')
    <section class="ac-services-index" aria-labelledby="ac-services-index-title">
        <h1 id="ac-services-index-title" class="sr-only">{{ $servicePageTitle ?? 'Usluge' }}</h1>

        <div class="ac-services-index-grid">
            @foreach ($serviceCards as $card)
                <a href="{{ $card['url'] }}" class="ac-services-index-card">
                    <img
                        src="{{ $card['image_url'] }}"
                        alt=""
                        aria-hidden="true"
                        loading="{{ $loop->index < 3 ? 'eager' : 'lazy' }}"
                        decoding="async"
                    >
                    <span class="ac-services-index-card-shade" aria-hidden="true"></span>
                    <span class="ac-services-index-card-title">{{ $card['title'] }}</span>
                </a>
            @endforeach
        </div>
    </section>
@endsection
