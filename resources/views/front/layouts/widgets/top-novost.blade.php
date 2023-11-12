<div class="container">
    <div class="row col-mb-50">
        <div class="col-md-5">
            <a href="{{ route('catalog.route.blog', ['blog' => $items->first()]) }}" class="d-block position-relative rounded overflow-hidden" data-lightbox="iframe">
                <img src="{{ asset($items->first()->image) }}" alt="{{ $items->first()->title }}" class="w-100">
            </a>
        </div>
        <div class="col-md-7">
            <div class="heading-block"><h2><a href="{{ route('catalog.route.blog', ['blog' => $items->first()]) }}">{{ $items->first()->title }}</a></h2></div>
            <p>{{ $items->first()->short_description }}</p>
            <div class="row col-mb-30">
                @if ($items->first()->gallery)
                    <div class="col-sm-6 col-md-12 col-lg-6">
                        <div class="feature-box fbox-lg fbox-border fbox-light fbox-effect">
                            <div class="fbox-icon"><a href="{{ route('catalog.route.blog', ['blog' => $items->first()]) }}"><i class="uil uil-camera"></i></a></div>
                            <div class="fbox-content"><h3>Galerija Fotografija</h3>
                                <p>{{ $items->first()->gallery->title }}</p>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="col-sm-6 col-md-12 col-lg-6">
                    <ul class="iconlist iconlist-color mb-0">
                        @foreach ($items->first()->tags as $tag)
                            <li><i class="fa-solid fa-caret-right"></i> {{ $tag->title }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>