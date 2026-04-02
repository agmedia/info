<?php

namespace App\Http\Controllers\Front\Concerns;

use App\Support\Content\YouTubeUrl;

trait ResolvesServiceVideos
{
    /**
     * @param  array<string, mixed>  $pagePayload
     * @param  array<string, mixed>  $translationPayload
     * @return array{section: array<string, mixed>, items: array<int, array<string, mixed>>}
     */
    protected function resolveServiceVideoPayload(array $pagePayload, array $translationPayload): array
    {
        return [
            'section' => (array) ($translationPayload['video_section'] ?? []),
            'items' => $this->resolveServiceVideos((array) data_get($pagePayload, 'video_source.items', [])),
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $videos
     * @return array<int, array<string, mixed>>
     */
    protected function resolveServiceVideos(array $videos): array
    {
        return collect($videos)
            ->map(function (array $video): ?array {
                $resolvedVideo = $this->resolveServiceVideo((string) ($video['youtube_url'] ?? $video['video_url'] ?? ''));

                if ($resolvedVideo === null) {
                    return null;
                }

                return [
                    ...$resolvedVideo,
                    'title' => trim((string) ($video['title'] ?? '')),
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    /**
     * @return array<string, string>|null
     */
    protected function resolveServiceVideo(string $url): ?array
    {
        $parsedVideo = YouTubeUrl::parse($url);

        if ($parsedVideo === null) {
            return null;
        }

        $separator = str_contains($parsedVideo['embed_url'], '?') ? '&' : '?';

        return [
            ...$parsedVideo,
            'embed_url' => $parsedVideo['embed_url'].$separator.'rel=0&modestbranding=1&playsinline=1&enablejsapi=1',
            'poster_url' => 'https://i.ytimg.com/vi/'.$parsedVideo['video_id'].'/hqdefault.jpg',
        ];
    }
}
