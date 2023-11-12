<div class="fancy-title title-border">
                            <h3>Novosti</h3>
                        </div>
                        <div class="posts-md row col-mb-30">
                            @foreach ($items as $story)
                                <div class="entry col-md-12">
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
                        <div class="row col-12 mt-5 mb-4">
                            {{ $items->links() }}
                        </div>