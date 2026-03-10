<?php
    $schemaSettings = $storeSettings['schema'] ?? [];
    $schemaEnabled = (bool) ($schemaSettings['enabled'] ?? true);
    if (! $schemaEnabled) {
        $schemaSettings = array_merge($schemaSettings, [
            'org_enabled' => false,
            'website_enabled' => false,
            'breadcrumbs_enabled' => false,
            'itemlist_enabled' => false,
            'home_enabled' => false,
            'category_enabled' => false,
            'blog_enabled' => false,
            'page_enabled' => false,
            'faq_enabled' => false,
        ]);
    }

    $locale = (string) ($locale ?? app()->getLocale());
    $fallbackLocale = (string) ($fallbackLocale ?? config('app.locale'));
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
                'target' => route('blog.index').'?q={search_term_string}',
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

        $homeImage = (string) ($og['home_image_url'] ?? $defaultImage);
        $homeSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => $homeTitle,
            'url' => $currentUrl,
            'description' => $defaultDescription,
        ];

        if ($homeImage !== '') {
            $homeSchema['primaryImageOfPage'] = $absolute($homeImage);
        }

        $schemas[] = $homeSchema;
    }

    if (request()->routeIs('pages.category') && isset($category) && (bool) ($schemaSettings['category_enabled'] ?? true)) {
        $translation = $category->translations->firstWhere('locale', $locale)
            ?? $category->translations->firstWhere('locale', $fallbackLocale);

        $categorySchema = [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => $text($translation?->meta_title ?: $translation?->name ?: $category->code, 191),
            'url' => $currentUrl,
            'description' => $text($translation?->meta_description ?: $translation?->description ?: $defaultDescription, 320),
        ];

        $categoryImage = (string) ($og['category_image_url'] ?? $defaultImage);
        if ($categoryImage !== '') {
            $categorySchema['image'] = $absolute($categoryImage);
        }

        $schemas[] = $categorySchema;
    }

    if (request()->routeIs('blog.show') && isset($post) && (bool) ($schemaSettings['blog_enabled'] ?? true)) {
        $translation = $post->translations->firstWhere('locale', $locale)
            ?? $post->translations->firstWhere('locale', $fallbackLocale);

        $authorName = trim((string) ($schemaSettings['blog_author_name'] ?? ''));
        if ($authorName === '') {
            $authorName = trim((string) ($post->creator?->name ?? ''));
        }
        if ($authorName === '') {
            $authorName = $businessName;
        }

        $authorUrl = trim((string) ($schemaSettings['blog_author_url'] ?? ''));

        $blogSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => $text($translation?->meta_title ?: $translation?->title ?: $post->code, 191),
            'description' => $text($translation?->meta_description ?: $translation?->excerpt ?: $defaultDescription, 320),
            'datePublished' => optional($post->published_at)->toIso8601String(),
            'dateModified' => optional($post->updated_at)->toIso8601String(),
            'mainEntityOfPage' => $currentUrl,
            'author' => ['@type' => 'Person', 'name' => $authorName],
            'publisher' => ['@type' => 'Organization', 'name' => $businessName],
        ];

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

    if (request()->routeIs('pages.show') && isset($page) && (bool) ($schemaSettings['page_enabled'] ?? true)) {
        $translation = $selectedTranslation
            ?? $page->translations->firstWhere('locale', $locale)
            ?? $page->translations->firstWhere('locale', $fallbackLocale);

        $pageSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => $text($translation?->meta_title ?: $translation?->title ?: $page->code, 191),
            'url' => $currentUrl,
            'description' => $text($translation?->meta_description ?: $translation?->excerpt ?: $defaultDescription, 320),
        ];

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
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $schemas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $schema): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <script type="application/ld+json"><?php echo json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?></script>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /Users/tomek/Herd/info/resources/views/front/partials/schema-markup.blade.php ENDPATH**/ ?>