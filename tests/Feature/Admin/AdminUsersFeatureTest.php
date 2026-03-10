<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\User\Form as UserForm;
use App\Models\User;
use App\Models\User\UserAddress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

class AdminUsersFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_privileged_admin_can_open_admin_users_index_and_edit_page(): void
    {
        $admin = $this->makeUserWithRole('admin');
        $target = $this->makeUserWithRole('editor');

        $this->actingAs($admin)
            ->get('/admin/users')
            ->assertOk()
            ->assertSee('Admin Users');

        $this->actingAs($admin)
            ->get('/admin/users/'.$target->id.'/edit')
            ->assertOk()
            ->assertSee('Edit Admin User');
    }

    public function test_editor_cannot_open_admin_users_index_or_edit_page(): void
    {
        $editor = $this->makeUserWithRole('editor');
        $target = $this->makeUserWithRole('admin');

        $this->actingAs($editor)->get('/admin/users')->assertForbidden();
        $this->actingAs($editor)->get('/admin/users/'.$target->id.'/edit')->assertForbidden();
    }

    public function test_removed_customer_only_pages_are_not_available(): void
    {
        $admin = $this->makeUserWithRole('admin');

        $this->actingAs($admin)->get('/admin/users/groups')->assertNotFound();
        $this->actingAs($admin)->get('/admin/users/activity')->assertNotFound();
    }

    public function test_index_only_lists_admin_accounts_and_hides_customer_users(): void
    {
        $admin = $this->makeUserWithRole('admin');
        $editor = $this->makeUserWithRole('editor');
        $customer = $this->makeUserWithRole('customer');

        $this->actingAs($admin)
            ->get('/admin/users')
            ->assertOk()
            ->assertSee($admin->email)
            ->assertSee($editor->email)
            ->assertDontSee($customer->email);
    }

    public function test_customer_user_cannot_be_opened_in_admin_edit_form(): void
    {
        $admin = $this->makeUserWithRole('admin');
        $customer = $this->makeUserWithRole('customer');

        $this->actingAs($admin)
            ->get('/admin/users/'.$customer->id.'/edit')
            ->assertNotFound();
    }

    public function test_admin_can_edit_admin_user_and_change_role(): void
    {
        $admin = $this->makeUserWithRole('admin');
        $target = $this->makeUserWithRole('editor');

        Livewire::actingAs($admin)
            ->test(UserForm::class, ['userId' => $target->id])
            ->set('form.name', 'Edited Admin')
            ->set('form.email', 'edited.admin@example.test')
            ->set('form.role', 'admin')
            ->set('form.email_verified', true)
            ->set('form.password', 'new-password-123')
            ->set('form.password_confirmation', 'new-password-123')
            ->set('form.profile.first_name', 'Ana')
            ->set('form.profile.last_name', 'Admin')
            ->set('form.profile.phone', '+38591111222')
            ->call('save')
            ->assertRedirect(route('admin.users'));

        $target = $target->fresh();

        $this->assertSame('Edited Admin', $target?->name);
        $this->assertSame('edited.admin@example.test', $target?->email);
        $this->assertNotNull($target?->email_verified_at);
        $this->assertTrue((bool) $target?->isA('admin'));

        $this->assertDatabaseHas('user_profiles', [
            'user_id' => $target->id,
            'first_name' => 'Ana',
            'last_name' => 'Admin',
            'phone' => '+38591111222',
        ]);
    }

    public function test_saving_admin_user_clears_legacy_addresses(): void
    {
        $admin = $this->makeUserWithRole('admin');
        $target = $this->makeUserWithRole('editor');

        UserAddress::query()->create([
            'user_id' => $target->id,
            'type' => UserAddress::TYPE_BILLING,
            'address_line_1' => 'Billing Street 1',
            'city' => 'Zagreb',
            'postal_code' => '10000',
            'country_code' => 'HR',
        ]);

        UserAddress::query()->create([
            'user_id' => $target->id,
            'type' => UserAddress::TYPE_SHIPPING,
            'address_line_1' => 'Shipping Street 2',
            'city' => 'Kutina',
            'postal_code' => '44320',
            'country_code' => 'HR',
        ]);

        Livewire::actingAs($admin)
            ->test(UserForm::class, ['userId' => $target->id])
            ->set('form.name', 'Editor Cleaned')
            ->set('form.email', 'editor.cleaned@example.test')
            ->set('form.role', 'editor')
            ->call('save')
            ->assertRedirect(route('admin.users'));

        $this->assertDatabaseMissing('user_addresses', [
            'user_id' => $target->id,
            'type' => UserAddress::TYPE_BILLING,
        ]);
        $this->assertDatabaseMissing('user_addresses', [
            'user_id' => $target->id,
            'type' => UserAddress::TYPE_SHIPPING,
        ]);
    }

    public function test_edit_form_prefers_superadmin_when_user_has_multiple_roles(): void
    {
        $admin = $this->makeUserWithRole('superadmin');
        $target = $this->makeUserWithRole('admin');
        Bouncer::assign('superadmin')->to($target);

        Livewire::actingAs($admin)
            ->test(UserForm::class, ['userId' => $target->id])
            ->assertSet('form.role', 'superadmin');
    }

    public function test_admin_cannot_manage_superadmin_user(): void
    {
        $admin = $this->makeUserWithRole('admin');
        $superadmin = $this->makeUserWithRole('superadmin');

        $this->actingAs($admin)
            ->get('/admin/users/'.$superadmin->id.'/edit')
            ->assertForbidden();
    }

    public function test_role_select_hides_superadmin_for_admin_and_shows_for_superadmin(): void
    {
        $admin = $this->makeUserWithRole('admin');
        $superadmin = $this->makeUserWithRole('superadmin');
        $target = $this->makeUserWithRole('editor');

        Livewire::actingAs($admin)
            ->test(UserForm::class, ['userId' => $target->id])
            ->assertDontSee('Super Administrator');

        Livewire::actingAs($superadmin)
            ->test(UserForm::class, ['userId' => $target->id])
            ->assertSee('Super Administrator');
    }

    public function test_edit_form_prefers_lowest_role_id_when_multiple_roles_are_assigned(): void
    {
        $admin = $this->makeUserWithRole('admin');
        $target = $this->makeUserWithRole('admin');

        Bouncer::role()->firstOrCreate(['name' => 'superadministrator'], ['title' => 'Super Administrator']);
        Bouncer::assign('superadministrator')->to($target);

        Livewire::actingAs($admin)
            ->test(UserForm::class, ['userId' => $target->id])
            ->assertSet('form.role', 'admin');
    }

    public function test_admin_edit_logs_activity_without_customer_group_payload(): void
    {
        $admin = $this->makeUserWithRole('admin');
        $target = $this->makeUserWithRole('editor');

        Livewire::actingAs($admin)
            ->test(UserForm::class, ['userId' => $target->id])
            ->set('form.name', 'Audit Admin')
            ->set('form.email', 'audit.admin@example.test')
            ->set('form.role', 'editor')
            ->set('form.profile.first_name', 'Audit')
            ->set('form.profile.last_name', 'User')
            ->call('save')
            ->assertRedirect(route('admin.users'));

        $activity = Activity::query()
            ->where('log_name', 'admin_users')
            ->where('subject_type', User::class)
            ->where('subject_id', $target->id)
            ->latest('id')
            ->first();

        $this->assertNotNull($activity);
        $this->assertSame('updated', $activity->event);
        $this->assertNull($activity->getExtraProperty('groups'));
        $this->assertNull($activity->getExtraProperty('billing_address'));
        $this->assertNull($activity->getExtraProperty('shipping_address'));
    }

    private function makeUserWithRole(string $role): User
    {
        Bouncer::role()->firstOrCreate(['name' => 'superadmin'], ['title' => 'Super Administrator']);
        Bouncer::role()->firstOrCreate(['name' => 'admin'], ['title' => 'Administrator']);
        Bouncer::role()->firstOrCreate(['name' => 'editor'], ['title' => 'Editor']);
        Bouncer::role()->firstOrCreate(['name' => 'customer'], ['title' => 'Customer']);

        $user = User::factory()->create();
        Bouncer::assign($role)->to($user);

        return $user;
    }
}
