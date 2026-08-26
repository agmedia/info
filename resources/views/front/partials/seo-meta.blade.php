@php
    $seoSettings = $storeSettings['seo'] ?? [];
    $ogSettings = $storeSettings['og'] ?? [];
    $brandSettings = $storeSettings['branding'] ?? [];

    $locale = (string) ($locale ?? app()->getLocale());
    $fallbackLocale = (string) ($fallbackLocale ?? config('app.locale'));
    $requiresExactTranslation = \App\Support\Localization\FrontendLocalePolicy::requiresExactTranslation($locale);
    $isServicesIndexRoute = request()->routeIs('services.index', 'services.index.en');
    $isAccountingRoute = request()->routeIs('accounting.show', 'accounting.show.en');
    $isAuditRoute = request()->routeIs('audit.show', 'audit.show.en');
    $isEuFundsRoute = request()->routeIs('eu-funds.show', 'eu-funds.show.en');
    $isCallRoute = request()->routeIs('eu-funds.calls.show', 'eu-funds.calls.show.en');
    $isServiceContentRoute = $isServicesIndexRoute
        || $isAccountingRoute
        || $isAuditRoute
        || $isEuFundsRoute
        || request()->routeIs('advisory.*', 'finance.show', 'tax.show');

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
    $sectionDescription = $cleanupText(\Illuminate\Support\Facades\View::yieldContent('description'), 320);

    $title = $sectionTitle !== ''
        ? $sectionTitle
        : ($defaultTitle !== '' ? $defaultTitle : (string) config('app.name', 'AG Shop'));
    $description = $sectionDescription !== ''
        ? $sectionDescription
        : ($requiresExactTranslation ? '' : $defaultDescription);
    $robots = trim((string) ($seoSettings['robots'] ?? 'index,follow'));
    if (request()->routeIs('search.index', 'search.index.en')) {
        $robots = 'noindex,follow';
    }
    $robotsOverride = trim((string) \Illuminate\Support\Facades\View::yieldContent('robots'));
    if ($robotsOverride !== '') {
        $robots = $robotsOverride;
    }
    $canonicalPolicyOverride = trim((string) \Illuminate\Support\Facades\View::yieldContent('canonical_policy'));
    $canonicalPolicy = $canonicalPolicyOverride !== ''
        ? $canonicalPolicyOverride
        : (string) ($seoSettings['canonical_policy'] ?? 'self');
    $canonicalUrl = $canonicalPolicy === 'self' ? url()->current() : '';

    $siteName = trim((string) ($brandSettings['store_name'] ?? ''));
    if ($siteName === '') {
        $siteName = (string) config('app.name', 'AG Shop');
    }

    $ogType = 'website';
    $ogImage = (string) ($ogSettings['default_image_url'] ?? '');

    if (request()->routeIs('home')) {
        $title = $sectionTitle !== '' ? $sectionTitle : ($defaultTitle !== '' ? $defaultTitle : $title);
        $description = $sectionDescription !== '' ? $sectionDescription : $defaultDescription;
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

    if (request()->routeIs('team.index', 'team.index.en')) {
        $translation = $teamPageTranslation
            ?? ($teamPage?->translations->firstWhere('locale', $locale) ?? null)
            ?? ($teamPage?->translations->firstWhere('locale', $fallbackLocale) ?? null)
            ?? ($teamPage?->translations->first() ?? null);
        $title = $cleanupText($translation?->meta_title ?: $translation?->title ?: (string) __('ui.team.page_title'), 191);
        $description = $cleanupText($translation?->meta_description ?: $translation?->excerpt ?: (string) __('ui.team.subtitle'), 320);
    }

    if ($isServicesIndexRoute) {
        $title = $cleanupText((string) ($servicePageMetaTitle ?? $servicePageTitle ?? $title), 191);
        $localizedDescription = $cleanupText((string) ($servicePageMetaDescription ?? ''), 320);
        $description = $localizedDescription !== '' || $requiresExactTranslation ? $localizedDescription : $description;
    }

    if (request()->routeIs('advisory.*')) {
        $title = $cleanupText((string) ($servicePageMetaTitle ?? $servicePageTitle ?? $title), 191);
        $localizedDescription = $cleanupText((string) ($servicePageMetaDescription ?? ''), 320);
        $description = $localizedDescription !== '' || $requiresExactTranslation ? $localizedDescription : $description;

        if (trim((string) ($servicePageOgImage ?? '')) !== '') {
            $ogImage = (string) $servicePageOgImage;
        }
    }

    if (request()->routeIs('finance.show', 'advisory.finance.show', 'advisory.finance.show.en')) {
        $title = $cleanupText((string) ($servicePageMetaTitle ?? $servicePageTitle ?? $title), 191);
        $localizedDescription = $cleanupText((string) ($servicePageMetaDescription ?? ''), 320);
        $description = $localizedDescription !== '' || $requiresExactTranslation ? $localizedDescription : $description;

        if (trim((string) ($servicePageOgImage ?? '')) !== '') {
            $ogImage = (string) $servicePageOgImage;
        }
    }

    if ($isAccountingRoute) {
        $title = $cleanupText((string) ($servicePageMetaTitle ?? $servicePageTitle ?? $title), 191);
        $localizedDescription = $cleanupText((string) ($servicePageMetaDescription ?? ''), 320);
        $description = $localizedDescription !== '' || $requiresExactTranslation ? $localizedDescription : $description;

        if (trim((string) ($servicePageOgImage ?? '')) !== '') {
            $ogImage = (string) $servicePageOgImage;
        }
    }

    if ($isAuditRoute) {
        $title = $cleanupText((string) ($servicePageMetaTitle ?? $servicePageTitle ?? $title), 191);
        $localizedDescription = $cleanupText((string) ($servicePageMetaDescription ?? ''), 320);
        $description = $localizedDescription !== '' || $requiresExactTranslation ? $localizedDescription : $description;

        if (trim((string) ($servicePageOgImage ?? '')) !== '') {
            $ogImage = (string) $servicePageOgImage;
        }
    }

    if (request()->routeIs('tax.show', 'advisory.tax.show', 'advisory.tax.show.en')) {
        $title = $cleanupText((string) ($servicePageMetaTitle ?? $servicePageTitle ?? $title), 191);
        $localizedDescription = $cleanupText((string) ($servicePageMetaDescription ?? ''), 320);
        $description = $localizedDescription !== '' || $requiresExactTranslation ? $localizedDescription : $description;

        if (trim((string) ($servicePageOgImage ?? '')) !== '') {
            $ogImage = (string) $servicePageOgImage;
        }
    }

    if ($isEuFundsRoute || request()->routeIs('advisory.funding.show', 'advisory.funding.show.en')) {
        $title = $cleanupText((string) ($servicePageMetaTitle ?? $servicePageTitle ?? $title), 191);
        $localizedDescription = $cleanupText((string) ($servicePageMetaDescription ?? ''), 320);
        $description = $localizedDescription !== '' || $requiresExactTranslation ? $localizedDescription : $description;

        if (trim((string) ($servicePageOgImage ?? '')) !== '') {
            $ogImage = (string) $servicePageOgImage;
        }
    }

    if ($isCallRoute && isset($callPost)) {
        $ogType = 'article';
        $translation = $callPost->translations->firstWhere('locale', $locale)
            ?? ($requiresExactTranslation ? null : $callPost->translations->firstWhere('locale', $fallbackLocale))
            ?? ($requiresExactTranslation ? null : $callPost->translations->first());
        $title = $cleanupText($translation?->meta_title ?: $translation?->title ?: $title, 191);
        $localizedDescription = $cleanupText($translation?->meta_description ?: $translation?->excerpt ?: $translation?->body_html, 320);
        $description = $localizedDescription !== '' || $requiresExactTranslation ? $localizedDescription : $description;

        if (method_exists($callPost, 'getFirstMediaUrl')) {
            $postImage = (string) ($callPost->getFirstMediaUrl('call_cover') ?: $callPost->getFirstMediaUrl());
            if ($postImage !== '') {
                $ogImage = $postImage;
            }
        }
    }

    if ($description === '' && ! $requiresExactTranslation) {
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
