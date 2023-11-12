@extends('front.layouts.app')

@section('content')

    <!-- Slider -->
    <section id="slider" class="slider-element" style="background-image: url('{{ asset('img/banners/banner1.jpg') }}'); background-size: cover; background-position: center center;">
        <div class="container">
            <div class="row h-100 py-4">
                <div class="col-lg-6 d-flex align-self-center flex-column pt-5 pb-0 py-lg-6 mb-0 my-lg-4">
                    <h2 class="display-4 dark" style="font-weight: 800">Dobrodošao u<br>naš SK Koros klub.</h2>
                    <div>
                        <a href="#" class="button button-large border border-width-2 bg-alt py-2 rounded-1 fw-medium text-transform-none ls-0 ms-0 ms-sm-1 h-op-09">
                            <i class="bi-file-earmark-plus"></i>Prijava u Klub
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 d-flex align-self-end flex-column">
                </div>
            </div>
        </div>
    </section>

    <!-- Content -->
    <section id="content">
        <div class="content-wrap">

            <div class="promo promo-light promo-full mb-6 header-stick border-top-0 p-5">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-12 col-lg">
                            <h3>Želiš li se prijaviti na naše novosti?</h3>
                            <span>Prijavi se na newsletter našeg kluba i doznaj odmah novosti i događanja...</span>
                        </div>
                        <div class="col-12 col-lg-auto mt-4 mt-lg-0">
                            <a href="#" class="button button-large button-circle m-0">Prijava na Newsletter</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container">
                <div class="row col-mb-50">
                    <div class="col-md-5">
                        <a href="{{ route('catalog.route.blog', ['blog' => $news['top']]) }}" class="d-block position-relative rounded overflow-hidden" data-lightbox="iframe">
                            <img src="{{ asset($news['top']->image) }}" alt="{{ $news['top']->title }}" class="w-100">
                        </a>
                    </div>

                    <div class="col-md-7">
                        <div class="heading-block">
                            <h2><a href="{{ route('catalog.route.blog', ['blog' => $news['top']]) }}">{{ $news['top']->title }}</a></h2>
                        </div>

                        <p>{{ $news['top']->short_description }}</p>

                        <div class="row col-mb-30">
                            @if ($news['top']->gallery)
                                <div class="col-sm-6 col-md-12 col-lg-6">
                                    <div class="feature-box fbox-lg fbox-border fbox-light fbox-effect">
                                        <div class="fbox-icon">
                                            <a href="{{ route('catalog.route.blog', ['blog' => $news['top']]) }}"><i class="uil uil-camera"></i></a>
                                        </div>
                                        <div class="fbox-content">
                                            <h3>Galerija Fotografija</h3>
                                            <p>{{ $news['top']->gallery->title }} <span class="font-weight-light">({{ $news['top']->gallery->images->count() }})</span></p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="col-sm-6 col-md-12 col-lg-6">
                                <ul class="iconlist iconlist-color mb-0">
                                    @foreach ($news['top']->tags as $tag)
                                        <li><i class="fa-solid fa-caret-right"></i> {{ $tag->title }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="section mt-6">
                <div class="container">
                    <div class="col-12">
                        <div class="fancy-title title-border">
                            <h3>Novosti</h3>
                        </div>

                        <div class="row">
                            <div class="col-8">
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
            </div>

            <div class="container">

                <div class="heading-block text-center">
                    <h3>Pogledajte zadnje <span>Fotografije</span> Sk Korosa</h3>
                    <span>Trudimo se dočarati vam barem dio našeg kluba. Dođite trenirati s nama.</span>
                </div>

                <div id="oc-portfolio" class="owl-carousel portfolio-carousel carousel-widget" data-margin="18" data-pagi="false" data-autoplay="5000" data-items-xs="1" data-items-sm="2" data-items-md="3" data-items-xl="4">
                    @foreach ($galleries as $gallery)
                        <div class="portfolio-item">
                            <div class="portfolio-image">
                                <a href="#">
                                    <img src="{{ asset($gallery['first']['image']) }}" alt="Morning Dew">
                                </a>
                                <div class="bg-overlay" data-lightbox="gallery">
                                    <div class="bg-overlay-content dark" data-hover-animate="fadeIn">
                                        <a href="{{ asset($gallery['first']['image']) }}" class="overlay-trigger-icon bg-light text-dark" data-hover-animate="fadeInDownSmall" data-hover-animate-out="fadeOutUpSmall" data-hover-speed="350" data-lightbox="gallery-item"><i class="uil uil-images"></i></a>
                                        @foreach ($gallery['others'] as $image)
                                            <a href="{{ asset($image['image']) }}" class="d-none" data-lightbox="gallery-item"></a>
                                        @endforeach
                                    </div>
                                    <div class="bg-overlay-bg dark" data-hover-animate="fadeIn"></div>
                                </div>
                            </div>
                            <div class="portfolio-desc">
                                <h3><a href="#">{{ $gallery['title'] }}</a></h3>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </section>

@endsection
