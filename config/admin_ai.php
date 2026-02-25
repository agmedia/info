<?php

return [
    'tools' => [
        'ensure_category_path' => true,
        'upsert_category_translation' => true,
        'set_category_state' => true,
    ],

    'domains' => [
        'category_management' => [
            'title' => 'Category Management',
            'summary' => 'Create or update category paths, translation text, and state.',
            'functions' => [
                'ensure_category_path',
                'upsert_category_translation',
                'set_category_state',
            ],
        ],
    ],

    'functions' => [
        'ensure_category_path' => [
            'title' => 'Ensure Category Path',
            'description' => 'Resolves category hierarchy path and creates missing nodes when allowed.',
            'params' => [
                ['key' => 'scope', 'value' => 'catalog | blog | page'],
                ['key' => 'locale', 'value' => 'Locale code used for translation matching/creation.'],
                ['key' => 'path_segments', 'value' => 'Ordered path array, from parent to target leaf.'],
                ['key' => 'create_missing', 'value' => 'If true, missing segments are created.'],
            ],
        ],
        'upsert_category_translation' => [
            'title' => 'Upsert Category Translation',
            'description' => 'Creates or updates localized category text (name, description, meta fields via existing flow).',
            'params' => [
                ['key' => 'scope', 'value' => 'Category scope.'],
                ['key' => 'locale', 'value' => 'Locale to update.'],
                ['key' => 'name', 'value' => 'Target category display name.'],
                ['key' => 'description', 'value' => 'Category description text.'],
            ],
        ],
        'set_category_state' => [
            'title' => 'Set Category State',
            'description' => 'Toggles active/inactive state for the resolved target category.',
            'params' => [
                ['key' => 'is_active', 'value' => 'Boolean target state.'],
            ],
        ],
    ],

    'help' => [
        'title' => 'AI Domain Functions',
        'summary' => 'Admin Agent executes safe, domain-scoped functions. You write intent in plain language; the system builds a tool plan, shows preview, and executes only after confirmation.',
        'sections' => [
            [
                'title' => 'How To Think About Domains',
                'subtitle' => 'A domain is a safety boundary around business operations.',
                'explanation' => [
                    'The agent does not run arbitrary actions. It maps your prompt to domain functions that are explicitly allowlisted.',
                    'Each function has known inputs and predictable side effects.',
                    'Preview step exists to make function plan readable before any writes happen.',
                ],
            ],
            [
                'title' => 'Execution Workflow',
                'subtitle' => 'Use this sequence on every AI request.',
                'explanation' => [
                    'Step 1: Write request in natural language (Croatian or English).',
                    'Step 2: Click Preview and review summary/actions/functions.',
                    'Step 3: Confirm only when plan matches expected scope.',
                    'Step 4: On success, open redirected entity and verify final state.',
                ],
            ],
            [
                'title' => 'Current Domain',
                'subtitle' => 'What is currently implemented and safe to use.',
                'explanation' => [
                    'Category Management domain is active.',
                    'It supports category path creation/upsert, translation updates, and state toggling.',
                    'If request is outside this domain, preview returns fallback guidance instead of forcing unsafe execution.',
                ],
            ],
            [
                'title' => 'Prompt Patterns',
                'subtitle' => 'Examples that parse reliably.',
                'explanation' => [
                    'Croatian: "Napravi mi kategoriju Novosti unutar Bloga i dodaj opis."',
                    'English: "Create category Press under Blog and add description."',
                    'Path format: "Parent > Child > Leaf" is supported for explicit hierarchy.',
                    'Locale hint: "locale hr" or "jezik hr". Scope hint: mention blog or page.',
                ],
            ],
            [
                'title' => 'Safety Rules',
                'subtitle' => 'What protects data from accidental broad changes.',
                'explanation' => [
                    'No execution happens without manual Confirm.',
                    'Only allowlisted functions can run.',
                    'Every execution is logged to admin activity.',
                ],
            ],
            [
                'title' => 'When Request Is Unsupported',
                'subtitle' => 'Expected behavior for out-of-domain prompts.',
                'explanation' => [
                    'Preview returns clear error/fallback notice with developer contact.',
                    'No partial execution is performed when plan cannot be built safely.',
                    'Use unsupported request as input for future domain expansion planning.',
                ],
            ],
        ],
    ],

    'fallback' => [
        'notice' => 'If this action is not possible, contact developers for estimate on delivery time and cost.',
        'contact' => env('ADMIN_AI_DEV_CONTACT', 'dev@agshop.local'),
    ],
];
