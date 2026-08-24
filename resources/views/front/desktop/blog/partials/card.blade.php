@php
    use Illuminate\Support\Str;

    $translation = $post->translations->firstWhere('locale', $locale)
        ?? $post->translations->firstWhere('locale', $fallbackLocale);
    $postSlug = trim((string) ($translation?->slug ?? ''));
    $postUrl = $postSlug !== '' ? route('blog.show', ['slug' => $postSlug]) : route('blog.index');
    $postTitle = trim((string) ($translation?->title ?? $post->code));
    $postExcerpt = trim((string) ($translation?->excerpt ?? '')) ?: __('ui.blog.excerpt_fallback');
    $postExcerpt = Str::limit($postExcerpt, 180, '...', true);
    $postImage = $post->getFirstMedia('blog_cover');
    $postImageUrl = $postImage?->getUrl();
    if ($postImageUrl) {
        $postImagePath = parse_url($postImageUrl, PHP_URL_PATH);
        if (is_string($postImagePath) && str_starts_with($postImagePath, '/storage/')) {
            $postImageUrl = $postImagePath;
        }
    }
    $primaryCategory = $post->categories
        ->sortByDesc(fn ($category) => (int) ($category->pivot->is_primary ?? false))
        ->first();
    $categoryTranslation = $primaryCategory?->translations->firstWhere('locale', $locale)
        ?? $primaryCategory?->translations->firstWhere('locale', $fallbackLocale);
    $categoryLabel = trim((string) ($categoryTranslation?->name ?? __('ui.blog.default_category')));
    $publishedAt = ($post->published_at ?? $post->created_at)?->copy()->setTimezone(config('admin_ui.timezone'));
    $publishedLabel = $publishedAt?->translatedFormat('j. F Y.');
    $animationIndex = max(0, min(3, (int) ($revealIndex ?? 0)));
    $cardHeadingLevel = (int) ($headingLevel ?? 2);
@endphp

<article class="ac-home-blog-card ac-blog-card content-reveal animation-index-{{ $animationIndex }}" data-image-reveal>
    <a href="{{ $postUrl }}" class="ac-home-blog-card-link" aria-label="{{ __('ui.blog.open_post', ['title' => $postTitle]) }}">
        <div class="ac-home-blog-card-media">
            @if ($postImageUrl)
                <img
                    src="{{ $postImageUrl }}"
                    alt="{{ $postTitle }}"
                    class="ac-home-blog-card-image"
                    loading="lazy"
                    decoding="async"
                >
            @else
                <div class="ac-home-blog-card-placeholder">
                    <span>{{ __('ui.blog.title') }}</span>
                </div>
            @endif
        </div>

        <div class="ac-home-blog-card-body">
            <div class="ac-blog-card-eyebrow">
                <span>{{ Str::upper(Str::limit($categoryLabel, 30, '')) }}</span>
                @if ($publishedLabel)
                    <time datetime="{{ $publishedAt?->toDateString() }}">{{ $publishedLabel }}</time>
                @endif
            </div>

            @if ($cardHeadingLevel === 3)
                <h3 class="ac-home-blog-card-title">{{ $postTitle }}</h3>
            @else
                <h2 class="ac-home-blog-card-title">{{ $postTitle }}</h2>
            @endif

            <p class="ac-home-blog-card-excerpt">{{ $postExcerpt }}</p>
            <span class="ac-home-blog-card-meta-link">
                <span>{{ __('ui.blog.read_more') }}</span>
                <i class="fa-duotone fa-thin fa-arrow-right" aria-hidden="true"></i>
            </span>
        </div>
    </a>
</article>
