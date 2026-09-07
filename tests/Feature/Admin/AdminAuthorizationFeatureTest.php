<?php

namespace Tests\Feature\Admin;

use App\Http\Middleware\EnsureAdminAbility;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class AdminAuthorizationFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_editor_can_remove_a_category_selection_while_editing_a_call(): void
    {
        $editor = $this->makeEditor();

        $this->assertTrue($editor->can('content.calls.update'));
        $this->assertFalse($editor->can('content.calls.delete'));

        $response = app(EnsureAdminAbility::class)->handle(
            $this->livewireRequest($editor, 'removeCategory'),
            fn () => response('ok')
        );

        $this->assertSame(200, $response->getStatusCode());
    }

    public function test_editor_still_cannot_delete_a_call(): void
    {
        $editor = $this->makeEditor();

        try {
            app(EnsureAdminAbility::class)->handle(
                $this->livewireRequest($editor, 'delete'),
                fn () => response('ok')
            );

            $this->fail('The editor must not be allowed to delete a call.');
        } catch (HttpException $exception) {
            $this->assertSame(403, $exception->getStatusCode());
        }
    }

    private function makeEditor(): User
    {
        $editor = User::factory()->create();
        Bouncer::assign('editor')->to($editor);

        return $editor;
    }

    private function livewireRequest(User $user, string $method): Request
    {
        $request = Request::create('/livewire/update', 'POST', [
            'components' => [[
                'snapshot' => json_encode([
                    'memo' => [
                        'path' => 'admin/content/calls/1/edit',
                        'method' => 'GET',
                    ],
                ], JSON_THROW_ON_ERROR),
                'calls' => [[
                    'method' => $method,
                ]],
            ]],
        ]);

        $request->setUserResolver(fn () => $user);
        $request->setRouteResolver(
            fn () => app('router')->getRoutes()->getByName('admin.content.calls.edit')
        );

        return $request;
    }
}
