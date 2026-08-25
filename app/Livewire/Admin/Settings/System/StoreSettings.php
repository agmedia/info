<?php

namespace App\Livewire\Admin\Settings\System;

use App\Models\Content\Page\InfoPage;
use App\Services\Newsletter\MailchimpCredentialCodec;
use App\Services\Settings\SystemSettingsService;
use App\Support\Front\FontRegistry;
use App\Support\Front\HeroFontRegistry;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Silber\Bouncer\BouncerFacade as Bouncer;

class StoreSettings extends Component
{
    use WithFileUploads;

    public string $tab = 'email';

    /** @var array<string, mixed> */
    public array $form = [
        'store_email_enabled' => false,
        'store_email_mailer' => 'smtp',
        'store_email_smtp_host' => '',
        'store_email_smtp_port' => 587,
        'store_email_smtp_username' => '',
        'store_email_smtp_password' => '',
        'store_email_smtp_encryption' => 'tls',
        'store_email_sendmail_path' => '/usr/sbin/sendmail -bs -i',
        'store_email_from_address' => '',
        'store_email_from_name' => '',
        'store_email_reply_to' => '',
        'store_email_contact_to' => '',

        'store_brand_name' => '',
        'store_front_google_font' => FontRegistry::DEFAULT,
        'store_home_hero_font' => HeroFontRegistry::DEFAULT,
        'store_home_hero_font_weight' => HeroFontRegistry::DEFAULT_WEIGHT,
        'store_home_hero_title' => 'Vaš kompas kroz svijet financija',
        'store_home_hero_subtitle' => 'Računovodstvo i porezi, revizija i savjetovanje — sve na jednom mjestu.',
        'store_home_hero_primary_label' => 'Dogovorite sastanak',
        'store_home_hero_primary_url' => '/contact',
        'store_home_hero_secondary_label' => 'Naše usluge',
        'store_home_hero_secondary_url' => '/usluge',
        'store_home_hero_desktop_video_path' => '',
        'store_home_hero_mobile_video_path' => '',
        'store_blog_header_eyebrow' => '',
        'store_blog_header_title' => '',
        'store_blog_header_intro' => '',
        'store_blog_header_cta_label' => '',
        'store_blog_header_cta_url' => '',
        'store_blog_category_preview_limit' => 8,
        'store_blog_posts_per_page' => 12,
        'store_footer_phone' => '',
        'store_footer_email_sales' => '',
        'store_footer_email_support' => '',
        'store_footer_hours' => '',
        'store_footer_bottom_link_page_ids' => [],
        'store_footer_bottom_copyright_text' => '',
        'store_social_x_url' => '',
        'store_social_facebook_url' => '',
        'store_social_linkedin_url' => '',
        'store_social_instagram_url' => '',
        'store_social_tiktok_url' => '',
        'store_social_youtube_url' => '',
        'store_footer_social_x_enabled' => true,
        'store_footer_social_facebook_enabled' => true,
        'store_footer_social_linkedin_enabled' => true,
        'store_footer_social_instagram_enabled' => true,
        'store_footer_social_tiktok_enabled' => true,
        'store_footer_social_youtube_enabled' => true,
        'store_brand_logo_path' => '',
        'store_brand_favicon_path' => '',
        'store_brand_favicon_16_path' => '',
        'store_brand_favicon_32_path' => '',
        'store_brand_favicon_180_path' => '',
        'store_brand_favicon_192_path' => '',
        'store_brand_favicon_512_path' => '',
        'store_brand_favicon_ico_path' => '',

        'store_newsletter_provider' => 'none',
        'store_newsletter_mailchimp_server_prefix' => '',
        'store_newsletter_mailchimp_list_id' => '',

        'store_captcha_recaptcha_v3_enabled' => false,
        'store_captcha_recaptcha_v3_site_key' => '',
        'store_captcha_recaptcha_v3_secret_key' => '',
        'store_captcha_recaptcha_v3_min_score' => 0.5,

        'store_analytics_enabled' => false,
        'store_analytics_ga4_measurement_id' => '',
        'store_analytics_gtm_enabled' => false,
        'store_analytics_gtm_container_id' => '',
        'store_analytics_google_ads_enabled' => false,
        'store_analytics_google_ads_conversion_id' => '',
        'store_analytics_meta_pixel_enabled' => false,
        'store_analytics_meta_pixel_id' => '',

        'store_seo_default_title' => '',
        'store_seo_default_description' => '',
        'store_seo_robots' => 'index,follow',
        'store_seo_canonical_policy' => 'self',

        'store_og_default_image_path' => '',
        'store_og_home_image_path' => '',
        'store_og_category_image_path' => '',
        'store_og_page_image_path' => '',
        'store_og_blog_image_path' => '',

        'store_schema_enabled' => true,
        'store_schema_org_enabled' => true,
        'store_schema_website_enabled' => true,
        'store_schema_breadcrumbs_enabled' => true,
        'store_schema_itemlist_enabled' => true,
        'store_schema_home_enabled' => true,
        'store_schema_blog_enabled' => true,
        'store_schema_page_enabled' => true,
        'store_schema_faq_enabled' => true,
        'store_schema_org_type' => 'Organization',
        'store_schema_business_name' => '',
        'store_schema_business_phone' => '',
        'store_schema_business_email' => '',
        'store_schema_address_street' => '',
        'store_schema_address_city' => '',
        'store_schema_address_region' => '',
        'store_schema_address_postal_code' => '',
        'store_schema_address_country' => 'HR',
        'store_schema_same_as' => '',
        'store_schema_blog_author_name' => '',
        'store_schema_blog_author_url' => '',
        'store_schema_faq_group' => '',
        'store_schema_faq_limit' => 8,
        'store_schema_itemlist_limit' => 12,

    ];

    public ?TemporaryUploadedFile $logoUpload = null;

    public ?TemporaryUploadedFile $faviconUpload = null;

    public ?TemporaryUploadedFile $ogDefaultImageUpload = null;

    public ?TemporaryUploadedFile $ogHomeImageUpload = null;

    public ?TemporaryUploadedFile $ogCategoryImageUpload = null;

    public ?TemporaryUploadedFile $ogPageImageUpload = null;

    public ?TemporaryUploadedFile $ogBlogImageUpload = null;

    public ?TemporaryUploadedFile $homeHeroDesktopVideoUpload = null;

    public ?TemporaryUploadedFile $homeHeroMobileVideoUpload = null;

    public string $mailchimpApiKey = '';

    public bool $hasMailchimpApiKey = false;

    public bool $clearMailchimpApiKey = false;

    #[Locked]
    public bool $canManageNewsletter = false;

    public function mount(): void
    {
        $this->authorizeAccess();
        $this->canManageNewsletter = $this->canManageNewsletterSettings();

        $settings = app(SystemSettingsService::class);
        $allSettings = $settings->all();
        foreach ($this->form as $key => $default) {
            $this->form[$key] = $settings->get($key, $default);
        }

        if ($this->canManageNewsletter) {
            $storedMailchimpApiKey = app(MailchimpCredentialCodec::class)->decode(
                (string) $settings->get('store_newsletter_mailchimp_api_key', '')
            );
            $this->hasMailchimpApiKey = $storedMailchimpApiKey !== '';
            $this->form['store_newsletter_provider'] = in_array(
                $this->form['store_newsletter_provider'] ?? null,
                ['none', 'mailchimp'],
                true,
            ) ? $this->form['store_newsletter_provider'] : 'none';
            $this->form['store_newsletter_mailchimp_server_prefix'] = strtolower(trim(
                (string) ($this->form['store_newsletter_mailchimp_server_prefix'] ?? '')
            ));

            if ($this->form['store_newsletter_mailchimp_server_prefix'] === '') {
                $this->form['store_newsletter_mailchimp_server_prefix'] = $this->mailchimpServerPrefixFromApiKey(
                    $storedMailchimpApiKey
                );
            }
        } else {
            $this->form['store_newsletter_provider'] = 'none';
            $this->form['store_newsletter_mailchimp_server_prefix'] = '';
            $this->form['store_newsletter_mailchimp_list_id'] = '';
        }

        $this->form['store_footer_bottom_link_page_ids'] = $this->normalizeIdList($this->form['store_footer_bottom_link_page_ids'] ?? []);
        $this->form['store_front_google_font'] = FontRegistry::normalize($this->form['store_front_google_font'] ?? null);
        $this->form['store_home_hero_font'] = HeroFontRegistry::normalize($this->form['store_home_hero_font'] ?? null);
        $this->form['store_home_hero_font_weight'] = HeroFontRegistry::normalizeWeight(
            $this->form['store_home_hero_font'],
            $this->form['store_home_hero_font_weight'] ?? null,
            $this->form['store_front_google_font'],
        );

        if (! array_key_exists('store_blog_header_eyebrow', $allSettings)) {
            $this->form['store_blog_header_eyebrow'] = (string) __('ui.blog.eyebrow');
        }
        if (! array_key_exists('store_blog_header_title', $allSettings)) {
            $this->form['store_blog_header_title'] = (string) __('ui.blog.title');
        }
        if (! array_key_exists('store_blog_header_intro', $allSettings)) {
            $this->form['store_blog_header_intro'] = (string) __('ui.blog.subtitle');
        }
        if (! array_key_exists('store_blog_header_cta_label', $allSettings)) {
            $this->form['store_blog_header_cta_label'] = (string) __('ui.blog.cta_default');
        }
        if (! array_key_exists('store_blog_header_cta_url', $allSettings)) {
            $this->form['store_blog_header_cta_url'] = '/contact';
        }
    }

    public function save(): void
    {
        $this->authorizeAccess();
        $canManageNewsletter = $this->canManageNewsletterSettings();
        $this->canManageNewsletter = $canManageNewsletter;

        foreach ([
            'store_analytics_ga4_measurement_id',
            'store_analytics_gtm_container_id',
            'store_analytics_google_ads_conversion_id',
        ] as $trackingIdKey) {
            $this->form[$trackingIdKey] = strtoupper(trim(
                (string) ($this->form[$trackingIdKey] ?? '')
            ));
        }
        $this->form['store_analytics_meta_pixel_id'] = trim(
            (string) ($this->form['store_analytics_meta_pixel_id'] ?? '')
        );
        $this->form['store_newsletter_mailchimp_server_prefix'] = strtolower(trim(
            (string) ($this->form['store_newsletter_mailchimp_server_prefix'] ?? '')
        ));
        $this->form['store_newsletter_mailchimp_list_id'] = trim(
            (string) ($this->form['store_newsletter_mailchimp_list_id'] ?? '')
        );
        $this->mailchimpApiKey = trim($this->mailchimpApiKey);

        $validated = $this->validate($this->rules());
        $payload = $validated['form'];
        $payload['store_schema_address_country'] = strtoupper((string) ($payload['store_schema_address_country'] ?? 'HR'));
        $payload['store_footer_bottom_link_page_ids'] = $this->normalizeIdList($payload['store_footer_bottom_link_page_ids'] ?? []);

        $settings = app(SystemSettingsService::class);
        if ($canManageNewsletter) {
            $mailchimpCredentialCodec = app(MailchimpCredentialCodec::class);
            $storedMailchimpApiKeyValue = trim((string) $settings->get('store_newsletter_mailchimp_api_key', ''));
            $storedMailchimpApiKey = $mailchimpCredentialCodec->decode($storedMailchimpApiKeyValue);
            $mailchimpApiKey = $this->mailchimpApiKey;

            if (($payload['store_newsletter_provider'] ?? 'none') === 'mailchimp') {
                if ($this->clearMailchimpApiKey || ($mailchimpApiKey === '' && $storedMailchimpApiKey === '')) {
                    $this->addError('mailchimpApiKey', __('admin.settings.store.newsletter.api_key_required'));

                    return;
                }

                $effectiveApiKey = $mailchimpApiKey !== '' ? $mailchimpApiKey : $storedMailchimpApiKey;
                if ($this->mailchimpServerPrefixFromApiKey($effectiveApiKey) !== $payload['store_newsletter_mailchimp_server_prefix']) {
                    $this->addError('mailchimpApiKey', __('admin.settings.store.newsletter.api_key_prefix_mismatch'));

                    return;
                }
            }

            if ($this->clearMailchimpApiKey) {
                $payload['store_newsletter_mailchimp_api_key'] = '';
            } elseif ($mailchimpApiKey !== '') {
                $payload['store_newsletter_mailchimp_api_key'] = $mailchimpCredentialCodec->encode($mailchimpApiKey);
            } elseif ($storedMailchimpApiKey !== '' && ! $mailchimpCredentialCodec->isEncrypted($storedMailchimpApiKeyValue)) {
                $payload['store_newsletter_mailchimp_api_key'] = $mailchimpCredentialCodec->encode($storedMailchimpApiKey);
            }
        } else {
            unset(
                $payload['store_newsletter_provider'],
                $payload['store_newsletter_mailchimp_server_prefix'],
                $payload['store_newsletter_mailchimp_list_id'],
            );
        }

        if ($this->logoUpload) {
            $payload['store_brand_logo_path'] = $this->logoUpload->store('store-settings', 'public');
        }
        if ($this->faviconUpload) {
            $payload = array_merge($payload, $this->processFaviconUpload($this->faviconUpload));
        }
        if ($this->ogDefaultImageUpload) {
            $payload['store_og_default_image_path'] = $this->ogDefaultImageUpload->store('store-settings', 'public');
        }
        if ($this->ogHomeImageUpload) {
            $payload['store_og_home_image_path'] = $this->ogHomeImageUpload->store('store-settings', 'public');
        }
        if ($this->ogCategoryImageUpload) {
            $payload['store_og_category_image_path'] = $this->ogCategoryImageUpload->store('store-settings', 'public');
        }
        if ($this->ogPageImageUpload) {
            $payload['store_og_page_image_path'] = $this->ogPageImageUpload->store('store-settings', 'public');
        }
        if ($this->ogBlogImageUpload) {
            $payload['store_og_blog_image_path'] = $this->ogBlogImageUpload->store('store-settings', 'public');
        }
        if ($this->homeHeroDesktopVideoUpload) {
            $payload['store_home_hero_desktop_video_path'] = $this->homeHeroDesktopVideoUpload->store('store-settings/home-hero', 'public');
        }
        if ($this->homeHeroMobileVideoUpload) {
            $payload['store_home_hero_mobile_video_path'] = $this->homeHeroMobileVideoUpload->store('store-settings/home-hero', 'public');
        }

        $settings->putMany(array_merge($payload, $this->legacyStoreCleanupPayload()));
        $this->form = array_merge($this->form, $payload);

        if (array_key_exists('store_newsletter_mailchimp_api_key', $payload)) {
            $this->hasMailchimpApiKey = $payload['store_newsletter_mailchimp_api_key'] !== '';
        }
        unset($this->form['store_newsletter_mailchimp_api_key']);
        $this->mailchimpApiKey = '';
        $this->clearMailchimpApiKey = false;

        $this->logoUpload = null;
        $this->faviconUpload = null;
        $this->ogDefaultImageUpload = null;
        $this->ogHomeImageUpload = null;
        $this->ogCategoryImageUpload = null;
        $this->ogPageImageUpload = null;
        $this->ogBlogImageUpload = null;
        $this->homeHeroDesktopVideoUpload = null;
        $this->homeHeroMobileVideoUpload = null;

        $this->dispatch('notify', type: 'success', message: __('Settings saved.'));
    }

    public function updatedFormStoreHomeHeroFont(mixed $value): void
    {
        if (! is_string($value) || ! in_array($value, HeroFontRegistry::keys(), true)) {
            return;
        }

        $this->form['store_home_hero_font_weight'] = HeroFontRegistry::normalizeWeight(
            $value,
            $this->form['store_home_hero_font_weight'] ?? null,
            $this->form['store_front_google_font'] ?? null,
        );
    }

    public function updatedFormStoreFrontGoogleFont(mixed $value): void
    {
        if (! is_string($value) || ! in_array($value, FontRegistry::keys(), true)) {
            return;
        }

        if (($this->form['store_home_hero_font'] ?? null) === 'website') {
            $this->form['store_home_hero_font_weight'] = HeroFontRegistry::normalizeWeight(
                'website',
                $this->form['store_home_hero_font_weight'] ?? null,
                $this->form['store_front_google_font'],
            );
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(): array
    {
        return [
            'form.store_email_enabled' => ['required', 'boolean'],
            'form.store_email_mailer' => ['required', 'string', 'in:smtp,sendmail,log'],
            'form.store_email_smtp_host' => ['nullable', 'string', 'max:191'],
            'form.store_email_smtp_port' => ['required', 'integer', 'min:1', 'max:65535'],
            'form.store_email_smtp_username' => ['nullable', 'string', 'max:191'],
            'form.store_email_smtp_password' => ['nullable', 'string', 'max:191'],
            'form.store_email_smtp_encryption' => ['nullable', 'string', 'in:,tls,ssl'],
            'form.store_email_sendmail_path' => ['nullable', 'string', 'max:255'],
            'form.store_email_from_address' => ['nullable', 'email', 'max:191'],
            'form.store_email_from_name' => ['nullable', 'string', 'max:191'],
            'form.store_email_reply_to' => ['nullable', 'email', 'max:191'],
            'form.store_email_contact_to' => ['nullable', 'email', 'max:191'],

            'form.store_brand_name' => ['nullable', 'string', 'max:191'],
            'form.store_front_google_font' => ['required', 'string', Rule::in(FontRegistry::keys())],
            'form.store_home_hero_font' => ['required', 'string', Rule::in(HeroFontRegistry::keys())],
            'form.store_home_hero_font_weight' => [
                'required',
                'integer',
                Rule::in(HeroFontRegistry::weights(
                    $this->form['store_home_hero_font'] ?? null,
                    $this->form['store_front_google_font'] ?? null,
                )),
            ],
            'form.store_home_hero_title' => ['required', 'string', 'max:191'],
            'form.store_home_hero_subtitle' => ['nullable', 'string', 'max:500'],
            'form.store_home_hero_primary_label' => ['nullable', 'string', 'max:120'],
            'form.store_home_hero_primary_url' => ['nullable', 'string', 'max:2048'],
            'form.store_home_hero_secondary_label' => ['nullable', 'string', 'max:120'],
            'form.store_home_hero_secondary_url' => ['nullable', 'string', 'max:2048'],
            'form.store_home_hero_desktop_video_path' => ['nullable', 'string', 'max:2048'],
            'form.store_home_hero_mobile_video_path' => ['nullable', 'string', 'max:2048'],
            'form.store_blog_header_eyebrow' => ['nullable', 'string', 'max:120'],
            'form.store_blog_header_title' => ['nullable', 'string', 'max:191'],
            'form.store_blog_header_intro' => ['nullable', 'string', 'max:500'],
            'form.store_blog_header_cta_label' => ['nullable', 'string', 'max:120'],
            'form.store_blog_header_cta_url' => ['nullable', 'string', 'max:2048'],
            'form.store_blog_category_preview_limit' => ['required', 'integer', 'min:1', 'max:40'],
            'form.store_blog_posts_per_page' => ['required', 'integer', 'min:1', 'max:48'],
            'form.store_footer_phone' => ['nullable', 'string', 'max:120'],
            'form.store_footer_email_sales' => ['nullable', 'email', 'max:191'],
            'form.store_footer_email_support' => ['nullable', 'email', 'max:191'],
            'form.store_footer_hours' => ['nullable', 'string', 'max:255'],
            'form.store_footer_bottom_link_page_ids' => ['nullable', 'array'],
            'form.store_footer_bottom_link_page_ids.*' => ['integer', 'exists:content_info_pages,id'],
            'form.store_footer_bottom_copyright_text' => ['nullable', 'string', 'max:255'],
            'form.store_social_x_url' => ['nullable', 'url', 'max:2048'],
            'form.store_social_facebook_url' => ['nullable', 'url', 'max:2048'],
            'form.store_social_linkedin_url' => ['nullable', 'url', 'max:2048'],
            'form.store_social_instagram_url' => ['nullable', 'url', 'max:2048'],
            'form.store_social_tiktok_url' => ['nullable', 'url', 'max:2048'],
            'form.store_social_youtube_url' => ['nullable', 'url', 'max:2048'],
            'form.store_footer_social_x_enabled' => ['required', 'boolean'],
            'form.store_footer_social_facebook_enabled' => ['required', 'boolean'],
            'form.store_footer_social_linkedin_enabled' => ['required', 'boolean'],
            'form.store_footer_social_instagram_enabled' => ['required', 'boolean'],
            'form.store_footer_social_tiktok_enabled' => ['required', 'boolean'],
            'form.store_footer_social_youtube_enabled' => ['required', 'boolean'],

            'form.store_newsletter_provider' => ['required', 'string', 'in:none,mailchimp'],
            'form.store_newsletter_mailchimp_server_prefix' => [
                Rule::requiredIf(fn (): bool => ($this->form['store_newsletter_provider'] ?? 'none') === 'mailchimp'),
                'nullable',
                'string',
                'max:16',
                'regex:/^us\d{1,3}$/',
            ],
            'form.store_newsletter_mailchimp_list_id' => [
                Rule::requiredIf(fn (): bool => ($this->form['store_newsletter_provider'] ?? 'none') === 'mailchimp'),
                'nullable',
                'string',
                'max:64',
                'regex:/^[A-Za-z0-9_-]+$/',
            ],
            'mailchimpApiKey' => ['nullable', 'string', 'max:255', 'regex:/^[A-Za-z0-9]{16,128}-us\d{1,3}$/i'],
            'clearMailchimpApiKey' => ['required', 'boolean'],

            'form.store_captcha_recaptcha_v3_enabled' => ['required', 'boolean'],
            'form.store_captcha_recaptcha_v3_site_key' => ['nullable', 'string', 'max:255'],
            'form.store_captcha_recaptcha_v3_secret_key' => ['nullable', 'string', 'max:255'],
            'form.store_captcha_recaptcha_v3_min_score' => ['required', 'numeric', 'min:0', 'max:1'],

            'form.store_analytics_enabled' => ['required', 'boolean'],
            'form.store_analytics_ga4_measurement_id' => [
                Rule::requiredIf(fn (): bool => (bool) ($this->form['store_analytics_enabled'] ?? false)),
                'nullable',
                'string',
                'max:32',
                'regex:/^G-[A-Z0-9]+$/',
            ],
            'form.store_analytics_gtm_enabled' => ['required', 'boolean'],
            'form.store_analytics_gtm_container_id' => [
                Rule::requiredIf(fn (): bool => (bool) ($this->form['store_analytics_gtm_enabled'] ?? false)),
                'nullable',
                'string',
                'max:32',
                'regex:/^GTM-[A-Z0-9]+$/',
            ],
            'form.store_analytics_google_ads_enabled' => ['required', 'boolean'],
            'form.store_analytics_google_ads_conversion_id' => [
                Rule::requiredIf(fn (): bool => (bool) ($this->form['store_analytics_google_ads_enabled'] ?? false)),
                'nullable',
                'string',
                'max:32',
                'regex:/^AW-[0-9]+$/',
            ],
            'form.store_analytics_meta_pixel_enabled' => ['required', 'boolean'],
            'form.store_analytics_meta_pixel_id' => [
                Rule::requiredIf(fn (): bool => (bool) ($this->form['store_analytics_meta_pixel_enabled'] ?? false)),
                'nullable',
                'string',
                'max:20',
                'regex:/^[0-9]{5,20}$/',
            ],

            'form.store_seo_default_title' => ['nullable', 'string', 'max:191'],
            'form.store_seo_default_description' => ['nullable', 'string', 'max:320'],
            'form.store_seo_robots' => ['nullable', 'string', 'max:120'],
            'form.store_seo_canonical_policy' => ['required', 'string', 'in:self,none'],

            'form.store_schema_enabled' => ['required', 'boolean'],
            'form.store_schema_org_enabled' => ['required', 'boolean'],
            'form.store_schema_website_enabled' => ['required', 'boolean'],
            'form.store_schema_breadcrumbs_enabled' => ['required', 'boolean'],
            'form.store_schema_itemlist_enabled' => ['required', 'boolean'],
            'form.store_schema_home_enabled' => ['required', 'boolean'],
            'form.store_schema_blog_enabled' => ['required', 'boolean'],
            'form.store_schema_page_enabled' => ['required', 'boolean'],
            'form.store_schema_faq_enabled' => ['required', 'boolean'],
            'form.store_schema_org_type' => ['required', 'string', 'in:Organization,LocalBusiness,Store'],
            'form.store_schema_business_name' => ['nullable', 'string', 'max:191'],
            'form.store_schema_business_phone' => ['nullable', 'string', 'max:120'],
            'form.store_schema_business_email' => ['nullable', 'email', 'max:191'],
            'form.store_schema_address_street' => ['nullable', 'string', 'max:191'],
            'form.store_schema_address_city' => ['nullable', 'string', 'max:120'],
            'form.store_schema_address_region' => ['nullable', 'string', 'max:120'],
            'form.store_schema_address_postal_code' => ['nullable', 'string', 'max:32'],
            'form.store_schema_address_country' => ['nullable', 'string', 'max:2'],
            'form.store_schema_same_as' => ['nullable', 'string', 'max:5000'],
            'form.store_schema_blog_author_name' => ['nullable', 'string', 'max:191'],
            'form.store_schema_blog_author_url' => ['nullable', 'url', 'max:2048'],
            'form.store_schema_faq_group' => ['nullable', 'string', 'max:120'],
            'form.store_schema_faq_limit' => ['required', 'integer', 'min:1', 'max:20'],
            'form.store_schema_itemlist_limit' => ['required', 'integer', 'min:1', 'max:48'],

            'logoUpload' => ['nullable', 'file', 'max:4096', 'mimes:jpg,jpeg,png,webp,avif,svg'],
            'faviconUpload' => ['nullable', 'image', 'max:2048'],
            'ogDefaultImageUpload' => ['nullable', 'image', 'max:4096'],
            'ogHomeImageUpload' => ['nullable', 'image', 'max:4096'],
            'ogCategoryImageUpload' => ['nullable', 'image', 'max:4096'],
            'ogPageImageUpload' => ['nullable', 'image', 'max:4096'],
            'ogBlogImageUpload' => ['nullable', 'image', 'max:4096'],
            'homeHeroDesktopVideoUpload' => ['nullable', 'file', 'max:12288', 'mimes:mp4,webm'],
            'homeHeroMobileVideoUpload' => ['nullable', 'file', 'max:12288', 'mimes:mp4,webm'],
        ];
    }

    /** @return array<string, string> */
    protected function messages(): array
    {
        return [
            'form.store_analytics_ga4_measurement_id.required' => __('Unesite GA4 Measurement ID ako je praćenje uključeno.'),
            'form.store_analytics_ga4_measurement_id.regex' => __('Measurement ID mora biti oblika G-XXXXXXXXXX. GTM- oznaka pripada Google Tag Manageru i ne radi u ovom polju.'),
            'form.store_analytics_gtm_container_id.required' => __('Unesite Google Tag Manager Container ID ako je GTM uključen.'),
            'form.store_analytics_gtm_container_id.regex' => __('Container ID mora biti oblika GTM-XXXXXXX.'),
            'form.store_analytics_google_ads_conversion_id.required' => __('Unesite Google Ads Conversion ID ako je praćenje uključeno.'),
            'form.store_analytics_google_ads_conversion_id.regex' => __('Google Ads Conversion ID mora biti oblika AW-123456789.'),
            'form.store_analytics_meta_pixel_id.required' => __('Unesite Meta Pixel ID ako je praćenje uključeno.'),
            'form.store_analytics_meta_pixel_id.regex' => __('Meta Pixel ID mora sadržavati samo 5 do 20 znamenki.'),
            'form.store_newsletter_mailchimp_server_prefix.required' => __('admin.settings.store.newsletter.server_prefix_required'),
            'form.store_newsletter_mailchimp_server_prefix.regex' => __('admin.settings.store.newsletter.server_prefix_invalid'),
            'form.store_newsletter_mailchimp_list_id.required' => __('admin.settings.store.newsletter.list_id_required'),
            'form.store_newsletter_mailchimp_list_id.regex' => __('admin.settings.store.newsletter.list_id_invalid'),
            'mailchimpApiKey.regex' => __('admin.settings.store.newsletter.api_key_invalid'),
        ];
    }

    public function render()
    {
        $locale = (string) app()->getLocale();
        $fallbackLocale = (string) config('app.locale');

        $pageOptions = InfoPage::query()
            ->where('is_active', true)
            ->with(['translations' => fn ($q) => $q->whereIn('locale', [$locale, $fallbackLocale])])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(function (InfoPage $page) use ($locale, $fallbackLocale): array {
                $translation = $page->translations->firstWhere('locale', $locale)
                    ?? $page->translations->firstWhere('locale', $fallbackLocale)
                    ?? $page->translations->first();

                return [
                    'id' => (int) $page->id,
                    'label' => (string) ($translation?->title ?? $page->code),
                ];
            })
            ->values()
            ->all();

        return view('livewire.admin.settings.system.store-settings', [
            'pageOptions' => $pageOptions,
            'fontOptions' => FontRegistry::options(),
            'heroFontOptions' => HeroFontRegistry::options(),
            'heroFontWeightOptions' => HeroFontRegistry::weightOptions(
                $this->form['store_home_hero_font'] ?? null,
                $this->form['store_front_google_font'] ?? null,
            ),
            'homeHeroDesktopVideoUrl' => $this->storedPublicUrl((string) ($this->form['store_home_hero_desktop_video_path'] ?? '')),
            'homeHeroMobileVideoUrl' => $this->storedPublicUrl((string) ($this->form['store_home_hero_mobile_video_path'] ?? '')),
        ]);
    }

    private function storedPublicUrl(string $path): ?string
    {
        $path = trim($path);

        return $path !== '' ? Storage::disk('public')->url($path) : null;
    }

    private function mailchimpServerPrefixFromApiKey(string $apiKey): string
    {
        if (preg_match('/-(us\d{1,3})$/i', trim($apiKey), $matches) !== 1) {
            return '';
        }

        return strtolower($matches[1]);
    }

    private function canManageNewsletterSettings(): bool
    {
        $user = auth()->user();

        return (bool) ($user && (
            Bouncer::is($user)->an('superadmin')
            || $user->can('settings.system.newsletter.manage')
        ));
    }

    /**
     * @return array<string, mixed>
     */
    private function legacyStoreCleanupPayload(): array
    {
        return [
            'store_email_orders_to' => '',
            'store_footer_col_1_category_ids' => [],
            'store_footer_col_2_category_ids' => [],
            'store_footer_col_3_category_ids' => [],
            'store_analytics_purchase_event_enabled' => false,
            'store_analytics_purchase_event_name' => 'purchase',
            'store_pricing_prices_include_tax' => false,
            'store_og_product_image_path' => '',
            'store_schema_category_enabled' => false,
            'store_schema_product_enabled' => false,
            'store_schema_product_currency' => 'EUR',
        ];
    }

    private function authorizeAccess(): void
    {
        $user = auth()->user();
        abort_unless(
            $user && (Bouncer::is($user)->an('superadmin') || $user->can('settings.system.store.manage')),
            403
        );
    }

    /**
     * @return array<string, string>
     */
    private function processFaviconUpload(TemporaryUploadedFile $upload): array
    {
        $extension = strtolower((string) $upload->getClientOriginalExtension());
        $storedOriginalPath = $upload->store('store-settings/favicon', 'public');
        $payload = ['store_brand_favicon_path' => $storedOriginalPath];

        $source = $this->createImageResourceFromUpload($upload);
        if (! $source) {
            return $payload;
        }

        $targets = [
            16 => 'store_brand_favicon_16_path',
            32 => 'store_brand_favicon_32_path',
            180 => 'store_brand_favicon_180_path',
            192 => 'store_brand_favicon_192_path',
            512 => 'store_brand_favicon_512_path',
        ];

        foreach ($targets as $size => $settingKey) {
            $pngPath = 'store-settings/favicon/'.uniqid('favicon-'.$size.'-', true).'.png';
            $pngBinary = $this->renderSquarePng($source, $size);
            if ($pngBinary === null) {
                continue;
            }

            Storage::disk('public')->put($pngPath, $pngBinary);
            $payload[$settingKey] = $pngPath;
        }

        $icoPath = 'store-settings/favicon/'.uniqid('favicon-ico-', true).'.ico';
        $icoBinary = $this->renderIcoFromImage($source, 32);
        if ($icoBinary !== null) {
            Storage::disk('public')->put($icoPath, $icoBinary);
            $payload['store_brand_favicon_ico_path'] = $icoPath;
        }

        imagedestroy($source);

        if (! in_array($extension, ['png', 'jpg', 'jpeg', 'webp', 'gif', 'avif'], true)) {
            $payload['store_brand_favicon_path'] = $payload['store_brand_favicon_32_path'] ?? $storedOriginalPath;
        }

        return $payload;
    }

    private function createImageResourceFromUpload(TemporaryUploadedFile $upload): mixed
    {
        $realPath = $upload->getRealPath();
        if (! is_string($realPath) || $realPath === '') {
            return null;
        }

        $mime = strtolower((string) $upload->getMimeType());

        return match ($mime) {
            'image/png' => @imagecreatefrompng($realPath) ?: null,
            'image/jpeg', 'image/jpg' => @imagecreatefromjpeg($realPath) ?: null,
            'image/gif' => @imagecreatefromgif($realPath) ?: null,
            'image/webp' => function_exists('imagecreatefromwebp') ? (@imagecreatefromwebp($realPath) ?: null) : null,
            'image/avif' => function_exists('imagecreatefromavif') ? (@imagecreatefromavif($realPath) ?: null) : null,
            default => null,
        };
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

    private function renderSquarePng(mixed $source, int $size): ?string
    {
        $target = imagecreatetruecolor($size, $size);
        if (! $target) {
            return null;
        }

        imagealphablending($target, false);
        imagesavealpha($target, true);
        $transparent = imagecolorallocatealpha($target, 0, 0, 0, 127);
        imagefill($target, 0, 0, $transparent);

        $srcW = (int) imagesx($source);
        $srcH = (int) imagesy($source);
        $srcMin = max(1, min($srcW, $srcH));
        $srcX = (int) floor(($srcW - $srcMin) / 2);
        $srcY = (int) floor(($srcH - $srcMin) / 2);

        imagecopyresampled($target, $source, 0, 0, $srcX, $srcY, $size, $size, $srcMin, $srcMin);

        ob_start();
        imagepng($target, null, 9);
        $binary = ob_get_clean();
        imagedestroy($target);

        return is_string($binary) ? $binary : null;
    }

    private function renderIcoFromImage(mixed $source, int $size): ?string
    {
        $pngBinary = $this->renderSquarePng($source, $size);
        if ($pngBinary === null) {
            return null;
        }

        $widthByte = $size >= 256 ? 0 : $size;
        $heightByte = $size >= 256 ? 0 : $size;
        $dataSize = strlen($pngBinary);
        $offset = 6 + 16;

        $header = pack('vvv', 0, 1, 1);
        $directory = pack(
            'CCCCvvVV',
            $widthByte,
            $heightByte,
            0,
            0,
            1,
            32,
            $dataSize,
            $offset
        );

        return $header.$directory.$pngBinary;
    }
}
