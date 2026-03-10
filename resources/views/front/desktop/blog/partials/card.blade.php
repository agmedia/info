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
    $primaryCategory = $post->categories
        ->sortByDesc(fn ($category) => (int) ($category->pivot->is_primary ?? false))
        ->first();
    $categoryTranslation = $primaryCategory?->translations->firstWhere('locale', $locale)
        ?? $primaryCategory?->translations->firstWhere('locale', $fallbackLocale);
    $categoryLabel = trim((string) ($categoryTranslation?->name ?? __('ui.blog.default_category')));
    $publishedLabel = ($post->published_at ?? $post->created_at)?->translatedFormat('j. F Y.');
@endphp

<article class="ac-home-blog-card ac-blog-card">
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

            <div class="ac-home-blog-card-overlay">
                <span class="ac-home-blog-card-overlay-kicker">
                    {{ Str::upper(Str::limit($categoryLabel, 24, '')) }}
                </span>
                <span class="ac-home-blog-card-overlay-line" aria-hidden="true"></span>
            </div>
        </div>

        <div class="ac-home-blog-card-body">
            <h2 class="ac-home-blog-card-title">{{ $postTitle }}</h2>
            <p class="ac-home-blog-card-excerpt">{{ $postExcerpt }}</p>
        </div>

        <div class="ac-home-blog-card-meta">
            <span class="ac-home-blog-card-meta-link">
                <span>{{ __('ui.blog.read_more') }}</span>
                <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M4 12L12 4"></path>
                    <path d="M6 4h6v6"></path>
                </svg>
            </span>
            @if ($publishedLabel)
                <span class="ac-home-blog-card-meta-date">{{ $publishedLabel }}</span>
            @endif
        </div>
    </a>
</article>
