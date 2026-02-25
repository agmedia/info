<?php

namespace Database\Seeders;

use App\Models\Content\ContentBlock;
use App\Models\User;
use Illuminate\Database\Seeder;

class ContentBlockSeeder extends Seeder
{
    public function run(): void
    {
        $userId = User::query()->value('id');

        $records = [
            [
                'code' => 'home-hero-main',
                'name' => 'Home Hero Main',
                'type' => 'desktop_hero_banner',
                'is_active' => true,
                'payload' => null,
                'translations' => [
                    'en' => [
                        'title' => 'Modern essentials, built for everyday carry.',
                        'subtitle' => 'AGinfo blends practical design and clean structure for fast browsing.',
                        'cta_label' => 'Explore content',
                        'cta_url' => '/blog',
                    ],
                    'hr' => [
                        'title' => 'Modern essentials, built for everyday carry.',
                        'subtitle' => 'AGinfo spaja praktican dizajn i cistu strukturu za brzo pregledavanje sadrzaja.',
                        'cta_label' => 'Istrazi sadrzaj',
                        'cta_url' => '/blog',
                    ],
                ],
            ],
            [
                'code' => 'home-hero-benefits',
                'name' => 'Home Hero Benefits',
                'type' => 'hero_highlights_strip',
                'is_active' => true,
                'payload' => null,
                'translations' => [
                    'en' => [
                        'title' => 'Why teams use this stack',
                        'subtitle' => 'Fast updates, clear structure, and reusable blocks.',
                        'cta_label' => null,
                        'cta_url' => null,
                    ],
                    'hr' => [
                        'title' => 'Zasto timovi koriste ovaj stack',
                        'subtitle' => 'Brze izmjene, jasna struktura i ponovno upotrebljivi blokovi.',
                        'cta_label' => null,
                        'cta_url' => null,
                    ],
                ],
            ],
            [
                'code' => 'home-mobile-hero',
                'name' => 'Home Mobile Hero',
                'type' => 'mobile_hero_banner',
                'is_active' => true,
                'payload' => null,
                'translations' => [
                    'en' => [
                        'title' => 'Build and publish faster',
                        'subtitle' => 'Mobile-first hero for campaign and landing navigation.',
                        'cta_label' => 'Open blog',
                        'cta_url' => '/blog',
                    ],
                    'hr' => [
                        'title' => 'Brze izgradi i objavi',
                        'subtitle' => 'Mobile-first hero za kampanje i navigaciju prema landing stranicama.',
                        'cta_label' => 'Otvori blog',
                        'cta_url' => '/blog',
                    ],
                ],
            ],
            [
                'code' => 'home-blog-grid',
                'name' => 'Home Blog Grid',
                'type' => 'blog_grid_3',
                'is_active' => true,
                'payload' => [
                    'limit' => 3,
                ],
                'translations' => [
                    'en' => [
                        'title' => 'Latest posts',
                        'subtitle' => 'Fresh updates and practical guides.',
                        'cta_label' => 'View all posts',
                        'cta_url' => '/blog',
                    ],
                    'hr' => [
                        'title' => 'Najnovije objave',
                        'subtitle' => 'Svjeze novosti i prakticni vodici.',
                        'cta_label' => 'Pogledaj sve objave',
                        'cta_url' => '/blog',
                    ],
                ],
            ],
            [
                'code' => 'page-contact-cta',
                'name' => 'Page Contact CTA',
                'type' => 'cta_banner',
                'is_active' => true,
                'payload' => null,
                'translations' => [
                    'en' => [
                        'title' => 'Need help planning your next page?',
                        'subtitle' => 'Send a message and we will reply with a clear implementation path.',
                        'cta_label' => 'Contact us',
                        'cta_url' => '/contact',
                    ],
                    'hr' => [
                        'title' => 'Trebas pomoc oko iduce stranice?',
                        'subtitle' => 'Posalji poruku i vracamo se s jasnim planom implementacije.',
                        'cta_label' => 'Kontaktiraj nas',
                        'cta_url' => '/contact',
                    ],
                ],
            ],
        ];

        foreach ($records as $record) {
            $block = ContentBlock::query()->updateOrCreate(
                ['code' => $record['code']],
                [
                    'name' => $record['name'],
                    'type' => $record['type'],
                    'is_active' => (bool) $record['is_active'],
                    'payload' => $record['payload'] ?? null,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]
            );

            foreach ($record['translations'] as $locale => $translation) {
                $block->translations()->updateOrCreate(
                    ['locale' => $locale],
                    [
                        'title' => $translation['title'] ?? null,
                        'subtitle' => $translation['subtitle'] ?? null,
                        'body_html' => null,
                        'cta_label' => $translation['cta_label'] ?? null,
                        'cta_url' => $translation['cta_url'] ?? null,
                        'payload' => null,
                    ]
                );
            }
        }
    }
}
