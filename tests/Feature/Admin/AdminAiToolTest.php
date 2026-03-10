<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Tests\TestCase;

class AdminAiToolTest extends TestCase
{
    use RefreshDatabase;

    public function test_ai_preview_returns_a_plan_for_category_command(): void
    {
        $user = $this->makeAdminUser();

        $response = $this->actingAs($user)->postJson('/admin/ai/preview', [
            'prompt' => 'Napravi mi kategoriju Ugljikohidrati unutar Prehrane, dodaj opis i dodaj danas dodane artikle u kategoriju.',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonPath('domain_key', 'category_management')
            ->assertJsonStructure([
                'plan_id',
                'summary',
                'actions',
                'warnings',
                'domain_key',
                'domain_title',
                'function_steps',
                'can_execute',
            ]);

        $this->assertNotEmpty((array) $response->json('actions'));
        $this->assertNotEmpty((array) $response->json('function_steps'));
        $this->assertTrue((bool) $response->json('can_execute'));
    }
    private function makeAdminUser(): User
    {
        $user = User::factory()->create();

        Bouncer::role()->firstOrCreate(['name' => 'admin']);
        Bouncer::assign('admin')->to($user);

        return $user;
    }
}
