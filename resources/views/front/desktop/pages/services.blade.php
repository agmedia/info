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

@push('styles')
    <style>
        .ac-services-index {
            min-height: calc(100vh - var(--front-header-offset, 0px));
            background:
                linear-gradient(180deg, #ffffff 0%, #ffffff 58%, #f5f7fa 100%);
            padding: clamp(1.5rem, 3vw, 2.6rem) clamp(1rem, 3.6vw, 3.75rem) clamp(3rem, 5vw, 5rem);
        }

        .ac-services-index-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: clamp(1.35rem, 3.2vw, 3.25rem);
            width: min(100%, 1450px);
            margin: 0 auto;
        }

        .ac-services-index-card {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            aspect-ratio: 1.24 / 1;
            min-height: 260px;
            border-radius: 12px;
            background: #10263c;
            border: 1px solid rgba(15, 32, 52, 0.08);
            box-shadow: 0 18px 34px rgba(15, 23, 42, 0.1);
            isolation: isolate;
            transform: translateY(0);
            transition: transform 0.24s ease, box-shadow 0.24s ease, border-color 0.24s ease;
        }

        .ac-services-index-card img,
        .ac-services-index-card-shade {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
        }

        .ac-services-index-card img {
            object-fit: cover;
            transform: scale(1.01);
            filter: saturate(0.92) contrast(1.02);
            transition: transform 0.36s ease, filter 0.36s ease;
            z-index: 0;
        }

        .ac-services-index-card-shade {
            background:
                linear-gradient(180deg, rgba(5, 15, 28, 0.18) 0%, rgba(7, 20, 36, 0.7) 100%),
                rgba(13, 33, 55, 0.34);
            z-index: 1;
            transition: background-color 0.24s ease;
        }

        .ac-services-index-card-title {
            position: relative;
            z-index: 2;
            display: grid;
            justify-items: center;
            gap: 0.85rem;
            max-width: 13ch;
            color: #ffffff;
            font-family: 'Public Sans', 'Segoe UI', Arial, sans-serif;
            font-size: clamp(1.65rem, 2.35vw, 2.45rem);
            font-weight: 800;
            line-height: 1.06;
            text-align: center;
            text-shadow: 0 10px 26px rgba(0, 0, 0, 0.44);
        }

        .ac-services-index-card-title::after {
            content: "";
            display: block;
            width: 3.25rem;
            height: 3px;
            border-radius: 999px;
            background: #ab8d52;
            box-shadow: 0 0 18px rgba(171, 141, 82, 0.35);
        }

        .ac-services-index-card:hover img,
        .ac-services-index-card:focus-visible img {
            filter: saturate(1.02) contrast(1.05);
            transform: scale(1.06);
        }

        .ac-services-index-card:hover .ac-services-index-card-shade,
        .ac-services-index-card:focus-visible .ac-services-index-card-shade {
            background:
                linear-gradient(180deg, rgba(5, 15, 28, 0.08) 0%, rgba(7, 20, 36, 0.58) 100%),
                rgba(13, 33, 55, 0.24);
        }

        .ac-services-index-card:hover,
        .ac-services-index-card:focus-visible {
            border-color: rgba(171, 141, 82, 0.42);
            box-shadow: 0 24px 44px rgba(15, 23, 42, 0.16);
            transform: translateY(-3px);
        }

        .ac-services-index-card:focus-visible {
            outline: 3px solid #ab8d52;
            outline-offset: 4px;
        }

        @media (max-width: 1080px) {
            .ac-services-index-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 720px) {
            .ac-services-index {
                padding: 1rem;
            }

            .ac-services-index-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .ac-services-index-card {
                min-height: 220px;
                aspect-ratio: 1.28 / 1;
            }
        }
    </style>
@endpush
