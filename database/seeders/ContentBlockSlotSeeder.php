<?php

namespace Database\Seeders;

use App\Models\Content\ContentBlock;
use App\Models\Content\ContentBlockSlot;
use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class ContentBlockSlotSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ContentBlockSeeder::class,
        ]);

        $userId = User::query()->value('id');
        $missingBlockCodes = [];

        $records = [
            [
                'block_code' => 'home-hero-main',
                'placement' => 'home.hero',
                'target_type' => null,
                'target_ref' => null,
                'sort_order' => 0,
                'is_active' => true,
                'frontend_variant' => 'desktop',
            ],
            [
                'block_code' => 'home-hero-benefits',
                'placement' => 'home.hero_benefits',
                'target_type' => null,
                'target_ref' => null,
                'sort_order' => 10,
                'is_active' => true,
                'frontend_variant' => 'desktop',
            ],
            [
                'block_code' => 'home-mobile-hero',
                'placement' => 'home.hero',
                'target_type' => null,
                'target_ref' => null,
                'sort_order' => 0,
                'is_active' => true,
                'frontend_variant' => 'mobile',
            ],
            [
                'block_code' => 'home-blog-grid',
                'placement' => 'home.after_products',
                'target_type' => null,
                'target_ref' => null,
                'sort_order' => 20,
                'is_active' => true,
                'frontend_variant' => 'all',
            ],
            [
                'block_code' => 'page-contact-cta',
                'placement' => 'page.bottom',
                'target_type' => null,
                'target_ref' => null,
                'sort_order' => 10,
                'is_active' => true,
                'frontend_variant' => 'all',
            ],
        ];

        foreach ($records as $record) {
            $blockId = ContentBlock::query()
                ->where('code', $record['block_code'])
                ->value('id');

            if (! $blockId) {
                $missingBlockCodes[] = (string) $record['block_code'];
                continue;
            }

            ContentBlockSlot::query()->updateOrCreate(
                [
                    'content_block_id' => $blockId,
                    'placement' => $record['placement'],
                    'target_type' => $record['target_type'],
                    'target_ref' => $record['target_ref'],
                    'frontend_variant' => $record['frontend_variant'] ?? 'all',
                ],
                [
                    'sort_order' => (int) $record['sort_order'],
                    'is_active' => (bool) $record['is_active'],
                    'starts_at' => null,
                    'ends_at' => null,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]
            );
        }

        if ($missingBlockCodes !== []) {
            $codes = implode(', ', array_unique($missingBlockCodes));
            throw new RuntimeException('ContentBlockSlotSeeder missing block codes: '.$codes);
        }
    }
}
