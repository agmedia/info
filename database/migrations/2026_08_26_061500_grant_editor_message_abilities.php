<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Silber\Bouncer\Database\Models;

return new class extends Migration
{
    /**
     * @var array<int, string>
     */
    private array $abilityNames = [
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
        'messages.newsletter.view',
    ];

    public function up(): void
    {
        $rolesTable = Models::table('roles');
        $abilitiesTable = Models::table('abilities');
        $permissionsTable = Models::table('permissions');

        if (! Schema::hasTable($rolesTable)
            || ! Schema::hasTable($abilitiesTable)
            || ! Schema::hasTable($permissionsTable)) {
            return;
        }

        $editorRole = DB::table($rolesTable)
            ->where('name', 'editor')
            ->whereNull('scope')
            ->first(['id']);

        if (! $editorRole) {
            return;
        }

        $abilities = DB::table($abilitiesTable)
            ->whereIn('name', $this->abilityNames)
            ->whereNull('entity_id')
            ->whereNull('entity_type')
            ->whereNull('scope')
            ->get(['id']);

        foreach ($abilities as $ability) {
            $permissionQuery = DB::table($permissionsTable)
                ->where('ability_id', $ability->id)
                ->where('entity_id', $editorRole->id)
                ->where('entity_type', $rolesTable)
                ->whereNull('scope');

            if ($permissionQuery->exists()) {
                $permissionQuery->update(['forbidden' => false]);

                continue;
            }

            DB::table($permissionsTable)->insert([
                'ability_id' => $ability->id,
                'entity_id' => $editorRole->id,
                'entity_type' => $rolesTable,
                'forbidden' => false,
                'scope' => null,
            ]);
        }
    }

    public function down(): void
    {
        // Access grants are intentionally not revoked automatically because
        // they may have been assigned manually before this migration ran.
    }
};
