<?php

namespace App\Services\Front;

use App\Models\Content\Page\InfoPage;
use App\Services\Settings\SystemSettingsService;
use App\Support\Front\FontRegistry;
use App\Support\Front\HeroFontRegistry;
use Illuminate\Support\Facades\Storage;

class StoreSettingsService
{
    public function __construct(
        private readonly SystemSettingsService $settings
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return [
            'branding' => $this->branding(),
            'typography' => $this->typography(),
            'home_hero' => $this->homeHero(),
            'blog' => $this->blog(),
            'footer' => $this->footer(),
            'captcha' => $this->publicCaptcha(),
            'analytics' => $this->analytics(),
            'seo' => $this->seo(),
            'og' => $this->og(),
            'schema' => $this->schema(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function branding(): array
    {
        return [
            'store_name' => (string) $this->settings->get('store_brand_name', config('app.name', 'AG Shop')),
            'logo_url' => $this->assetUrl((string) $this->settings->get('store_brand_logo_path', '')),
            'favicon_url' => $this->assetUrl((string) $this->settings->get('store_brand_favicon_path', '')),
            'favicons' => [
                'ico_url' => $this->assetUrl((string) $this->settings->get('store_brand_favicon_ico_path', '')),
                '16_url' => $this->assetUrl((string) $this->settings->get('store_brand_favicon_16_path', '')),
                '32_url' => $this->assetUrl((string) $this->settings->get('store_brand_favicon_32_path', '')),
                '180_url' => $this->assetUrl((string) $this->settings->get('store_brand_favicon_180_path', '')),
                '192_url' => $this->assetUrl((string) $this->settings->get('store_brand_favicon_192_path', '')),
                '512_url' => $this->assetUrl((string) $this->settings->get('store_brand_favicon_512_path', '')),
            ],
            'social' => [
                'x' => [
                    'url' => trim((string) $this->settings->get('store_social_x_url', '')),
                    'enabled' => (bool) $this->settings->get('store_footer_social_x_enabled', true),
                ],
                'facebook' => [
                    'url' => trim((string) $this->settings->get('store_social_facebook_url', '')),
                    'enabled' => (bool) $this->settings->get('store_footer_social_facebook_enabled', true),
                ],
                'instagram' => [
                    'url' => trim((string) $this->settings->get('store_social_instagram_url', '')),
                    'enabled' => (bool) $this->settings->get('store_footer_social_instagram_enabled', true),
                ],
                'linkedin' => [
                    'url' => trim((string) $this->settings->get('store_social_linkedin_url', '')),
                    'enabled' => (bool) $this->settings->get('store_footer_social_linkedin_enabled', true),
                ],
                'tiktok' => [
                    'url' => trim((string) $this->settings->get('store_social_tiktok_url', '')),
                    'enabled' => (bool) $this->settings->get('store_footer_social_tiktok_enabled', true),
                ],
                'youtube' => [
                    'url' => trim((string) $this->settings->get('store_social_youtube_url', '')),
                    'enabled' => (bool) $this->settings->get('store_footer_social_youtube_enabled', true),
                ],
            ],
        ];
    }

    /**
     * @return array{key: string, family: string, stylesheet_url: string}
     */
    public function typography(): array
    {
        return FontRegistry::resolve(
            $this->settings->get('store_front_google_font', FontRegistry::DEFAULT)
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function homeHero(): array
    {
        $allSettings = $this->settings->all();
        $desktopVideoPath = (string) $this->settings->get('store_home_hero_desktop_video_path', '');
        $mobileVideoPath = (string) $this->settings->get('store_home_hero_mobile_video_path', '');
        $websiteTypography = $this->typography();
        $fontKey = HeroFontRegistry::normalize(
            $this->settings->get('store_home_hero_font', HeroFontRegistry::DEFAULT)
        );

        return [
            'is_configured' => array_key_exists('store_home_hero_title', $allSettings),
            'title' => trim((string) $this->settings->get('store_home_hero_title', 'Vaš kompas kroz svijet financija')),
            'subtitle' => trim((string) $this->settings->get('store_home_hero_subtitle', 'Računovodstvo, revizija i savjetovanje — sve na jednom mjestu.')),
            'primary_label' => trim((string) $this->settings->get('store_home_hero_primary_label', 'Dogovorite sastanak')),
            'primary_url' => trim((string) $this->settings->get('store_home_hero_primary_url', '/contact')),
            'secondary_label' => trim((string) $this->settings->get('store_home_hero_secondary_label', 'Naše usluge')),
            'secondary_url' => trim((string) $this->settings->get('store_home_hero_secondary_url', '/usluge')),
            'desktop_video_path' => $desktopVideoPath,
            'desktop_video_url' => $this->assetUrl($desktopVideoPath),
            'mobile_video_path' => $mobileVideoPath,
            'mobile_video_url' => $this->assetUrl($mobileVideoPath),
            'font_weight' => HeroFontRegistry::normalizeWeight(
                $fontKey,
                $this->settings->get('store_home_hero_font_weight', HeroFontRegistry::DEFAULT_WEIGHT),
                $websiteTypography['key'] ?? null,
            ),
            'typography' => HeroFontRegistry::resolve(
                $fontKey,
                $websiteTypography,
            ),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function blog(): array
    {
        return [
            'hero_eyebrow' => trim((string) $this->settings->get('store_blog_header_eyebrow', __('ui.blog.eyebrow'))),
            'hero_title' => trim((string) $this->settings->get('store_blog_header_title', __('ui.blog.title'))),
            'hero_intro' => trim((string) $this->settings->get('store_blog_header_intro', __('ui.blog.subtitle'))),
            'hero_cta_label' => trim((string) $this->settings->get('store_blog_header_cta_label', __('ui.blog.cta_default'))),
            'hero_cta_url' => trim((string) $this->settings->get('store_blog_header_cta_url', '/contact')),
            'category_preview_limit' => max(1, min(40, (int) $this->settings->get('store_blog_category_preview_limit', 8))),
            'posts_per_page' => max(1, min(48, (int) $this->settings->get('store_blog_posts_per_page', 12))),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function footer(): array
    {
        $locale = app()->getLocale();
        $fallbackLocale = (string) config('app.fallback_locale', 'hr');
        if (app()->bound('request')) {
            $fallbackLocale = (string) request()->attributes->get('front_default_locale', $fallbackLocale);
        }
        $bottomLinks = $this->resolveFooterPageLinks(
            $locale,
            $fallbackLocale,
            $this->normalizeIdList($this->settings->get('store_footer_bottom_link_page_ids', []))
        );

        return [
            'phone' => trim((string) $this->settings->get('store_footer_phone', '')),
            'email_sales' => trim((string) $this->settings->get('store_footer_email_sales', '')),
            'email_support' => trim((string) $this->settings->get('store_footer_email_support', '')),
            'hours' => trim((string) $this->settings->get('store_footer_hours', '')),
            'bottom_links' => $bottomLinks,
            'bottom_copyright_text' => trim((string) $this->settings->get('store_footer_bottom_copyright_text', '')),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function captcha(): array
    {
        return [
            'recaptcha_v3_enabled' => (bool) $this->settings->get('store_captcha_recaptcha_v3_enabled', false),
            'recaptcha_v3_site_key' => trim((string) $this->settings->get('store_captcha_recaptcha_v3_site_key', '')),
            'recaptcha_v3_secret_key' => trim((string) $this->settings->get('store_captcha_recaptcha_v3_secret_key', '')),
            'recaptcha_v3_min_score' => (float) $this->settings->get('store_captcha_recaptcha_v3_min_score', 0.5),
        ];
    }

    /**
     * @return array{recaptcha_v3_enabled: bool, recaptcha_v3_site_key: string}
     */
    public function publicCaptcha(): array
    {
        $captcha = $this->captcha();
        $isConfigured = (bool) $captcha['recaptcha_v3_enabled']
            && $captcha['recaptcha_v3_site_key'] !== ''
            && $captcha['recaptcha_v3_secret_key'] !== '';

        return [
            'recaptcha_v3_enabled' => $isConfigured,
            'recaptcha_v3_site_key' => $captcha['recaptcha_v3_site_key'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function analytics(): array
    {
        return [
            'enabled' => (bool) $this->settings->get('store_analytics_enabled', false),
            'ga4_measurement_id' => trim((string) $this->settings->get('store_analytics_ga4_measurement_id', '')),
            'gtm_enabled' => (bool) $this->settings->get('store_analytics_gtm_enabled', false),
            'gtm_container_id' => trim((string) $this->settings->get('store_analytics_gtm_container_id', '')),
            'google_ads_enabled' => (bool) $this->settings->get('store_analytics_google_ads_enabled', false),
            'google_ads_conversion_id' => trim((string) $this->settings->get('store_analytics_google_ads_conversion_id', '')),
            'meta_pixel_enabled' => (bool) $this->settings->get('store_analytics_meta_pixel_enabled', false),
            'meta_pixel_id' => trim((string) $this->settings->get('store_analytics_meta_pixel_id', '')),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function email(): array
    {
        return [
            'enabled' => (bool) $this->settings->get('store_email_enabled', false),
            'mailer' => (string) $this->settings->get('store_email_mailer', config('mail.default', 'smtp')),
            'host' => (string) $this->settings->get('store_email_smtp_host', ''),
            'port' => (int) $this->settings->get('store_email_smtp_port', 587),
            'username' => (string) $this->settings->get('store_email_smtp_username', ''),
            'password' => (string) $this->settings->get('store_email_smtp_password', ''),
            'encryption' => (string) $this->settings->get('store_email_smtp_encryption', 'tls'),
            'sendmail_path' => (string) $this->settings->get('store_email_sendmail_path', '/usr/sbin/sendmail -bs -i'),
            'from_address' => (string) $this->settings->get('store_email_from_address', (string) config('mail.from.address', '')),
            'from_name' => (string) $this->settings->get('store_email_from_name', (string) config('mail.from.name', '')),
            'reply_to' => (string) $this->settings->get('store_email_reply_to', ''),
            'contact_to' => (string) $this->settings->get('store_email_contact_to', ''),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function seo(): array
    {
        return [
            'default_title' => trim((string) $this->settings->get('store_seo_default_title', '')),
            'default_description' => trim((string) $this->settings->get('store_seo_default_description', '')),
            'robots' => trim((string) $this->settings->get('store_seo_robots', 'index,follow')),
            'canonical_policy' => (string) $this->settings->get('store_seo_canonical_policy', 'self'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function og(): array
    {
        return [
            'default_image_url' => $this->assetUrl((string) $this->settings->get('store_og_default_image_path', '')),
            'home_image_url' => $this->assetUrl((string) $this->settings->get('store_og_home_image_path', '')),
            'category_image_url' => $this->assetUrl((string) $this->settings->get('store_og_category_image_path', '')),
            'page_image_url' => $this->assetUrl((string) $this->settings->get('store_og_page_image_path', '')),
            'blog_image_url' => $this->assetUrl((string) $this->settings->get('store_og_blog_image_path', '')),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function schema(): array
    {
        return [
            'enabled' => (bool) $this->settings->get('store_schema_enabled', true),
            'org_enabled' => (bool) $this->settings->get('store_schema_org_enabled', true),
            'website_enabled' => (bool) $this->settings->get('store_schema_website_enabled', true),
            'breadcrumbs_enabled' => (bool) $this->settings->get('store_schema_breadcrumbs_enabled', true),
            'itemlist_enabled' => (bool) $this->settings->get('store_schema_itemlist_enabled', true),
            'home_enabled' => (bool) $this->settings->get('store_schema_home_enabled', true),
            'blog_enabled' => (bool) $this->settings->get('store_schema_blog_enabled', true),
            'page_enabled' => (bool) $this->settings->get('store_schema_page_enabled', true),
            'faq_enabled' => (bool) $this->settings->get('store_schema_faq_enabled', true),
            'org_type' => (string) $this->settings->get('store_schema_org_type', 'Organization'),
            'business_name' => trim((string) $this->settings->get('store_schema_business_name', '')),
            'business_phone' => trim((string) $this->settings->get('store_schema_business_phone', '')),
            'business_email' => trim((string) $this->settings->get('store_schema_business_email', '')),
            'address_street' => trim((string) $this->settings->get('store_schema_address_street', '')),
            'address_city' => trim((string) $this->settings->get('store_schema_address_city', '')),
            'address_region' => trim((string) $this->settings->get('store_schema_address_region', '')),
            'address_postal_code' => trim((string) $this->settings->get('store_schema_address_postal_code', '')),
            'address_country' => strtoupper(trim((string) $this->settings->get('store_schema_address_country', 'HR'))),
            'same_as' => trim((string) $this->settings->get('store_schema_same_as', '')),
            'blog_author_name' => trim((string) $this->settings->get('store_schema_blog_author_name', '')),
            'blog_author_url' => trim((string) $this->settings->get('store_schema_blog_author_url', '')),
            'faq_group' => trim((string) $this->settings->get('store_schema_faq_group', '')),
            'faq_limit' => (int) $this->settings->get('store_schema_faq_limit', 8),
            'itemlist_limit' => (int) $this->settings->get('store_schema_itemlist_limit', 12),
        ];
    }

    private function assetUrl(string $path): ?string
    {
        $path = trim($path);
        if ($path === '') {
            return null;
        }

        return Storage::disk('public')->url($path);
    }

    /**
     * @param  array<int, int>  $pageIds
     * @return array<int, array{label:string,url:string,type:string}>
     */
    private function resolveFooterPageLinksMap(string $locale, string $fallbackLocale, array $pageIds): array
    {
        if ($pageIds === []) {
            return [];
        }

        return InfoPage::query()
            ->whereIn('id', $pageIds)
            ->where('is_active', true)
            ->where(function ($q): void {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->with('translations')
            ->get()
            ->mapWithKeys(function (InfoPage $page) use ($locale, $fallbackLocale): array {
                $translation = strtolower($locale) !== strtolower($fallbackLocale)
                    ? $page->translations->firstWhere('locale', strtolower($locale))
                    : $this->pickPreferredPageTranslation($page, $locale, $fallbackLocale);
                $slug = trim((string) ($translation?->slug ?? ''));
                if ($slug === '') {
                    return [];
                }

                return [
                    (int) $page->id => [
                        'label' => (string) ($translation?->title ?? $page->code),
                        'url' => route('pages.show', ['slug' => $slug]),
                        'type' => 'page',
                    ],
                ];
            })
            ->all();
    }

    /**
     * @param  array<int, int>  $pageIds
     * @return array<int, array{label:string,url:string,type:string}>
     */
    private function resolveFooterPageLinks(string $locale, string $fallbackLocale, array $pageIds): array
    {
        $map = $this->resolveFooterPageLinksMap($locale, $fallbackLocale, $pageIds);
        $links = [];
        foreach ($pageIds as $pageId) {
            $entry = $map[(int) $pageId] ?? null;
            if (is_array($entry)) {
                $links[] = $entry;
            }
        }

        return $links;
    }

    /**
     * @return array<int, int>
     */
    private function normalizeIdList(mixed $value): array
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $value = is_array($decoded) ? $decoded : [];
        }

        if (! is_array($value)) {
            return [];
        }

        $normalized = [];
        foreach ($value as $id) {
            $intId = (int) $id;
            if ($intId > 0) {
                $normalized[] = $intId;
            }
        }

        return array_values(array_unique($normalized));
    }

    private function pickPreferredPageTranslation(InfoPage $page, string $locale, string $fallbackLocale): mixed
    {
        $translations = $page->translations;
        if (! $translations || $translations->isEmpty()) {
            return null;
        }

        $isPlaceholder = static function ($tr): bool {
            $slug = strtolower(trim((string) ($tr->slug ?? '')));
            $title = strtolower(trim((string) ($tr->title ?? '')));

            return str_starts_with($slug, 'demo-')
                || str_contains($title, 'demo ');
        };

        $preferred = $translations->first(fn ($tr) => (string) ($tr->locale ?? '') === $locale && ! $isPlaceholder($tr));
        if ($preferred) {
            return $preferred;
        }

        $preferred = $translations->first(fn ($tr) => (string) ($tr->locale ?? '') === $fallbackLocale && ! $isPlaceholder($tr));
        if ($preferred) {
            return $preferred;
        }

        $preferred = $translations->first(fn ($tr) => ! $isPlaceholder($tr));
        if ($preferred) {
            return $preferred;
        }

        return $translations->firstWhere('locale', $locale)
            ?? $translations->firstWhere('locale', $fallbackLocale)
            ?? $translations->first();
    }
}
