<?php

namespace App\Services\Front;

use App\Models\Content\Page\InfoPage;
use App\Services\Settings\SystemSettingsService;
use Illuminate\Support\Facades\Storage;

class StoreSettingsService
{
    public function __construct(
        private readonly SystemSettingsService $settings
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return [
            'announcement' => $this->announcement(),
            'branding' => $this->branding(),
            'blog' => $this->blog(),
            'footer' => $this->footer(),
            'official_entities' => $this->officialEntities(),
            'newsletter' => $this->newsletter(),
            'captcha' => $this->captcha(),
            'analytics' => $this->analytics(),
            'email' => $this->email(),
            'seo' => $this->seo(),
            'og' => $this->og(),
            'schema' => $this->schema(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function announcement(): array
    {
        return [
            'enabled' => (bool) $this->settings->get('store_announcement_enabled', true),
            'text' => (string) $this->settings->get('store_announcement_text', __('ui.front.desktop.promo_bar')),
            'url' => trim((string) $this->settings->get('store_announcement_url', '')),
            'new_tab' => (bool) $this->settings->get('store_announcement_new_tab', false),
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
                'facebook' => [
                    'url' => trim((string) $this->settings->get('store_social_facebook_url', '')),
                    'enabled' => (bool) $this->settings->get('store_footer_social_facebook_enabled', true),
                ],
                'instagram' => [
                    'url' => trim((string) $this->settings->get('store_social_instagram_url', '')),
                    'enabled' => (bool) $this->settings->get('store_footer_social_instagram_enabled', true),
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
        $fallbackLocale = (string) config('app.locale');
        $linkColumns = $this->resolveFooterLinkColumns($locale, $fallbackLocale);
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
            'link_columns' => $linkColumns,
            'bottom_links' => $bottomLinks,
            'bottom_copyright_text' => trim((string) $this->settings->get('store_footer_bottom_copyright_text', '')),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function officialEntities(): array
    {
        return [
            [
                'key' => 'alpha-capitalis',
                'name' => 'ALPHA CAPITALIS D.O.O.',
                'company' => (string) __('contact.offices.zagreb.company'),
                'office_label' => (string) __('contact.offices.zagreb.label'),
                'map_label' => (string) __('contact.offices.zagreb.map_label'),
                'address' => ['Ul. Roberta Frangeša Mihanovića 9,', '10110 Zagreb / Sky Office, 19.kat'],
                'contact_address' => ['Ulica R. F. Mihanovića 9', '10110 Zagreb, Sky Office / XIX. kat'],
                'oib' => '40742241290',
                'mbs' => '080810593',
                'iban' => 'HR4323600001102358715',
                'phone' => '+385 (1) 580 6656',
                'email' => 'info@alphacapitalis.com',
                'show_on_contact' => true,
                'map_query' => 'Ulica R. F. Mihanovića 9, 10110 Zagreb, Sky Office',
                'map_embed_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2781.7413420479693!2d15.907458076538921!3d45.796409471081354!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x4765d6ffe74dee09%3A0xf32defacc421b2a3!2sALPHA%20CAPITALIS%20LTD.!5e0!3m2!1shr!2shr!4v1772955873924!5m2!1shr!2shr',
            ],
            [
                'key' => 'alpha-audit',
                'name' => 'ALPHA AUDIT D.O.O.',
                'company' => 'Alpha Audit d.o.o.',
                'office_label' => '',
                'map_label' => '',
                'address' => ['Ul. Roberta Frangeša Mihanovića 9,', '10110 Zagreb / Sky Office, 19.kat'],
                'contact_address' => ['Ul. Roberta Frangeša Mihanovića 9', '10110 Zagreb / Sky Office, 19.kat'],
                'oib' => '14404485248',
                'mbs' => '080893769',
                'iban' => 'HR9823600001102399338',
                'phone' => '+385 (1) 580 6656',
                'email' => 'info@alphacapitalis.com',
                'show_on_contact' => false,
                'map_query' => '',
                'map_embed_url' => null,
            ],
            [
                'key' => 'alpha-capitalis-concept',
                'name' => 'ALPHA CAPITALIS CONCEPT D.O.O.',
                'company' => 'ALPHA CAPITALIS CONCEPT d.o.o.',
                'office_label' => '',
                'map_label' => '',
                'address' => ['Jezerska 8,', '10430 Samobor'],
                'contact_address' => ['Jezerska 8', '10430 Samobor'],
                'oib' => '16796363725',
                'mbs' => '081084856',
                'iban' => 'HR3023600001102595920',
                'phone' => '+385 (1) 580 6656',
                'email' => 'info@alphacapitalis.com',
                'show_on_contact' => false,
                'map_query' => '',
                'map_embed_url' => null,
            ],
            [
                'key' => 'alpha-capitalis-east',
                'name' => 'ALPHA CAPITALIS EAST D.O.O.',
                'company' => (string) __('contact.offices.vinkovci.company'),
                'office_label' => (string) __('contact.offices.vinkovci.label'),
                'map_label' => (string) __('contact.offices.vinkovci.map_label'),
                'address' => ['Duga ulica 67', '32100 Vinkovci'],
                'contact_address' => ['Duga ulica 67', '32100 Vinkovci'],
                'oib' => '43202073675',
                'mbs' => '081451393',
                'iban' => 'HR7223600001103012943',
                'phone' => '+385 (1) 580 6656',
                'email' => 'info@alphacapitalis.com',
                'show_on_contact' => true,
                'map_query' => 'Duga ulica 67, 32100 Vinkovci',
                'map_embed_url' => null,
            ],
            [
                'key' => 'alpha-capitalis-timia',
                'name' => 'ALPHA CAPITALIS TIMIA D.O.O.',
                'company' => (string) __('contact.offices.rijeka.company'),
                'office_label' => (string) __('contact.offices.rijeka.label'),
                'map_label' => (string) __('contact.offices.rijeka.map_label'),
                'address' => ['Korzo 30,', '51 000 Rijeka'],
                'contact_address' => ['Korzo 30', '51 000 Rijeka'],
                'oib' => '',
                'mbs' => '040282129',
                'iban' => 'HR6524020061500165841',
                'phone' => '+385 (0) 51 301 503',
                'email' => 'info@alphacapitalis.com',
                'show_on_contact' => true,
                'map_query' => 'Korzo 30, 51000 Rijeka',
                'map_embed_url' => null,
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function newsletter(): array
    {
        return [
            'provider' => (string) $this->settings->get('store_newsletter_provider', 'none'),
            'mailchimp_api_key' => (string) $this->settings->get('store_newsletter_mailchimp_api_key', ''),
            'mailchimp_list_id' => (string) $this->settings->get('store_newsletter_mailchimp_list_id', ''),
            'klaviyo_api_key' => (string) $this->settings->get('store_newsletter_klaviyo_api_key', ''),
            'klaviyo_list_id' => (string) $this->settings->get('store_newsletter_klaviyo_list_id', ''),
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
     * @return array<string, mixed>
     */
    public function analytics(): array
    {
        return [
            'enabled' => (bool) $this->settings->get('store_analytics_enabled', false),
            'ga4_measurement_id' => trim((string) $this->settings->get('store_analytics_ga4_measurement_id', '')),
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
            'category_enabled' => (bool) $this->settings->get('store_schema_category_enabled', false),
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
     * @return array<int, array{title:string, links:array<int, array{label:string,url:string,type:string}>}>
     */
    private function resolveFooterLinkColumns(string $locale, string $fallbackLocale): array
    {
        $pageIds = [];
        foreach ([1, 2, 3] as $col) {
            $pageIds = array_merge($pageIds, $this->normalizeIdList($this->settings->get('store_footer_col_'.$col.'_page_ids', [])));
        }
        $pageIds = array_values(array_unique($pageIds));

        $pageMap = $this->resolveFooterPageLinksMap($locale, $fallbackLocale, $pageIds);

        $defaults = [
            1 => (string) __('ui.front.desktop.footer.info'),
            2 => (string) __('ui.front.desktop.footer.help'),
            3 => (string) __('ui.front.desktop.footer.support'),
        ];

        $result = [];
        foreach ([1, 2, 3] as $col) {
            $title = trim((string) $this->settings->get('store_footer_col_'.$col.'_title', $defaults[$col]));
            if ($title === '') {
                $title = $defaults[$col];
            }

            $links = [];
            foreach ($this->normalizeIdList($this->settings->get('store_footer_col_'.$col.'_page_ids', [])) as $pageId) {
                $entry = $pageMap[(int) $pageId] ?? null;
                if (is_array($entry)) {
                    $links[] = $entry;
                }
            }
            $links = array_merge($links, $this->parseCustomFooterLinks((string) $this->settings->get('store_footer_col_'.$col.'_custom_links', '')));

            $result[] = [
                'title' => $title,
                'links' => $links,
            ];
        }

        return $result;
    }

    /**
     * @param array<int, int> $pageIds
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
                $translation = $this->pickPreferredPageTranslation($page, $locale, $fallbackLocale);
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
     * @param array<int, int> $pageIds
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
     * @param mixed $value
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

    /**
     * @return array<int, array{label:string,url:string,type:string}>
     */
    private function parseCustomFooterLinks(string $raw): array
    {
        $result = [];
        $lines = preg_split('/\r\n|\r|\n/', trim($raw)) ?: [];
        foreach ($lines as $line) {
            $line = trim((string) $line);
            if ($line === '' || ! str_contains($line, '|')) {
                continue;
            }

            [$label, $url] = array_pad(explode('|', $line, 2), 2, '');
            $label = trim($label);
            $url = trim($url);
            if ($label === '' || $url === '') {
                continue;
            }

            if (
                ! str_starts_with($url, '/')
                && ! str_starts_with($url, '#')
                && ! str_starts_with($url, 'http://')
                && ! str_starts_with($url, 'https://')
                && ! str_starts_with($url, 'mailto:')
                && ! str_starts_with($url, 'tel:')
            ) {
                $url = '/'.$url;
            }

            $result[] = [
                'label' => $label,
                'url' => $url,
                'type' => 'custom',
            ];
        }

        return $result;
    }
}
