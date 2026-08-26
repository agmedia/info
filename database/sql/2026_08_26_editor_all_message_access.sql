-- Give the editor role access to every admin message section and status workflow.
-- MySQL/MariaDB; safe to import repeatedly through phpMyAdmin.

START TRANSACTION;

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
WHERE role_row.name = 'editor'
  AND ability_row.name IN (
      'messages.contact.view',
      'messages.contact.moderate',
      'messages.collaboration_assessment.view',
      'messages.collaboration_assessment.moderate',
      'messages.career.view',
      'messages.career.moderate',
      'messages.download_requests.view',
      'messages.download_requests.moderate',
      'messages.eu_funds_questionnaire.view',
      'messages.eu_funds_questionnaire.moderate',
      'messages.newsletter.view'
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
WHERE role_row.name = 'editor'
  AND role_row.scope IS NULL
  AND ability_row.name IN (
      'messages.contact.view',
      'messages.contact.moderate',
      'messages.collaboration_assessment.view',
      'messages.collaboration_assessment.moderate',
      'messages.career.view',
      'messages.career.moderate',
      'messages.download_requests.view',
      'messages.download_requests.moderate',
      'messages.eu_funds_questionnaire.view',
      'messages.eu_funds_questionnaire.moderate',
      'messages.newsletter.view'
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
