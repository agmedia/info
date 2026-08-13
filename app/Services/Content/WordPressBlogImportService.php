<?php

namespace App\Services\Content;

use App\Models\Catalog\Category\Category;
use App\Models\Content\Blog\BlogPost;
use App\Models\Content\Blog\BlogPostTranslation;
use App\Models\Settings\Local\Language;
use Carbon\CarbonImmutable;
use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;
use SimpleXMLElement;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class WordPressBlogImportService
{
    /**
     * @return array<int, array{
     *     wp_post_id:int|null,
     *     title:string,
     *     source_slug:string,
     *     legacy_url:string,
     *     legacy_path:string,
     *     published_at:string|null,
     *     excerpt:string|null,
     *     meta_description:string|null,
     *     body_html:string|null,
     *     featured_image_url:string|null,
     *     inline_image_urls:array<int,string>,
     *     source_categories:array<int,array{slug:string,name:string}>
     * }>
     */
    public function parsePublishedPosts(string $filePath): array
    {
        $this->extendExecutionTime();

        return $this->loadPublishedPosts($this->resolveFilePath($filePath));
    }

    /**
     * @param  array{
     *     limit?:int,
     *     offset?:int,
     *     locale?:string|null,
     *     category_mode?:string|null,
     *     category_name?:string|null,
     *     category_slug?:string|null,
     *     slugs?:array<int, string>,
     *     user_id?:int|null
     * }  $options
     * @return array{
     *     locale:string,
     *     category_mode:string,
     *     categories:array<int,array{id:int,code:string,name:string,slug:string}>,
     *     imported:array<int,array{
     *         id:int,
     *         code:string,
     *         title:string,
     *         slug:string,
     *         status:string,
     *         legacy_path:string,
     *         canonical_path:string,
     *         categories:array<int,string>
     *     }>
     * }
     */
    public function import(string $filePath, array $options = []): array
    {
        $this->extendExecutionTime();

        $resolvedPath = $this->resolveFilePath($filePath);
        $locale = $this->resolveLocale($options['locale'] ?? null);
        $categoryMode = ($options['category_mode'] ?? 'single') === 'source' ? 'source' : 'single';
        $limit = max(0, (int) ($options['limit'] ?? 0));
        $offset = max(0, (int) ($options['offset'] ?? 0));
        $slugs = collect($options['slugs'] ?? [])
            ->map(fn (mixed $slug): string => $this->normalizeSlug((string) $slug))
            ->filter()
            ->values()
            ->all();
        $userId = isset($options['user_id']) ? (int) $options['user_id'] : null;
        $categoryName = trim((string) ($options['category_name'] ?? 'Novosti'));
        $categorySlug = $this->normalizeSlug((string) ($options['category_slug'] ?? $categoryName));

        if ($categorySlug === '') {
            throw new RuntimeException('Destination blog category slug could not be resolved.');
        }

        $posts = $this->loadPublishedPosts($resolvedPath);

        if ($slugs !== []) {
            $allowedSlugs = array_flip($slugs);
            $posts = array_values(array_filter(
                $posts,
                static fn (array $post): bool => isset($allowedSlugs[$post['source_slug']])
            ));
        }

        if ($offset > 0) {
            $posts = array_slice($posts, $offset);
        }

        if ($limit > 0) {
            $posts = array_slice($posts, 0, $limit);
        }

        if ($posts === []) {
            throw new RuntimeException('No published WordPress posts matched the requested filters.');
        }

        $defaultCategory = null;
        $categories = [];

        if ($categoryMode === 'single') {
            $defaultCategory = DB::transaction(function () use ($categoryName, $categorySlug, $locale, $userId): Category {
                return $this->ensureBlogCategory($categoryName, $categorySlug, $locale, $userId);
            });

            $categories[] = $this->categorySummary($defaultCategory, $locale, $categoryName, $categorySlug);
        }

        $imported = [];

        foreach ($posts as $postData) {
            $this->extendExecutionTime();

            $imported[] = DB::transaction(function () use ($postData, $locale, $categoryMode, $defaultCategory, $categoryName, $categorySlug, $userId, &$categories): array {
                $postCategories = $categoryMode === 'source'
                    ? $this->resolveCategoriesForPost($postData, $locale, $categoryName, $categorySlug, $userId)
                    : [$defaultCategory];

                foreach ($postCategories as $postCategory) {
                    if (! $postCategory instanceof Category) {
                        continue;
                    }

                    $summary = $this->categorySummary($postCategory, $locale);
                    if (! collect($categories)->contains(fn (array $row): bool => (int) $row['id'] === (int) $summary['id'])) {
                        $categories[] = $summary;
                    }
                }

                return $this->persistPost($postData, $locale, array_values(array_filter($postCategories)), $userId);
            });
        }

        return [
            'locale' => $locale,
            'category_mode' => $categoryMode,
            'categories' => array_values($categories),
            'imported' => $imported,
        ];
    }

    /**
     * @return array<int, array{
     *     wp_post_id:int|null,
     *     title:string,
     *     source_slug:string,
     *     legacy_url:string,
     *     legacy_path:string,
     *     published_at:string|null,
     *     excerpt:string|null,
     *     meta_description:string|null,
     *     body_html:string|null,
     *     featured_image_url:string|null,
     *     inline_image_urls:array<int,string>,
     *     source_categories:array<int,array{slug:string,name:string}>
     * }>
     */
    private function loadPublishedPosts(string $filePath): array
    {
        $previous = libxml_use_internal_errors(true);

        try {
            $xml = simplexml_load_file($filePath, SimpleXMLElement::class, LIBXML_NOCDATA | LIBXML_NONET);
        } finally {
            libxml_use_internal_errors($previous);
        }

        if (! $xml instanceof SimpleXMLElement || ! isset($xml->channel)) {
            throw new RuntimeException('The WordPress XML export could not be parsed.');
        }

        $attachmentIndex = $this->buildAttachmentIndex($xml->channel->item);
        $items = [];

        foreach ($xml->channel->item as $item) {
            $parsed = $this->parseItem($item, $attachmentIndex);

            if ($parsed !== null) {
                $items[] = $parsed;
            }
        }

        return $items;
    }

    /**
     * @param  SimpleXMLElement  $items
     * @return array<int, array{id:int,parent_id:int|null,url:string,title:string}>
     */
    private function buildAttachmentIndex(SimpleXMLElement $items): array
    {
        $attachments = [];

        foreach ($items as $item) {
            $namespaces = $item->getNamespaces(true);
            $wp = isset($namespaces['wp']) ? $item->children($namespaces['wp']) : null;

            if (! $wp instanceof SimpleXMLElement || trim((string) $wp->post_type) !== 'attachment') {
                continue;
            }

            $id = is_numeric((string) $wp->post_id) ? (int) $wp->post_id : 0;
            $url = $this->normalizeRemoteUrl((string) $wp->attachment_url);

            if ($id <= 0 || $url === '') {
                continue;
            }

            $attachments[$id] = [
                'id' => $id,
                'parent_id' => is_numeric((string) $wp->post_parent) ? (int) $wp->post_parent : null,
                'url' => $url,
                'title' => trim((string) $item->title),
            ];
        }

        return $attachments;
    }

    /**
     * @param  array<int, array{id:int,parent_id:int|null,url:string,title:string}>  $attachmentIndex
     * @return array{
     *     wp_post_id:int|null,
     *     title:string,
     *     source_slug:string,
     *     legacy_url:string,
     *     legacy_path:string,
     *     published_at:string|null,
     *     excerpt:string|null,
     *     meta_description:string|null,
     *     body_html:string|null,
     *     featured_image_url:string|null,
     *     inline_image_urls:array<int,string>,
     *     source_categories:array<int,array{slug:string,name:string}>
     * }|null
     */
    private function parseItem(SimpleXMLElement $item, array $attachmentIndex): ?array
    {
        $namespaces = $item->getNamespaces(true);
        $wp = isset($namespaces['wp']) ? $item->children($namespaces['wp']) : null;
        $content = isset($namespaces['content']) ? $item->children($namespaces['content']) : null;
        $excerpt = isset($namespaces['excerpt']) ? $item->children($namespaces['excerpt']) : null;

        if (! $wp instanceof SimpleXMLElement) {
            return null;
        }

        $postType = trim((string) $wp->post_type);
        $status = trim((string) $wp->status);

        if ($postType !== 'post' || $status !== 'publish') {
            return null;
        }

        $wpPostId = is_numeric((string) $wp->post_id) ? (int) $wp->post_id : null;
        $title = trim((string) $item->title);
        $legacyUrl = trim((string) $item->link);
        $legacyPath = $this->normalizeLegacyPath($legacyUrl);
        $sourceSlug = $this->resolveSourceSlug(
            trim((string) $wp->post_name),
            $legacyPath,
            $title,
            (int) ($wpPostId ?? 0)
        );
        $bodyHtml = $this->normalizeBodyHtml((string) ($content?->encoded ?? ''));
        $inlineImageUrls = $this->extractImageUrls($bodyHtml);
        $featuredImageUrl = $this->resolveFeaturedImageUrl($wp, $attachmentIndex);

        if ($featuredImageUrl === null && $inlineImageUrls !== []) {
            $featuredImageUrl = $inlineImageUrls[0];
        }

        $excerptText = $this->resolveExcerpt((string) ($excerpt?->encoded ?? ''), $bodyHtml);
        $metaDescription = $this->resolveMetaDescription($excerptText, $bodyHtml);

        if ($legacyPath === '' || $sourceSlug === '') {
            return null;
        }

        $sourceCategories = [];
        foreach ($item->category as $category) {
            if (trim((string) ($category['domain'] ?? '')) !== 'category') {
                continue;
            }

            $sourceCategories[] = [
                'slug' => trim((string) ($category['nicename'] ?? '')),
                'name' => trim((string) $category),
            ];
        }

        return [
            'wp_post_id' => $wpPostId,
            'title' => $title,
            'source_slug' => $sourceSlug,
            'legacy_url' => $legacyUrl,
            'legacy_path' => $legacyPath,
            'published_at' => $this->resolvePublishedAt(
                trim((string) $item->pubDate),
                trim((string) $wp->post_date_gmt),
                trim((string) $wp->post_date)
            ),
            'excerpt' => $excerptText,
            'meta_description' => $metaDescription,
            'body_html' => $bodyHtml !== '' ? $bodyHtml : null,
            'featured_image_url' => $featuredImageUrl,
            'inline_image_urls' => $inlineImageUrls,
            'source_categories' => $sourceCategories,
        ];
    }

    /**
     * @param  array{
     *     wp_post_id:int|null,
     *     title:string,
     *     source_slug:string,
     *     legacy_url:string,
     *     legacy_path:string,
     *     published_at:string|null,
     *     excerpt:string|null,
     *     meta_description:string|null,
     *     body_html:string|null,
     *     featured_image_url:string|null,
     *     inline_image_urls:array<int,string>,
     *     source_categories:array<int,array{slug:string,name:string}>
     * }  $postData
     * @return array{
     *     id:int,
     *     code:string,
     *     title:string,
     *     slug:string,
     *     status:string,
     *     legacy_path:string,
     *     canonical_path:string,
     *     categories:array<int,string>
     * }
     */
    private function persistPost(array $postData, string $locale, array $categories, ?int $userId): array
    {
        $code = $this->resolveCode($postData['wp_post_id'], $postData['source_slug']);
        $existing = BlogPost::query()->where('code', $code)->first();

        $post = BlogPost::query()->updateOrCreate(
            ['code' => $code],
            [
                'is_active' => true,
                'is_featured' => false,
                'published_at' => $postData['published_at'],
                'sort_order' => 0,
                'payload' => [
                    'import_source' => 'wordpress',
                    'wp_post_id' => $postData['wp_post_id'],
                    'legacy_url' => $postData['legacy_url'],
                    'legacy_path' => $postData['legacy_path'],
                    'featured_image_url' => $postData['featured_image_url'],
                    'inline_image_urls' => $postData['inline_image_urls'],
                    'source_categories' => $postData['source_categories'],
                ],
                'created_by' => $existing?->created_by ?? $userId,
                'updated_by' => $userId,
            ]
        );

        $mediaUrlMap = $this->syncImportedMedia($post, $postData);
        $bodyHtml = $this->rewriteBodyAssetUrls((string) ($postData['body_html'] ?? ''), $mediaUrlMap);
        $bodyHtml = $this->finalizeBodyHtml(
            $bodyHtml,
            (string) ($postData['featured_image_url'] ?? ''),
            $postData['featured_image_url'] ? ($mediaUrlMap[$this->normalizeRemoteUrl((string) $postData['featured_image_url'])] ?? null) : null
        );
        $slug = $this->resolveUniqueSlug($postData['source_slug'], $locale, (int) $post->id, $postData['wp_post_id']);

        $post->translations()->updateOrCreate(
            ['locale' => $locale],
            [
                'title' => $postData['title'] !== '' ? $postData['title'] : Str::headline($postData['source_slug']),
                'slug' => $slug,
                'excerpt' => $postData['excerpt'],
                'body_html' => $bodyHtml !== '' ? $bodyHtml : null,
                'meta_title' => $postData['title'] !== '' ? $postData['title'] : null,
                'meta_description' => $postData['meta_description'],
                'payload' => [
                    'import_source' => 'wordpress',
                    'wp_post_id' => $postData['wp_post_id'],
                    'source_slug' => $postData['source_slug'],
                    'legacy_url' => $postData['legacy_url'],
                    'legacy_path' => $postData['legacy_path'],
                ],
            ]
        );

        $syncPayload = [];
        $categoryLabels = [];
        foreach (array_values($categories) as $index => $category) {
            if (! $category instanceof Category) {
                continue;
            }

            $syncPayload[(int) $category->id] = [
                'sort_order' => $index,
                'is_primary' => $index === 0,
            ];

            $categoryLabels[] = (string) ($category->translation($locale)->first()?->name ?? $category->code);
        }

        if ($syncPayload !== []) {
            $post->categories()->sync($syncPayload);
        }

        return [
            'id' => (int) $post->id,
            'code' => $code,
            'title' => (string) ($postData['title'] !== '' ? $postData['title'] : Str::headline($postData['source_slug'])),
            'slug' => $slug,
            'status' => $existing ? 'updated' : 'created',
            'legacy_path' => $postData['legacy_path'],
            'canonical_path' => '/blog/'.$slug,
            'categories' => $categoryLabels,
        ];
    }

    /**
     * @param  array{
     *     wp_post_id:int|null,
     *     title:string,
     *     source_slug:string,
     *     legacy_url:string,
     *     legacy_path:string,
     *     published_at:string|null,
     *     excerpt:string|null,
     *     meta_description:string|null,
     *     body_html:string|null,
     *     featured_image_url:string|null,
     *     inline_image_urls:array<int,string>,
     *     source_categories:array<int,array{slug:string,name:string}>
     * }  $postData
     * @return array<string,string>
     */
    private function syncImportedMedia(BlogPost $post, array $postData): array
    {
        return $this->withoutBlogMediaConversions(function () use ($post, $postData): array {
            $map = [];

            $this->clearImportedMediaCollection($post, 'blog_gallery');

            $featuredImageUrl = $this->normalizeRemoteUrl((string) ($postData['featured_image_url'] ?? ''));
            if ($featuredImageUrl !== '') {
                $cover = $this->attachRemoteImage(
                    $post,
                    $featuredImageUrl,
                    'blog_cover',
                    $postData['title'] !== '' ? $postData['title'] : $postData['source_slug']
                );

                if ($cover instanceof Media) {
                    $map[$featuredImageUrl] = $this->sameOriginStorageUrl($cover->getUrl());
                }
            } else {
                $this->clearImportedMediaCollection($post, 'blog_cover');
            }

            foreach (array_values(array_unique($postData['inline_image_urls'] ?? [])) as $index => $imageUrl) {
                $imageUrl = $this->normalizeRemoteUrl((string) $imageUrl);
                if ($imageUrl === '' || $this->matchesAnyAssetSourceUrl($imageUrl, array_keys($map))) {
                    continue;
                }

                $galleryMedia = $this->attachRemoteImage(
                    $post,
                    $imageUrl,
                    'blog_gallery',
                    sprintf('%s image %d', $postData['title'] !== '' ? $postData['title'] : $postData['source_slug'], $index + 1)
                );

                if ($galleryMedia instanceof Media) {
                    $map[$imageUrl] = $this->sameOriginStorageUrl($galleryMedia->getUrl());
                }
            }

            return $map;
        });
    }

    /**
     * @param  array<int, string>  $knownUrls
     */
    private function matchesAnyAssetSourceUrl(string $candidateUrl, array $knownUrls): bool
    {
        foreach ($knownUrls as $knownUrl) {
            if ($this->urlsReferToSameAsset($candidateUrl, (string) $knownUrl)) {
                return true;
            }
        }

        return false;
    }

    private function withoutBlogMediaConversions(callable $callback): mixed
    {
        $coverKey = 'media_profiles.models.'.BlogPost::class.'.collections.blog_cover.conversions';
        $galleryKey = 'media_profiles.models.'.BlogPost::class.'.collections.blog_gallery.conversions';
        $originalConfig = [
            $coverKey => config($coverKey, []),
            $galleryKey => config($galleryKey, []),
        ];

        config([
            $coverKey => [],
            $galleryKey => [],
        ]);

        try {
            return $callback();
        } finally {
            config($originalConfig);
        }
    }

    private function clearImportedMediaCollection(BlogPost $post, string $collectionName): void
    {
        foreach ($post->getMedia($collectionName) as $media) {
            if ((string) data_get($media->custom_properties, 'import_source') !== 'wordpress') {
                continue;
            }

            $media->delete();
        }
    }

    private function attachRemoteImage(BlogPost $post, string $remoteUrl, string $collectionName, string $name): ?Media
    {
        $remoteUrl = $this->normalizeRemoteUrl($remoteUrl);
        if ($remoteUrl === '') {
            return null;
        }

        try {
            $response = Http::timeout(30)
                ->retry(2, 250)
                ->withHeaders([
                    'User-Agent' => 'AGINFO WordPress Import',
                    'Accept' => 'image/*,*/*;q=0.8',
                ])
                ->get($remoteUrl);
        } catch (\Throwable) {
            return null;
        }

        if (! $response->successful()) {
            return null;
        }

        $contentType = Str::lower(trim((string) $response->header('Content-Type', '')));
        if ($contentType !== '' && ! str_starts_with($contentType, 'image/')) {
            return null;
        }

        $extension = $this->resolveMediaExtension($remoteUrl, $contentType);
        $fileName = $this->resolveMediaFileName($remoteUrl, $extension);
        $tempPath = tempnam(sys_get_temp_dir(), 'wp-import-');

        if ($tempPath === false) {
            return null;
        }

        $finalTempPath = $tempPath.'.'.$extension;
        @rename($tempPath, $finalTempPath);
        file_put_contents($finalTempPath, $response->body());

        try {
            return $post->addMedia($finalTempPath)
                ->usingName(Str::limit(trim($name), 200, ''))
                ->usingFileName($fileName)
                ->withCustomProperties([
                    'import_source' => 'wordpress',
                    'source_url' => $remoteUrl,
                ])
                ->toMediaCollection($collectionName);
        } catch (\Throwable) {
            return null;
        } finally {
            if (is_file($finalTempPath)) {
                @unlink($finalTempPath);
            }
        }
    }

    private function rewriteBodyAssetUrls(string $html, array $assetUrlMap): string
    {
        if ($html === '' || $assetUrlMap === []) {
            return $html;
        }

        return preg_replace_callback(
            '/\b(src|href)=(["\'])(.*?)\2/i',
            function (array $matches) use ($assetUrlMap): string {
                $attribute = (string) $matches[1];
                $quote = (string) $matches[2];
                $original = (string) $matches[3];
                $normalized = $this->normalizeRemoteUrl($original);
                $replacement = $assetUrlMap[$normalized]
                    ?? $assetUrlMap[$this->normalizeWordPressImageVariantUrl($normalized)]
                    ?? null;

                if ($replacement === null) {
                    return (string) $matches[0];
                }

                return sprintf('%s=%s%s%s', $attribute, $quote, $replacement, $quote);
            },
            $html
        ) ?? $html;
    }

    private function sameOriginStorageUrl(string $url): string
    {
        $value = trim($url);
        if ($value === '') {
            return '';
        }

        $path = parse_url($value, PHP_URL_PATH);
        if (! is_string($path) || ! str_starts_with($path, '/storage/')) {
            return $value;
        }

        $query = parse_url($value, PHP_URL_QUERY);
        $fragment = parse_url($value, PHP_URL_FRAGMENT);

        return $path
            .(is_string($query) && $query !== '' ? '?'.$query : '')
            .(is_string($fragment) && $fragment !== '' ? '#'.$fragment : '');
    }

    private function finalizeBodyHtml(string $html, ?string $featuredImageUrl, ?string $featuredLocalUrl): string
    {
        $html = trim($html);
        if ($html === '') {
            return '';
        }

        $fragment = $this->loadHtmlFragment($html);
        if ($fragment === null) {
            return $html;
        }

        ['dom' => $dom, 'root' => $root, 'xpath' => $xpath] = $fragment;

        $this->removeDuplicateLeadImage($xpath, $featuredImageUrl, $featuredLocalUrl);
        $this->removeLeadingMediaBlocks($root);
        $this->removeUnwantedImages($xpath, $root);
        $this->normalizeLists($dom, $xpath, $root);
        $this->normalizeTables($dom, $xpath, $root);
        $this->removeRedundantBreaks($dom, $xpath, $root);
        $this->normalizeEmbeddedVideos($dom, $xpath, $root);
        $this->sanitizeImportedAttributes($xpath, $root);
        $this->removeEmptyParagraphs($xpath, $root);

        $html = trim($this->extractFragmentHtml($dom, $root));

        return preg_replace("/\n{3,}/u", "\n\n", $html) ?? $html;
    }

    /**
     * @return array{dom:DOMDocument,root:DOMElement,xpath:DOMXPath}|null
     */
    private function loadHtmlFragment(string $html): ?array
    {
        if (! class_exists(DOMDocument::class)) {
            return null;
        }

        $dom = new DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        $loaded = false;

        try {
            $loaded = $dom->loadHTML(
                '<?xml encoding="utf-8" ?><!DOCTYPE html><html><body><div id="wp-import-root">'.$html.'</div></body></html>',
                LIBXML_HTML_NODEFDTD | LIBXML_NONET
            );
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }

        if (! $loaded) {
            return null;
        }

        $xpath = new DOMXPath($dom);
        $root = $xpath->query('//*[@id="wp-import-root"]')->item(0);

        if (! $root instanceof DOMElement) {
            return null;
        }

        return [
            'dom' => $dom,
            'root' => $root,
            'xpath' => $xpath,
        ];
    }

    private function stripLeadingMediaBlocks(string $html): string
    {
        $fragment = $this->loadHtmlFragment($html);
        if ($fragment === null) {
            return $html;
        }

        $this->removeLeadingMediaBlocks($fragment['root']);

        return trim($this->extractFragmentHtml($fragment['dom'], $fragment['root']));
    }

    private function removeDuplicateLeadImage(DOMXPath $xpath, ?string $featuredImageUrl, ?string $featuredLocalUrl): void
    {
        $image = $xpath->query('//*[@id="wp-import-root"]//img')->item(0);

        if (! $image instanceof DOMElement) {
            return;
        }

        $imageUrl = $this->normalizeRemoteUrl((string) $image->getAttribute('src'));
        if ($imageUrl === '') {
            return;
        }

        $candidates = array_values(array_filter([
            $this->normalizeRemoteUrl((string) $featuredImageUrl),
            $this->normalizeRemoteUrl((string) $featuredLocalUrl),
        ]));

        foreach ($candidates as $candidate) {
            if (! $this->urlsReferToSameAsset($imageUrl, $candidate)) {
                continue;
            }

            $node = $this->resolveRemovableImageNode($image);
            if ($node->parentNode) {
                $node->parentNode->removeChild($node);
            }

            break;
        }
    }

    private function removeLeadingMediaBlocks(DOMElement $root): void
    {
        foreach ($this->nodeListToArray($root->childNodes) as $childNode) {
            if ($this->isIgnorableLeadNode($childNode)) {
                $childNode->parentNode?->removeChild($childNode);

                continue;
            }

            if (! $this->isLeadingMediaNode($childNode)) {
                break;
            }

            $childNode->parentNode?->removeChild($childNode);
        }
    }

    private function removeUnwantedImages(DOMXPath $xpath, DOMElement $root): void
    {
        foreach ($this->nodeListToArray($xpath->query('.//img', $root)) as $image) {
            if (! $image instanceof DOMElement) {
                continue;
            }

            $src = $this->normalizeRemoteUrl((string) $image->getAttribute('src'));
            $alt = trim((string) $image->getAttribute('alt'));

            if (! $this->shouldRemoveImportedImage($src, $alt)) {
                continue;
            }

            $nextSibling = $image->nextSibling;
            if ($nextSibling?->nodeType === XML_TEXT_NODE) {
                $nextSibling->textContent = ltrim((string) $nextSibling->textContent);
            }

            $previousSibling = $image->previousSibling;
            if ($previousSibling?->nodeType === XML_TEXT_NODE) {
                $previousSibling->textContent = rtrim((string) $previousSibling->textContent);
            }

            $node = $this->resolveRemovableImageNode($image);
            $node->parentNode?->removeChild($node);
        }
    }

    private function isIgnorableLeadNode(DOMNode $node): bool
    {
        if ($node->nodeType === XML_TEXT_NODE) {
            return trim(str_replace("\u{00A0}", ' ', (string) $node->textContent)) === '';
        }

        if (! $node instanceof DOMElement) {
            return false;
        }

        $tagName = strtolower($node->tagName);

        if ($tagName === 'br') {
            return true;
        }

        if (! in_array($tagName, ['p', 'div'], true)) {
            return false;
        }

        return ! $this->nodeHasMeaningfulText($node)
            && ! $this->nodeContainsMedia($node)
            && ! $this->nodeContainsEmbeddableMedia($node);
    }

    private function isLeadingMediaNode(DOMNode $node): bool
    {
        if (! $node instanceof DOMElement) {
            return false;
        }

        $tagName = strtolower($node->tagName);

        if (! in_array($tagName, ['img', 'figure', 'picture', 'p', 'div', 'a'], true)) {
            return false;
        }

        return ! $this->nodeHasMeaningfulText($node) && $this->nodeContainsMedia($node);
    }

    private function nodeHasMeaningfulText(DOMNode $node): bool
    {
        $text = trim(preg_replace('/\s+/u', ' ', str_replace("\u{00A0}", ' ', (string) $node->textContent)) ?? '');

        return $text !== '';
    }

    private function nodeContainsMedia(DOMNode $node): bool
    {
        if (! $node instanceof DOMElement) {
            return false;
        }

        $tagName = strtolower($node->tagName);
        if (in_array($tagName, ['img', 'picture'], true)) {
            return true;
        }

        return $node->getElementsByTagName('img')->length > 0
            || $node->getElementsByTagName('picture')->length > 0;
    }

    private function nodeContainsEmbeddableMedia(DOMNode $node): bool
    {
        if (! $node instanceof DOMElement) {
            return false;
        }

        $tagName = strtolower($node->tagName);
        if (in_array($tagName, ['iframe', 'video'], true)) {
            return true;
        }

        return $node->getElementsByTagName('iframe')->length > 0
            || $node->getElementsByTagName('video')->length > 0;
    }

    private function resolveRemovableImageNode(DOMElement $image): DOMNode
    {
        $candidate = $image;

        while ($candidate->parentNode instanceof DOMElement) {
            $parent = $candidate->parentNode;
            $tagName = strtolower($parent->tagName);

            if (! in_array($tagName, ['a', 'figure', 'p', 'div'], true)) {
                break;
            }

            if (! $this->elementContainsOnlyNodeAndWhitespace($parent, $candidate)) {
                break;
            }

            $candidate = $parent;
        }

        return $candidate;
    }

    private function elementContainsOnlyNodeAndWhitespace(DOMElement $element, DOMNode $allowedChild): bool
    {
        foreach ($element->childNodes as $childNode) {
            if ($childNode->isSameNode($allowedChild)) {
                continue;
            }

            if ($childNode->nodeType === XML_TEXT_NODE && trim(str_replace("\u{00A0}", ' ', (string) $childNode->textContent)) === '') {
                continue;
            }

            return false;
        }

        return true;
    }

    private function normalizeTables(DOMDocument $dom, DOMXPath $xpath, DOMElement $root): void
    {
        foreach ($this->nodeListToArray($xpath->query('.//td | .//th', $root)) as $cellNode) {
            if (! $cellNode instanceof DOMElement) {
                continue;
            }

            $this->normalizeTableCell($dom, $cellNode);
        }
    }

    private function normalizeTableCell(DOMDocument $dom, DOMElement $cell): void
    {
        $alignment = $this->extractTextAlign($cell);

        foreach ($this->nodeListToArray($cell->childNodes) as $childNode) {
            if ($childNode instanceof DOMElement && $alignment === '') {
                $alignment = $this->extractTextAlign($childNode);
            }
        }

        if ($alignment !== '') {
            $cell->setAttribute('data-align', $alignment);
        }

        $parts = [];
        $needsFlatten = false;

        foreach ($this->nodeListToArray($cell->childNodes) as $childNode) {
            if ($childNode instanceof DOMElement && in_array(strtolower($childNode->tagName), ['p', 'div'], true)) {
                $needsFlatten = true;
                $part = trim($this->extractFragmentHtml($dom, $childNode));

                if ($part !== '') {
                    $parts[] = $part;
                }

                continue;
            }

            if ($childNode->nodeType === XML_TEXT_NODE && trim(str_replace("\u{00A0}", ' ', (string) $childNode->textContent)) === '') {
                continue;
            }

            $rendered = trim($dom->saveHTML($childNode));
            if ($rendered !== '') {
                $parts[] = $rendered;
            }
        }

        if (! $needsFlatten) {
            return;
        }

        while ($cell->firstChild) {
            $cell->removeChild($cell->firstChild);
        }

        $this->appendHtmlFragment($dom, $cell, implode('<br>', $parts));
    }

    private function normalizeLists(DOMDocument $dom, DOMXPath $xpath, DOMElement $root): void
    {
        foreach ($this->nodeListToArray($xpath->query('.//li', $root)) as $listItem) {
            if (! $listItem instanceof DOMElement) {
                continue;
            }

            foreach ($this->nodeListToArray($xpath->query('.//br', $listItem)) as $breakNode) {
                if (! $breakNode instanceof DOMElement || ! $breakNode->parentNode) {
                    continue;
                }

                $breakNode->parentNode->replaceChild($dom->createTextNode(' '), $breakNode);
            }

            foreach ($this->nodeListToArray($listItem->childNodes) as $childNode) {
                if (! $childNode instanceof DOMElement || ! in_array(strtolower($childNode->tagName), ['p', 'div'], true)) {
                    continue;
                }

                if ($childNode->getElementsByTagName('ul')->length > 0 || $childNode->getElementsByTagName('ol')->length > 0) {
                    continue;
                }

                $html = trim($this->extractFragmentHtml($dom, $childNode));
                $html = preg_replace('#<br\s*/?>#i', ' ', $html) ?? $html;
                $fragment = $this->loadHtmlFragment($html);

                if ($fragment !== null) {
                    foreach ($this->nodeListToArray($fragment['root']->childNodes) as $fragmentChild) {
                        $listItem->insertBefore($dom->importNode($fragmentChild, true), $childNode);
                    }
                } else {
                    $listItem->insertBefore($dom->createTextNode(trim(strip_tags($html))), $childNode);
                }

                $listItem->removeChild($childNode);
            }
        }
    }

    private function removeRedundantBreaks(DOMDocument $dom, DOMXPath $xpath, DOMElement $root): void
    {
        foreach ($this->nodeListToArray($xpath->query('.//br', $root)) as $breakNode) {
            if (! $breakNode instanceof DOMElement || ! $breakNode->parentNode instanceof DOMElement) {
                continue;
            }

            $parent = $breakNode->parentNode;
            $parentTag = strtolower($parent->tagName);

            if (in_array($parentTag, ['td', 'th'], true)) {
                continue;
            }

            $previous = $this->previousSignificantSibling($breakNode);
            $next = $this->nextSignificantSibling($breakNode);

            if ($previous instanceof DOMElement && strtolower($previous->tagName) === 'br') {
                $parent->removeChild($breakNode);

                continue;
            }

            if ($next instanceof DOMElement && strtolower($next->tagName) === 'br') {
                $parent->removeChild($breakNode);

                continue;
            }

            if ($previous === null || $next === null) {
                $parent->removeChild($breakNode);

                continue;
            }

            if ($this->isBlockLikeNode($previous) || $this->isBlockLikeNode($next)) {
                $parent->removeChild($breakNode);
            }
        }
    }

    private function normalizeEmbeddedVideos(DOMDocument $dom, DOMXPath $xpath, DOMElement $root): void
    {
        foreach ($this->nodeListToArray($xpath->query('.//*[contains(concat(" ", normalize-space(@class), " "), " elementor-widget-video ")]', $root)) as $widgetNode) {
            if (! $widgetNode instanceof DOMElement || ! $widgetNode->parentNode) {
                continue;
            }

            $payload = $this->resolveEmbeddedVideoPayload($widgetNode);
            if ($payload === null) {
                continue;
            }

            $widgetNode->parentNode->replaceChild(
                $this->buildEmbeddedVideoIframe($dom, $payload['src'], $payload['title']),
                $widgetNode
            );
        }

        foreach ($this->nodeListToArray($xpath->query('.//iframe', $root)) as $iframeNode) {
            if (! $iframeNode instanceof DOMElement || ! $iframeNode->parentNode) {
                continue;
            }

            $payload = $this->resolveEmbeddedVideoPayload($iframeNode);
            if ($payload === null) {
                continue;
            }

            $iframeNode->parentNode->replaceChild(
                $this->buildEmbeddedVideoIframe($dom, $payload['src'], $payload['title']),
                $iframeNode
            );
        }

        foreach ($this->nodeListToArray($xpath->query('.//p', $root)) as $paragraphNode) {
            if (! $paragraphNode instanceof DOMElement || ! $paragraphNode->parentNode) {
                continue;
            }

            $payload = $this->resolveStandaloneEmbeddedVideoPayload($paragraphNode);
            if ($payload === null) {
                continue;
            }

            $paragraphNode->parentNode->replaceChild(
                $this->buildEmbeddedVideoIframe($dom, $payload['src'], $payload['title']),
                $paragraphNode
            );
        }

        foreach ($this->nodeListToArray($root->childNodes) as $childNode) {
            $payload = $this->resolveStandaloneEmbeddedVideoTextPayload($childNode);
            if ($payload === null || ! $childNode->parentNode) {
                continue;
            }

            $childNode->parentNode->replaceChild(
                $this->buildEmbeddedVideoIframe($dom, $payload['src'], $payload['title']),
                $childNode
            );
        }
    }

    private function sanitizeImportedAttributes(DOMXPath $xpath, DOMElement $root): void
    {
        foreach ($this->nodeListToArray($xpath->query('.//*', $root)) as $node) {
            if (! $node instanceof DOMElement || ! $node->hasAttributes()) {
                continue;
            }

            $allowedAttributes = match (strtolower($node->tagName)) {
                'a' => ['href', 'target', 'rel'],
                'img' => ['src', 'alt', 'loading', 'decoding'],
                'iframe' => ['src', 'title', 'frameborder', 'allow', 'allowfullscreen', 'referrerpolicy', 'loading'],
                'td', 'th' => ['colspan', 'rowspan', 'data-align'],
                'ol' => ['start'],
                default => [],
            };

            foreach ($this->nodeListToArray($node->attributes) as $attribute) {
                $attributeName = strtolower((string) $attribute->nodeName);

                if (in_array($attributeName, $allowedAttributes, true)) {
                    continue;
                }

                $node->removeAttributeNode($attribute);
            }
        }
    }

    private function removeEmptyParagraphs(DOMXPath $xpath, DOMElement $root): void
    {
        foreach ($this->nodeListToArray($xpath->query('.//p', $root)) as $paragraph) {
            if (! $paragraph instanceof DOMElement) {
                continue;
            }

            $text = trim(str_replace("\u{00A0}", ' ', (string) $paragraph->textContent));
            $hasMedia = $paragraph->getElementsByTagName('img')->length > 0
                || $paragraph->getElementsByTagName('iframe')->length > 0
                || $paragraph->getElementsByTagName('video')->length > 0;

            if ($text === '' && ! $hasMedia) {
                $paragraph->parentNode?->removeChild($paragraph);
            }
        }
    }

    /**
     * @return array<int, DOMNode>
     */
    private function nodeListToArray(iterable $nodeList): array
    {
        $nodes = [];

        foreach ($nodeList as $node) {
            if ($node instanceof DOMNode) {
                $nodes[] = $node;
            }
        }

        return $nodes;
    }

    private function appendHtmlFragment(DOMDocument $dom, DOMElement $parent, string $html): void
    {
        $fragment = $this->loadHtmlFragment($html);

        if ($fragment === null) {
            $parent->appendChild($dom->createTextNode(strip_tags($html)));

            return;
        }

        foreach ($this->nodeListToArray($fragment['root']->childNodes) as $childNode) {
            $parent->appendChild($dom->importNode($childNode, true));
        }
    }

    private function extractFragmentHtml(DOMDocument $dom, DOMElement $element): string
    {
        $html = '';

        foreach ($element->childNodes as $childNode) {
            $html .= $dom->saveHTML($childNode);
        }

        return $html;
    }

    private function extractTextAlign(DOMElement $element): string
    {
        $align = Str::lower(trim((string) $element->getAttribute('align')));
        if (in_array($align, ['left', 'center', 'right', 'justify'], true)) {
            return $align;
        }

        $style = (string) $element->getAttribute('style');
        if (preg_match('/text-align\s*:\s*(left|center|right|justify)/i', $style, $matches) === 1) {
            return Str::lower((string) $matches[1]);
        }

        return '';
    }

    /**
     * @return array{src:string,title:string}|null
     */
    private function resolveEmbeddedVideoPayload(DOMElement $node): ?array
    {
        $rawUrl = '';
        $title = '';
        $iframe = null;

        if (strtolower($node->tagName) === 'iframe') {
            $rawUrl = (string) $node->getAttribute('src');
            $title = trim((string) $node->getAttribute('title'));
        } else {
            $settings = $this->decodeElementorVideoSettings((string) $node->getAttribute('data-settings'));
            $rawUrl = trim((string) ($settings['youtube_url'] ?? $settings['url'] ?? ''));

            $iframe = $this->nodeListToArray($node->getElementsByTagName('iframe'))[0] ?? null;
            if ($rawUrl === '' && $iframe instanceof DOMElement) {
                $rawUrl = (string) $iframe->getAttribute('src');
            }

            if ($iframe instanceof DOMElement) {
                $title = trim((string) $iframe->getAttribute('title'));
            }
        }

        $embedUrl = $this->resolveYouTubeEmbedUrl($rawUrl);
        if ($embedUrl === '' && $iframe instanceof DOMElement) {
            $embedUrl = $this->resolveYouTubeEmbedUrl((string) $iframe->getAttribute('src'));
        }
        if ($embedUrl === '') {
            return null;
        }

        return [
            'src' => $embedUrl,
            'title' => $title,
        ];
    }

    /**
     * @return array<string,mixed>
     */
    private function decodeElementorVideoSettings(string $rawSettings): array
    {
        $settings = trim(html_entity_decode($rawSettings, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        if ($settings === '') {
            return [];
        }

        $decoded = json_decode($settings, true);

        return is_array($decoded) ? $decoded : [];
    }

    private function buildEmbeddedVideoIframe(DOMDocument $dom, string $src, string $title = ''): DOMElement
    {
        $iframe = $dom->createElement('iframe');
        $iframe->setAttribute('src', $src);
        $iframe->setAttribute('frameborder', '0');
        $iframe->setAttribute('allow', 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share');
        $iframe->setAttribute('allowfullscreen', 'allowfullscreen');
        $iframe->setAttribute('referrerpolicy', 'strict-origin-when-cross-origin');
        $iframe->setAttribute('loading', 'lazy');
        $iframe->setAttribute('title', $title !== '' ? $title : 'YouTube video');

        return $iframe;
    }

    /**
     * @return array{src:string,title:string}|null
     */
    private function resolveStandaloneEmbeddedVideoPayload(DOMElement $node): ?array
    {
        if (strtolower($node->tagName) !== 'p') {
            return null;
        }

        if ($node->getElementsByTagName('iframe')->length > 0 || $node->getElementsByTagName('video')->length > 0) {
            return null;
        }

        $rawUrl = '';
        $title = '';
        $anchor = null;

        foreach ($this->nodeListToArray($node->childNodes) as $childNode) {
            if ($childNode->nodeType === XML_TEXT_NODE) {
                if (trim(str_replace("\u{00A0}", ' ', (string) $childNode->textContent)) === '') {
                    continue;
                }

                if ($anchor instanceof DOMElement) {
                    return null;
                }

                $rawUrl = trim((string) $node->textContent);

                continue;
            }

            if (! $childNode instanceof DOMElement) {
                return null;
            }

            if (strtolower($childNode->tagName) !== 'a' || $anchor instanceof DOMElement) {
                return null;
            }

            $anchor = $childNode;
        }

        if ($anchor instanceof DOMElement) {
            foreach ($this->nodeListToArray($anchor->childNodes) as $anchorChild) {
                if ($anchorChild->nodeType === XML_TEXT_NODE) {
                    if (trim(str_replace("\u{00A0}", ' ', (string) $anchorChild->textContent)) === '') {
                        continue;
                    }

                    $title = trim((string) $anchor->textContent);

                    continue;
                }

                return null;
            }

            $rawUrl = trim((string) $anchor->getAttribute('href'));
            if ($rawUrl === '') {
                $rawUrl = trim((string) $anchor->textContent);
            }
        }

        $rawUrl = preg_replace('/\s+/u', ' ', $rawUrl) ?? $rawUrl;
        $rawUrl = trim($rawUrl);

        if ($rawUrl === '' || preg_match('#^(https?:)?//\S+$#u', $rawUrl) !== 1) {
            return null;
        }

        $embedUrl = $this->resolveYouTubeEmbedUrl($rawUrl);
        if ($embedUrl === '') {
            return null;
        }

        return [
            'src' => $embedUrl,
            'title' => $title,
        ];
    }

    /**
     * @return array{src:string,title:string}|null
     */
    private function resolveStandaloneEmbeddedVideoTextPayload(DOMNode $node): ?array
    {
        if ($node->nodeType !== XML_TEXT_NODE) {
            return null;
        }

        $rawUrl = trim(preg_replace('/\s+/u', ' ', str_replace("\u{00A0}", ' ', (string) $node->textContent)) ?? '');
        if ($rawUrl === '' || preg_match('#^(https?:)?//\S+$#u', $rawUrl) !== 1) {
            return null;
        }

        $embedUrl = $this->resolveYouTubeEmbedUrl($rawUrl);
        if ($embedUrl === '') {
            return null;
        }

        return [
            'src' => $embedUrl,
            'title' => '',
        ];
    }

    private function resolveYouTubeEmbedUrl(string $rawValue): string
    {
        $input = trim(html_entity_decode($rawValue, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        if ($input === '') {
            return '';
        }

        if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $input) === 1) {
            return 'https://www.youtube.com/embed/'.$input;
        }

        if (str_starts_with($input, '//')) {
            $input = 'https:'.$input;
        } elseif (! preg_match('#^[a-z][a-z\d+.-]*://#i', $input)) {
            $input = 'https://'.ltrim($input, '/');
        }

        $parts = parse_url($input);
        if (! is_array($parts)) {
            return '';
        }

        $host = Str::lower((string) preg_replace('/^(www\.|m\.)/i', '', (string) ($parts['host'] ?? '')));
        $pathSegments = array_values(array_filter(explode('/', trim((string) ($parts['path'] ?? ''), '/')), static fn (string $segment): bool => $segment !== ''));

        $videoId = '';
        if ($host === 'youtu.be') {
            $videoId = $pathSegments[0] ?? '';
        } elseif (in_array($host, ['youtube.com', 'youtube-nocookie.com'], true)) {
            if (($pathSegments[0] ?? '') === 'watch') {
                parse_str((string) ($parts['query'] ?? ''), $queryParams);
                $videoId = (string) ($queryParams['v'] ?? '');
            } elseif (in_array($pathSegments[0] ?? '', ['embed', 'shorts', 'live'], true)) {
                $videoId = $pathSegments[1] ?? '';
            }
        }

        $videoId = preg_replace('/[^a-zA-Z0-9_-]/', '', $videoId) ?? '';
        if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $videoId) !== 1) {
            return '';
        }

        $embedUrl = 'https://www.youtube.com/embed/'.$videoId;
        $startSeconds = $this->extractYouTubeStartSeconds(
            (string) ($parts['query'] ?? ''),
            (string) ($parts['fragment'] ?? '')
        );

        if ($startSeconds > 0) {
            $embedUrl .= '?start='.$startSeconds;
        }

        return $embedUrl;
    }

    private function extractYouTubeStartSeconds(string $query, string $fragment): int
    {
        $queryParams = [];
        parse_str($query, $queryParams);

        foreach ([$queryParams['start'] ?? null, $queryParams['t'] ?? null] as $candidate) {
            $seconds = $this->parseYouTubeTimestamp((string) $candidate);
            if ($seconds > 0) {
                return $seconds;
            }
        }

        $normalizedFragment = ltrim(trim($fragment), '#');
        if ($normalizedFragment === '') {
            return 0;
        }

        if (str_contains($normalizedFragment, '=')) {
            $fragmentParams = [];
            parse_str($normalizedFragment, $fragmentParams);

            foreach ([$fragmentParams['start'] ?? null, $fragmentParams['t'] ?? null] as $candidate) {
                $seconds = $this->parseYouTubeTimestamp((string) $candidate);
                if ($seconds > 0) {
                    return $seconds;
                }
            }

            return 0;
        }

        return $this->parseYouTubeTimestamp($normalizedFragment);
    }

    private function parseYouTubeTimestamp(string $rawValue): int
    {
        $value = Str::lower(trim($rawValue));
        if ($value === '') {
            return 0;
        }

        if (preg_match('/^\d+$/', $value) === 1) {
            return (int) $value;
        }

        preg_match_all('/(\d+)(h|m|s)/', $value, $matches, PREG_SET_ORDER);
        if ($matches === []) {
            return 0;
        }

        $totalSeconds = 0;
        $matchedLength = 0;

        foreach ($matches as $match) {
            $amount = (int) ($match[1] ?? 0);
            $unit = (string) ($match[2] ?? '');

            $totalSeconds += match ($unit) {
                'h' => $amount * 3600,
                'm' => $amount * 60,
                default => $amount,
            };
            $matchedLength += strlen((string) ($match[0] ?? ''));
        }

        return $matchedLength === strlen($value) ? $totalSeconds : 0;
    }

    private function previousSignificantSibling(DOMNode $node): ?DOMNode
    {
        $sibling = $node->previousSibling;

        while ($sibling) {
            if ($sibling->nodeType === XML_TEXT_NODE && trim(str_replace("\u{00A0}", ' ', (string) $sibling->textContent)) === '') {
                $sibling = $sibling->previousSibling;

                continue;
            }

            return $sibling;
        }

        return null;
    }

    private function nextSignificantSibling(DOMNode $node): ?DOMNode
    {
        $sibling = $node->nextSibling;

        while ($sibling) {
            if ($sibling->nodeType === XML_TEXT_NODE && trim(str_replace("\u{00A0}", ' ', (string) $sibling->textContent)) === '') {
                $sibling = $sibling->nextSibling;

                continue;
            }

            return $sibling;
        }

        return null;
    }

    private function isBlockLikeNode(DOMNode $node): bool
    {
        if (! $node instanceof DOMElement) {
            return false;
        }

        return in_array(strtolower($node->tagName), [
            'p', 'div', 'figure', 'table', 'thead', 'tbody', 'tfoot', 'tr', 'td', 'th',
            'ul', 'ol', 'li', 'blockquote', 'pre', 'hr', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
        ], true);
    }

    private function urlsReferToSameAsset(string $leftUrl, string $rightUrl): bool
    {
        $left = $this->normalizeRemoteUrl($leftUrl);
        $right = $this->normalizeRemoteUrl($rightUrl);

        if ($left === '' || $right === '') {
            return false;
        }

        $leftCandidates = [$left, $this->normalizeWordPressImageVariantUrl($left)];
        $rightCandidates = [$right, $this->normalizeWordPressImageVariantUrl($right)];

        return count(array_intersect($leftCandidates, $rightCandidates)) > 0;
    }

    /**
     * @param  array{
     *     wp_post_id:int|null,
     *     title:string,
     *     source_slug:string,
     *     legacy_url:string,
     *     legacy_path:string,
     *     published_at:string|null,
     *     excerpt:string|null,
     *     meta_description:string|null,
     *     body_html:string|null,
     *     featured_image_url:string|null,
     *     inline_image_urls:array<int,string>,
     *     source_categories:array<int,array{slug:string,name:string}>
     * }  $postData
     * @return array<int, Category>
     */
    private function resolveCategoriesForPost(array $postData, string $locale, string $fallbackName, string $fallbackSlug, ?int $userId): array
    {
        $resolved = [];
        $sourceCategories = $postData['source_categories'];

        if ($sourceCategories === []) {
            return [$this->ensureBlogCategory($fallbackName, $fallbackSlug, $locale, $userId)];
        }

        foreach ($sourceCategories as $sourceCategory) {
            $name = trim((string) ($sourceCategory['name'] ?? ''));
            $slug = $this->normalizeSlug((string) ($sourceCategory['slug'] ?? $name));

            if ($slug === '') {
                continue;
            }

            $resolved[] = $this->ensureBlogCategory(
                $name !== '' ? $name : Str::headline($slug),
                $slug,
                $locale,
                $userId
            );
        }

        return $resolved !== []
            ? $resolved
            : [$this->ensureBlogCategory($fallbackName, $fallbackSlug, $locale, $userId)];
    }

    private function ensureBlogCategory(string $name, string $slug, string $locale, ?int $userId): Category
    {
        $code = $slug;

        $category = Category::query()
            ->where('scope', Category::SCOPE_BLOG)
            ->where(function ($query) use ($code, $slug, $name): void {
                $query
                    ->where('code', $code)
                    ->orWhereHas('translations', function ($translationQuery) use ($slug, $name): void {
                        $translationQuery
                            ->where('scope', Category::SCOPE_BLOG)
                            ->where(function ($nestedQuery) use ($slug, $name): void {
                                $nestedQuery->where('slug', $slug);

                                if ($name !== '') {
                                    $nestedQuery->orWhereRaw('LOWER(name) = ?', [Str::lower($name)]);
                                }
                            });
                    });
            })
            ->first();

        if (! $category) {
            $category = new Category([
                'scope' => Category::SCOPE_BLOG,
                'code' => $code,
                'is_active' => true,
                'show_in_menu' => true,
                'sort_order' => 0,
                'payload' => ['import_source' => 'wordpress'],
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);

            $category->saveAsRoot();
        } else {
            $category->fill([
                'is_active' => true,
                'show_in_menu' => true,
                'updated_by' => $userId,
            ])->save();
        }

        $category->translations()->updateOrCreate(
            ['scope' => Category::SCOPE_BLOG, 'locale' => $locale],
            [
                'name' => $name,
                'slug' => $slug,
                'description' => null,
                'meta_title' => $name,
                'meta_description' => null,
                'payload' => ['import_source' => 'wordpress'],
            ]
        );

        return $category;
    }

    /**
     * @return array{id:int,code:string,name:string,slug:string}
     */
    private function categorySummary(Category $category, string $locale, ?string $name = null, ?string $slug = null): array
    {
        $translation = $category->translations()
            ->where('locale', $locale)
            ->first();

        return [
            'id' => (int) $category->id,
            'code' => (string) $category->code,
            'name' => $name !== null && $name !== ''
                ? $name
                : (string) ($translation?->name ?? $category->translations()->orderByDesc('id')->value('name') ?? $category->code),
            'slug' => $slug !== null && $slug !== ''
                ? $slug
                : (string) ($translation?->slug ?? $category->translations()->orderByDesc('id')->value('slug') ?? $category->code),
        ];
    }

    private function resolveFilePath(string $filePath): string
    {
        $trimmed = trim($filePath);

        if ($trimmed === '') {
            throw new RuntimeException('A WordPress XML export path is required.');
        }

        if (is_file($trimmed)) {
            return $trimmed;
        }

        $basePath = base_path($trimmed);
        if (is_file($basePath)) {
            return $basePath;
        }

        throw new RuntimeException('WordPress XML export not found: '.$filePath);
    }

    private function extendExecutionTime(): void
    {
        @ini_set('max_execution_time', '0');

        if (function_exists('set_time_limit')) {
            @set_time_limit(0);
        }
    }

    private function resolveLocale(?string $locale): string
    {
        $normalized = $this->normalizeLocale((string) $locale);
        if ($normalized !== '') {
            return $normalized;
        }

        try {
            $defaultLanguage = Language::query()
                ->where('is_active', true)
                ->orderByDesc('is_default')
                ->orderBy('sort_order')
                ->value('code');

            $normalized = $this->normalizeLocale((string) $defaultLanguage);
            if ($normalized !== '') {
                return $normalized;
            }
        } catch (\Throwable) {
            // Fall back to config locale when language settings are not available.
        }

        return $this->normalizeLocale((string) config('app.locale', 'hr')) ?: 'hr';
    }

    private function normalizeLocale(string $locale): string
    {
        $normalized = Str::lower(trim($locale));

        if ($normalized === '') {
            return '';
        }

        foreach (['_', '-'] as $separator) {
            if (str_contains($normalized, $separator)) {
                $normalized = (string) explode($separator, $normalized)[0];
            }
        }

        return $normalized;
    }

    private function normalizeLegacyPath(string $url): string
    {
        $path = trim((string) parse_url($url, PHP_URL_PATH));

        if ($path === '') {
            return '';
        }

        $path = '/'.ltrim($path, '/');

        return rtrim($path, '/');
    }

    private function resolveSourceSlug(string $postName, string $legacyPath, string $title, int $postId): string
    {
        $slug = $this->normalizeSlug($postName);

        if ($slug !== '') {
            return $slug;
        }

        $legacySegments = array_values(array_filter(explode('/', trim($legacyPath, '/'))));
        $legacySlug = $this->normalizeSlug((string) end($legacySegments));
        if ($legacySlug !== '') {
            return $legacySlug;
        }

        $titleSlug = $this->normalizeSlug($title);
        if ($titleSlug !== '') {
            return $titleSlug;
        }

        return 'wordpress-post-'.$postId;
    }

    private function normalizeSlug(string $value): string
    {
        $normalized = trim(Str::slug($value));
        $normalized = preg_replace('/-+/u', '-', $normalized) ?? $normalized;

        return trim($normalized, '-');
    }

    private function resolvePublishedAt(string $pubDate, string $postDateGmt, string $postDate): ?string
    {
        foreach ([$pubDate, $postDateGmt, $postDate] as $candidate) {
            $candidate = trim($candidate);
            if ($candidate === '' || str_starts_with($candidate, '0000-00-00')) {
                continue;
            }

            try {
                return CarbonImmutable::parse($candidate)->toDateTimeString();
            } catch (\Throwable) {
                continue;
            }
        }

        return null;
    }

    private function resolveFeaturedImageUrl(SimpleXMLElement $wp, array $attachmentIndex): ?string
    {
        foreach ($wp->postmeta as $postMeta) {
            $key = trim((string) $postMeta->meta_key);
            if ($key !== '_thumbnail_id') {
                continue;
            }

            $attachmentId = is_numeric((string) $postMeta->meta_value)
                ? (int) $postMeta->meta_value
                : 0;

            $url = $attachmentIndex[$attachmentId]['url'] ?? null;
            if (is_string($url) && $url !== '') {
                return $url;
            }
        }

        return null;
    }

    private function normalizeBodyHtml(string $bodyHtml): string
    {
        $html = trim(html_entity_decode($bodyHtml, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        if ($html === '') {
            return '';
        }

        $html = str_replace(["\r\n", "\r", "\u{00A0}"], ["\n", "\n", ' '], $html);
        $html = preg_replace('#<script\b[^>]*>.*?</script>#is', '', $html) ?? $html;
        $html = preg_replace('#<style\b[^>]*>.*?</style>#is', '', $html) ?? $html;
        $html = preg_replace('/<!--.*?-->/s', '', $html) ?? $html;
        $html = $this->stripWordPressShortcodes($html);

        $blocks = [];
        $html = $this->extractProtectedBlocks($html, $blocks);
        $html = preg_replace("/[ \t]+\n/u", "\n", $html) ?? $html;
        $html = preg_replace("/\n{3,}/u", "\n\n", $html) ?? $html;
        $html = $this->autoParagraph($html);
        $html = strtr($html, $blocks);
        $html = $this->stripLeadingMediaBlocks($html);
        $html = preg_replace("/\n{3,}/u", "\n\n", $html) ?? $html;
        $html = trim($html);

        return $html;
    }

    private function stripWordPressShortcodes(string $html): string
    {
        $html = preg_replace('#\[caption[^\]]*\](.*?)\[/caption\]#is', '$1', $html) ?? $html;
        $html = preg_replace('/\[(?:\/)?[a-z][\w:-]*(?:[^\]]*)\]/iu', ' ', $html) ?? $html;

        return trim($html);
    }

    /**
     * @param  array<string,string>  $blocks
     */
    private function extractProtectedBlocks(string $html, array &$blocks): string
    {
        $counter = 0;
        $storeBlock = function (string $markup) use (&$blocks, &$counter): string {
            $key = sprintf('__WP_IMPORT_BLOCK_%d__', ++$counter);
            $blocks[$key] = trim($markup);

            return "\n\n".$key."\n\n";
        };

        $html = preg_replace_callback(
            '#<a\b[^>]*>\s*<img\b[^>]*>\s*</a>#is',
            fn (array $matches): string => $storeBlock($this->normalizeLinkedImageMarkup((string) $matches[0])),
            $html
        ) ?? $html;

        $html = preg_replace_callback(
            '#<img\b[^>]*>#is',
            fn (array $matches): string => $storeBlock($this->normalizeStandaloneImageMarkup((string) $matches[0])),
            $html
        ) ?? $html;

        $html = preg_replace_callback(
            '#<hr\b[^>]*\/?>#is',
            fn (): string => $storeBlock('<hr>'),
            $html
        ) ?? $html;

        return $html;
    }

    private function normalizeLinkedImageMarkup(string $markup): string
    {
        $href = $this->extractTagAttribute($markup, 'href');
        $imageTag = '';

        if (preg_match('#<img\b[^>]*>#is', $markup, $matches) === 1) {
            $imageTag = $this->sanitizeImageTag((string) $matches[0]);
        }

        if ($imageTag === '') {
            return '';
        }

        if ($href !== '') {
            return '<figure><a href="'.e($href).'">'.$imageTag.'</a></figure>';
        }

        return '<figure>'.$imageTag.'</figure>';
    }

    private function normalizeStandaloneImageMarkup(string $markup): string
    {
        $imageTag = $this->sanitizeImageTag($markup);

        return $imageTag !== ''
            ? '<figure>'.$imageTag.'</figure>'
            : '';
    }

    private function sanitizeImageTag(string $markup): string
    {
        $src = $this->normalizeRemoteUrl($this->extractTagAttribute($markup, 'src'));
        if ($src === '') {
            return '';
        }

        $alt = trim($this->extractTagAttribute($markup, 'alt'));
        if ($this->shouldRemoveImportedImage($src, $alt)) {
            return '';
        }

        return sprintf(
            '<img src="%s" alt="%s" loading="lazy" decoding="async">',
            e($src),
            e($alt)
        );
    }

    private function extractTagAttribute(string $markup, string $attribute): string
    {
        $pattern = sprintf('/\b%s=(["\'])(.*?)\1/i', preg_quote($attribute, '/'));

        if (preg_match($pattern, $markup, $matches) !== 1) {
            return '';
        }

        return trim(html_entity_decode((string) $matches[2], ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }

    private function autoParagraph(string $html): string
    {
        $html = trim($html);

        if ($html === '') {
            return '';
        }

        $chunks = preg_split("/\n{2,}/u", $html) ?: [$html];
        $wrapped = [];

        foreach ($chunks as $chunk) {
            $chunk = trim($chunk);
            if ($chunk === '') {
                continue;
            }

            if ($this->isProtectedBlockChunk($chunk)) {
                $wrapped[] = $chunk;
                continue;
            }

            $wrapped[] = '<p>'.preg_replace("/\n/u", "<br>\n", $chunk).'</p>';
        }

        return implode("\n\n", $wrapped);
    }

    private function isProtectedBlockChunk(string $chunk): bool
    {
        if ((bool) preg_match('/^__WP_IMPORT_BLOCK_\d+__$/', $chunk)) {
            return true;
        }

        return (bool) preg_match('/^<(?:figure|hr|p|ul|ol|li|blockquote|pre|table|thead|tbody|tfoot|tr|td|th|div|h[1-6])\b/i', $chunk);
    }

    /**
     * @return array<int,string>
     */
    private function extractImageUrls(?string $html): array
    {
        if (! is_string($html) || trim($html) === '') {
            return [];
        }

        preg_match_all('/\bsrc=(["\'])(.*?)\1/i', $html, $matches);

        return collect($matches[2] ?? [])
            ->map(fn (mixed $url): string => $this->normalizeRemoteUrl((string) $url))
            ->filter(fn (string $url): bool => $url !== '' && ! $this->shouldRemoveImportedImage($url))
            ->unique()
            ->values()
            ->all();
    }

    private function shouldRemoveImportedImage(string $src, string $alt = ''): bool
    {
        $src = $this->normalizeRemoteUrl($src);
        if ($src === '') {
            return false;
        }

        $host = Str::lower((string) parse_url($src, PHP_URL_HOST));
        $path = Str::lower((string) parse_url($src, PHP_URL_PATH));

        if ($host === 's.w.org' && str_contains($path, '/images/core/emoji/')) {
            return true;
        }

        if (str_contains($path, '/emoji/') && preg_match('/\/[0-9a-f]+(?:-[0-9a-f]+)*\.svg$/i', $path) === 1) {
            return true;
        }

        return false;
    }

    private function cleanText(string $value): string
    {
        $decoded = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $decoded = $this->stripWordPressShortcodes($decoded);
        $plain = trim(strip_tags($decoded));

        if ($plain === '') {
            return '';
        }

        return preg_replace('/\s+/u', ' ', $plain) ?: $plain;
    }

    private function normalizeRemoteUrl(string $url): string
    {
        $normalized = trim(html_entity_decode($url, ENT_QUOTES | ENT_HTML5, 'UTF-8'));

        if ($normalized === '') {
            return '';
        }

        if (str_starts_with($normalized, '//')) {
            return 'https:'.$normalized;
        }

        return $normalized;
    }

    private function normalizeWordPressImageVariantUrl(string $url): string
    {
        if ($url === '') {
            return '';
        }

        $parts = parse_url($url);
        $path = (string) ($parts['path'] ?? '');
        $normalizedPath = preg_replace('/-\d+x\d+(?=\.[^.]+$)/i', '', $path) ?? $path;

        if ($normalizedPath === $path) {
            return $url;
        }

        $rebuilt = '';
        if (isset($parts['scheme'])) {
            $rebuilt .= $parts['scheme'].'://';
        }
        if (isset($parts['user'])) {
            $rebuilt .= $parts['user'];
            if (isset($parts['pass'])) {
                $rebuilt .= ':'.$parts['pass'];
            }
            $rebuilt .= '@';
        }
        if (isset($parts['host'])) {
            $rebuilt .= $parts['host'];
        }
        if (isset($parts['port'])) {
            $rebuilt .= ':'.$parts['port'];
        }

        $rebuilt .= $normalizedPath;

        if (isset($parts['query'])) {
            $rebuilt .= '?'.$parts['query'];
        }
        if (isset($parts['fragment'])) {
            $rebuilt .= '#'.$parts['fragment'];
        }

        return $rebuilt;
    }

    private function resolveMediaExtension(string $remoteUrl, string $contentType): string
    {
        $extension = Str::lower(pathinfo((string) parse_url($remoteUrl, PHP_URL_PATH), PATHINFO_EXTENSION));

        if ($extension !== '' && in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'avif'], true)) {
            return $extension;
        }

        return match ($contentType) {
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
            'image/avif' => 'avif',
            default => 'jpg',
        };
    }

    private function resolveMediaFileName(string $remoteUrl, string $extension): string
    {
        $name = pathinfo((string) parse_url($remoteUrl, PHP_URL_PATH), PATHINFO_FILENAME);
        $name = $this->normalizeSlug($name);

        if ($name === '') {
            $name = 'wordpress-image';
        }

        return $name.'.'.$extension;
    }

    private function resolveExcerpt(string $excerpt, ?string $bodyHtml): ?string
    {
        $source = trim($excerpt) !== '' ? $excerpt : (string) $bodyHtml;
        $text = $this->cleanText($source);

        return $text !== '' ? Str::limit($text, 320, '') : null;
    }

    private function resolveMetaDescription(?string $excerpt, ?string $bodyHtml): ?string
    {
        $source = trim((string) $excerpt) !== '' ? (string) $excerpt : (string) $bodyHtml;
        $text = $this->cleanText($source);

        return $text !== '' ? Str::limit($text, 320, '') : null;
    }

    private function resolveCode(?int $wpPostId, string $sourceSlug): string
    {
        if ($wpPostId !== null && $wpPostId > 0) {
            return 'wordpress-post-'.$wpPostId;
        }

        return 'wordpress-post-'.$sourceSlug;
    }

    private function resolveUniqueSlug(string $desiredSlug, string $locale, int $postId, ?int $wpPostId): string
    {
        $base = $desiredSlug !== '' ? $desiredSlug : 'wordpress-post-'.($wpPostId ?: $postId);
        $attempt = 0;

        while (true) {
            $suffix = $attempt === 0 ? '' : '-'.($wpPostId ?: $postId).'-'.$attempt;
            $candidate = $this->trimSlugToLength($base, 191 - strlen($suffix)).$suffix;

            $exists = BlogPostTranslation::query()
                ->where('locale', $locale)
                ->where('slug', $candidate)
                ->where('post_id', '!=', $postId)
                ->exists();

            if (! $exists) {
                return $candidate;
            }

            $attempt++;
        }
    }

    private function trimSlugToLength(string $slug, int $maxLength): string
    {
        if (strlen($slug) <= $maxLength) {
            return $slug;
        }

        return rtrim(substr($slug, 0, max(1, $maxLength)), '-');
    }
}
