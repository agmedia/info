<?php

namespace Database\Seeders;

use App\Models\Catalog\Category\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $userId = User::query()->value('id');

        $guides = $this->upsertCategory(
            code: 'guides',
            scope: Category::SCOPE_BLOG,
            sortOrder: 10,
            parent: null,
            userId: $userId,
            translations: [
                'en' => ['name' => 'Guides', 'slug' => 'guides'],
                'hr' => ['name' => 'Vodiči', 'slug' => 'vodici'],
            ]
        );

        $this->upsertCategory(
            code: 'recipes',
            scope: Category::SCOPE_BLOG,
            sortOrder: 11,
            parent: $guides,
            userId: $userId,
            translations: [
                'en' => ['name' => 'Recipes', 'slug' => 'recipes'],
                'hr' => ['name' => 'Recepti', 'slug' => 'recepti'],
            ]
        );

        $this->upsertCategory(
            code: 'shipping-returns',
            scope: Category::SCOPE_PAGE,
            sortOrder: 10,
            parent: null,
            userId: $userId,
            translations: [
                'en' => ['name' => 'Shipping & Returns', 'slug' => 'shipping-returns'],
                'hr' => ['name' => 'Dostava i povrat', 'slug' => 'dostava-i-povrat'],
            ],
            payload: ['show_in_footer' => true]
        );

        $this->upsertCategory(
            code: 'about',
            scope: Category::SCOPE_PAGE,
            sortOrder: 11,
            parent: null,
            userId: $userId,
            translations: [
                'en' => ['name' => 'About Us', 'slug' => 'about-us'],
                'hr' => ['name' => 'O nama', 'slug' => 'o-nama'],
            ],
            payload: ['show_in_footer' => true]
        );

        Category::query()->fixTree();
    }

    /**
     * @param array<string, array{name:string,slug?:string,description?:string}> $translations
     * @param array<string, mixed>|null $payload
     */
    private function upsertCategory(
        string $code,
        string $scope,
        int $sortOrder,
        ?Category $parent,
        ?int $userId,
        array $translations,
        ?array $payload = null
    ): Category {
        $desiredParentId = $parent?->id;
        $now = now();

        $categoryId = Category::query()
            ->where('scope', $scope)
            ->where('code', $code)
            ->value('id');

        $basePayload = [
            'scope' => $scope,
            'code' => $code,
            'is_active' => true,
            'show_in_menu' => true,
            'sort_order' => $sortOrder,
            'payload' => is_array($payload)
                ? json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                : null,
            'parent_id' => $desiredParentId,
            'updated_by' => $userId,
            'updated_at' => $now,
        ];

        if ($categoryId) {
            DB::table('categories')
                ->where('id', $categoryId)
                ->update($basePayload);
        } else {
            $categoryId = (int) DB::table('categories')->insertGetId($basePayload + [
                'created_by' => $userId,
                'created_at' => $now,
                '_lft' => 0,
                '_rgt' => 0,
            ]);
        }

        $category = Category::query()->findOrFail($categoryId);

        foreach ($translations as $locale => $data) {
            $name = $data['name'];
            $slug = $data['slug'] ?? Str::slug($name);

            $category->translations()->updateOrCreate(
                ['locale' => $locale],
                [
                    'scope' => $scope,
                    'name' => $name,
                    'slug' => $slug,
                    'description' => $data['description'] ?? null,
                    'meta_title' => $name,
                    'meta_description' => $data['description'] ?? null,
                    'payload' => null,
                ]
            );
        }

        return $category->refresh();
    }
}
