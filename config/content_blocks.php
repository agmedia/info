<?php

return [
    'types' => [
        'banner' => 'Banner (Static)',
        'categories' => 'Categories Block (Selected)',
        'blogs' => 'Blogs Block (Selected)',
        'hero_single' => 'Hero Single Banner',
        'hero_slider' => 'Hero Slider (multi banner)',
        'blogs_carousel' => 'Blogs Carousel',
        'blog_grid_3' => 'Blog Category Grid (3 Cards)',
        'cards_2' => 'Cards (2 Col)',
        'hero_main' => 'Hero Main',
        'split_message' => 'Split Message (2 Col)',
        'cards_3' => 'Cards (3 Col)',
        'rich_text' => 'Rich Text',
        'cta_banner' => 'CTA Banner',
        'desktop_hero_banner' => 'Desktop Hero Banner',
        'full_width_image_slider' => 'Desktop Full Width Image Slider',
        'dual_image_cta' => 'Desktop Dual Image CTA',
        'mobile_hero_banner' => 'Mobile Hero Banner',
        'hero_highlights_strip' => 'Hero Highlights Strip',
        'home_hero' => 'Home Hero (Alpha)',
        'home_stats' => 'Home Stats (Alpha)',
        'home_services' => 'Home Services (Alpha)',
        'custom' => 'Custom',
    ],

    'placements' => [
        'home.hero' => 'Home Hero',
        'home.stats' => 'Home Stats',
        'home.services' => 'Home Services',
        'home.hero_benefits' => 'Home Hero Benefits',
        'home.before_products' => 'Home Before Content',
        'home.categories' => 'Home Categories',
        'home.after_products' => 'Home After Content',
        'home.bottom' => 'Home Bottom',
        'category.top' => 'Category Top',
        'category.bottom' => 'Category Bottom',
        'blog.top' => 'Blog Top',
        'blog.bottom' => 'Blog Bottom',
        'page.top' => 'Page Top',
        'page.bottom' => 'Page Bottom',
    ],

    'cache' => [
        'ttl_seconds' => 3600,
        'version_key' => 'content_blocks:version',
    ],

    'route_whitelist' => [
        'home',
        'blog.*',
        'faq.*',
        'pages.*',
        'contact.*',
        'admin.*',
    ],

    'view_overrides' => [
        'prefix' => 'front.content-blocks.instances.',
    ],
];
