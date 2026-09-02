<?php

namespace Tests\Feature\Content;

use App\Models\Catalog\Category\Category;
use App\Models\Content\Call\CallPost;
use App\Services\Content\EuFundsCallImportService;
use App\Support\Content\EuFundsCallCategoryRegistry;
use App\Support\Content\EuFundsServicePageDefaults;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use RuntimeException;
use Tests\TestCase;

class EuFundsCallImportProductionShapeRegressionTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_reimport_repairs_production_shape_without_deleting_managed_extras_and_is_idempotent(): void
    {
        $blueprint = $this->blueprint();
        $categories = $this->createStatusCategories();
        $managedExtraIds = $this->seedProductionShape($blueprint, $categories);
        $xmlPath = $this->makeWordPressXmlExport($blueprint);

        try {
            $this->assertStatusCounts([7, 8, 20], $categories);

            app(EuFundsCallImportService::class)->import($xmlPath, ['locale' => 'hr']);

            $this->assertStatusCounts([5, 7, 20], $categories);
            $this->assertBlueprintIdentities($blueprint);

            foreach ($managedExtraIds as $extraId) {
                $this->assertDatabaseHas('content_call_posts', ['id' => $extraId]);
                $this->assertDatabaseMissing('content_call_post_category', ['post_id' => $extraId]);
            }

            $afterFirstImport = $this->semanticSnapshot();

            app(EuFundsCallImportService::class)->import($xmlPath, ['locale' => 'hr']);

            $this->assertStatusCounts([5, 7, 20], $categories);
            $this->assertSame($afterFirstImport, $this->semanticSnapshot());
        } finally {
            @unlink($xmlPath);
        }
    }

    public function test_full_reimport_rejects_an_unmanaged_status_extra_and_rolls_back_everything(): void
    {
        $blueprint = $this->blueprint();
        $categories = $this->createStatusCategories();
        $this->seedProductionShape($blueprint, $categories);
        $manualPost = CallPost::query()->create([
            'code' => 'manual-call-that-importer-must-not-own',
            'is_active' => true,
            'published_at' => now()->subDay(),
            'sort_order' => 99,
            'payload' => ['editor_owner' => 'uAdmin'],
        ]);
        $manualPost->translations()->create([
            'locale' => 'hr',
            'title' => 'Ručni poziv koji importer ne smije dirati',
            'slug' => 'rucni-poziv-koji-importer-ne-smije-dirari',
            'body_html' => '<p>Ručno uređeni sadržaj.</p>',
            'payload' => ['editor_owner' => 'uAdmin'],
        ]);
        $manualPost->categories()->attach($categories[EuFundsCallCategoryRegistry::OPEN]->id, [
            'sort_order' => 99,
            'is_primary' => true,
        ]);
        $beforeImport = $this->semanticSnapshot();
        $xmlPath = $this->makeWordPressXmlExport($blueprint);

        $caughtException = null;
        try {
            app(EuFundsCallImportService::class)->import($xmlPath, ['locale' => 'hr']);
        } catch (RuntimeException $exception) {
            $caughtException = $exception;
        } finally {
            @unlink($xmlPath);
        }

        $this->assertNotNull($caughtException, 'An unmanaged call inside a managed status category must block the full import.');
        $this->assertStringContainsString('unmanaged', strtolower($caughtException->getMessage()));
        $this->assertSame($beforeImport, $this->semanticSnapshot());
        $this->assertDatabaseHas('content_call_post_category', [
            'post_id' => $manualPost->id,
            'category_id' => $categories[EuFundsCallCategoryRegistry::OPEN]->id,
        ]);
    }

    /** @return array<int, array{group_key:string,title:string,slug:string,code:string,sort_order:int,wp_post_id:int}> */
    private function blueprint(): array
    {
        $rows = [];
        $wpPostId = 60000;

        foreach ((array) data_get(EuFundsServicePageDefaults::defaultsForLocale('hr'), 'calls.groups', []) as $group) {
            $definition = collect(EuFundsCallCategoryRegistry::definitions('hr'))
                ->firstWhere('title', (string) ($group['title'] ?? ''));
            $this->assertIsArray($definition);

            foreach (array_values((array) ($group['items'] ?? [])) as $sortOrder => $item) {
                $title = (string) ($item['title'] ?? '');
                $rows[] = [
                    'group_key' => (string) $definition['key'],
                    'title' => $title,
                    'slug' => (string) data_get($item, 'link.slug'),
                    'code' => $this->importCode($title),
                    'sort_order' => $sortOrder,
                    'wp_post_id' => ++$wpPostId,
                ];
            }
        }

        return $rows;
    }

    /** @return array<string, Category> */
    private function createStatusCategories(): array
    {
        $categories = [];

        foreach (EuFundsCallCategoryRegistry::definitions('hr') as $index => $definition) {
            $category = new Category([
                'scope' => Category::SCOPE_CALL,
                'code' => $definition['code'],
                'is_active' => true,
                'show_in_menu' => false,
                'sort_order' => $index,
                'payload' => ['import_source' => 'eu_funds_calls'],
            ]);
            $category->saveAsRoot();
            $category->translations()->create([
                'scope' => Category::SCOPE_CALL,
                'locale' => 'hr',
                'name' => $definition['title'],
                'slug' => $definition['slug'],
                'payload' => ['status_label' => $definition['status_label']],
            ]);
            $categories[$definition['key']] = $category;
        }

        return $categories;
    }

    /**
     * @param  array<int, array{group_key:string,title:string,slug:string,code:string,sort_order:int,wp_post_id:int}>  $blueprint
     * @param  array<string, Category>  $categories
     * @return array<int, int>
     */
    private function seedProductionShape(array $blueprint, array $categories): array
    {
        $pendingRows = array_values(array_filter($blueprint, fn (array $row): bool => $row['group_key'] === EuFundsCallCategoryRegistry::UPCOMING));

        foreach ($blueprint as $row) {
            if ($row['group_key'] === EuFundsCallCategoryRegistry::UPCOMING) {
                // Production contained crossed identities: the desired code and
                // imported source payload could belong to a different public
                // slug owner. Pairwise swaps force reconciliation to resolve
                // globally and to quarantine unique-code collisions safely.
                $pairedIndex = match ($row['sort_order']) {
                    0 => 1,
                    1 => 0,
                    2 => 3,
                    3 => 2,
                    default => 4,
                };
                $publicIdentity = $pendingRows[$pairedIndex];
                $duplicatedPayloadIdentity = in_array($row['sort_order'], [1, 3], true)
                    ? $pendingRows[$row['sort_order'] - 1]
                    : $row;
                $this->createManagedCall(
                    code: $row['code'],
                    title: $row['title'],
                    slug: $publicIdentity['slug'],
                    sourceSlug: $duplicatedPayloadIdentity['slug'],
                    wpPostId: $duplicatedPayloadIdentity['wp_post_id'],
                    category: $categories[$row['group_key']],
                    sortOrder: $row['sort_order'],
                );

                continue;
            }

            $this->createManagedCall(
                code: $row['code'],
                title: $row['title'],
                slug: $row['slug'],
                sourceSlug: $row['slug'],
                wpPostId: $row['wp_post_id'],
                category: $categories[$row['group_key']],
                sortOrder: $row['sort_order'],
            );
        }

        $extraIds = [];
        foreach ([5, 6] as $sortOrder) {
            $extraIds[] = $this->createManagedCall(
                code: 'legacy-removed-pending-'.$sortOrder,
                title: 'Stari uklonjeni poziv '.$sortOrder,
                slug: 'stari-uklonjeni-poziv-'.$sortOrder,
                sourceSlug: 'stari-uklonjeni-poziv-'.$sortOrder,
                wpPostId: 61000 + $sortOrder,
                category: $categories[EuFundsCallCategoryRegistry::UPCOMING],
                sortOrder: $sortOrder,
            );
        }

        $extraIds[] = $this->createManagedCall(
            code: 'gs-integrator-2018',
            title: 'GS Integrator 2018',
            slug: 'gs-integrator-2018',
            sourceSlug: 'gs-integrator-2018',
            wpPostId: 62000,
            category: $categories[EuFundsCallCategoryRegistry::OPEN],
            sortOrder: 0,
        );

        return $extraIds;
    }

    private function createManagedCall(
        string $code,
        string $title,
        string $slug,
        string $sourceSlug,
        int $wpPostId,
        Category $category,
        int $sortOrder,
    ): int {
        $post = CallPost::query()->create([
            'code' => $code,
            'is_active' => true,
            'published_at' => now()->subDay(),
            'sort_order' => $sortOrder,
            'payload' => [
                'import_source' => 'eu_funds_calls',
                'group_key' => $category->code,
                'source_hint_blog_slug' => $sourceSlug,
            ],
        ]);
        $post->translations()->create([
            'locale' => 'hr',
            'title' => $title,
            'slug' => $slug,
            'body_html' => '<p>Managed legacy content for '.$title.'.</p>',
            'payload' => [
                'import_source' => 'xml',
                'wp_post_id' => $wpPostId,
                'source_slug' => $sourceSlug,
            ],
        ]);
        $post->categories()->attach($category->id, [
            'sort_order' => $sortOrder,
            'is_primary' => true,
        ]);

        return (int) $post->id;
    }

    /** @param array<int, Category> $categories */
    private function assertStatusCounts(array $expected, array $categories): void
    {
        $actual = collect(EuFundsCallCategoryRegistry::orderedKeys())
            ->map(fn (string $key): int => $categories[$key]->callPosts()->count())
            ->all();

        $this->assertSame($expected, $actual);
    }

    /** @param array<int, array{title:string,slug:string,code:string,wp_post_id:int}> $blueprint */
    private function assertBlueprintIdentities(array $blueprint): void
    {
        foreach ($blueprint as $row) {
            $post = CallPost::query()->where('code', $row['code'])->with('translations')->sole();
            $translation = $post->translations->firstWhere('locale', 'hr');

            $this->assertNotNull($translation, $row['title']);
            $this->assertSame($row['title'], $translation->title);
            $this->assertSame($row['slug'], data_get($translation->payload, 'source_slug'));
            $this->assertSame($row['wp_post_id'], (int) data_get($translation->payload, 'wp_post_id'));
        }
    }

    /** @return array<string, mixed> */
    private function semanticSnapshot(): array
    {
        return [
            'posts' => CallPost::query()->orderBy('id')->get()
                ->map(fn (CallPost $post): array => $post->only(['id', 'code', 'is_active', 'sort_order', 'payload']))
                ->all(),
            'translations' => \DB::table('content_call_post_translations')
                ->orderBy('id')->get(['post_id', 'locale', 'title', 'slug', 'body_html', 'payload'])
                ->map(fn (object $row): array => (array) $row)->all(),
            'categories' => \DB::table('content_call_post_category')
                ->orderBy('post_id')->orderBy('category_id')
                ->get(['post_id', 'category_id', 'sort_order', 'is_primary'])
                ->map(fn (object $row): array => (array) $row)->all(),
        ];
    }

    /** @param array<int, array{title:string,slug:string,wp_post_id:int}> $blueprint */
    private function makeWordPressXmlExport(array $blueprint): string
    {
        $items = collect($blueprint)->map(fn (array $row): string => sprintf(<<<'XML'
        <item>
            <title><![CDATA[%s]]></title>
            <link>https://www.alphacapitalis.com/2026/08/27/%s/</link>
            <pubDate>Thu, 27 Aug 2026 10:00:00 +0000</pubDate>
            <dc:creator><![CDATA[admin]]></dc:creator>
            <guid isPermaLink="false">https://www.alphacapitalis.com/?p=%d</guid>
            <description></description>
            <content:encoded><![CDATA[<p>Fresh WXR content for %s.</p>]]></content:encoded>
            <excerpt:encoded><![CDATA[Sažetak iz izvoza.]]></excerpt:encoded>
            <wp:post_id>%d</wp:post_id>
            <wp:post_date><![CDATA[2026-08-27 12:00:00]]></wp:post_date>
            <wp:post_date_gmt><![CDATA[2026-08-27 10:00:00]]></wp:post_date_gmt>
            <wp:post_name><![CDATA[%s]]></wp:post_name>
            <wp:status><![CDATA[publish]]></wp:status>
            <wp:post_parent>0</wp:post_parent>
            <wp:menu_order>0</wp:menu_order>
            <wp:post_type><![CDATA[post]]></wp:post_type>
            <category domain="category" nicename="eu-fondovi"><![CDATA[EU fondovi]]></category>
        </item>
XML,
            $row['title'],
            $row['slug'],
            $row['wp_post_id'],
            $row['title'],
            $row['wp_post_id'],
            $row['slug'],
        ))->implode("\n");

        $path = tempnam(sys_get_temp_dir(), 'eu-funds-production-shape-');
        $this->assertNotFalse($path);
        file_put_contents($path, sprintf(<<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0"
    xmlns:excerpt="http://wordpress.org/export/1.2/excerpt/"
    xmlns:content="http://purl.org/rss/1.0/modules/content/"
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:wp="http://wordpress.org/export/1.2/">
    <channel>
        <title>WordPress Export</title>
%s
    </channel>
</rss>
XML, $items));

        return $path;
    }

    private function importCode(string $title): string
    {
        $prefix = 'eu-funds-call-';
        $slug = Str::slug($title) ?: 'poziv';
        $hash = substr(sha1($title), 0, 8);
        $maxSlugLength = 120 - strlen($prefix) - 1 - strlen($hash);

        return $prefix.trim(substr($slug, 0, max(1, $maxSlugLength)), '-').'-'.$hash;
    }
}
