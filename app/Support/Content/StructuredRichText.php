<?php

namespace App\Support\Content;

class StructuredRichText
{
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

        preg_match_all(
            '/<(p|h[1-6]|blockquote|ul|ol)\b[^>]*>.*?<\/\1>/isu',
            $html,
            $matches,
        );

        $blocks = collect((array) ($matches[0] ?? []))
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
}
