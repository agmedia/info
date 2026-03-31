@php
    $seoSettings = $storeSettings['seo'] ?? [];
    $ogSettings = $storeSettings['og'] ?? [];
    $brandSettings = $storeSettings['branding'] ?? [];

    $locale = (string) ($locale ?? app()->getLocale());
    $fallbackLocale = (string) ($fallbackLocale ?? config('app.locale'));

    $cleanupText = static function (mixed $value, int $limit = 320): string {
        $plain = trim((string) strip_tags((string) $value));
        if ($plain === '') {
            return '';
        }

        $plain = preg_replace('/\s+/u', ' ', $plain) ?: $plain;

        return \Illuminate\Support\Str::limit($plain, $limit, '');
    };

    $defaultTitle = trim((string) ($seoSettings['default_title'] ?? ''));
    $defaultDescription = $cleanupText($seoSettings['default_description'] ?? '', 320);
    $sectionTitle = trim((string) \Illuminate\Support\Facades\View::yieldContent('title'));

    $title = $sectionTitle !== ''
        ? $sectionTitle
        : ($defaultTitle !== '' ? $defaultTitle : (string) config('app.name', 'AG Shop'));
    $description = $defaultDescription;
    $robots = trim((string) ($seoSettings['robots'] ?? 'index,follow'));
    $canonicalPolicy = (string) ($seoSettings['canonical_policy'] ?? 'self');
    $canonicalUrl = $canonicalPolicy === 'self' ? url()->current() : '';

    $siteName = trim((string) ($brandSettings['store_name'] ?? ''));
    if ($siteName === '') {
        $siteName = (string) config('app.name', 'AG Shop');
    }

    $ogType = 'website';
    $ogImage = (string) ($ogSettings['default_image_url'] ?? '');

    if (request()->routeIs('home')) {
        $title = $defaultTitle !== '' ? $defaultTitle : $title;
        $description = $defaultDescription;
        $ogImage = (string) ($ogSettings['home_image_url'] ?? $ogImage);
    }

    if (request()->routeIs('pages.category') && isset($category)) {
        $categoryTranslation = $category->translations->firstWhere('locale', $locale)
            ?? $category->translations->firstWhere('locale', $fallbackLocale);
        $title = $cleanupText($categoryTranslation?->meta_title ?: $categoryTranslation?->name ?: $title, 191);
        $description = $cleanupText($categoryTranslation?->meta_description ?: $categoryTranslation?->description ?: $description, 320);

        if (trim((string) ($ogSettings['category_image_url'] ?? '')) !== '') {
            $ogImage = (string) $ogSettings['category_image_url'];
        } elseif (method_exists($category, 'getFirstMediaUrl')) {
            $categoryImage = (string) ($category->getFirstMediaUrl('category_banner') ?: $category->getFirstMediaUrl());
            if ($categoryImage !== '') {
                $ogImage = $categoryImage;
            }
        }
    }

    if (request()->routeIs('glossary.index') && isset($glossaryPage)) {
        $translation = $glossaryPageTranslation
            ?? $glossaryPage->translations->firstWhere('locale', $locale)
            ?? $glossaryPage->translations->firstWhere('locale', $fallbackLocale)
            ?? $glossaryPage->translations->first();
        $title = $cleanupText($translation?->meta_title ?: $translation?->title ?: $title, 191);
        $description = $cleanupText($translation?->meta_description ?: $translation?->excerpt ?: $description, 320);

        if (trim((string) ($ogSettings['page_image_url'] ?? '')) !== '') {
            $ogImage = (string) $ogSettings['page_image_url'];
        }
    }

    if (request()->routeIs('glossary.show') && isset($glossaryTerm)) {
        $translation = $glossaryTermTranslation
            ?? $glossaryTerm->translations->firstWhere('locale', $locale)
            ?? $glossaryTerm->translations->firstWhere('locale', $fallbackLocale)
            ?? $glossaryTerm->translations->first();
        $title = $cleanupText($translation?->meta_title ?: $translation?->title ?: $title, 191);
        $description = $cleanupText($translation?->meta_description ?: $translation?->excerpt ?: $translation?->body_html ?: $description, 320);

        if (trim((string) ($ogSettings['page_image_url'] ?? '')) !== '') {
            $ogImage = (string) $ogSettings['page_image_url'];
        }
    }

    if (request()->routeIs('pages.show') && isset($page)) {
        $pageTranslation = $selectedTranslation
            ?? $page->translations->firstWhere('locale', $locale)
            ?? $page->translations->firstWhere('locale', $fallbackLocale)
            ?? (isset($slug) ? $page->translations->firstWhere('slug', (string) $slug) : null);
        $title = $cleanupText($pageTranslation?->meta_title ?: $pageTranslation?->title ?: $title, 191);
        $description = $cleanupText($pageTranslation?->meta_description ?: $pageTranslation?->excerpt ?: $description, 320);

        if (trim((string) ($ogSettings['page_image_url'] ?? '')) !== '') {
            $ogImage = (string) $ogSettings['page_image_url'];
        }
    }

    if (request()->routeIs('blog.*')) {
        $ogType = request()->routeIs('blog.show') ? 'article' : 'website';

        if (isset($post)) {
            $postTranslation = $post->translations->firstWhere('locale', $locale)
                ?? $post->translations->firstWhere('locale', $fallbackLocale);
            $title = $cleanupText($postTranslation?->meta_title ?: $postTranslation?->title ?: $title, 191);
            $description = $cleanupText($postTranslation?->meta_description ?: $postTranslation?->excerpt ?: $description, 320);

            if (trim((string) ($ogSettings['blog_image_url'] ?? '')) !== '') {
                $ogImage = (string) $ogSettings['blog_image_url'];
            } elseif (method_exists($post, 'getFirstMediaUrl')) {
                $postImage = (string) ($post->getFirstMediaUrl('blog_cover') ?: $post->getFirstMediaUrl());
                if ($postImage !== '') {
                    $ogImage = $postImage;
                }
            }
        } elseif (trim((string) ($ogSettings['blog_image_url'] ?? '')) !== '') {
            $ogImage = (string) $ogSettings['blog_image_url'];
        }
    }

    if (request()->routeIs('faq.index')) {
        $title = $cleanupText((string) __('ui.faq.page_title'), 191);
        $description = $cleanupText((string) __('ui.faq.subtitle'), 320);
    }

    if (request()->routeIs('team.index')) {
        $title = $cleanupText((string) __('ui.team.page_title'), 191);
        $description = $cleanupText((string) __('ui.team.subtitle'), 320);
    }

    if (request()->routeIs('family-business.show')) {
        $title = $cleanupText((string) ($servicePageMetaTitle ?? $servicePageTitle ?? $title), 191);
        $description = $cleanupText((string) ($servicePageMetaDescription ?? $description), 320);

        if (trim((string) ($servicePageOgImage ?? '')) !== '') {
            $ogImage = (string) $servicePageOgImage;
        }
    }

    if (request()->routeIs('finance.show')) {
        $title = $cleanupText((string) ($servicePageMetaTitle ?? $servicePageTitle ?? $title), 191);
        $description = $cleanupText((string) ($servicePageMetaDescription ?? $description), 320);

        if (trim((string) ($servicePageOgImage ?? '')) !== '') {
            $ogImage = (string) $servicePageOgImage;
        }
    }

    if (request()->routeIs('audit.show')) {
        $title = $cleanupText((string) ($servicePageMetaTitle ?? $servicePageTitle ?? $title), 191);
        $description = $cleanupText((string) ($servicePageMetaDescription ?? $description), 320);

        if (trim((string) ($servicePageOgImage ?? '')) !== '') {
            $ogImage = (string) $servicePageOgImage;
        }
    }

    if (request()->routeIs('tax.show')) {
        $title = $cleanupText((string) ($servicePageMetaTitle ?? $servicePageTitle ?? $title), 191);
        $description = $cleanupText((string) ($servicePageMetaDescription ?? $description), 320);

        if (trim((string) ($servicePageOgImage ?? '')) !== '') {
            $ogImage = (string) $servicePageOgImage;
        }
    }

    if (request()->routeIs('eu-funds.show')) {
        $title = $cleanupText((string) ($servicePageMetaTitle ?? $servicePageTitle ?? $title), 191);
        $description = $cleanupText((string) ($servicePageMetaDescription ?? $description), 320);

        if (trim((string) ($servicePageOgImage ?? '')) !== '') {
            $ogImage = (string) $servicePageOgImage;
        }
    }

    if (request()->routeIs('eu-funds.calls.show') && isset($callPost)) {
        $ogType = 'article';
        $translation = $callPost->translations->firstWhere('locale', $locale)
            ?? $callPost->translations->firstWhere('locale', $fallbackLocale)
            ?? $callPost->translations->first();
        $title = $cleanupText($translation?->meta_title ?: $translation?->title ?: $title, 191);
        $description = $cleanupText($translation?->meta_description ?: $translation?->excerpt ?: $translation?->body_html ?: $description, 320);

        if (method_exists($callPost, 'getFirstMediaUrl')) {
            $postImage = (string) ($callPost->getFirstMediaUrl('call_cover') ?: $callPost->getFirstMediaUrl());
            if ($postImage !== '') {
                $ogImage = $postImage;
            }
        }
    }

    if ($description === '') {
        $description = $defaultDescription;
    }

    if ($title === '') {
        $title = (string) config('app.name', 'AG Shop');
    }
@endphp

<title>{{ $title }}</title>
@if ($description !== '')
    <meta name="description" content="{{ $description }}">
@endif
@if ($robots !== '')
    <meta name="robots" content="{{ $robots }}">
@endif
@if ($canonicalUrl !== '')
    <link rel="canonical" href="{{ $canonicalUrl }}">
@endif

<meta property="og:locale" content="{{ str_replace('_', '-', app()->getLocale()) }}">
<meta property="og:type" content="{{ $ogType }}">
<meta property="og:title" content="{{ $title }}">
@if ($description !== '')
    <meta property="og:description" content="{{ $description }}">
@endif
<meta property="og:url" content="{{ $canonicalUrl !== '' ? $canonicalUrl : request()->fullUrl() }}">
<meta property="og:site_name" content="{{ $siteName }}">
@if ($ogImage !== '')
    <meta property="og:image" content="{{ $ogImage }}">
@endif

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $title }}">
@if ($description !== '')
    <meta name="twitter:description" content="{{ $description }}">
@endif
@if ($ogImage !== '')
    <meta name="twitter:image" content="{{ $ogImage }}">
@endif
