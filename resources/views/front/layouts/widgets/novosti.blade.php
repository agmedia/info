<div class="section mt-6">
    <div class="container">
        <div class="col-12">
            <div class="row">
                <div class="col-8">
                    <div class="fancy-title title-border"><h3>Novosti</h3></div>
                    <div class="posts-md">
                        <div class="entry row mb-5">
                            <div class="col-md-6">
                                <div class="entry-image"><a href="{{ route('catalog.route.blog', ['blog' => $items[1]]) }}">
                                        <img src="{{ asset($items[1]->image) }}" alt="Image">
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-6 mt-3 mt-md-0">
                                <div class="entry-title title-sm text-transform-none">
                                    <h3><a href="{{ route('catalog.route.blog', ['blog' => $items[1]]) }}">{{ $items[1]->title }}</a></h3>
                                </div>
                                <div class="entry-meta">
                                    <ul>
                                        <li><i class="uil uil-schedule"></i> {{ \Illuminate\Support\Carbon::make($items[1]->created_at)->format('d.m.Y') }}</li>
                                        @if ($items[1]->gallery)
                                            <li><i class="uil uil-camera"></i> {{ $items[1]->gallery->images->count() }}</li>
                                        @endif
                                    </ul>
                                </div>
                                <div class="entry-content"><p class="mb-0">{{ $items[1]->short_description }}</p></div>
                            </div>
                        </div>
                    </div>
                    <div class="posts-md row col-mb-30">
                        @foreach ($items as $key => $story)
                            @if ($key > 1 && $key < 6)
                                <div class="entry col-md-6">
                                    <div class="row">
                                        <div class="col-4">
                                            <div class="entry-image"><a href="{{ route('catalog.route.blog', ['blog' => $story]) }}"><img src="{{ asset($story->image) }}" alt="Image"></a></div>
                                        </div>
                                        <div class="col-8 ps-1">
                                            <div class="entry-title"><h4 class="mb-1"><a href="{{ route('catalog.route.blog', ['blog' => $story]) }}">{{ $story->title }}</a></h4>
                                                <p class="small mb-0">{{ $story->short_description }}</p></div>
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
                            @endif
                        @endforeach
                    </div>
                </div>
                <div class="col-4">
                    <div class="fancy-title title-border"><h3 class="mb-2">Najčitanije</h3></div>
                    <div class="posts-sm row col-mb-30" style="padding-left: 40px;">
                        @foreach ($items as $key => $story)
                            @if ($key > 5)
                                <div class="entry col-12">
                                    <div class="grid-inner row g-0">
                                        <div class="col-auto">
                                            <div class="entry-image"><a href="{{ route('catalog.route.blog', ['blog' => $story]) }}"><img src="{{ asset($story->image) }}" alt="Image"></a></div>
                                        </div>
                                        <div class="col ps-3">
                                            <div class="entry-title"><h4><a href="{{ route('catalog.route.blog', ['blog' => $story]) }}">{{ $story->title }}</a></h4></div>
                                            <div class="entry-meta">
                                                <ul>
                                                    <li><i class="uil uil-schedule"></i> {{ \Illuminate\Support\Carbon::make($story->created_at)->format('d.m.Y') }}</li> @if ($story->gallery)
                                                        <li><i class="uil uil-camera"></i> {{ $story->gallery->images->count() }}</li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>