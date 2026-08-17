<?php

use App\Models\Catalog\Category\Category;
use App\Models\Content\Blog\BlogPost;
use App\Models\Content\Call\CallPost;
use App\Models\Content\ContentBlock;
use App\Models\Content\Page\InfoPage;
use App\Models\Content\Service\ServicePage;
use App\Models\Content\Team\TeamMember;

return [
    'presets' => [
        'thumb_100x100' => [
            'fit' => 'crop',
            'width' => 100,
            'height' => 100,
            'quality' => 86,
            'format' => null,
        ],
        'icon_96x96' => [
            'fit' => 'crop',
            'width' => 96,
            'height' => 96,
            'quality' => 86,
            'format' => null,
        ],
        'card_360x240' => [
            'fit' => 'crop',
            'width' => 360,
            'height' => 240,
            'quality' => 86,
            'format' => null,
        ],
        'detail_960x960' => [
            'fit' => 'contain',
            'width' => 960,
            'height' => 960,
            'quality' => 88,
            'format' => null,
        ],
        'hero_1440x480' => [
            'fit' => 'crop',
            'width' => 1440,
            'height' => 480,
            'quality' => 86,
            'format' => null,
        ],
        'about_hero_1440x1059' => [
            'fit' => 'crop',
            'width' => 1440,
            'height' => 1059,
            'quality' => 86,
            'format' => null,
        ],
        'career_hero_1440x1059' => [
            'fit' => 'crop',
            'width' => 1440,
            'height' => 1059,
            'quality' => 86,
            'format' => 'webp',
        ],
        'services_index_card_1080x1350' => [
            'fit' => 'crop',
            'width' => 1080,
            'height' => 1350,
            'quality' => 88,
            'format' => 'webp',
        ],
    ],

    'models' => [
        BlogPost::class => [
            'label' => 'Blog Post',
            'main_collection' => 'blog_cover',
            'collections' => [
                'blog_cover' => [
                    'label' => 'Cover Image',
                    'single_file' => true,
                    'max_upload_kb' => 8192,
                    'accept_mime_types' => ['image/jpeg', 'image/png', 'image/webp', 'image/avif'],
                    'conversions' => ['thumb_100x100', 'card_360x240', 'hero_1440x480'],
                    'preview_conversion' => 'card_360x240',
                ],
                'blog_gallery' => [
                    'label' => 'Gallery',
                    'single_file' => false,
                    'max_upload_kb' => 8192,
                    'accept_mime_types' => ['image/jpeg', 'image/png', 'image/webp', 'image/avif'],
                    'conversions' => ['thumb_100x100', 'card_360x240', 'detail_960x960'],
                    'preview_conversion' => 'card_360x240',
                ],
            ],
        ],
        CallPost::class => [
            'label' => 'Call Post',
            'main_collection' => 'call_cover',
            'collections' => [
                'call_cover' => [
                    'label' => 'Cover Image',
                    'single_file' => true,
                    'max_upload_kb' => 8192,
                    'accept_mime_types' => ['image/jpeg', 'image/png', 'image/webp', 'image/avif'],
                    'conversions' => ['thumb_100x100', 'card_360x240', 'hero_1440x480'],
                    'preview_conversion' => 'card_360x240',
                ],
                'call_gallery' => [
                    'label' => 'Gallery',
                    'single_file' => false,
                    'max_upload_kb' => 8192,
                    'accept_mime_types' => ['image/jpeg', 'image/png', 'image/webp', 'image/avif'],
                    'conversions' => ['thumb_100x100', 'card_360x240', 'detail_960x960'],
                    'preview_conversion' => 'card_360x240',
                ],
            ],
        ],
        InfoPage::class => [
            'label' => 'Info Page',
            'collections' => [
                'about_hero_image' => [
                    'label' => 'About Hero Image',
                    'single_file' => true,
                    'max_upload_kb' => 8192,
                    'accept_mime_types' => ['image/jpeg', 'image/png', 'image/webp', 'image/avif'],
                    'conversions' => ['card_360x240', 'about_hero_1440x1059'],
                    'preview_conversion' => 'card_360x240',
                ],
                'career_hero_image' => [
                    'label' => 'Career Hero Image',
                    'single_file' => true,
                    'max_upload_kb' => 8192,
                    'accept_mime_types' => ['image/jpeg', 'image/png', 'image/webp', 'image/avif'],
                    'conversions' => ['card_360x240', 'career_hero_1440x1059'],
                    'preview_conversion' => 'card_360x240',
                ],
                'academy_gallery' => [
                    'label' => 'Academy Media Gallery',
                    'single_file' => false,
                    'only_keep_latest' => 24,
                    'max_upload_kb' => 8192,
                    'accept_mime_types' => ['image/jpeg', 'image/png', 'image/webp', 'image/avif'],
                    'conversions' => ['thumb_100x100', 'card_360x240', 'detail_960x960'],
                    'preview_conversion' => 'card_360x240',
                ],
                'reference_logos' => [
                    'label' => 'Reference Logos',
                    'single_file' => false,
                    'only_keep_latest' => 200,
                    'max_upload_kb' => 8192,
                    'accept_mime_types' => ['image/jpeg', 'image/png', 'image/webp', 'image/avif', 'image/svg+xml'],
                    'conversions' => [],
                    'preview_conversion' => '',
                ],
            ],
        ],
        ServicePage::class => [
            'label' => 'Service Page',
            'collections' => [
                'service_hero_image' => [
                    'label' => 'Hero Background',
                    'single_file' => true,
                    'max_upload_kb' => 8192,
                    'accept_mime_types' => ['image/jpeg', 'image/png', 'image/webp', 'image/avif', 'image/svg+xml'],
                    'conversions' => ['card_360x240', 'hero_1440x480'],
                    'preview_conversion' => 'card_360x240',
                ],
                'service_logo' => [
                    'label' => 'Service Logo',
                    'single_file' => true,
                    'max_upload_kb' => 4096,
                    'accept_mime_types' => ['image/jpeg', 'image/png', 'image/webp', 'image/avif', 'image/svg+xml'],
                    'conversions' => ['thumb_100x100', 'detail_960x960'],
                    'preview_conversion' => 'thumb_100x100',
                ],
                'services_index_audit_image' => [
                    'label' => 'Usluge: slika kartice Revizija',
                    'single_file' => true,
                    'max_upload_kb' => 8192,
                    'accept_mime_types' => ['image/jpeg', 'image/png', 'image/webp', 'image/avif'],
                    'conversions' => ['thumb_100x100', 'services_index_card_1080x1350'],
                    'preview_conversion' => 'thumb_100x100',
                ],
                'services_index_accounting_image' => [
                    'label' => 'Usluge: slika kartice Računovodstvo',
                    'single_file' => true,
                    'max_upload_kb' => 8192,
                    'accept_mime_types' => ['image/jpeg', 'image/png', 'image/webp', 'image/avif'],
                    'conversions' => ['thumb_100x100', 'services_index_card_1080x1350'],
                    'preview_conversion' => 'thumb_100x100',
                ],
                'services_index_advisory_image' => [
                    'label' => 'Usluge: slika kartice Savjetovanje',
                    'single_file' => true,
                    'max_upload_kb' => 8192,
                    'accept_mime_types' => ['image/jpeg', 'image/png', 'image/webp', 'image/avif'],
                    'conversions' => ['thumb_100x100', 'services_index_card_1080x1350'],
                    'preview_conversion' => 'thumb_100x100',
                ],
            ],
        ],
        TeamMember::class => [
            'label' => 'Team Member',
            'main_collection' => 'team_photo',
            'collections' => [
                'team_photo' => [
                    'label' => 'Member Photo',
                    'single_file' => true,
                    'max_upload_kb' => 8192,
                    'accept_mime_types' => ['image/jpeg', 'image/png', 'image/webp', 'image/avif'],
                    'conversions' => ['thumb_100x100', 'card_360x240', 'detail_960x960'],
                    'preview_conversion' => 'card_360x240',
                ],
            ],
        ],
        Category::class => [
            'label' => 'Category',
            'collections' => [
                'category_icon' => [
                    'label' => 'Icon Image',
                    'single_file' => true,
                    'max_upload_kb' => 4096,
                    'accept_mime_types' => ['image/jpeg', 'image/png', 'image/webp', 'image/avif', 'image/svg+xml'],
                    'conversions' => ['icon_96x96', 'thumb_100x100'],
                    'preview_conversion' => 'icon_96x96',
                ],
                'category_banner' => [
                    'label' => 'Banner Image',
                    'single_file' => true,
                    'max_upload_kb' => 8192,
                    'accept_mime_types' => ['image/jpeg', 'image/png', 'image/webp', 'image/avif'],
                    'conversions' => ['card_360x240', 'hero_1440x480'],
                    'preview_conversion' => 'card_360x240',
                ],
            ],
        ],
        ContentBlock::class => [
            'label' => 'Content Block',
            'collections' => [
                'block_background' => [
                    'label' => 'Block Background',
                    'single_file' => true,
                    'max_upload_kb' => 8192,
                    'accept_mime_types' => ['image/jpeg', 'image/png', 'image/webp', 'image/avif'],
                    'conversions' => ['card_360x240', 'hero_1440x480'],
                    'preview_conversion' => 'card_360x240',
                ],
                'block_slides' => [
                    'label' => 'Block Slides',
                    'single_file' => false,
                    'only_keep_latest' => 30,
                    'max_upload_kb' => 12288,
                    'accept_mime_types' => ['image/jpeg', 'image/png', 'image/webp', 'image/avif'],
                    'conversions' => ['card_360x240', 'hero_1440x480'],
                    'preview_conversion' => 'card_360x240',
                ],
            ],
        ],
    ],
];
