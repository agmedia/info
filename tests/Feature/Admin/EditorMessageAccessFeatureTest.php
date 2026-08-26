<?php

namespace Tests\Feature\Admin;

use App\Models\Content\Support\ContactMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Silber\Bouncer\Database\Ability;
use Silber\Bouncer\Database\Models;
use Silber\Bouncer\Database\Role;
use Tests\TestCase;

class EditorMessageAccessFeatureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var array<int, string>
     */
    private const EDITOR_MESSAGE_ABILITIES = [
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

    /**
     * @var array<int, string>
     */
    private const MESSAGE_ROUTES = [
        'admin.messages.contact.index',
        'admin.messages.collaboration-assessment.index',
        'admin.messages.career.index',
        'admin.messages.download-requests.index',
        'admin.messages.eu-funds-questionnaire.index',
        'admin.messages.newsletter.index',
    ];

    public function test_editor_can_open_every_message_section_and_sees_the_header_icon(): void
    {
        $editor = $this->makeEditor();

        ContactMessage::query()->create([
            'name' => 'Editor Visible',
            'email' => 'editor-visible@example.test',
            'subject' => 'Editor message access',
            'message' => 'This message should be counted in the editor header.',
            'status' => ContactMessage::STATUS_NEW,
            'form_type' => ContactMessage::FORM_TYPE_CONTACT,
            'payload' => [
                'form_type' => ContactMessage::FORM_TYPE_CONTACT,
            ],
        ]);

        foreach (self::EDITOR_MESSAGE_ABILITIES as $ability) {
            $this->assertTrue($editor->can($ability), "Editor is missing [{$ability}].");
        }

        $dashboard = $this->actingAs($editor)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('class="admin-message-notifications" data-admin-message-notifications', false)
            ->assertSee('data-admin-message-notification-count="1"', false)
            ->assertSee('data-admin-message-group="contact"', false)
            ->assertSee('data-admin-message-group="collaboration-assessment"', false)
            ->assertSee('data-admin-message-group="career"', false)
            ->assertSee('data-admin-message-group="download-requests"', false)
            ->assertSee('data-admin-message-group="eu-funds-questionnaire"', false)
            ->assertDontSee('data-admin-message-group="newsletter"', false);

        foreach (self::MESSAGE_ROUTES as $routeName) {
            $dashboard->assertSee(route($routeName), false);
            $this->actingAs($editor)->get(route($routeName))->assertOk();
        }
    }

    public function test_data_migration_upgrades_an_existing_editor_role_idempotently(): void
    {
        $editorRole = Role::query()->where('name', 'editor')->firstOrFail();
        $abilityIds = Ability::query()
            ->whereIn('name', self::EDITOR_MESSAGE_ABILITIES)
            ->whereNull('entity_id')
            ->whereNull('entity_type')
            ->whereNull('scope')
            ->pluck('id', 'name');
        $permissionsTable = Models::table('permissions');
        $rolesTable = Models::table('roles');

        DB::table($permissionsTable)
            ->where('entity_id', $editorRole->id)
            ->where('entity_type', $rolesTable)
            ->whereIn('ability_id', $abilityIds->values())
            ->delete();

        DB::table($permissionsTable)->insert([
            'ability_id' => $abilityIds->get('messages.contact.view'),
            'entity_id' => $editorRole->id,
            'entity_type' => $rolesTable,
            'forbidden' => true,
            'scope' => null,
        ]);

        $migration = require database_path('migrations/2026_08_26_061500_grant_editor_message_abilities.php');
        $migration->up();
        $migration->up();

        foreach ($abilityIds as $abilityName => $abilityId) {
            $permissions = DB::table($permissionsTable)
                ->where('ability_id', $abilityId)
                ->where('entity_id', $editorRole->id)
                ->where('entity_type', $rolesTable)
                ->whereNull('scope')
                ->get();

            $this->assertCount(1, $permissions, "Duplicate permission for [{$abilityName}].");
            $this->assertFalse((bool) $permissions->first()->forbidden);
        }
    }

    private function makeEditor(): User
    {
        $editor = User::factory()->create();
        Bouncer::assign('editor')->to($editor);
        Bouncer::refreshFor($editor);

        return $editor;
    }
}
