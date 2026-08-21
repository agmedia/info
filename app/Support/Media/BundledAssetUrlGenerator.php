<?php

namespace App\Support\Media;

use DateTimeInterface;
use Spatie\MediaLibrary\Support\UrlGenerator\DefaultUrlGenerator;

class BundledAssetUrlGenerator extends DefaultUrlGenerator
{
    public function getUrl(): string
    {
        $path = $this->bundledAssetPath();

        if ($path !== null && $this->conversion === null) {
            return asset($path);
        }

        return parent::getUrl();
    }

    public function getPath(): string
    {
        $path = $this->bundledAssetPath();

        if ($path !== null && $this->conversion === null) {
            return public_path($path);
        }

        return parent::getPath();
    }

    public function getTemporaryUrl(DateTimeInterface $expiration, array $options = []): string
    {
        if ($this->bundledAssetPath() !== null && $this->conversion === null) {
            return $this->getUrl();
        }

        return parent::getTemporaryUrl($expiration, $options);
    }

    private function bundledAssetPath(): ?string
    {
        $path = trim((string) $this->media?->getCustomProperty('bundled_asset_path'));

        if ($path === '' || str_contains($path, '..')) {
            return null;
        }

        return ltrim($path, '/');
    }
}
