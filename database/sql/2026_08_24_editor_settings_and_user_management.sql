-- Editor access to Site Settings, Admin Appearance, User Settings and editor-user creation.
-- MySQL/MariaDB; safe to import repeatedly through phpMyAdmin after deploying the matching code.

START TRANSACTION;

INSERT INTO abilities (name, title, entity_id, entity_type, only_owned, options, scope, created_at, updated_at)
SELECT
    'users.editor.create',
    'Create editor users',
    NULL,
    NULL,
    0,
    JSON_OBJECT('group', 'users.core'),
    NULL,
    NOW(),
    NOW()
WHERE NOT EXISTS (
    SELECT 1
    FROM abilities
    WHERE name = 'users.editor.create'
      AND entity_id IS NULL
      AND entity_type IS NULL
      AND scope IS NULL
);

INSERT INTO abilities (name, title, entity_id, entity_type, only_owned, options, scope, created_at, updated_at)
SELECT
    'settings.system.imports.manage',
    'Manage content imports',
    NULL,
    NULL,
    0,
    JSON_OBJECT('group', 'settings.system'),
    NULL,
    NOW(),
    NOW()
WHERE NOT EXISTS (
    SELECT 1
    FROM abilities
    WHERE name = 'settings.system.imports.manage'
      AND entity_id IS NULL
      AND entity_type IS NULL
      AND scope IS NULL
);

UPDATE permissions AS permission_row
INNER JOIN roles AS role_row
    ON role_row.id = permission_row.entity_id
   AND permission_row.entity_type = 'roles'
   AND role_row.scope IS NULL
INNER JOIN abilities AS ability_row
    ON ability_row.id = permission_row.ability_id
   AND ability_row.entity_id IS NULL
   AND ability_row.entity_type IS NULL
   AND ability_row.scope IS NULL
SET permission_row.forbidden = 0
WHERE (
    role_row.name = 'admin'
    AND ability_row.name IN (
        'users.list.view',
        'users.editor.create',
        'settings.system.admin_appearance.manage',
        'settings.system.store.manage',
        'settings.system.imports.manage',
        'settings.user.manage'
    )
) OR (
    role_row.name = 'editor'
    AND ability_row.name IN (
        'users.list.view',
        'users.editor.create',
        'settings.system.admin_appearance.manage',
        'settings.system.store.manage',
        'settings.user.manage'
    )
);

INSERT INTO permissions (ability_id, entity_id, entity_type, forbidden, scope)
SELECT
    ability_row.id,
    role_row.id,
    'roles',
    0,
    NULL
FROM roles AS role_row
INNER JOIN abilities AS ability_row
    ON ability_row.entity_id IS NULL
   AND ability_row.entity_type IS NULL
   AND ability_row.scope IS NULL
WHERE role_row.scope IS NULL
  AND (
    (
      role_row.name = 'admin'
      AND ability_row.name IN (
          'users.list.view',
          'users.editor.create',
          'settings.system.admin_appearance.manage',
          'settings.system.store.manage',
          'settings.system.imports.manage',
          'settings.user.manage'
      )
    ) OR (
      role_row.name = 'editor'
      AND ability_row.name IN (
          'users.list.view',
          'users.editor.create',
          'settings.system.admin_appearance.manage',
          'settings.system.store.manage',
          'settings.user.manage'
      )
    )
  )
  AND NOT EXISTS (
      SELECT 1
      FROM permissions AS existing_permission
      WHERE existing_permission.ability_id = ability_row.id
        AND existing_permission.entity_id = role_row.id
        AND existing_permission.entity_type = 'roles'
        AND existing_permission.scope IS NULL
  );

COMMIT;
