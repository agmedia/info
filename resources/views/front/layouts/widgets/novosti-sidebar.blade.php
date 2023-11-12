<div class="fancy-title title-border">
                            <h3 class="mb-2">Najčitanije</h3>
                        </div>
                        <div class="posts-sm row col-mb-30" style="padding-left: 40px;">
                            @foreach ($items as $story)
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