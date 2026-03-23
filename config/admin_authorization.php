<?php

return [
    'route_rules' => [
        'admin.dashboard' => [
            'view' => ['dashboard.view'],
        ],

        'admin.categories.create' => [
            'view' => ['catalog.categories.create'],
            'mutate' => ['catalog.categories.create'],
        ],
        'admin.categories.edit' => [
            'view' => ['catalog.categories.update'],
            'mutate' => ['catalog.categories.update'],
            'delete' => ['catalog.categories.delete'],
        ],
        'admin.categories' => [
            'view' => ['catalog.categories.view'],
            'mutate' => ['catalog.categories.update', 'catalog.categories.create'],
            'delete' => ['catalog.categories.delete'],
        ],

        'admin.content.blog.create' => [
            'view' => ['content.blog.create'],
            'mutate' => ['content.blog.create'],
        ],
        'admin.content.blog.edit' => [
            'view' => ['content.blog.update'],
            'mutate' => ['content.blog.update'],
            'delete' => ['content.blog.delete'],
        ],
        'admin.content.blog.editor-image.upload' => [
            'mutate' => ['content.blog.create', 'content.blog.update'],
        ],
        'admin.content.blog.*' => [
            'view' => ['content.blog.view'],
        ],

        'admin.content.team.create' => [
            'view' => ['content.team.create'],
            'mutate' => ['content.team.create'],
        ],
        'admin.content.team.edit' => [
            'view' => ['content.team.update'],
            'mutate' => ['content.team.update'],
            'delete' => ['content.team.delete'],
        ],
        'admin.content.team.*' => [
            'view' => ['content.team.view'],
        ],

        'admin.content.glossary.create' => [
            'view' => ['content.glossary.create'],
            'mutate' => ['content.glossary.create'],
        ],
        'admin.content.glossary.edit' => [
            'view' => ['content.glossary.update'],
            'mutate' => ['content.glossary.update'],
            'delete' => ['content.glossary.delete'],
        ],
        'admin.content.glossary.*' => [
            'view' => ['content.glossary.view'],
        ],

        'admin.content.pages.create' => [
            'view' => ['content.pages.create'],
            'mutate' => ['content.pages.create'],
        ],
        'admin.content.pages.edit' => [
            'view' => ['content.pages.update'],
            'mutate' => ['content.pages.update'],
            'delete' => ['content.pages.delete'],
        ],
        'admin.content.pages.*' => [
            'view' => ['content.pages.view'],
        ],

        'admin.content.services.create' => [
            'view' => ['content.services.create'],
            'mutate' => ['content.services.create'],
        ],
        'admin.content.services.edit' => [
            'view' => ['content.services.update'],
            'mutate' => ['content.services.update'],
            'delete' => ['content.services.delete'],
        ],
        'admin.content.services.*' => [
            'view' => ['content.services.view'],
        ],

        'admin.content.faqs.create' => [
            'view' => ['content.faqs.create'],
            'mutate' => ['content.faqs.create'],
        ],
        'admin.content.faqs.edit' => [
            'view' => ['content.faqs.update'],
            'mutate' => ['content.faqs.update'],
            'delete' => ['content.faqs.delete'],
        ],
        'admin.content.faqs.*' => [
            'view' => ['content.faqs.view'],
        ],

        'admin.content.comments.*' => [
            'view' => ['content.comments.view'],
            'mutate' => ['content.comments.moderate'],
            'delete' => ['content.comments.delete'],
        ],

        'admin.content.blocks.create' => [
            'view' => ['content.blocks.create'],
            'mutate' => ['content.blocks.create'],
        ],
        'admin.content.blocks.edit' => [
            'view' => ['content.blocks.update'],
            'mutate' => ['content.blocks.update'],
            'delete' => ['content.blocks.delete'],
        ],
        'admin.content.blocks*' => [
            'view' => ['content.blocks.view'],
            'mutate' => ['content.blocks.update', 'content.blocks.create'],
            'delete' => ['content.blocks.delete'],
        ],

        'admin.content.navigation*' => [
            'view' => ['content.navigation.view'],
            'mutate' => ['content.navigation.update'],
        ],

        'admin.content.slots.create' => [
            'view' => ['content.slots.create'],
            'mutate' => ['content.slots.create'],
        ],
        'admin.content.slots.edit' => [
            'view' => ['content.slots.update'],
            'mutate' => ['content.slots.update'],
            'delete' => ['content.slots.delete'],
        ],
        'admin.content.slots*' => [
            'view' => ['content.slots.view'],
            'mutate' => ['content.slots.update', 'content.slots.create'],
            'delete' => ['content.slots.delete'],
        ],

        'admin.messages.career.*' => [
            'view' => ['messages.career.view'],
            'mutate' => ['messages.career.moderate'],
        ],
        'admin.messages.download-requests.*' => [
            'view' => ['messages.download_requests.view'],
            'mutate' => ['messages.download_requests.moderate'],
        ],

        'admin.settings.system.runtime' => [
            'view' => ['settings.system.runtime.manage'],
            'mutate' => ['settings.system.runtime.manage'],
        ],
        'admin.settings.system.admin-appearance-controls' => [
            'view' => ['settings.system.admin_appearance.manage'],
            'mutate' => ['settings.system.admin_appearance.manage'],
        ],
        'admin.settings.system.store-settings' => [
            'view' => ['settings.system.store.manage'],
            'mutate' => ['settings.system.store.manage'],
        ],
        'admin.settings.system.imports' => [
            'view' => ['settings.system.store.manage'],
            'mutate' => ['settings.system.store.manage'],
        ],
        'admin.settings.local.languages' => [
            'view' => ['settings.local.languages.manage'],
            'mutate' => ['settings.local.languages.manage'],
        ],
        'admin.settings.user.*' => [
            'view' => ['settings.user.manage'],
            'mutate' => ['settings.user.manage'],
        ],

        'admin.users.edit' => [
            'view' => ['users.profile.update'],
            'mutate' => ['users.profile.update'],
        ],
        'admin.users.access' => [
            'view' => ['users.access.manage'],
            'mutate' => ['users.access.manage'],
        ],
        'admin.users' => [
            'view' => ['users.list.view'],
        ],

        'admin.profile' => [
            'view' => ['users.profile.update'],
        ],

        'admin.system.cache.clear' => [
            'mutate' => ['settings.system.runtime.manage'],
        ],
        'admin.system.maintenance.on' => [
            'mutate' => ['settings.system.runtime.manage'],
        ],
        'admin.system.maintenance.off' => [
            'mutate' => ['settings.system.runtime.manage'],
        ],

        'admin.ai.preview' => [
            'mutate' => ['ai.admin.use'],
        ],
        'admin.ai.execute' => [
            'mutate' => ['ai.admin.use'],
        ],
    ],

    'livewire_readonly_methods' => [
        'render',
        'mount',
        'backToList',
        'openPreview',
        'closePreview',
        'sort',
        'clearFilters',
        'cancelEdit',
        'toggleGroup',
        'toggleExpand',
        'refreshState',
    ],

    'livewire_delete_keywords' => [
        'delete',
        'remove',
        'spam',
        'reject',
    ],

    'livewire_mutate_keywords' => [
        'save',
        'create',
        'update',
        'toggle',
        'move',
        'make',
        'approve',
        'apply',
        'upload',
        'copy',
        'add',
        'generate',
        'clear',
        'sync',
    ],
];
