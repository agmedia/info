<?php

namespace Database\Seeders;

use App\Models\Catalog\Category\Category;
use App\Models\Content\Blog\BlogPost;
use App\Models\Content\Page\InfoPage;
use App\Models\Content\Support\Faq;
use App\Models\Settings\Local\Language;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Silber\Bouncer\Database\Role;

class DummyContentSeeder extends Seeder
{
    private const TARGET_USERS = 220;
    private const TARGET_BLOG_CATEGORIES = 18;
    private const TARGET_PAGE_CATEGORIES = 14;
    private const TARGET_BLOG_POSTS = 180;
    private const TARGET_INFO_PAGES = 80;
    private const TARGET_FAQS = 90;

    /**
     * @var array<int, string>
     */
    private array $locales = [];

    private ?int $authorId = null;

    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            RoleAbilitySeeder::class,
            CustomerGroupSeeder::class,
            UserSedder::class,
            SettingsLocalSeeder::class,
            SystemSettingsSeeder::class,
            CategorySeeder::class,
            BlogPostSeeder::class,
            InfoPageSeeder::class,
            FaqSeeder::class,
            ContentBlockSeeder::class,
            ContentBlockSlotSeeder::class,
        ]);

        $this->locales = $this->resolveLocales();
        $this->authorId = $this->resolveAuthorId();

        $this->command?->info('Seeding extended dummy content data.');

        $this->seedUsers();
        $categories = $this->seedCategories();
        $this->seedBlogPosts($categories['blog']);
        $this->seedInfoPages($categories['page']);
        $this->seedFaqs();

        $this->command?->info('Extended dummy content data complete.');
    }

    /**
     * @return array<int, string>
     */
    private function resolveLocales(): array
    {
        $locales = Language::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('code')
            ->map(fn ($code): string => (string) $code)
            ->filter(fn (string $code): bool => $code !== '')
            ->values()
            ->all();

        return $locales !== [] ? $locales : ['hr', 'en'];
    }

    private function resolveAuthorId(): ?int
    {
        return User::query()->value('id');
    }

    private function seedUsers(): void
    {
        $existingCount = (int) User::query()->count();
        $toCreate = max(0, self::TARGET_USERS - $existingCount);

        if ($toCreate === 0) {
            return;
        }

        $nextIndex = $this->nextIndexByPattern(
            values: User::query()->where('email', 'like', 'demo.content.user%')->pluck('email')->all(),
            pattern: '/^demo\.content\.user(\d{4})@example\.test$/'
        );

        $customerRoleId = (int) Role::query()->where('name', 'customer')->value('id');

        for ($i = 0; $i < $toCreate; $i++) {
            $seq = $nextIndex + $i;
            $firstName = fake()->firstName();
            $lastName = fake()->lastName();

            $user = User::query()->create([
                'name' => trim($firstName.' '.$lastName),
                'email' => sprintf('demo.content.user%04d@example.test', $seq),
                'password' => 'password',
                'email_verified_at' => now()->subDays(random_int(0, 240)),
            ]);

            if ($customerRoleId > 0) {
                $user->roles()->syncWithoutDetaching([$customerRoleId]);
            }
        }
    }

    /**
     * @return array{blog: array<int, int>, page: array<int, int>}
     */
    private function seedCategories(): array
    {
        $blogIds = $this->seedCategoriesForScope(Category::SCOPE_BLOG, self::TARGET_BLOG_CATEGORIES);
        $pageIds = $this->seedCategoriesForScope(Category::SCOPE_PAGE, self::TARGET_PAGE_CATEGORIES);

        Category::query()->fixTree();

        return [
            'blog' => $blogIds,
            'page' => $pageIds,
        ];
    }

    /**
     * @return array<int, int>
     */
    private function seedCategoriesForScope(string $scope, int $target): array
    {
        $existingCount = (int) Category::query()->where('scope', $scope)->count();
        $toCreate = max(0, $target - $existingCount);

        $nextIndex = $this->nextIndexByPattern(
            values: Category::query()
                ->where('scope', $scope)
                ->where('code', 'like', 'demo-'.$scope.'-cat-%')
                ->pluck('code')
                ->all(),
            pattern: '/^demo\-'.preg_quote($scope, '/').'\-cat\-(\d{3})$/'
        );

        for ($i = 0; $i < $toCreate; $i++) {
            $seq = $nextIndex + $i;
            $code = sprintf('demo-%s-cat-%03d', $scope, $seq);
            $nameEn = ucfirst($scope).' Topic '.$seq;
            $nameHr = ucfirst($scope).' tema '.$seq;

            $category = new Category([
                'scope' => $scope,
                'code' => $code,
                'is_active' => true,
                'show_in_menu' => true,
                'sort_order' => $seq * 10,
                'payload' => $scope === Category::SCOPE_PAGE
                    ? ['show_in_footer' => (bool) random_int(0, 1)]
                    : ['seed' => 'dummy-content'],
                'created_by' => $this->authorId,
                'updated_by' => $this->authorId,
            ]);

            $category->saveAsRoot();

            foreach ($this->locales as $locale) {
                $name = $locale === 'hr' ? $nameHr : $nameEn;
                $slug = Str::slug($name).'-'.$seq;

                $category->translations()->updateOrCreate(
                    ['locale' => $locale],
                    [
                        'scope' => $scope,
                        'name' => $name,
                        'slug' => $slug,
                        'description' => $this->localeLine($locale, 'Structured category used for large demo content datasets.', 'Kategorija za prosireni demo set sadrzaja.'),
                        'meta_title' => $name,
                        'meta_description' => $this->localeLine($locale, 'Demo category for seeded content data.', 'Demo kategorija za seedani sadrzaj.'),
                        'payload' => null,
                    ]
                );
            }
        }

        return Category::query()
            ->where('scope', $scope)
            ->orderBy('id')
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();
    }

    /**
     * @param array<int, int> $blogCategoryIds
     */
    private function seedBlogPosts(array $blogCategoryIds): void
    {
        $existingCount = (int) BlogPost::query()->where('code', 'like', 'demo-blog-post-%')->count();
        $toCreate = max(0, self::TARGET_BLOG_POSTS - $existingCount);

        $nextIndex = $this->nextIndexByPattern(
            values: BlogPost::query()->where('code', 'like', 'demo-blog-post-%')->pluck('code')->all(),
            pattern: '/^demo\-blog\-post\-(\d{4})$/'
        );

        for ($i = 0; $i < $toCreate; $i++) {
            $seq = $nextIndex + $i;
            $code = sprintf('demo-blog-post-%04d', $seq);
            $titleEn = 'Demo Insight '.$seq;
            $titleHr = 'Demo uvid '.$seq;

            $post = BlogPost::query()->updateOrCreate(
                ['code' => $code],
                [
                    'is_active' => true,
                    'is_featured' => $seq % 9 === 0,
                    'published_at' => now()->subDays(random_int(0, 365)),
                    'sort_order' => $seq,
                    'payload' => ['seed' => 'dummy-content'],
                    'created_by' => $this->authorId,
                    'updated_by' => $this->authorId,
                ]
            );

            foreach ($this->locales as $locale) {
                $title = $locale === 'hr' ? $titleHr : $titleEn;
                $excerpt = $this->localeLine(
                    $locale,
                    'Practical engineering notes, operations learnings, and rollout updates for project '.$seq.'.',
                    'Prakticne tehnicke biljeske, operativni uvidi i updateovi implementacije za projekt '.$seq.'.'
                );

                $post->translations()->updateOrCreate(
                    ['locale' => $locale],
                    [
                        'title' => $title,
                        'slug' => Str::slug($title).'-'.$seq,
                        'excerpt' => $excerpt,
                        'body_html' => '<p>'.$title.'</p><p>'.$excerpt.'</p>',
                        'meta_title' => $title,
                        'meta_description' => $excerpt,
                        'payload' => null,
                    ]
                );
            }

            if ($blogCategoryIds !== []) {
                $selected = $this->pickMany($blogCategoryIds, random_int(1, min(2, count($blogCategoryIds))));
                $sync = [];
                foreach ($selected as $index => $categoryId) {
                    $sync[$categoryId] = [
                        'sort_order' => $index,
                        'is_primary' => $index === 0,
                    ];
                }

                $post->categories()->sync($sync);
            }
        }
    }

    /**
     * @param array<int, int> $pageCategoryIds
     */
    private function seedInfoPages(array $pageCategoryIds): void
    {
        $existingCount = (int) InfoPage::query()->where('code', 'like', 'demo-info-page-%')->count();
        $toCreate = max(0, self::TARGET_INFO_PAGES - $existingCount);

        $nextIndex = $this->nextIndexByPattern(
            values: InfoPage::query()->where('code', 'like', 'demo-info-page-%')->pluck('code')->all(),
            pattern: '/^demo\-info\-page\-(\d{4})$/'
        );

        for ($i = 0; $i < $toCreate; $i++) {
            $seq = $nextIndex + $i;
            $code = sprintf('demo-info-page-%04d', $seq);
            $titleEn = 'Info Page '.$seq;
            $titleHr = 'Info stranica '.$seq;

            $page = InfoPage::query()->updateOrCreate(
                ['code' => $code],
                [
                    'layout' => random_int(0, 4) === 0 ? 'legal' : 'default',
                    'is_active' => true,
                    'show_in_footer' => random_int(0, 1) === 1,
                    'published_at' => now()->subDays(random_int(0, 365)),
                    'sort_order' => $seq,
                    'payload' => ['seed' => 'dummy-content'],
                    'created_by' => $this->authorId,
                    'updated_by' => $this->authorId,
                ]
            );

            foreach ($this->locales as $locale) {
                $title = $locale === 'hr' ? $titleHr : $titleEn;
                $excerpt = $this->localeLine(
                    $locale,
                    'Reusable informational page seeded for content load testing and navigation QA.',
                    'Visekratna info stranica za testiranje opterecenja sadrzaja i QA navigacije.'
                );

                $page->translations()->updateOrCreate(
                    ['locale' => $locale],
                    [
                        'title' => $title,
                        'slug' => Str::slug($title).'-'.$seq,
                        'excerpt' => $excerpt,
                        'body_html' => '<p>'.$title.'</p><p>'.$excerpt.'</p>',
                        'meta_title' => $title,
                        'meta_description' => $excerpt,
                        'payload' => null,
                    ]
                );
            }

            if ($pageCategoryIds !== []) {
                $selected = $this->pickMany($pageCategoryIds, random_int(0, min(2, count($pageCategoryIds))));
                $sync = [];
                foreach ($selected as $index => $categoryId) {
                    $sync[$categoryId] = [
                        'sort_order' => $index,
                        'is_primary' => $index === 0,
                    ];
                }

                $page->categories()->sync($sync);
            }
        }
    }

    private function seedFaqs(): void
    {
        $groups = ['general', 'content', 'support', 'deployments', 'accounts'];

        $existingCount = (int) Faq::query()->where('code', 'like', 'demo-faq-%')->count();
        $toCreate = max(0, self::TARGET_FAQS - $existingCount);

        $nextIndex = $this->nextIndexByPattern(
            values: Faq::query()->where('code', 'like', 'demo-faq-%')->pluck('code')->all(),
            pattern: '/^demo\-faq\-(\d{4})$/'
        );

        for ($i = 0; $i < $toCreate; $i++) {
            $seq = $nextIndex + $i;
            $code = sprintf('demo-faq-%04d', $seq);

            $faq = Faq::query()->updateOrCreate(
                ['code' => $code],
                [
                    'group_code' => $groups[array_rand($groups)],
                    'is_active' => true,
                    'is_featured' => $seq % 10 === 0,
                    'sort_order' => $seq,
                    'payload' => ['seed' => 'dummy-content'],
                    'created_by' => $this->authorId,
                    'updated_by' => $this->authorId,
                ]
            );

            foreach ($this->locales as $locale) {
                $question = $this->localeLine(
                    $locale,
                    'How is demo content item '.$seq.' managed?',
                    'Kako se upravlja demo sadrzajem broj '.$seq.'?'
                );
                $answer = $this->localeLine(
                    $locale,
                    'Demo content is versioned in admin blocks and can be updated safely without changing code.',
                    'Demo sadrzaj se verzionira kroz admin blokove i moze se sigurno mijenjati bez izmjene koda.'
                );

                $faq->translations()->updateOrCreate(
                    ['locale' => $locale],
                    [
                        'question' => $question,
                        'slug' => Str::slug($question).'-'.$seq,
                        'answer_html' => '<p>'.$answer.'</p>',
                        'meta_title' => $question,
                        'meta_description' => $answer,
                        'payload' => null,
                    ]
                );
            }
        }
    }

    /**
     * @param array<int, string> $values
     */
    private function nextIndexByPattern(array $values, string $pattern): int
    {
        $max = 0;

        foreach ($values as $value) {
            if (! is_string($value) || ! preg_match($pattern, $value, $matches)) {
                continue;
            }

            $num = (int) ($matches[1] ?? 0);
            if ($num > $max) {
                $max = $num;
            }
        }

        return $max + 1;
    }

    /**
     * @param array<int, int> $ids
     * @return array<int, int>
     */
    private function pickMany(array $ids, int $count): array
    {
        if ($count <= 0 || $ids === []) {
            return [];
        }

        $pool = array_values($ids);
        shuffle($pool);

        return array_slice($pool, 0, min($count, count($pool)));
    }

    private function localeLine(string $locale, string $enLine, string $hrLine): string
    {
        return $locale === 'hr' ? $hrLine : $enLine;
    }
}
