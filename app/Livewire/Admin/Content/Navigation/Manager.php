<?php

namespace App\Livewire\Admin\Content\Navigation;

use App\Models\Content\Page\InfoPage;
use App\Services\Front\NavigationMenuService;
use App\Services\Settings\SystemSettingsService;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class Manager extends Component
{
    use WithFileUploads;

    /**
     * @var array{
     *     items: array<int, array<string, mixed>>,
     *     chrome: array<string, string>,
     *     chrome_translations: array<string, array<string, string>>
     * }
     */
    public array $form = [
        'items' => [],
        'chrome' => [],
        'chrome_translations' => [],
    ];

    public string $locale = 'en';

    public string $previousLocale = 'en';

    /** @var array<int, TemporaryUploadedFile|null> */
    public array $desktopPromoUploads = [];

    public function mount(): void
    {
        $this->locale = (string) (request()->query('locale') ?: app()->getLocale() ?: config('admin_ui.locale.default', 'hr'));
        $this->previousLocale = $this->locale;

        $navigation = app(NavigationMenuService::class);
        $items = collect($navigation->configuredItems())
            ->reject(fn (array $item): bool => (string) ($item['type'] ?? '') === 'category')
            ->values()
            ->all();

        $this->form['items'] = $items;
        $this->form['chrome_translations'] = $navigation->configuredChromeTranslations();
        $this->syncInputsFromLocaleTranslations($this->locale);
        $this->syncChromeInputsFromLocale($this->locale);
    }

    public function updatedLocale(): void
    {
        $this->syncLocaleTranslationsFromInputs($this->previousLocale);
        $this->syncChromeTranslationsFromInputs($this->previousLocale);
        $this->syncInputsFromLocaleTranslations($this->locale);
        $this->syncChromeInputsFromLocale($this->locale);
        $this->previousLocale = $this->locale;
    }

    public function addPageItem(): void
    {
        $this->form['items'][] = $this->makeDefaultItem('page');
    }

    public function addBlogItem(): void
    {
        $item = $this->makeDefaultItem('blog');
        $item['label'] = 'Blog';
        $item['label_translations'] = [$this->locale => 'Blog'];
        $this->form['items'][] = $item;
    }

    public function addContactItem(): void
    {
        $item = $this->makeDefaultItem('contact');
        $item['label'] = (string) __('admin.content.navigation.defaults.contact');
        $item['label_translations'] = [$this->locale => (string) __('admin.content.navigation.defaults.contact')];
        $this->form['items'][] = $item;
    }

    public function addFaqItem(): void
    {
        $item = $this->makeDefaultItem('faq');
        $item['label'] = 'FAQ';
        $item['label_translations'] = [$this->locale => 'FAQ'];
        $this->form['items'][] = $item;
    }

    public function addCustomItem(): void
    {
        $item = $this->makeDefaultItem('custom');
        $item['label'] = (string) __('admin.content.navigation.defaults.custom_label');
        $item['url'] = '/';
        $item['label_translations'] = [$this->locale => (string) __('admin.content.navigation.defaults.custom_label')];
        $item['url_translations'] = [$this->locale => '/'];
        $this->form['items'][] = $item;
    }

    public function removeItem(int $index): void
    {
        if (! isset($this->form['items'][$index])) {
            return;
        }

        unset($this->form['items'][$index]);
        $this->form['items'] = array_values($this->form['items']);
    }

    public function moveUp(int $index): void
    {
        if ($index <= 0 || ! isset($this->form['items'][$index])) {
            return;
        }

        [$this->form['items'][$index - 1], $this->form['items'][$index]] = [$this->form['items'][$index], $this->form['items'][$index - 1]];
    }

    public function moveDown(int $index): void
    {
        $lastIndex = count($this->form['items']) - 1;

        if ($index < 0 || $index >= $lastIndex || ! isset($this->form['items'][$index])) {
            return;
        }

        [$this->form['items'][$index + 1], $this->form['items'][$index]] = [$this->form['items'][$index], $this->form['items'][$index + 1]];
    }

    public function save(): void
    {
        $this->syncLocaleTranslationsFromInputs($this->locale);
        $this->syncChromeTranslationsFromInputs($this->locale);

        $rules = [
            'form.items' => ['array'],
            'form.items.*.type' => ['required', 'in:page,blog,contact,faq,custom'],
            'form.items.*.label' => ['nullable', 'string', 'max:120'],
            'form.items.*.page_id' => ['nullable', 'integer', 'min:0'],
            'form.items.*.url' => ['nullable', 'string', 'max:2048'],
            'form.items.*.label_translations' => ['nullable', 'array'],
            'form.items.*.label_translations.*' => ['nullable', 'string', 'max:120'],
            'form.items.*.url_translations' => ['nullable', 'array'],
            'form.items.*.url_translations.*' => ['nullable', 'string', 'max:2048'],
            'form.items.*.is_active' => ['required', 'boolean'],
            'form.items.*.show_dropdown' => ['required', 'boolean'],
            'form.items.*.open_in_new_tab' => ['required', 'boolean'],
            'form.items.*.sort_order' => ['required', 'integer', 'min:0', 'max:9999'],
            'form.items.*.desktop_promo_image_path' => ['nullable', 'string', 'max:2048'],
            'form.items.*.desktop_promo_title' => ['nullable', 'string', 'max:120'],
            'form.items.*.desktop_promo_subtitle' => ['nullable', 'string', 'max:255'],
            'form.items.*.desktop_promo_cta_label' => ['nullable', 'string', 'max:80'],
            'form.items.*.desktop_promo_cta_url' => ['nullable', 'string', 'max:2048'],
            'form.chrome' => ['array'],
            'form.chrome_translations' => ['array'],
            'form.chrome_translations.*' => ['array'],
            'desktopPromoUploads.*' => ['nullable', 'image', 'max:4096'],
        ];

        foreach (NavigationMenuService::CHROME_FIELDS as $field => $maxLength) {
            $rules['form.chrome.'.$field] = ['nullable', 'string', 'max:'.$maxLength];
            $rules['form.chrome_translations.*.'.$field] = ['nullable', 'string', 'max:'.$maxLength];
        }

        $validated = $this->validate($rules);

        $normalizedItems = [];
        foreach (($validated['form']['items'] ?? []) as $index => $item) {
            $normalized = $this->normalizeItem($item, $index, $this->desktopPromoUploads[$index] ?? null);
            $normalizedItems[] = $normalized;

            $type = (string) $normalized['type'];
            if ($type === 'page' && (int) $normalized['page_id'] <= 0) {
                $this->addError('form.items.'.$index.'.page_id', (string) __('admin.content.navigation.errors.select_page'));
            }
            if ($type === 'custom') {
                if (trim((string) $normalized['label']) === '') {
                    $this->addError('form.items.'.$index.'.label', (string) __('admin.content.navigation.errors.enter_label'));
                }
                if (trim((string) $normalized['url']) === '') {
                    $this->addError('form.items.'.$index.'.url', (string) __('admin.content.navigation.errors.enter_url'));
                }
            }
        }

        if ($this->getErrorBag()->isNotEmpty()) {
            return;
        }

        app(SystemSettingsService::class)->putMany([
            NavigationMenuService::SETTINGS_KEY => $normalizedItems,
            NavigationMenuService::CHROME_SETTINGS_KEY => $this->normalizeChromeTranslations(
                $validated['form']['chrome_translations'] ?? []
            ),
        ]);
        $this->desktopPromoUploads = [];

        $this->dispatch('notify', type: 'success', message: (string) __('admin.content.navigation.notify_saved'));
    }

    public function clearDesktopPromoImage(int $index): void
    {
        if (! isset($this->form['items'][$index])) {
            return;
        }

        $this->form['items'][$index]['desktop_promo_image_path'] = '';
        unset($this->desktopPromoUploads[$index]);
    }

    public function render()
    {
        $fallbackLocale = (string) config('app.fallback_locale', 'hr');
        $locales = array_values(array_unique([$this->locale, $fallbackLocale]));

        $pageOptions = InfoPage::query()
            ->where('is_active', true)
            ->with([
                'translations' => fn ($q) => $q->whereIn('locale', $locales),
            ])
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get()
            ->map(function (InfoPage $page) use ($fallbackLocale): array {
                $translation = $page->translations->firstWhere('locale', $this->locale)
                    ?? $page->translations->firstWhere('locale', $fallbackLocale)
                    ?? $page->translations->first();

                return [
                    'id' => (int) $page->id,
                    'label' => (string) ($translation?->title ?? $page->code),
                ];
            })
            ->values()
            ->all();

        return view('livewire.admin.content.navigation.manager', [
            'pageOptions' => $pageOptions,
        ]);
    }

    /**
     * @param  array<string, mixed>  $item
     * @return array<string, mixed>
     */
    private function normalizeItem(array $item, int $index, mixed $desktopPromoUpload = null): array
    {
        $type = (string) ($item['type'] ?? 'custom');
        $locale = strtolower(trim($this->locale));
        $fallbackLocale = strtolower((string) config('app.fallback_locale', 'hr'));
        $labelTranslations = $this->normalizeTranslations($item['label_translations'] ?? []);
        $urlTranslations = $this->normalizeTranslations($item['url_translations'] ?? []);

        $label = trim((string) ($item['label'] ?? ''));
        if ($label !== '' && $locale !== '') {
            $labelTranslations[$locale] = $label;
        }
        if ($label === '' && $locale !== '') {
            unset($labelTranslations[$locale]);
        }

        $url = trim((string) ($item['url'] ?? ''));
        if ($url !== '' && $locale !== '') {
            $urlTranslations[$locale] = $url;
        }
        if ($url === '' && $locale !== '') {
            unset($urlTranslations[$locale]);
        }

        $storedLabel = $this->pickTranslationValue($labelTranslations, $fallbackLocale);
        $storedUrl = $this->pickTranslationValue($urlTranslations, $fallbackLocale);

        $desktopPromoImagePath = trim((string) ($item['desktop_promo_image_path'] ?? ''));
        if ($desktopPromoUpload instanceof TemporaryUploadedFile) {
            $desktopPromoImagePath = $desktopPromoUpload->store('navigation/mega-promo', 'public');
        }

        return [
            'type' => $type,
            'label' => $storedLabel,
            'label_translations' => $labelTranslations,
            'page_id' => (int) ($item['page_id'] ?? 0),
            'url' => $storedUrl,
            'url_translations' => $urlTranslations,
            'open_in_new_tab' => (bool) ($item['open_in_new_tab'] ?? false),
            'show_dropdown' => (bool) ($item['show_dropdown'] ?? true),
            'is_active' => (bool) ($item['is_active'] ?? true),
            'sort_order' => (int) ($item['sort_order'] ?? $index),
            'desktop_promo_image_path' => $desktopPromoImagePath,
            'desktop_promo_title' => trim((string) ($item['desktop_promo_title'] ?? '')),
            'desktop_promo_subtitle' => trim((string) ($item['desktop_promo_subtitle'] ?? '')),
            'desktop_promo_cta_label' => trim((string) ($item['desktop_promo_cta_label'] ?? '')),
            'desktop_promo_cta_url' => trim((string) ($item['desktop_promo_cta_url'] ?? '')),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function makeDefaultItem(string $type): array
    {
        return [
            'type' => $type,
            'label' => '',
            'label_translations' => [],
            'page_id' => 0,
            'url' => '',
            'url_translations' => [],
            'open_in_new_tab' => false,
            'show_dropdown' => true,
            'is_active' => true,
            'sort_order' => count($this->form['items']),
            'desktop_promo_image_path' => '',
            'desktop_promo_title' => '',
            'desktop_promo_subtitle' => '',
            'desktop_promo_cta_label' => '',
            'desktop_promo_cta_url' => '',
        ];
    }

    private function syncLocaleTranslationsFromInputs(string $locale): void
    {
        $normalizedLocale = strtolower(trim($locale));
        if ($normalizedLocale === '') {
            return;
        }

        foreach ($this->form['items'] as $index => $item) {
            $labelTranslations = $this->normalizeTranslations($item['label_translations'] ?? []);
            $urlTranslations = $this->normalizeTranslations($item['url_translations'] ?? []);

            $label = trim((string) ($item['label'] ?? ''));
            if ($label !== '') {
                $labelTranslations[$normalizedLocale] = $label;
            } else {
                unset($labelTranslations[$normalizedLocale]);
            }

            $url = trim((string) ($item['url'] ?? ''));
            if ($url !== '') {
                $urlTranslations[$normalizedLocale] = $url;
            } else {
                unset($urlTranslations[$normalizedLocale]);
            }

            $this->form['items'][$index]['label_translations'] = $labelTranslations;
            $this->form['items'][$index]['url_translations'] = $urlTranslations;
        }
    }

    private function syncInputsFromLocaleTranslations(string $locale): void
    {
        $normalizedLocale = strtolower(trim($locale));

        foreach ($this->form['items'] as $index => $item) {
            $labelTranslations = $this->normalizeTranslations($item['label_translations'] ?? []);
            $urlTranslations = $this->normalizeTranslations($item['url_translations'] ?? []);

            $resolvedLabel = trim((string) ($labelTranslations[$normalizedLocale] ?? ''));
            $resolvedUrl = trim((string) ($urlTranslations[$normalizedLocale] ?? ''));

            $this->form['items'][$index]['label'] = $resolvedLabel;
            $this->form['items'][$index]['url'] = $resolvedUrl;
            $this->form['items'][$index]['label_translations'] = $labelTranslations;
            $this->form['items'][$index]['url_translations'] = $urlTranslations;
        }
    }

    private function syncChromeTranslationsFromInputs(string $locale): void
    {
        $normalizedLocale = strtolower(trim($locale));
        if ($normalizedLocale === '') {
            return;
        }

        $translations = $this->normalizeChromeTranslations($this->form['chrome_translations'] ?? []);
        $values = [];

        foreach (array_keys(NavigationMenuService::CHROME_FIELDS) as $field) {
            $value = trim((string) ($this->form['chrome'][$field] ?? ''));
            if ($value !== '') {
                $values[$field] = $value;
            }
        }

        if ($values === []) {
            unset($translations[$normalizedLocale]);
        } else {
            $translations[$normalizedLocale] = $values;
        }

        $this->form['chrome_translations'] = $translations;
    }

    private function syncChromeInputsFromLocale(string $locale): void
    {
        $normalizedLocale = strtolower(trim($locale));
        $translations = $this->normalizeChromeTranslations($this->form['chrome_translations'] ?? []);
        $values = $translations[$normalizedLocale] ?? [];

        foreach (array_keys(NavigationMenuService::CHROME_FIELDS) as $field) {
            $this->form['chrome'][$field] = trim((string) ($values[$field] ?? ''));
        }

        $this->form['chrome_translations'] = $translations;
    }

    /**
     * @return array<string, array<string, string>>
     */
    private function normalizeChromeTranslations(mixed $translations): array
    {
        if (! is_array($translations)) {
            return [];
        }

        $normalized = [];
        foreach ($translations as $locale => $values) {
            $normalizedLocale = strtolower(trim((string) $locale));
            if ($normalizedLocale === '' || ! is_array($values)) {
                continue;
            }

            $normalizedValues = [];
            foreach (array_keys(NavigationMenuService::CHROME_FIELDS) as $field) {
                $value = trim((string) ($values[$field] ?? ''));
                if ($value !== '') {
                    $normalizedValues[$field] = $value;
                }
            }

            if ($normalizedValues !== []) {
                $normalized[$normalizedLocale] = $normalizedValues;
            }
        }

        return $normalized;
    }

    /**
     * @return array<string, string>
     */
    private function normalizeTranslations(mixed $translations): array
    {
        if (! is_array($translations)) {
            return [];
        }

        $normalized = [];
        foreach ($translations as $locale => $value) {
            $key = strtolower(trim((string) $locale));
            if ($key === '') {
                continue;
            }

            $text = trim((string) $value);
            if ($text === '') {
                continue;
            }

            $normalized[$key] = $text;
        }

        return $normalized;
    }

    /**
     * @param  array<string, string>  $translations
     */
    private function pickTranslationValue(array $translations, string ...$preferredLocales): string
    {
        foreach ($preferredLocales as $locale) {
            $key = strtolower(trim($locale));
            if ($key !== '' && isset($translations[$key]) && trim((string) $translations[$key]) !== '') {
                return trim((string) $translations[$key]);
            }
        }

        foreach ($translations as $value) {
            $text = trim((string) $value);
            if ($text !== '') {
                return $text;
            }
        }

        return '';
    }
}
