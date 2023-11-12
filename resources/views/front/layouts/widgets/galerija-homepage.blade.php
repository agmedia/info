<div class="container">
    <div class="heading-block text-center">
        <h3>Pogledajte zadnje <span>Fotografije</span> Sk Korosa</h3>
        <span>Trudimo se dočarati vam barem dio našeg kluba. Dođite trenirati s nama.</span>
    </div>
    <div id="oc-portfolio" class="owl-carousel portfolio-carousel carousel-widget" data-margin="18" data-pagi="false" data-autoplay="5000" data-items-xs="1" data-items-sm="2" data-items-md="3" data-items-xl="4">
        @foreach ($items as $gallery)
            @if (isset($gallery->images))
                <div class="portfolio-item">
                    <div class="portfolio-image">
                        <a href="#">
                            <img src="{{ asset($gallery->images->first()->image) }}" alt="Morning Dew">
                        </a>
                        <div class="bg-overlay" data-lightbox="gallery">
                            <div class="bg-overlay-content dark" data-hover-animate="fadeIn">
                                <a href="{{ asset($gallery->images->first()->image) }}" class="overlay-trigger-icon bg-light text-dark" data-hover-animate="fadeInDownSmall" data-hover-animate-out="fadeOutUpSmall" data-hover-speed="350" data-lightbox="gallery-item"><i class="uil uil-images"></i></a>
                                @foreach ($gallery->images as $key => $image)
                                    @if ($key > 0)
                                        <a href="{{ asset($image->image) }}" class="d-none" data-lightbox="gallery-item"></a>
                                    @endif
                                @endforeach
                            </div>
                            <div class="bg-overlay-bg dark" data-hover-animate="fadeIn"></div>
                        </div>
                    </div>
                    <div class="portfolio-desc">
                        <h3><a href="#">{{ $gallery->title }}</a></h3>
                    </div>
                </div>
            @endif
        @endforeach
    </div>
</div>