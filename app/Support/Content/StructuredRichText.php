<?php

namespace App\Support\Content;

class StructuredRichText
{
    /**
     * Keep service-editor formatting while removing executable markup and
     * attributes. The CMS is privileged, but its HTML still reaches a public
     * page and therefore must not rely on client-side editor filtering.
     */
    public static function sanitize(mixed $html): string
    {
        $html = trim((string) $html);
        if ($html === '') {
            return '';
        }

        if (! class_exists(\DOMDocument::class)) {
            return (string) preg_replace(
                '/\s(?:on[a-z]+|style)\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]+)/iu',
                '',
                strip_tags($html, '<p><h2><h3><blockquote><ul><ol><li><strong><em><u><s><a><br>'),
            );
        }

        $document = new \DOMDocument('1.0', 'UTF-8');
        $previousErrors = libxml_use_internal_errors(true);
        $loaded = $document->loadHTML(
            '<?xml encoding="utf-8" ?><div id="structured-rich-text-root">'.$html.'</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD,
        );
        libxml_clear_errors();
        libxml_use_internal_errors($previousErrors);

        if (! $loaded) {
            return '';
        }

        $root = $document->getElementById('structured-rich-text-root');
        if (! $root) {
            return '';
        }

        self::sanitizeNodeChildren($root);

        $sanitized = '';
        foreach ($root->childNodes as $child) {
            $sanitized .= (string) $document->saveHTML($child);
        }

        return trim($sanitized);
    }

    /**
     * @param  array<int|string, mixed>  $paragraphs
     */
    public static function fromParagraphs(array $paragraphs): string
    {
        return collect($paragraphs)
            ->map(static fn ($paragraph): string => trim((string) $paragraph))
            ->filter()
            ->map(static function (string $paragraph): string {
                $escaped = htmlspecialchars($paragraph, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

                return '<p>'.nl2br($escaped, false).'</p>';
            })
            ->implode('');
    }

    /**
     * @return array<int, string>
     */
    public static function blocks(mixed $html): array
    {
        $html = trim((string) $html);
        if ($html === '') {
            return [];
        }

        $blockHtml = [];

        if (class_exists(\DOMDocument::class)) {
            $document = new \DOMDocument('1.0', 'UTF-8');
            $previousErrors = libxml_use_internal_errors(true);
            $loaded = $document->loadHTML(
                '<?xml encoding="utf-8" ?><div id="structured-rich-text-blocks-root">'.$html.'</div>',
                LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD,
            );
            libxml_clear_errors();
            libxml_use_internal_errors($previousErrors);

            $root = $loaded ? $document->getElementById('structured-rich-text-blocks-root') : null;
            if ($root) {
                $blockTags = ['p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'blockquote', 'ul', 'ol'];
                $blockNodes = [];
                $collectBlockNodes = function (\DOMNode $parent) use (&$collectBlockNodes, &$blockNodes, $blockTags): void {
                    foreach (iterator_to_array($parent->childNodes) as $child) {
                        if (! $child instanceof \DOMElement) {
                            continue;
                        }

                        if (in_array(strtolower($child->tagName), $blockTags, true)) {
                            $blockNodes[] = $child;

                            continue;
                        }

                        $collectBlockNodes($child);
                    }
                };
                $collectBlockNodes($root);

                $blockHtml = array_map(
                    static fn (\DOMNode $block): string => (string) $document->saveHTML($block),
                    $blockNodes,
                );
            }
        } else {
            preg_match_all(
                '/<(p|h[1-6]|blockquote|ul|ol)\b[^>]*>.*?<\/\1>/isu',
                $html,
                $matches,
            );
            $blockHtml = (array) ($matches[0] ?? []);
        }

        $blocks = collect($blockHtml)
            ->map(static fn ($block): string => trim((string) $block))
            ->filter(static fn (string $block): bool => trim(strip_tags(str_replace(['<br>', '<br/>', '<br />'], '', $block))) !== '')
            ->values()
            ->all();

        return $blocks !== [] ? $blocks : ['<p>'.$html.'</p>'];
    }

    public static function addClassToFirstBlock(string $html, string $class): string
    {
        $html = trim($html);
        $class = trim($class);

        if ($html === '' || $class === '') {
            return $html;
        }

        return (string) preg_replace_callback(
            '/^<([a-z][a-z0-9]*)(\s[^>]*)?>/iu',
            static function (array $matches) use ($class): string {
                $tag = (string) ($matches[1] ?? 'p');
                $attributes = (string) ($matches[2] ?? '');

                if (preg_match('/\bclass=("|\')(.*?)\1/iu', $attributes, $classMatch)) {
                    $existing = trim((string) ($classMatch[2] ?? ''));
                    $replacement = 'class="'.trim($existing.' '.$class).'"';
                    $attributes = (string) preg_replace('/\bclass=("|\')(.*?)\1/iu', $replacement, $attributes, 1);
                } else {
                    $attributes .= ' class="'.$class.'"';
                }

                return '<'.$tag.$attributes.'>';
            },
            $html,
            1,
        );
    }

    /**
     * @param  array<int|string, mixed>  $items
     */
    public static function lines(array $items): string
    {
        return collect($items)
            ->map(static fn ($item): string => trim((string) $item))
            ->filter()
            ->implode("\n");
    }

    /**
     * @return array<int, string>
     */
    public static function itemsFromLines(mixed $text): array
    {
        return collect(preg_split('/\R/u', (string) $text) ?: [])
            ->map(static fn ($item): string => trim((string) $item))
            ->filter()
            ->values()
            ->all();
    }

    private static function sanitizeNodeChildren(\DOMNode $parent): void
    {
        $allowedTags = [
            'p', 'h2', 'h3', 'blockquote', 'ul', 'ol', 'li',
            'strong', 'em', 'u', 's', 'a', 'br',
        ];
        $removeWithContent = ['script', 'style', 'iframe', 'object', 'embed', 'svg', 'math'];

        foreach (iterator_to_array($parent->childNodes) as $child) {
            if ($child instanceof \DOMComment) {
                $parent->removeChild($child);

                continue;
            }

            if (! $child instanceof \DOMElement) {
                continue;
            }

            $tag = strtolower($child->tagName);
            if (in_array($tag, $removeWithContent, true)) {
                $parent->removeChild($child);

                continue;
            }

            if (! in_array($tag, $allowedTags, true)) {
                self::sanitizeNodeChildren($child);
                while ($child->firstChild) {
                    $parent->insertBefore($child->firstChild, $child);
                }
                $parent->removeChild($child);

                continue;
            }

            self::sanitizeElementAttributes($child, $tag);
            self::sanitizeNodeChildren($child);
        }
    }

    private static function sanitizeElementAttributes(\DOMElement $element, string $tag): void
    {
        foreach (iterator_to_array($element->attributes) as $attribute) {
            $name = strtolower($attribute->name);
            $allowed = $tag === 'a' && in_array($name, ['href', 'target', 'rel'], true);

            if (! $allowed) {
                $element->removeAttributeNode($attribute);
            }
        }

        if ($tag !== 'a') {
            return;
        }

        $href = trim($element->getAttribute('href'));
        if ($href !== '' && ! self::isSafeHref($href)) {
            $element->removeAttribute('href');
        }

        if ($element->getAttribute('target') === '_blank') {
            $element->setAttribute('rel', 'noopener noreferrer');
        } else {
            $element->removeAttribute('target');
            $element->removeAttribute('rel');
        }
    }

    private static function isSafeHref(string $href): bool
    {
        if (str_starts_with($href, '#') || str_starts_with($href, '/') || str_starts_with($href, './') || str_starts_with($href, '../')) {
            return true;
        }

        $scheme = strtolower((string) parse_url($href, PHP_URL_SCHEME));

        return in_array($scheme, ['http', 'https', 'mailto', 'tel'], true);
    }
}
