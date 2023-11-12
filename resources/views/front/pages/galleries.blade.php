@extends('front.layouts.app')

@section('content')

    <!-- Page Title -->
    <section id="slider" class="slider-element" style="background-image: url('{{ asset('img/banners/banner1.jpg') }}'); background-size: cover; background-position: center center;">
        <div class="container">
            <div class="row h-100 py-4">
                <div class="col-lg-6 d-flex align-self-center flex-column pt-5 pb-0 py-lg-6 mb-0 my-lg-4">
                    <h2 class="display-4 dark" style="font-weight: 800">Galerije Fotografija SK Koros kluba.</h2>
                    <div>
                        <a href="#" class="button button-large border border-width-2 bg-alt py-2 rounded-1 fw-medium text-transform-none ls-0 ms-0 ms-sm-1 h-op-09">
                            <i class="bi-file-earmark-plus"></i>Prijavi se u Klub
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 d-flex align-self-end flex-column">
                </div>
            </div>
        </div>
    </section>

    <!-- Content
    ============================================= -->
    <section id="content">
        <div class="content-wrap pt-0">
            <div class="section mt-0">
                <div class="container">
                    <div class="col-12">
                        <div class="row">
                            <div class="col-8">
                                <div class="fancy-title title-border">
                                    <h3>Najnovije Galerije</h3>
                                </div>
                                @foreach ($galleries as $gallery)
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h4 class="mb-3">{{ $gallery['title'] }}</h4>
                                        </div>
                                        <div class="col-12">
                                            <div class="masonry-thumbs grid-container row row-cols-6" data-lightbox="gallery" style="--bs-gutter-x: 18px; --bs-gutter-y: 18px;">
                                                @foreach ($gallery->images as $image)
                                                    <a class="col" href="{{ asset($image['image']) }}" data-lightbox="gallery-item" data-zoom="true" title="{{ $image['title'] }}">
                                                        <div class="grid-inner">
                                                            <img src="{{ asset($image['image']) }}" alt="{{ $image['alt'] }}">
                                                            <div class="bg-overlay">
                                                                <div class="bg-overlay-content dark">
                                                                    <i class="uil uil-images h4 mb-0" data-hover-animate="fadeIn"></i>
                                                                </div>
                                                                <div class="bg-overlay-bg dark" data-hover-animate="fadeIn"></div>
                                                            </div>
                                                        </div>
                                                    </a>
                                                @endforeach
                                            </div>
                                            <div class="row mt-2">
                                                <div class="col-1 text-center pt-2">
                                                    <h2><i class="uil uil-comments-alt"></i></h2>
                                                </div>
                                                <div class="col-11">
                                                    <div class="entry-title mt-2">
                                                        <h4 class="mb-1"><a href="{{ route('catalog.route.blog', ['blog' => $gallery->blog]) }}">{{ $gallery->blog->title }}</a></h4>
                                                        <p class="small">{{ $gallery->blog->short_description }}</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <hr class="mx-5 mt-3 mb-2">
                                        </div>
                                    </div>
                                @endforeach

                            </div>
                            <div class="col-4">
                                <div class="fancy-title title-border">
                                    <h3 class="mb-2">Najčitanije</h3>
                                </div>
                                <div class="posts-sm row col-mb-30" style="padding-left: 40px;">
                                    @foreach ($news['side'] as $story)
                                        <div class="entry col-12">
                                            <div class="grid-inner row g-0">
                                                <div class="col-auto">
                                                    <div class="entry-image">
                                                        <a href="{{ route('catalog.route.blog', ['blog' => $story]) }}"><img src="{{ asset($story->image) }}" alt="Image"></a>
                                                    </div>
                                                </div>
                                                <div class="col ps-3">
                                                    <div class="entry-title">
                                                        <h4><a href="{{ route('catalog.route.blog', ['blog' => $story]) }}">{{ $story->title }}</a></h4>
                                                    </div>
                                                    <div class="entry-meta">
                                                        <ul>
                                                            <li><i class="uil uil-schedule"></i> {{ \Illuminate\Support\Carbon::make($story->created_at)->format('d.m.Y') }}</li>
                                                            @if ($story->gallery)
                                                                <li><i class="uil uil-camera"></i> {{ $story->gallery->images->count() }}</li>
                                                            @endif
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container mt-0 dark">
                <div class="row">
                    <div class="col-8">
                        <div class="fancy-title title-border">
                            <h3>Najnovije Vijesti</h3>
                        </div>
                        <div class="posts-md">
                            <div class="entry row mb-5">
                                <div class="col-md-6">
                                    <div class="entry-image">
                                        <a href="{{ route('catalog.route.blog', ['blog' => $news['first']]) }}"><img src="{{ asset($news['first']->image) }}" alt="Image"></a>
                                    </div>
                                </div>
                                <div class="col-md-6 mt-3 mt-md-0">
                                    <div class="entry-title title-sm text-transform-none">
                                        <h3><a href="{{ route('catalog.route.blog', ['blog' => $news['first']]) }}">{{ $news['first']->title }}</a></h3>
                                    </div>
                                    <div class="entry-meta">
                                        <ul>
                                            <li><i class="uil uil-schedule"></i> {{ \Illuminate\Support\Carbon::make($news['first']->created_at)->format('d.m.Y') }}</li>
                                            @if ($news['first']->gallery)
                                                <li><i class="uil uil-camera"></i> {{ $news['first']->gallery->images->count() }}</li>
                                            @endif
                                        </ul>
                                    </div>
                                    <div class="entry-content">
                                        <p class="mb-0">{{ $news['first']->short_description }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="posts-md row col-mb-30">
                            @foreach ($news['news'] as $story)
                                <div class="entry col-md-6">
                                    <div class="row">
                                        <div class="col-4">
                                            <div class="entry-image">
                                                <a href="{{ route('catalog.route.blog', ['blog' => $story]) }}"><img src="{{ asset($story->image) }}" alt="Image"></a>
                                            </div>
                                        </div>
                                        <div class="col-8 ps-1">
                                            <div class="entry-title">
                                                <h4 class="mb-1"><a href="{{ route('catalog.route.blog', ['blog' => $story]) }}">{{ $story->title }}</a></h4>
                                                <p class="small mb-0">{{ $story->short_description }}</p>
                                            </div>
                                            <div class="entry-meta">
                                                <ul>
                                                    <li><i class="uil uil-schedule"></i> {{ \Illuminate\Support\Carbon::make($story->created_at)->format('d.m.Y') }}</li>
                                                    @if ($story->gallery)
                                                        <li><i class="uil uil-camera"></i> {{ $story->gallery->images->count() }}</li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="fancy-title title-border">
                            <h3 class="mb-2">Najčitanije</h3>
                        </div>
                        <div class="posts-sm row col-mb-30" style="padding-left: 40px;">
                            @foreach ($news['side'] as $story)
                                <div class="entry col-12">
                                    <div class="grid-inner row g-0">
                                        <div class="col-auto">
                                            <div class="entry-image">
                                                <a href="{{ route('catalog.route.blog', ['blog' => $story]) }}"><img src="{{ asset($story->image) }}" alt="Image"></a>
                                            </div>
                                        </div>
                                        <div class="col ps-3">
                                            <div class="entry-title">
                                                <h4><a href="{{ route('catalog.route.blog', ['blog' => $story]) }}">{{ $story->title }}</a></h4>
                                            </div>
                                            <div class="entry-meta">
                                                <ul>
                                                    <li><i class="uil uil-schedule"></i> {{ \Illuminate\Support\Carbon::make($story->created_at)->format('d.m.Y') }}</li>
                                                    @if ($story->gallery)
                                                        <li><i class="uil uil-camera"></i> {{ $story->gallery->images->count() }}</li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
