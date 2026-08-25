@php
    $schemaSettings = $storeSettings['schema'] ?? [];
    $schemaEnabled = (bool) ($schemaSettings['enabled'] ?? true);
    if (! $schemaEnabled) {
        $schemaSettings = array_merge($schemaSettings, [
            'org_enabled' => false,
            'website_enabled' => false,
            'breadcrumbs_enabled' => false,
            'itemlist_enabled' => false,
            'home_enabled' => false,
            'blog_enabled' => false,
            'page_enabled' => false,
            'faq_enabled' => false,
        ]);
    }

    $locale = (string) ($locale ?? app()->getLocale());
    $fallbackLocale = (string) ($fallbackLocale ?? config('app.locale'));
    $requiresExactTranslation = \App\Support\Localization\FrontendLocalePolicy::requiresExactTranslation($locale);
    $isCallRoute = request()->routeIs('eu-funds.calls.show', 'eu-funds.calls.show.en');
    $isEuFundsRoute = request()->routeIs('eu-funds.show', 'eu-funds.show.en');
    $isServiceContentRoute = request()->routeIs(
        'services.index',
        'services.index.en',
        'audit.show',
        'audit.show.en',
        'accounting.show',
        'accounting.show.en',
        'advisory.*',
        'finance.show',
        'tax.show',
        'eu-funds.show',
        'eu-funds.show.en'
    );
    $siteUrl = url('/');
    $currentUrl = url()->current();

    $brand = $storeSettings['branding'] ?? [];
    $footer = $storeSettings['footer'] ?? [];
    $og = $storeSettings['og'] ?? [];
    $seo = $storeSettings['seo'] ?? [];

    $text = static function (mixed $value, int $limit = 300): string {
        $plain = trim((string) strip_tags((string) $value));
        if ($plain === '') {
            return '';
        }

        $plain = preg_replace('/\s+/u', ' ', $plain) ?: $plain;

        return \Illuminate\Support\Str::limit($plain, $limit, '');
    };

    $nonEmpty = static fn (mixed $value): bool => trim((string) $value) !== '';
    $absolute = static fn (string $url): string => \Illuminate\Support\Str::startsWith($url, ['http://', 'https://']) ? $url : url($url);

    $businessName = trim((string) ($schemaSettings['business_name'] ?? ''));
    if ($businessName === '') {
        $businessName = trim((string) ($brand['store_name'] ?? ''));
    }
    if ($businessName === '') {
        $businessName = (string) config('app.name', 'AG Shop');
    }

    $defaultDescription = $text($seo['default_description'] ?? '', 320);
    $defaultImage = (string) ($og['default_image_url'] ?? '');

    $sameAs = collect(preg_split('/\r\n|\r|\n/', (string) ($schemaSettings['same_as'] ?? '')) ?: [])
        ->map(static fn (string $url): string => trim($url))
        ->filter(static fn (string $url): bool => filter_var($url, FILTER_VALIDATE_URL) !== false)
        ->values()
        ->all();

    if ($sameAs === []) {
        $sameAs = collect($brand['social'] ?? [])
            ->filter(static fn ($social): bool => is_array($social) && (bool) ($social['enabled'] ?? true))
            ->pluck('url')
            ->map(static fn ($url): string => trim((string) $url))
            ->filter(static fn (string $url): bool => filter_var($url, FILTER_VALIDATE_URL) !== false)
            ->values()
            ->all();
    }

    $itemListLimit = max(1, min(48, (int) ($schemaSettings['itemlist_limit'] ?? 12)));
    $schemas = [];

    if ((bool) ($schemaSettings['org_enabled'] ?? true)) {
        $organization = [
            '@context' => 'https://schema.org',
            '@type' => (string) ($schemaSettings['org_type'] ?? 'Organization'),
            '@id' => $siteUrl.'#organization',
            'name' => $businessName,
            'url' => $siteUrl,
        ];

        if ($nonEmpty($brand['logo_url'] ?? '')) {
            $organization['logo'] = $absolute((string) $brand['logo_url']);
        }
        if ($sameAs !== []) {
            $organization['sameAs'] = $sameAs;
        }

        $phone = trim((string) ($schemaSettings['business_phone'] ?? ($footer['phone'] ?? '')));
        $email = trim((string) ($schemaSettings['business_email'] ?? ($footer['email_support'] ?? $footer['email_sales'] ?? '')));
        if ($phone !== '' || $email !== '') {
            $contactPoint = ['@type' => 'ContactPoint', 'contactType' => 'customer support'];
            if ($phone !== '') {
                $contactPoint['telephone'] = $phone;
            }
            if ($email !== '') {
                $contactPoint['email'] = $email;
            }
            $organization['contactPoint'] = [$contactPoint];
        }

        $addressValues = [
            'streetAddress' => trim((string) ($schemaSettings['address_street'] ?? '')),
            'addressLocality' => trim((string) ($schemaSettings['address_city'] ?? '')),
            'addressRegion' => trim((string) ($schemaSettings['address_region'] ?? '')),
            'postalCode' => trim((string) ($schemaSettings['address_postal_code'] ?? '')),
            'addressCountry' => trim((string) ($schemaSettings['address_country'] ?? '')),
        ];
        $addressValues = array_filter($addressValues, static fn (string $value): bool => $value !== '');
        if ($addressValues !== []) {
            $organization['address'] = array_merge(['@type' => 'PostalAddress'], $addressValues);
        }

        $schemas[] = $organization;
    }

    if ((bool) ($schemaSettings['website_enabled'] ?? true)) {
        $schemas[] = [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            '@id' => $siteUrl.'#website',
            'url' => $siteUrl,
            'name' => $businessName,
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => \App\Support\Localization\FrontendRoute::url('search.index').'?q={search_term_string}',
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }

    if ((bool) ($schemaSettings['breadcrumbs_enabled'] ?? true)) {
        $breadcrumbItems = [];
        $position = 1;

        $breadcrumbItems[] = [
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => __('ui.front.desktop.footer.home'),
            'item' => route('home'),
        ];

        if (request()->routeIs('blog.*')) {
            $breadcrumbItems[] = [
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => 'Blog',
                'item' => route('blog.index'),
            ];
        }

        if (request()->routeIs('eu-funds.*')) {
            $euFundsBreadcrumbName = trim((string) ($servicePageTitle ?? data_get($callDetailUi ?? [], 'eu_funds_label', '')));

            if ($euFundsBreadcrumbName !== '') {
                $breadcrumbItems[] = [
                    '@type' => 'ListItem',
                    'position' => $position++,
                    'name' => $euFundsBreadcrumbName,
                    'item' => $isEuFundsRoute
                        ? $currentUrl
                        : \App\Support\Localization\FrontendRoute::url('eu-funds.show', locale: $locale),
                ];
            }
        }

        if (request()->routeIs('blog.show') && isset($post)) {
            $postTranslation = $post->translations->firstWhere('locale', $locale)
                ?? $post->translations->firstWhere('locale', $fallbackLocale);

            $breadcrumbItems[] = [
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => (string) ($postTranslation?->title ?? $post->code),
                'item' => $currentUrl,
            ];
        }

        if ($isCallRoute && isset($callPost)) {
            $translation = $callPost->translations->firstWhere('locale', $locale)
                ?? ($requiresExactTranslation ? null : $callPost->translations->firstWhere('locale', $fallbackLocale));

            $breadcrumbItems[] = [
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => (string) ($translation?->title ?? $callPost->code),
                'item' => $currentUrl,
            ];
        }

        if ($isServiceContentRoute && ! $isEuFundsRoute) {
            $serviceBreadcrumbName = $text($servicePageTitle ?? $servicePageMetaTitle ?? '', 191);

            if ($serviceBreadcrumbName !== '') {
                $breadcrumbItems[] = [
                    '@type' => 'ListItem',
                    'position' => $position++,
                    'name' => $serviceBreadcrumbName,
                    'item' => $currentUrl,
                ];
            }
        }

        if (request()->routeIs('pages.category') && isset($category)) {
            $categoryTranslation = $category->translations->firstWhere('locale', $locale)
                ?? $category->translations->firstWhere('locale', $fallbackLocale);

            $breadcrumbItems[] = [
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => (string) ($categoryTranslation?->name ?? $category->code),
                'item' => $currentUrl,
            ];
        }

        if (request()->routeIs('pages.show') && isset($page)) {
            $pageTranslation = $selectedTranslation
                ?? $page->translations->firstWhere('locale', $locale)
                ?? $page->translations->firstWhere('locale', $fallbackLocale);

            $breadcrumbItems[] = [
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => (string) ($pageTranslation?->title ?? $page->code),
                'item' => $currentUrl,
            ];
        }

        if (request()->routeIs('faq.index')) {
            $breadcrumbItems[] = [
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => 'FAQ',
                'item' => $currentUrl,
            ];
        }

        if (count($breadcrumbItems) > 1) {
            $schemas[] = [
                '@context' => 'https://schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => $breadcrumbItems,
            ];
        }
    }

    if (request()->routeIs('home') && (bool) ($schemaSettings['home_enabled'] ?? true)) {
        $homeTitle = trim((string) \Illuminate\Support\Facades\View::yieldContent('title'));
        if ($homeTitle === '') {
            $homeTitle = $businessName;
        }
        $homeDescription = $text(\Illuminate\Support\Facades\View::yieldContent('description'), 320);
        if ($homeDescription === '' && ! $requiresExactTranslation) {
            $homeDescription = $defaultDescription;
        }

        $homeImage = (string) ($og['home_image_url'] ?? $defaultImage);
        $homeSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => $homeTitle,
            'url' => $currentUrl,
        ];

        if ($homeDescription !== '') {
            $homeSchema['description'] = $homeDescription;
        }

        if ($homeImage !== '') {
            $homeSchema['primaryImageOfPage'] = $absolute($homeImage);
        }

        $schemas[] = $homeSchema;
    }

    if (request()->routeIs('blog.show') && isset($post) && (bool) ($schemaSettings['blog_enabled'] ?? true)) {
        $translation = $post->translations->firstWhere('locale', $locale)
            ?? ($requiresExactTranslation ? null : $post->translations->firstWhere('locale', $fallbackLocale));

        $authorName = trim((string) ($schemaSettings['blog_author_name'] ?? ''));
        if ($authorName === '') {
            $authorName = trim((string) ($post->creator?->name ?? ''));
        }
        if ($authorName === '') {
            $authorName = $businessName;
        }

        $authorUrl = trim((string) ($schemaSettings['blog_author_url'] ?? ''));
        $blogDescription = $text($translation?->meta_description ?: $translation?->excerpt, 320);
        if ($blogDescription === '' && ! $requiresExactTranslation) {
            $blogDescription = $defaultDescription;
        }

        $blogSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => $text($translation?->meta_title ?: $translation?->title ?: $post->code, 191),
            'datePublished' => optional($post->published_at)->toIso8601String(),
            'dateModified' => optional($post->updated_at)->toIso8601String(),
            'mainEntityOfPage' => $currentUrl,
            'author' => ['@type' => 'Person', 'name' => $authorName],
            'publisher' => ['@type' => 'Organization', 'name' => $businessName],
        ];

        if ($blogDescription !== '') {
            $blogSchema['description'] = $blogDescription;
        }

        if ($authorUrl !== '') {
            $blogSchema['author']['url'] = $authorUrl;
        }

        $blogImage = (string) ($og['blog_image_url'] ?? '');
        if ($blogImage === '' && method_exists($post, 'getFirstMediaUrl')) {
            $blogImage = (string) ($post->getFirstMediaUrl('blog_cover') ?: $post->getFirstMediaUrl());
        }
        if ($blogImage === '' && $defaultImage !== '') {
            $blogImage = $defaultImage;
        }

        if ($blogImage !== '') {
            $blogSchema['image'] = [['@type' => 'ImageObject', 'url' => $absolute($blogImage)]];
        }

        $schemas[] = $blogSchema;
    }

    if ($isServiceContentRoute && (bool) ($schemaSettings['page_enabled'] ?? true)) {
        $serviceSchemaDescription = $text($servicePageMetaDescription ?? '', 320);
        if ($serviceSchemaDescription === '' && ! $requiresExactTranslation) {
            $serviceSchemaDescription = $defaultDescription;
        }

        $serviceSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => $text($servicePageMetaTitle ?? $servicePageTitle ?? '', 191),
            'url' => $currentUrl,
        ];

        if ($serviceSchemaDescription !== '') {
            $serviceSchema['description'] = $serviceSchemaDescription;
        }

        $serviceImage = trim((string) ($servicePageOgImage ?? ''));
        if ($serviceImage !== '') {
            $serviceSchema['primaryImageOfPage'] = $absolute($serviceImage);
        }

        $schemas[] = $serviceSchema;
    }

    if ($isCallRoute && isset($callPost) && (bool) ($schemaSettings['blog_enabled'] ?? true)) {
        $translation = $callPost->translations->firstWhere('locale', $locale)
            ?? ($requiresExactTranslation ? null : $callPost->translations->firstWhere('locale', $fallbackLocale))
            ?? ($requiresExactTranslation ? null : $callPost->translations->first());

        $callDescription = $text(
            $translation?->meta_description ?: $translation?->excerpt ?: $translation?->body_html,
            320
        );
        if ($callDescription === '' && ! $requiresExactTranslation) {
            $callDescription = $defaultDescription;
        }

        $callSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $text($translation?->meta_title ?: $translation?->title ?: $callPost->code, 191),
            'datePublished' => optional($callPost->published_at)->toIso8601String(),
            'dateModified' => optional($callPost->updated_at)->toIso8601String(),
            'mainEntityOfPage' => $currentUrl,
            'publisher' => ['@type' => 'Organization', 'name' => $businessName],
        ];
        if ($callDescription !== '') {
            $callSchema['description'] = $callDescription;
        }

        $callImage = method_exists($callPost, 'getFirstMediaUrl')
            ? (string) ($callPost->getFirstMediaUrl('call_cover') ?: $callPost->getFirstMediaUrl())
            : '';
        if ($callImage === '' && $defaultImage !== '') {
            $callImage = $defaultImage;
        }
        if ($callImage !== '') {
            $callSchema['image'] = [['@type' => 'ImageObject', 'url' => $absolute($callImage)]];
        }

        $schemas[] = $callSchema;
    }

    if (request()->routeIs('blog.index') && (bool) ($schemaSettings['blog_enabled'] ?? true)) {
        $blogIndex = [
            '@context' => 'https://schema.org',
            '@type' => 'Blog',
            'name' => 'Blog',
            'url' => $currentUrl,
            'publisher' => ['@type' => 'Organization', 'name' => $businessName],
        ];

        if (isset($posts) && $posts instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator) {
            $items = collect($posts->items())
                ->map(function ($item) use ($locale, $fallbackLocale, $text) {
                    $tr = $item->translations->firstWhere('locale', $locale)
                        ?? $item->translations->firstWhere('locale', $fallbackLocale);

                    if (! $tr) {
                        return null;
                    }

                    return [
                        '@type' => 'ListItem',
                        'position' => null,
                        'url' => route('blog.show', ['slug' => $tr->slug ?? $item->id]),
                        'name' => $text($tr->title, 191),
                    ];
                })
                ->filter()
                ->values();

            if ($items->isNotEmpty()) {
                $blogIndex['blogPost'] = $items->map(function (array $entry, int $index): array {
                    $entry['position'] = $index + 1;
                    return $entry;
                })->all();
            }
        }

        $schemas[] = $blogIndex;
    }

    if ((bool) ($schemaSettings['itemlist_enabled'] ?? true) && request()->routeIs('blog.index') && isset($posts) && $posts instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator) {
        $listItems = collect($posts->items())
            ->take($itemListLimit)
            ->map(function ($item, int $index) use ($locale, $fallbackLocale, $text) {
                $tr = $item->translations->firstWhere('locale', $locale)
                    ?? $item->translations->firstWhere('locale', $fallbackLocale);

                return [
                    '@type' => 'ListItem',
                    'position' => $index + 1,
                    'url' => route('blog.show', ['slug' => $tr?->slug ?? $item->id]),
                    'name' => $text($tr?->title ?: $item->code, 191),
                ];
            })
            ->values()
            ->all();

        if ($listItems !== []) {
            $schemas[] = [
                '@context' => 'https://schema.org',
                '@type' => 'ItemList',
                'name' => 'Blog posts',
                'itemListElement' => $listItems,
            ];
        }
    }

    if (request()->routeIs('glossary.index') && isset($glossaryPage) && (bool) ($schemaSettings['page_enabled'] ?? true)) {
        $translation = $glossaryPageTranslation
            ?? $glossaryPage->translations->firstWhere('locale', $locale)
            ?? ($requiresExactTranslation ? null : $glossaryPage->translations->firstWhere('locale', $fallbackLocale))
            ?? ($requiresExactTranslation ? null : $glossaryPage->translations->first());
        $glossaryDescription = $text($translation?->meta_description ?: $translation?->excerpt, 320);
        if ($glossaryDescription === '' && ! $requiresExactTranslation) {
            $glossaryDescription = $defaultDescription;
        }

        $glossarySchema = [
            '@context' => 'https://schema.org',
            '@type' => 'DefinedTermSet',
            'name' => $text($translation?->meta_title ?: $translation?->title ?: $glossaryPage->code, 191),
            'url' => $currentUrl,
        ];

        if ($glossaryDescription !== '') {
            $glossarySchema['description'] = $glossaryDescription;
        }

        $termItems = collect($glossaryTerms ?? [])
            ->take($itemListLimit)
            ->map(function (array $term): ?array {
                $title = trim((string) ($term['title'] ?? ''));
                $url = trim((string) ($term['url'] ?? ''));

                if ($title === '' || $url === '') {
                    return null;
                }

                return [
                    '@type' => 'DefinedTerm',
                    'name' => $title,
                    'url' => $url,
                ];
            })
            ->filter()
            ->values()
            ->all();

        if ($termItems !== []) {
            $glossarySchema['hasDefinedTerm'] = $termItems;
        }

        $schemas[] = $glossarySchema;
    }

    if (request()->routeIs('glossary.show') && isset($glossaryTerm) && isset($glossaryTermTranslation) && (bool) ($schemaSettings['page_enabled'] ?? true)) {
        $payload = is_array($glossaryTermTranslation->payload ?? null) ? $glossaryTermTranslation->payload : [];
        $glossaryTermDescription = $text(
            $glossaryTermTranslation->meta_description ?: $glossaryTermTranslation->excerpt ?: $glossaryTermTranslation->body_html,
            320
        );
        if ($glossaryTermDescription === '' && ! $requiresExactTranslation) {
            $glossaryTermDescription = $defaultDescription;
        }
        $glossaryTermSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'DefinedTerm',
            'name' => $text($glossaryTermTranslation->meta_title ?: $glossaryTermTranslation->title ?: $glossaryTerm->code, 191),
            'url' => $currentUrl,
            'termCode' => (string) $glossaryTerm->code,
            'inDefinedTermSet' => route('glossary.index'),
        ];

        if ($glossaryTermDescription !== '') {
            $glossaryTermSchema['description'] = $glossaryTermDescription;
        }

        $synonyms = collect($payload['synonyms'] ?? [])
            ->map(fn ($item): string => trim((string) $item))
            ->filter()
            ->values()
            ->all();

        if ($synonyms !== []) {
            $glossaryTermSchema['alternateName'] = count($synonyms) === 1 ? $synonyms[0] : $synonyms;
        }

        $schemas[] = $glossaryTermSchema;
    }

    if (request()->routeIs('pages.show') && isset($page) && (bool) ($schemaSettings['page_enabled'] ?? true)) {
        $translation = $selectedTranslation
            ?? $page->translations->firstWhere('locale', $locale)
            ?? ($requiresExactTranslation ? null : $page->translations->firstWhere('locale', $fallbackLocale));
        $pageDescription = $text($translation?->meta_description ?: $translation?->excerpt, 320);
        if ($pageDescription === '' && ! $requiresExactTranslation) {
            $pageDescription = $defaultDescription;
        }

        $pageSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => $text($translation?->meta_title ?: $translation?->title ?: $page->code, 191),
            'url' => $currentUrl,
        ];

        if ($pageDescription !== '') {
            $pageSchema['description'] = $pageDescription;
        }

        $pageImage = (string) ($og['page_image_url'] ?? $defaultImage);
        if ($pageImage !== '') {
            $pageSchema['primaryImageOfPage'] = $absolute($pageImage);
        }

        $schemas[] = $pageSchema;
    }

    if ((request()->routeIs('home') || request()->routeIs('faq.index')) && (bool) ($schemaSettings['faq_enabled'] ?? true)) {
        try {
            $faqLimit = max(1, min(20, (int) ($schemaSettings['faq_limit'] ?? 8)));
            $faqGroup = trim((string) ($schemaSettings['faq_group'] ?? ''));

            $faqs = \App\Models\Content\Support\Faq::query()
                ->where('is_active', true)
                ->when($faqGroup !== '', fn ($q) => $q->where('group_code', $faqGroup))
                ->with(['translations' => fn ($q) => $q->whereIn('locale', [$locale, $fallbackLocale])])
                ->orderByDesc('is_featured')
                ->orderBy('sort_order')
                ->orderBy('id')
                ->limit($faqLimit)
                ->get();

            $faqEntities = $faqs->map(function ($faq) use ($locale, $fallbackLocale, $text) {
                $tr = $faq->translations->firstWhere('locale', $locale)
                    ?? $faq->translations->firstWhere('locale', $fallbackLocale);

                $q = $text($tr?->question, 280);
                $a = $text($tr?->answer_html, 2000);

                if ($q === '' || $a === '') {
                    return null;
                }

                return [
                    '@type' => 'Question',
                    'name' => $q,
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => $a,
                    ],
                ];
            })->filter()->values()->all();

            if ($faqEntities !== []) {
                $schemas[] = [
                    '@context' => 'https://schema.org',
                    '@type' => 'FAQPage',
                    'mainEntity' => $faqEntities,
                ];
            }
        } catch (\Throwable) {
            // Skip FAQ schema if data unavailable.
        }
    }
@endphp

@foreach ($schemas as $schema)
    <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@endforeach
