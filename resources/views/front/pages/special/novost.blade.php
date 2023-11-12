<!-- Page Title -->
    <section id="slider" class="slider-element" style="background: linear-gradient(0deg, rgba(0,0,0,0.54), rgba(0,0,0,0.54)), url('{{ asset($item->image) }}'); background-size: cover; background-position: center center;">
        <div class="container">
            <div class="row h-100 pb-2 pt-4">
                <div class="col-lg-7 d-flex align-self-center flex-column pt-5 pb-0 py-lg-4 mb-0 my-lg-4">
                    <h2 class="display-4 dark" style="font-weight: 800">{{ $item->title }}</h2>
                </div>
                <div class="col-lg-5 d-flex align-self-end flex-column"></div>
            </div>
        </div>
    </section>

    <!-- Content -->
    <section id="content">
        <div class="content-wrap">
            <div class="container">

                <div class="row gx-5 col-mb-80">
                    <main class="postcontent col-lg-9">
                        <div class="single-post mb-0">
                            <div class="entry">
                                <div class="entry-title">
                                    <h2>{{ $item->title }}</h2>
                                </div>

                                <div class="entry-meta">
                                    <ul>
                                        <li><i class="uil uil-schedule"></i> {{ \Illuminate\Support\Carbon::make($item->created_at)->format('d.m.Y') }}</li>
                                        <li><a href="#"><i class="uil uil-user"></i> admin</a></li>
                                        @if ($item->gallery)
                                            <li><i class="uil uil-camera"></i> {{ $item->gallery->images->count() }}</li>
                                        @endif
                                    </ul>
                                </div>

                                <div class="entry-image">
                                    <a href="#"><img src="{{ asset($item->image) }}" alt="{{ $item->title }}"></a>
                                </div>

                                <div class="entry-content mt-0">
                                    <div class="card border-default mb-4">
                                        <div class="card-body py-2">
                                            <div class="row align-items-center justify-content-between fs-6">
                                                <div class="col-md-auto fst-italic text-muted">Edit & Print</div>
                                                <div class="col-md-auto">
                                                    <div class="d-flex">
                                                        <div class="font-sizer" data-step="20" data-target=".entry-content">
                                                            <button type="button" class="font-size-minus btn btn-outline-secondary border-contrast-200 h-bg-contrast-200 h-text-contrast-900 border-0 ms-1"><i class="bi-type" style="font-size: 12px;"></i></button>
                                                            <button type="button" class="font-size-plus btn btn-outline-secondary border-contrast-200 h-bg-contrast-200 h-text-contrast-900 border-0 ms-1"><i class="bi-type" style="font-size: 20px;"></i></button>
                                                        </div>
                                                        <button type="button" class="font-size-plus btn btn-outline-secondary border-contrast-200 h-bg-contrast-200 h-text-contrast-900 border-0 ms-1" onclick="window.print();"><i class="bi-printer"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <p>{!! $item->description !!}</p>

                                    @if ($item->tags)
                                        <!-- Tag Cloud -->
                                        <div class="tagcloud mb-5">
                                            @foreach ($item->tags as $tag)
                                                <a href="#">{{ $tag->title }}</a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                <div class="entry-content mb-0">
                                    <div class="masonry-thumbs grid-container row row-cols-6" data-lightbox="gallery" style="--bs-gutter-x: 18px; --bs-gutter-y: 18px;">
                                        @if ($item->gallery)
                                            @foreach ($item->gallery->images as $image)
                                                <a class="col" href="{{ asset($image->image) }}" data-lightbox="gallery-item" data-zoom="true" title="#1 Image Caption for Gallery Image">
                                                    <div class="grid-inner">
                                                        <img src="{{ asset($image->image) }}" alt="Gallery Image">
                                                        <div class="bg-overlay">
                                                            <div class="bg-overlay-content dark">
                                                                <i class="uil uil-images h4 mb-0" data-hover-animate="fadeIn"></i>
                                                            </div>
                                                            <div class="bg-overlay-bg dark" data-hover-animate="fadeIn"></div>
                                                        </div>
                                                    </div>
                                                </a>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                    </main>

                    <!-- Sidebar -->
                    <aside class="sidebar col-lg-3">
                        <div class="sidebar-widgets-wrap">

                            @if ($item->gallery)
                                <div class="widget">
                                    <h4>Galerija</h4>
                                    <div class="masonry-thumbs grid-container row row-cols-4" data-lightbox="gallery" style="--bs-gutter-x: 5px; --bs-gutter-y: 5px;">
                                        @foreach ($item->gallery->images as $image)
                                            <a class="col" href="{{ asset($image->image) }}" data-lightbox="gallery-item" data-zoom="true" title="#1 Image Caption for Gallery Image">
                                                <div class="grid-inner">
                                                    <img src="{{ asset($image->image) }}" alt="Gallery Image">
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
                                </div>
                            @endif

                            @if ($item->tags)
                                <div class="widget">
                                    <h4>Tagovi</h4>
                                    <div class="tagcloud">
                                        @foreach ($item->tags as $tag)
                                            <a href="#">{{ $tag->title }}</a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                        </div>
                    </aside>
                </div>

            </div>
        </div>
    </section>