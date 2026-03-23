<?php

namespace App\Support\Content;

use Illuminate\Support\Str;

class YouTubeUrl
{
    /**
     * @return array{
     *     video_id:string,
     *     start_seconds:int,
     *     watch_url:string,
     *     embed_url:string
     * }|null
     */
    public static function parse(string $rawValue): ?array
    {
        $input = trim(html_entity_decode($rawValue, ENT_QUOTES | ENT_HTML5, 'UTF-8'));

        if ($input === '') {
            return null;
        }

        if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $input) === 1) {
            return self::buildPayload($input, 0);
        }

        if (str_starts_with($input, '//')) {
            $input = 'https:'.$input;
        } elseif (! preg_match('#^[a-z][a-z\d+.-]*://#i', $input)) {
            $input = 'https://'.ltrim($input, '/');
        }

        $parts = parse_url($input);
        if (! is_array($parts)) {
            return null;
        }

        $host = Str::lower((string) preg_replace('/^(www\.|m\.)/i', '', (string) ($parts['host'] ?? '')));
        $pathSegments = array_values(array_filter(
            explode('/', trim((string) ($parts['path'] ?? ''), '/')),
            static fn (string $segment): bool => $segment !== ''
        ));

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
            return null;
        }

        $startSeconds = self::extractStartSeconds(
            (string) ($parts['query'] ?? ''),
            (string) ($parts['fragment'] ?? '')
        );

        return self::buildPayload($videoId, $startSeconds);
    }

    public static function embedUrl(string $rawValue): string
    {
        return self::parse($rawValue)['embed_url'] ?? '';
    }

    public static function watchUrl(string $rawValue): string
    {
        return self::parse($rawValue)['watch_url'] ?? '';
    }

    /**
     * @return array{
     *     video_id:string,
     *     start_seconds:int,
     *     watch_url:string,
     *     embed_url:string
     * }
     */
    private static function buildPayload(string $videoId, int $startSeconds): array
    {
        $watchUrl = 'https://www.youtube.com/watch?v='.$videoId;
        $embedUrl = 'https://www.youtube.com/embed/'.$videoId;

        if ($startSeconds > 0) {
            $watchUrl .= '&t='.$startSeconds.'s';
            $embedUrl .= '?start='.$startSeconds;
        }

        return [
            'video_id' => $videoId,
            'start_seconds' => $startSeconds,
            'watch_url' => $watchUrl,
            'embed_url' => $embedUrl,
        ];
    }

    private static function extractStartSeconds(string $query, string $fragment): int
    {
        $queryParams = [];
        parse_str($query, $queryParams);

        foreach ([$queryParams['start'] ?? null, $queryParams['t'] ?? null] as $candidate) {
            $seconds = self::parseTimestamp((string) $candidate);

            if ($seconds > 0) {
                return $seconds;
            }
        }

        if ($fragment !== '') {
            $seconds = self::parseTimestamp($fragment);

            if ($seconds > 0) {
                return $seconds;
            }

            parse_str(ltrim($fragment, '#'), $fragmentParams);
            foreach ([$fragmentParams['t'] ?? null, $fragmentParams['start'] ?? null] as $candidate) {
                $seconds = self::parseTimestamp((string) $candidate);

                if ($seconds > 0) {
                    return $seconds;
                }
            }
        }

        return 0;
    }

    private static function parseTimestamp(string $value): int
    {
        $value = trim($value);

        if ($value === '') {
            return 0;
        }

        if (preg_match('/^\d+$/', $value) === 1) {
            return (int) $value;
        }

        if (preg_match('/(?:(\d+)h)?(?:(\d+)m)?(?:(\d+)s)?/i', $value, $matches) === 1) {
            $hours = (int) ($matches[1] ?? 0);
            $minutes = (int) ($matches[2] ?? 0);
            $seconds = (int) ($matches[3] ?? 0);
            $total = ($hours * 3600) + ($minutes * 60) + $seconds;

            if ($total > 0) {
                return $total;
            }
        }

        return 0;
    }
}
