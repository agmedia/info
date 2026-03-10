<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Content\Team\Form as TeamForm;
use App\Models\Content\Team\TeamMember;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Silber\Bouncer\Database\Ability;
use Tests\TestCase;

class ContentTeamFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_team_page(): void
    {
        $user = $this->makeAdminUser();

        $this->actingAs($user)
            ->get('/admin/content/team')
            ->assertOk()
            ->assertSee('Tim');
    }

    public function test_admin_can_create_team_member(): void
    {
        $user = $this->makeAdminUser();

        Livewire::actingAs($user)
            ->test(TeamForm::class)
            ->set('form.code', 'ivan-horvat')
            ->set('form.locale', 'hr')
            ->set('form.name', 'Ivan Horvat, ovl. rev.')
            ->set('form.position', 'Partner')
            ->set('selectedDepartments', ['Revizija', 'Porezno savjetovanje'])
            ->set('form.description_html', '<p>Vodi tim za reviziju i financijsko savjetovanje.</p>')
            ->set('form.email', 'ivan@example.test')
            ->set('form.facebook_url', 'https://facebook.com/ivan.horvat')
            ->set('form.twitter_url', 'https://x.com/ivan_horvat')
            ->set('form.linkedin_url', 'https://linkedin.com/in/ivan-horvat')
            ->call('save')
            ->assertRedirect(route('admin.content.team.index', ['locale' => 'hr']));

        $member = TeamMember::query()->where('code', 'ivan-horvat')->first();

        $this->assertNotNull($member);
        $this->assertSame('ivan@example.test', $member->email);
        $this->assertSame('https://linkedin.com/in/ivan-horvat', $member->linkedin_url);
        $this->assertSame('Ivan Horvat, ovl. rev.', (string) $member->translation('hr')->first()?->name);
        $this->assertSame('Partner', (string) $member->translation('hr')->first()?->position);
        $this->assertSame("Revizija\nPorezno savjetovanje", (string) $member->translation('hr')->first()?->departments);
    }

    public function test_team_abilities_are_auto_synced_for_existing_admin_role(): void
    {
        $user = $this->makeAdminUser();

        $abilityIds = Ability::query()
            ->whereIn('name', [
                'content.team.view',
                'content.team.create',
                'content.team.update',
                'content.team.delete',
            ])
            ->pluck('id')
            ->all();

        if ($abilityIds !== []) {
            DB::table('permissions')->whereIn('ability_id', $abilityIds)->delete();
            Ability::query()->whereIn('id', $abilityIds)->delete();
        }

        Bouncer::refresh();

        $this->actingAs($user)
            ->get('/admin/content/team')
            ->assertOk();
    }

    private function makeAdminUser(): User
    {
        $user = User::factory()->create();

        Bouncer::role()->firstOrCreate(['name' => 'admin']);
        Bouncer::assign('admin')->to($user);

        return $user;
    }
}
