<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Front\Concerns\ResolvesFrontendView;
use App\Models\Catalog\Category\Category;
use App\Models\Content\Call\CallPost;
use App\Models\Content\Service\ServicePage;
use App\Support\Content\ServicePageTemplateRegistry;
use App\Support\Localization\FrontendLocalePolicy;
use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CallPostController extends Controller
{
    use ResolvesFrontendView;

    public function show(Request $request, string $slug): View
    {
        $locale = app()->getLocale();
        $fallbackLocale = FrontendLocalePolicy::fallbackLocale(
            (string) $locale,
            (string) config('app.fallback_locale', config('app.locale', 'hr'))
        );
        $requiresExactTranslation = FrontendLocalePolicy::requiresExactTranslation((string) $locale);
        $translationLocales = $requiresExactTranslation
            ? [(string) $locale]
            : array_values(array_unique([$locale, $fallbackLocale]));

        $callPost = CallPost::query()
            ->where('is_active', true)
            ->where(function (Builder $query): void {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->whereHas('translations', function (Builder $query) use ($translationLocales, $slug): void {
                $query
                    ->whereIn('locale', $translationLocales)
                    ->where('slug', $slug);
            })
            ->with([
                'translations' => fn ($query) => $query->whereIn('locale', $translationLocales),
                'categories' => fn ($query) => $query
                    ->where('scope', Category::SCOPE_CALL)
                    ->when(
                        FrontendLocalePolicy::requiresExactTranslation((string) $locale),
                        fn ($categoryQuery) => $categoryQuery->whereHas('translations', fn ($translationQuery) => $translationQuery
                            ->where('scope', Category::SCOPE_CALL)
                            ->where('locale', $locale))
                    )
                    ->with([
                        'translations' => fn ($translationQuery) => $translationQuery
                            ->where('scope', Category::SCOPE_CALL)
                            ->whereIn('locale', array_values(array_unique([$locale, $fallbackLocale]))),
                    ]),
                'media',
            ])
            ->firstOrFail();

        $translation = $callPost->translations->firstWhere('locale', $locale)
            ?? ($requiresExactTranslation ? null : $callPost->translations->firstWhere('locale', $fallbackLocale))
            ?? ($requiresExactTranslation ? null : $callPost->translations->first());
        abort_if($requiresExactTranslation && ! $translation, 404);
        $callPostBodyHtml = $this->removeDuplicateLeadImageFromBody(
            (string) ($translation?->body_html ?? ''),
            (string) ($callPost->getFirstMediaUrl('call_cover') ?: $callPost->getFirstMediaUrl())
        );

        return view($this->frontendView($request, 'calls.show'), [
            'callPost' => $callPost,
            'callPostBodyHtml' => $callPostBodyHtml,
            'locale' => $locale,
            'fallbackLocale' => $fallbackLocale,
            'callDetailUi' => $this->resolveCallDetailUi((string) $locale),
        ]);
    }

    /**
     * @return array{
     *     eu_funds_label:string,
     *     calls_label:string,
     *     empty_body_copy:string,
     *     meeting:array{title:string,contact_title:string,intro:string,button_label:string,status:string}
     * }
     */
    private function resolveCallDetailUi(string $locale): array
    {
        $servicePage = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::EU_FUNDS)
            ->where('is_active', true)
            ->where(function (Builder $query): void {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->whereHas('translations', fn (Builder $query) => $query->where('locale', $locale))
            ->with(['translations' => fn ($query) => $query->where('locale', $locale)])
            ->orderByRaw('case when code = ? then 0 else 1 end', [ServicePageTemplateRegistry::defaultCode(ServicePageTemplateRegistry::EU_FUNDS)])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->first();

        $translation = $servicePage?->translations->firstWhere('locale', $locale);
        $payload = is_array($translation?->payload) ? $translation->payload : [];
        $calls = (array) data_get($payload, 'calls', []);
        $meeting = (array) data_get($payload, 'meeting', []);

        return [
            'eu_funds_label' => trim((string) ($translation?->title ?? '')),
            'calls_label' => trim((string) ($calls['title'] ?? $calls['kicker'] ?? '')),
            'empty_body_copy' => trim((string) ($calls['intro'] ?? '')),
            'meeting' => [
                'title' => trim((string) ($meeting['title'] ?? '')),
                'contact_title' => trim((string) ($meeting['contact_title'] ?? '')),
                'intro' => trim((string) ($meeting['intro'] ?? '')),
                'button_label' => trim((string) ($meeting['button_label'] ?? '')),
                'status' => trim((string) ($meeting['status'] ?? '')),
            ],
        ];
    }

    private function removeDuplicateLeadImageFromBody(string $html, ?string $coverImageUrl): string
    {
        $html = trim($html);
        $coverImageUrl = trim((string) $coverImageUrl);

        if ($html === '' || $coverImageUrl === '') {
            return $html;
        }

        $fragment = $this->loadHtmlFragment($html);
        if ($fragment === null) {
            return $html;
        }

        ['dom' => $dom, 'root' => $root, 'xpath' => $xpath] = $fragment;

        $image = $xpath->query('//*[@id="call-post-body-root"]//img')->item(0);
        if (! $image instanceof DOMElement) {
            return $html;
        }

        $imageUrl = $this->normalizeAssetUrl((string) $image->getAttribute('src'));
        if (! $this->urlsReferToSameAsset($imageUrl, $coverImageUrl)) {
            return $html;
        }

        $node = $this->resolveRemovableImageNode($image);
        $node->parentNode?->removeChild($node);

        return trim($this->extractFragmentHtml($dom, $root));
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
                '<?xml encoding="utf-8" ?><!DOCTYPE html><html><body><div id="call-post-body-root">'.$html.'</div></body></html>',
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
        $root = $xpath->query('//*[@id="call-post-body-root"]')->item(0);

        if (! $root instanceof DOMElement) {
            return null;
        }

        return [
            'dom' => $dom,
            'root' => $root,
            'xpath' => $xpath,
        ];
    }

    private function extractFragmentHtml(DOMDocument $dom, DOMElement $root): string
    {
        $html = '';

        foreach ($root->childNodes as $childNode) {
            $html .= $dom->saveHTML($childNode);
        }

        return $html;
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

    private function urlsReferToSameAsset(string $leftUrl, string $rightUrl): bool
    {
        $left = $this->normalizeAssetUrl($leftUrl);
        $right = $this->normalizeAssetUrl($rightUrl);

        if ($left === '' || $right === '') {
            return false;
        }

        $leftCandidates = [$left, $this->normalizeWordPressImageVariantUrl($left)];
        $rightCandidates = [$right, $this->normalizeWordPressImageVariantUrl($right)];

        return count(array_intersect($leftCandidates, $rightCandidates)) > 0;
    }

    private function normalizeAssetUrl(string $url): string
    {
        $normalized = trim(html_entity_decode($url, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        if ($normalized === '') {
            return '';
        }

        if (str_starts_with($normalized, '//')) {
            $normalized = 'https:'.$normalized;
        }

        return preg_replace('/\s+/u', '', $normalized) ?? $normalized;
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
}
