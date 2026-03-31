<?php

namespace App\Services\Content;

use App\Models\Content\Page\InfoPage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class ReferenceLogoImportService
{
    /**
     * @return array{page_code:string, source_url:string, parsed_count:int, imported_count:int, skipped_count:int}
     */
    public function import(string $pageCode, string $sourceUrl): array
    {
        $page = InfoPage::query()->where('code', $pageCode)->first();

        if (! $page) {
            throw new RuntimeException(sprintf('Info page with code "%s" was not found.', $pageCode));
        }

        $items = $this->fetchItems($sourceUrl);
        $existingKeys = $page->getMedia('reference_logos')
            ->mapWithKeys(function ($media): array {
                $key = trim((string) data_get($media->custom_properties, 'reference_import_key'));

                return $key !== '' ? [$key => true] : [];
            })
            ->all();

        $importedCount = 0;
        $skippedCount = 0;

        foreach ($items as $item) {
            $key = (string) ($item['key'] ?? '');

            if ($key === '' || isset($existingKeys[$key])) {
                $skippedCount++;
                continue;
            }

            if ($this->attachRemoteImage($page, $item)) {
                $existingKeys[$key] = true;
                $importedCount++;

                continue;
            }

            $skippedCount++;
        }

        return [
            'page_code' => $pageCode,
            'source_url' => $sourceUrl,
            'parsed_count' => count($items),
            'imported_count' => $importedCount,
            'skipped_count' => $skippedCount,
        ];
    }

    /**
     * @return array<int, array{key:string,name:string,src:string}>
     */
    private function fetchItems(string $sourceUrl): array
    {
        try {
            $response = Http::timeout(60)
                ->retry(2, 300)
                ->withHeaders([
                    'User-Agent' => 'AlphaCapitalis-ReferenceImport/1.0',
                    'Accept' => 'text/html,application/xhtml+xml',
                ])
                ->get($sourceUrl);
        } catch (Throwable $exception) {
            throw new RuntimeException('Unable to fetch the legacy references page.', 0, $exception);
        }

        if (! $response->successful()) {
            throw new RuntimeException(sprintf(
                'Legacy references page request failed with status %d.',
                $response->status()
            ));
        }

        $html = (string) $response->body();
        $start = strpos($html, '>Reference</h2>');

        if ($start === false) {
            throw new RuntimeException('The Reference section marker was not found on the legacy page.');
        }

        $html = substr($html, $start);
        $end = strpos($html, 'Alpha Capitalis © Sva prava pridržana');

        if ($end !== false) {
            $html = substr($html, 0, $end);
        }

        preg_match_all(
            '~<div class="has_ae_slider elementor-column.*?</div>\s*</div>\s*</div>\s*</div>~si',
            $html,
            $columnMatches
        );

        $items = [];

        foreach ((array) ($columnMatches[0] ?? []) as $columnHtml) {
            if (! preg_match('~<img[^>]*src="([^"]+)"~i', $columnHtml, $imageMatch)) {
                continue;
            }

            if (! preg_match('~<(h6|p)[^>]*class="elementor-heading-title[^"]*"[^>]*>(.*?)</\1>~si', $columnHtml, $headingMatch)) {
                continue;
            }

            $name = $this->cleanName((string) ($headingMatch[2] ?? ''));
            $src = html_entity_decode((string) ($imageMatch[1] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');

            if ($name === '' || $src === '') {
                continue;
            }

            $key = md5(mb_strtolower($name, 'UTF-8').'|'.$src);

            if (! isset($items[$key])) {
                $items[$key] = [
                    'key' => $key,
                    'name' => $name,
                    'src' => $src,
                ];
            }
        }

        if ($items === []) {
            throw new RuntimeException('No reference logos were parsed from the legacy page.');
        }

        return array_values($items);
    }

    /**
     * @param  array{key:string,name:string,src:string}  $item
     */
    private function attachRemoteImage(InfoPage $page, array $item): bool
    {
        try {
            $response = Http::timeout(60)
                ->retry(2, 300)
                ->withHeaders([
                    'User-Agent' => 'AlphaCapitalis-ReferenceImport/1.0',
                    'Accept' => 'image/*,*/*;q=0.8',
                ])
                ->get($item['src']);
        } catch (Throwable) {
            return false;
        }

        if (! $response->successful()) {
            return false;
        }

        $contentType = Str::lower(trim((string) $response->header('Content-Type', '')));
        if ($contentType !== '' && ! str_starts_with($contentType, 'image/')) {
            return false;
        }

        $extension = $this->resolveExtension((string) $item['src'], $contentType);
        $tempPath = tempnam(sys_get_temp_dir(), 'reference-logo-');

        if ($tempPath === false) {
            return false;
        }

        $finalTempPath = $tempPath.'.'.$extension;
        @rename($tempPath, $finalTempPath);
        file_put_contents($finalTempPath, $response->body());

        try {
            $page->addMedia($finalTempPath)
                ->usingName(Str::limit($item['name'], 200, ''))
                ->usingFileName($this->resolveFileName((string) $item['src'], $item['key'], $extension))
                ->withCustomProperties([
                    'import_source' => 'legacy_reference_page',
                    'source_url' => $item['src'],
                    'reference_import_key' => $item['key'],
                    'alt' => [
                        'hr' => $item['name'],
                        'en' => $item['name'],
                    ],
                    'caption' => [
                        'hr' => $item['name'],
                        'en' => $item['name'],
                    ],
                ])
                ->toMediaCollection('reference_logos');

            return true;
        } catch (Throwable) {
            return false;
        } finally {
            if (is_file($finalTempPath)) {
                @unlink($finalTempPath);
            }
        }
    }

    private function cleanName(string $value): string
    {
        $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $value = str_replace(['<br>', '<br/>', '<br />'], ' ', $value);
        $value = strip_tags($value);
        $value = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $value) ?? $value;
        $value = preg_replace('/\s+/u', ' ', $value) ?? $value;

        return trim($value);
    }

    private function resolveExtension(string $remoteUrl, string $contentType = ''): string
    {
        $path = (string) parse_url($remoteUrl, PHP_URL_PATH);
        $extension = strtolower((string) pathinfo($path, PATHINFO_EXTENSION));

        if ($extension !== '') {
            return $extension;
        }

        return match ($contentType) {
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/avif' => 'avif',
            'image/svg+xml' => 'svg',
            'image/gif' => 'gif',
            default => 'jpg',
        };
    }

    private function resolveFileName(string $remoteUrl, string $key, string $extension): string
    {
        $path = (string) parse_url($remoteUrl, PHP_URL_PATH);
        $base = Str::slug((string) pathinfo($path, PATHINFO_FILENAME));

        if ($base === '') {
            $base = 'reference-logo';
        }

        return $base.'-'.substr($key, 0, 10).'.'.$extension;
    }
}
