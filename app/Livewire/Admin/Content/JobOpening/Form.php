<?php

namespace App\Livewire\Admin\Content\JobOpening;

use App\Models\Content\Career\JobOpening;
use App\Models\Content\Career\JobOpeningTranslation;
use App\Models\Settings\Local\Language;
use App\Support\Admin\AdminLocale;
use App\Support\Content\StructuredRichText;
use Carbon\CarbonImmutable;
use Closure;
use DateTimeZone;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Form extends Component
{
    private const TAB_OPTIONS = ['content', 'seo'];

    public ?int $jobOpeningId = null;

    public string $activeTab = 'content';

    public bool $metaTitleIsAutomatic = true;

    public bool $metaDescriptionIsAutomatic = true;

    public array $form = [
        'code' => '',
        'is_active' => true,
        'published_at' => '',
        'sort_order' => 0,
        'locale' => 'hr',
        'title' => '',
        'slug' => '',
        'locations' => '',
        'excerpt' => '',
        'body_html' => '',
        'meta_title' => '',
        'meta_description' => '',
    ];

    public function mount(?int $jobOpeningId = null): void
    {
        $requestedLocale = AdminLocale::normalize((string) (
            request()->query('locale')
            ?: app()->getLocale()
            ?: AdminLocale::default()
        ));
        $localeOptions = $this->activeContentLocaleOptions();

        $this->form['locale'] = in_array($requestedLocale, $localeOptions, true)
            ? $requestedLocale
            : ($localeOptions[0] ?? AdminLocale::default());

        if ($jobOpeningId) {
            $this->jobOpeningId = $jobOpeningId;
            $this->loadJobOpening();
        }
    }

    public function updatedFormLocale(): void
    {
        $this->loadTranslationForLocale();
    }

    public function updatedFormTitle(mixed $value): void
    {
        $title = trim((string) $value);
        if ($title === '') {
            if ($this->metaTitleIsAutomatic) {
                $this->form['meta_title'] = '';
            }

            return;
        }

        if (! $this->jobOpeningId) {
            $slug = Str::slug($title);
            if ($slug !== '') {
                $this->form['slug'] = $slug;
                $this->form['code'] = $this->uniqueCodeFromBase($slug);
            }
        }

        if ($this->metaTitleIsAutomatic || trim((string) ($this->form['meta_title'] ?? '')) === '') {
            $this->form['meta_title'] = Str::limit($title, 255, '');
            $this->metaTitleIsAutomatic = true;
        }
    }

    public function updatedFormExcerpt(): void
    {
        $this->fillMetaDescriptionWhenEmpty();
    }

    public function updatedFormBodyHtml(): void
    {
        $this->fillMetaDescriptionWhenEmpty();
    }

    public function updatedFormMetaTitle(mixed $value): void
    {
        $this->metaTitleIsAutomatic = trim((string) $value) === '';

        if ($this->metaTitleIsAutomatic) {
            $this->form['meta_title'] = Str::limit(trim((string) ($this->form['title'] ?? '')), 255, '');
        }
    }

    public function updatedFormMetaDescription(mixed $value): void
    {
        $this->metaDescriptionIsAutomatic = trim((string) $value) === '';

        if ($this->metaDescriptionIsAutomatic) {
            $this->fillMetaDescriptionWhenEmpty();
        }
    }

    public function generateSlug(): void
    {
        $slug = Str::slug((string) ($this->form['title'] ?? ''));
        if ($slug !== '') {
            $this->form['slug'] = $slug;
        }
    }

    public function setTab(string $tab): void
    {
        if (in_array($tab, self::TAB_OPTIONS, true)) {
            $this->activeTab = $tab;
        }
    }

    public function save()
    {
        if (trim((string) ($this->form['slug'] ?? '')) === '') {
            $this->form['slug'] = Str::slug((string) ($this->form['title'] ?? ''));
        }

        if (! $this->jobOpeningId && trim((string) ($this->form['code'] ?? '')) === '') {
            $this->form['code'] = $this->uniqueCodeFromBase((string) ($this->form['slug'] ?? ''));
        }

        $this->form['body_html'] = StructuredRichText::sanitize($this->form['body_html'] ?? '');
        if ($this->metaTitleIsAutomatic) {
            $this->form['meta_title'] = Str::limit(trim((string) ($this->form['title'] ?? '')), 255, '');
        }
        if ($this->metaDescriptionIsAutomatic) {
            $this->form['meta_description'] = $this->metaDescriptionFromContent(
                (string) ($this->form['excerpt'] ?? ''),
                (string) $this->form['body_html'],
            );
        }

        $validated = $this->validate($this->rules());
        $wasEditing = (bool) $this->jobOpeningId;
        $userId = auth()->id();

        DB::transaction(function () use ($validated, $wasEditing, $userId): void {
            $isActive = (bool) $validated['form']['is_active'];
            $openingData = [
                'code' => trim((string) $validated['form']['code']),
                'is_active' => $isActive,
                'published_at' => $this->publishedAtForStorage(
                    $validated['form']['published_at'] ?? null,
                    $isActive,
                ),
                'sort_order' => (int) $validated['form']['sort_order'],
                'updated_by' => $userId,
            ];

            if ($this->jobOpeningId) {
                $opening = JobOpening::query()->findOrFail($this->jobOpeningId);
                $opening->fill($openingData)->save();
            } else {
                $opening = JobOpening::query()->create($openingData + ['created_by' => $userId]);
                $this->jobOpeningId = (int) $opening->id;
            }

            $opening->translations()->updateOrCreate(
                ['locale' => $validated['form']['locale']],
                [
                    'title' => trim((string) $validated['form']['title']),
                    'slug' => trim((string) $validated['form']['slug']),
                    'locations' => trim((string) $validated['form']['locations']),
                    'excerpt' => trim((string) ($validated['form']['excerpt'] ?? '')) ?: null,
                    'body_html' => (string) $validated['form']['body_html'],
                    'meta_title' => $this->resolvedMetaTitle(
                        (string) $validated['form']['title'],
                        $validated['form']['meta_title'] ?? null,
                    ),
                    'meta_description' => $this->resolvedMetaDescription(
                        (string) ($validated['form']['excerpt'] ?? ''),
                        (string) $validated['form']['body_html'],
                        $validated['form']['meta_description'] ?? null,
                    ),
                ],
            );

            activity('content_job_openings')
                ->performedOn($opening)
                ->causedBy(auth()->user())
                ->event($wasEditing ? 'updated' : 'created')
                ->withProperties([
                    'locale' => $validated['form']['locale'],
                    'slug' => $validated['form']['slug'],
                ])
                ->log('Job opening saved');
        });

        return redirect()
            ->route('admin.content.job-openings.index', ['locale' => $this->form['locale']])
            ->with('notify', [
                'type' => 'success',
                'message' => $wasEditing
                    ? __('admin.content.job_openings.form.notify_updated')
                    : __('admin.content.job_openings.form.notify_created'),
            ]);
    }

    public function backToList()
    {
        return redirect()->route('admin.content.job-openings.index', [
            'locale' => $this->form['locale'],
        ]);
    }

    public function render()
    {
        return view('livewire.admin.content.job-opening.form', [
            'isEdit' => (bool) $this->jobOpeningId,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(): array
    {
        return [
            'form.code' => [
                'required',
                'string',
                'max:120',
                Rule::unique('content_job_openings', 'code')->ignore($this->jobOpeningId),
            ],
            'form.is_active' => ['boolean'],
            'form.published_at' => [
                'bail',
                'nullable',
                'date_format:Y-m-d\TH:i',
                function (string $attribute, mixed $value, Closure $fail): void {
                    $publishedAt = trim((string) $value);
                    if ($publishedAt !== '' && ! $this->publicationDateTimeIsUnambiguous($publishedAt)) {
                        $fail(__('admin.content.job_openings.form.publication_invalid'));
                    }
                },
            ],
            'form.sort_order' => ['nullable', 'integer', 'min:0'],
            'form.locale' => [
                'required',
                'string',
                'max:12',
                Rule::in($this->activeContentLocaleOptions()),
            ],
            'form.title' => ['required', 'string', 'max:255'],
            'form.slug' => [
                'required',
                'string',
                'max:191',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('content_job_opening_translations', 'slug')
                    ->where(fn ($query) => $query->where('locale', $this->form['locale']))
                    ->ignore($this->jobOpeningId, 'job_opening_id'),
            ],
            'form.locations' => ['required', 'string', 'max:500'],
            'form.excerpt' => ['nullable', 'string'],
            'form.body_html' => ['required', 'string'],
            'form.meta_title' => ['nullable', 'string', 'max:255'],
            'form.meta_description' => ['nullable', 'string', 'max:500'],
        ];
    }

    private function loadJobOpening(): void
    {
        if (! $this->jobOpeningId) {
            return;
        }

        $locale = (string) $this->form['locale'];
        $opening = JobOpening::query()
            ->with(['translations' => fn ($query) => $query->where('locale', $locale)])
            ->findOrFail($this->jobOpeningId);

        $this->form['code'] = (string) $opening->code;
        $this->form['is_active'] = (bool) $opening->is_active;
        $this->form['published_at'] = $opening->published_at
            ?->copy()
            ->setTimezone($this->publicationTimezone())
            ->format('Y-m-d\TH:i') ?? '';
        $this->form['sort_order'] = (int) $opening->sort_order;

        $translation = $opening->translations->first();
        if (! $translation) {
            $this->clearTranslationFields();

            return;
        }

        $this->fillTranslationFields($translation);
    }

    private function loadTranslationForLocale(): void
    {
        if (! $this->jobOpeningId) {
            $this->clearTranslationFields();

            return;
        }

        $translation = JobOpeningTranslation::query()
            ->where('job_opening_id', $this->jobOpeningId)
            ->where('locale', $this->form['locale'])
            ->first();

        if (! $translation) {
            $this->clearTranslationFields();

            return;
        }

        $this->fillTranslationFields($translation);
    }

    private function fillTranslationFields(JobOpeningTranslation $translation): void
    {
        $this->form['title'] = (string) $translation->title;
        $this->form['slug'] = (string) $translation->slug;
        $this->form['locations'] = (string) $translation->locations;
        $this->form['excerpt'] = (string) ($translation->excerpt ?? '');
        $this->form['body_html'] = (string) $translation->body_html;
        $this->form['meta_title'] = (string) ($translation->meta_title ?? '');
        $this->form['meta_description'] = (string) ($translation->meta_description ?? '');
        $this->metaTitleIsAutomatic = trim($this->form['meta_title']) === ''
            || trim($this->form['meta_title']) === Str::limit(trim($this->form['title']), 255, '');
        $this->metaDescriptionIsAutomatic = trim($this->form['meta_description']) === ''
            || trim($this->form['meta_description']) === $this->metaDescriptionFromContent(
                $this->form['excerpt'],
                $this->form['body_html'],
            );
    }

    private function clearTranslationFields(): void
    {
        foreach (['title', 'slug', 'locations', 'excerpt', 'body_html', 'meta_title', 'meta_description'] as $field) {
            $this->form[$field] = '';
        }

        $this->metaTitleIsAutomatic = true;
        $this->metaDescriptionIsAutomatic = true;
    }

    private function uniqueCodeFromBase(string $base): string
    {
        $slug = Str::slug($base);
        $cleanBase = trim(Str::limit($slug !== '' ? $slug : 'job-opening', 110, ''), '-');
        $code = $cleanBase;
        $suffix = 2;

        while (JobOpening::query()->where('code', $code)->exists()) {
            $code = $cleanBase.'-'.$suffix;
            $suffix++;
        }

        return $code;
    }

    private function publishedAtForStorage(?string $value, bool $isActive): ?CarbonImmutable
    {
        $publishedAt = trim((string) $value);
        if ($publishedAt === '') {
            return $isActive ? CarbonImmutable::now('UTC')->startOfMinute() : null;
        }

        return CarbonImmutable::createFromFormat(
            '!Y-m-d\TH:i',
            $publishedAt,
            $this->publicationTimezone(),
        )->utc();
    }

    private function publicationTimezone(): string
    {
        return (string) config('admin_ui.timezone', 'Europe/Zagreb');
    }

    private function publicationDateTimeIsUnambiguous(string $value): bool
    {
        $wallClock = CarbonImmutable::createFromFormat('!Y-m-d\TH:i', $value, 'UTC');
        $timezone = new DateTimeZone($this->publicationTimezone());
        $wallTimestamp = $wallClock->getTimestamp();
        $transitions = $timezone->getTransitions($wallTimestamp - 172800, $wallTimestamp + 172800);

        if (! is_array($transitions)) {
            return false;
        }

        $matchingOffsets = collect($transitions)
            ->pluck('offset')
            ->unique()
            ->filter(function (mixed $offset) use ($timezone, $value, $wallTimestamp): bool {
                return CarbonImmutable::createFromTimestampUTC($wallTimestamp - (int) $offset)
                    ->setTimezone($timezone)
                    ->format('Y-m-d\TH:i') === $value;
            });

        return $matchingOffsets->count() === 1;
    }

    private function fillMetaDescriptionWhenEmpty(): void
    {
        if (! $this->metaDescriptionIsAutomatic && trim((string) ($this->form['meta_description'] ?? '')) !== '') {
            return;
        }

        $this->form['meta_description'] = $this->metaDescriptionFromContent(
            (string) ($this->form['excerpt'] ?? ''),
            (string) ($this->form['body_html'] ?? ''),
        );
        $this->metaDescriptionIsAutomatic = true;
    }

    private function metaDescriptionFromContent(string $excerpt, string $bodyHtml): string
    {
        $excerptDescription = $this->metaDescriptionFromText($excerpt);

        return $excerptDescription !== ''
            ? $excerptDescription
            : $this->metaDescriptionFromText($bodyHtml);
    }

    private function metaDescriptionFromText(string $value): string
    {
        $plain = preg_replace('/\s+/u', ' ', trim(strip_tags($value)));
        $plain = html_entity_decode((string) $plain, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return Str::limit(trim($plain), 160, '...');
    }

    private function resolvedMetaTitle(string $title, mixed $metaTitle): ?string
    {
        $value = trim((string) $metaTitle);
        if ($value !== '') {
            return Str::limit($value, 255, '');
        }

        $fallback = trim($title);

        return $fallback !== '' ? Str::limit($fallback, 255, '') : null;
    }

    private function resolvedMetaDescription(string $excerpt, string $bodyHtml, mixed $metaDescription): ?string
    {
        $value = trim((string) $metaDescription);
        if ($value !== '') {
            return $value;
        }

        $fallback = $this->metaDescriptionFromContent($excerpt, $bodyHtml);

        return $fallback !== '' ? $fallback : null;
    }

    /**
     * @return array<int, string>
     */
    private function activeContentLocaleOptions(): array
    {
        $fallbackOptions = AdminLocale::fallbackOptions();

        try {
            $activeOptions = Language::query()
                ->where('is_active', true)
                ->orderByDesc('is_default')
                ->orderBy('sort_order')
                ->orderBy('code')
                ->pluck('code')
                ->map(fn ($code): string => AdminLocale::normalize((string) $code))
                ->filter()
                ->unique()
                ->values()
                ->all();
        } catch (\Throwable) {
            $activeOptions = [];
        }

        if ($activeOptions === []) {
            return $fallbackOptions;
        }

        $preferred = array_values(array_filter(
            $fallbackOptions,
            static fn (string $locale): bool => in_array($locale, $activeOptions, true),
        ));
        $remaining = array_values(array_filter(
            $activeOptions,
            static fn (string $locale): bool => ! in_array($locale, $preferred, true),
        ));

        return array_values(array_unique([...$preferred, ...$remaining]));
    }
}
