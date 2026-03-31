<?php

return [
    'default' => [
        'title' => 'Admin Help',
        'summary' => 'Use this panel to manage content, users, and system controls for the info-site build.',
        'sections' => [
            [
                'title' => 'Working Model',
                'subtitle' => 'How this admin is organized.',
                'explanation' => [
                    'Content Blocks are reusable visual components.',
                    'Slots decide where and when blocks render.',
                    'Pages, Blog, and FAQs hold your long-form content.',
                ],
            ],
            [
                'title' => 'Safe Changes',
                'subtitle' => 'Recommended update flow.',
                'explanation' => [
                    'Edit content in draft-like increments and verify output on frontend pages.',
                    'Use clear block codes and keep templates focused and reusable.',
                    'Prefer media uploads through Admin Media manager over inline base64 assets.',
                ],
            ],
        ],
    ],

    'routes' => [
        'admin.dashboard' => [
            'title' => 'Dashboard',
            'summary' => 'Operational overview for content, users, and recent activity.',
        ],

        'admin.categories*' => [
            'title' => 'Categories',
            'summary' => 'Manage category trees used by pages and blog organization.',
        ],

        'admin.content.blog.*' => [
            'title' => 'Blog',
            'summary' => 'Create and edit blog posts, metadata, and taxonomy links.',
        ],

        'admin.content.calls.*' => [
            'title' => 'Calls',
            'summary' => 'Create and edit EU fund call posts, grouped by current call status.',
        ],

        'admin.content.team.*' => [
            'title' => 'Team',
            'summary' => 'Manage team members, profile copy, contact links, and profile photos.',
        ],

        'admin.content.glossary.*' => [
            'title' => 'Glossary',
            'summary' => 'Manage finance glossary terms used by the Svijet financija page.',
        ],

        'admin.content.pages.*' => [
            'title' => 'Pages',
            'summary' => 'Manage static pages and legal content.',
        ],

        'admin.content.faqs.*' => [
            'title' => 'FAQs',
            'summary' => 'Maintain frequently asked questions by group and locale.',
        ],

        'admin.content.comments.*' => [
            'title' => 'Comments',
            'summary' => 'Moderate user comments across supported content targets.',
        ],

        'admin.content.blocks*' => [
            'title' => 'Content Blocks',
            'summary' => 'Build reusable block templates and assign localized text/media.',
            'sections' => [
                [
                    'title' => 'Builder Flow',
                    'subtitle' => 'Create, tune, and publish a block.',
                    'explanation' => [
                        'Pick a visual type preset.',
                        'Edit body/template in Ace for per-block customization.',
                        'Attach selected items when block type supports item lists.',
                        'Save and place the block via Slots.',
                    ],
                ],
                [
                    'title' => 'Template Data',
                    'subtitle' => 'Variables available in templates.',
                    'params' => [
                        ['key' => '$block', 'value' => 'Current ContentBlock model.'],
                        ['key' => '$translation', 'value' => 'Current locale translation for the block.'],
                        ['key' => '$slot', 'value' => 'Resolved slot metadata for placement context.'],
                        ['key' => '$categories', 'value' => 'Selected categories for category-based blocks.'],
                        ['key' => '$blogs', 'value' => 'Selected blog posts for blog-based blocks.'],
                    ],
                ],
            ],
        ],

        'admin.content.navigation*' => [
            'title' => 'Navigation',
            'summary' => 'Configure primary navigation links and ordering.',
        ],

        'admin.content.slots*' => [
            'title' => 'Content Slots',
            'summary' => 'Attach blocks to placements with optional target and scheduling.',
        ],

        'admin.users' => [
            'title' => 'Admin Users',
            'summary' => 'Search and manage administrator accounts.',
        ],
        'admin.users.edit' => [
            'title' => 'Edit Admin User',
            'summary' => 'Update core admin account data and access flags.',
        ],
        'admin.users.access' => [
            'title' => 'Roles & Abilities',
            'summary' => 'Manage access control matrix for admin users.',
        ],

        'admin.settings.system.runtime' => [
            'title' => 'Runtime Controls',
            'summary' => 'Run cache and maintenance operations safely.',
        ],

        'admin.settings.system.admin-appearance-controls' => [
            'title' => 'Admin Appearance',
            'summary' => 'Tune admin UI behavior and display controls.',
        ],

        'admin.settings.system.store-settings' => [
            'title' => 'Site Settings',
            'summary' => 'Global branding, SEO, schema, and frontend behavior settings.',
        ],

        'admin.messages.eu-funds-questionnaire.*' => [
            'title' => 'EU Funds Questionnaire',
            'summary' => 'Review and moderate submissions collected from the EU Funds questionnaire form.',
        ],

        'admin.settings.local.languages' => [
            'title' => 'Languages',
            'summary' => 'Manage active frontend/admin locales and choose the default language.',
            'sections' => [
                [
                    'title' => 'Language Setup',
                    'subtitle' => 'How to think about language records.',
                    'explanation' => [
                        'Each row is one language definition consumed by frontend locale switching and admin locale options.',
                        'Use a short code (for example en or hr), a full locale (for example en_US), and a readable name.',
                        'Only keep languages active that you truly support with translations to avoid mixed-language pages.',
                    ],
                ],
                [
                    'title' => 'Field Reference',
                    'subtitle' => 'What each key controls.',
                    'params' => [
                        ['key' => 'code', 'value' => 'Short language code used in URLs and switchers.'],
                        ['key' => 'locale', 'value' => 'Laravel/app locale identifier used for translation files.'],
                        ['key' => 'name', 'value' => 'Language label shown in admin and storefront pickers.'],
                        ['key' => 'native_name', 'value' => 'Self name (for example Hrvatski, English).'],
                        ['key' => 'direction', 'value' => 'Text direction (LTR or RTL).'],
                        ['key' => 'is_default', 'value' => 'Primary fallback language for missing translations.'],
                        ['key' => 'is_active', 'value' => 'Controls if language appears in switchers and can be selected.'],
                    ],
                ],
            ],
        ],

        'admin.settings.user.*' => [
            'title' => 'User Settings',
            'summary' => 'Configure user-facing feature switches and account defaults.',
        ],

        'admin.ai.*' => [
            'title' => 'Admin AI',
            'summary' => 'Preview and execute domain-scoped AI function plans.',
        ],
    ],
];
