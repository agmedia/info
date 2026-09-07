<?php

use App\Models\Catalog\Category\Category;
use App\Support\Content\EuFundsCallCategoryRegistry;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * @var array<int, string>
     */
    private array $callTitles = [
        'Izgradnja i opremanje postrojenja za obradu reciklabilnog otpada',
        'Podrška uključivanju MSP-ova u lance vrijednosti (Integrator)',
        'Postrojenja za skladištenje vlastite proizvodnje električne energije za potrebe odgođene isporuke energije u mrežu',
        'Inovacijski vaučeri',
    ];

    public function up(): void
    {
        if (! Schema::hasTable('categories')
            || ! Schema::hasTable('content_call_post_translations')
            || ! Schema::hasTable('content_call_post_category')) {
            return;
        }

        $statusCategories = DB::table('categories')
            ->where('scope', Category::SCOPE_CALL)
            ->whereIn('code', EuFundsCallCategoryRegistry::orderedKeys())
            ->pluck('id', 'code');
        $closedCategoryId = (int) ($statusCategories[EuFundsCallCategoryRegistry::CLOSED] ?? 0);

        if ($closedCategoryId === 0) {
            return;
        }

        $postIds = DB::table('content_call_post_translations')
            ->where('locale', 'hr')
            ->whereIn('title', $this->callTitles)
            ->pluck('post_id')
            ->map(static fn ($id): int => (int) $id)
            ->unique()
            ->values();

        if ($postIds->isEmpty()) {
            return;
        }

        DB::transaction(function () use ($closedCategoryId, $postIds, $statusCategories): void {
            $statusCategoryIds = $statusCategories
                ->map(static fn ($id): int => (int) $id)
                ->values()
                ->all();
            $existingStatuses = DB::table('content_call_post_category')
                ->whereIn('post_id', $postIds->all())
                ->whereIn('category_id', $statusCategoryIds)
                ->orderByDesc('is_primary')
                ->orderBy('sort_order')
                ->get()
                ->groupBy('post_id')
                ->map(static fn ($rows) => $rows->first());

            DB::table('content_call_post_category')
                ->whereIn('post_id', $postIds->all())
                ->whereIn('category_id', $statusCategoryIds)
                ->delete();

            $timestamp = now();

            foreach ($postIds as $postId) {
                $previousStatus = $existingStatuses->get($postId);

                DB::table('content_call_post_category')->insert([
                    'post_id' => $postId,
                    'category_id' => $closedCategoryId,
                    'sort_order' => (int) ($previousStatus->sort_order ?? 0),
                    'is_primary' => (bool) ($previousStatus->is_primary ?? true),
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ]);
            }
        });
    }

    public function down(): void
    {
        // Editorial status changes are intentionally preserved.
    }
};
