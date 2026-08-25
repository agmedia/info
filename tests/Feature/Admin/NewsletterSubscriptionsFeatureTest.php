<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Message\NewsletterSubscriptionManager;
use App\Models\Content\Support\NewsletterSubscription;
use App\Models\User;
use App\Services\Settings\SystemSettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Tests\TestCase;

class NewsletterSubscriptionsFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_newsletter_subscriptions_overview(): void
    {
        $admin = $this->makeUserWithRole('admin');
        NewsletterSubscription::query()->create($this->subscriptionPayload([
            'email' => 'reader@example.test',
            'status' => NewsletterSubscription::STATUS_CONFIRMATION_PENDING,
        ]));
        NewsletterSubscription::query()->create($this->subscriptionPayload([
            'email' => 'local@example.test',
            'provider' => 'none',
            'status' => NewsletterSubscription::STATUS_RECEIVED,
        ]));

        $this->actingAs($admin)
            ->get(route('admin.messages.newsletter.index'))
            ->assertOk()
            ->assertSee(__('admin.messages.newsletter.manager.title'))
            ->assertSee('reader@example.test')
            ->assertSee('local@example.test')
            ->assertSee(__('admin.messages.newsletter.status.received'));

        Livewire::actingAs($admin)
            ->test(NewsletterSubscriptionManager::class)
            ->assertViewHas('totals', static fn (array $totals): bool => $totals['all'] === 2
                && $totals['received'] === 1
                && $totals['awaiting_confirmation'] === 1);
    }

    public function test_editor_can_open_the_read_only_newsletter_overview(): void
    {
        $editor = $this->makeUserWithRole('editor');
        NewsletterSubscription::query()->create($this->subscriptionPayload([
            'email' => 'editor-visible@example.test',
        ]));

        $this->actingAs($editor)
            ->get(route('admin.messages.newsletter.index'))
            ->assertOk()
            ->assertSee(__('admin.messages.newsletter.manager.title'))
            ->assertSee('editor-visible@example.test');
    }

    public function test_manager_filters_by_search_status_and_locale(): void
    {
        $admin = $this->makeUserWithRole('admin');

        NewsletterSubscription::query()->create($this->subscriptionPayload([
            'email' => 'target.en@example.test',
            'locale' => 'en',
            'status' => NewsletterSubscription::STATUS_SUBSCRIBED,
        ]));
        NewsletterSubscription::query()->create($this->subscriptionPayload([
            'email' => 'target.hr@example.test',
            'locale' => 'hr',
            'status' => NewsletterSubscription::STATUS_FAILED,
        ]));
        NewsletterSubscription::query()->create($this->subscriptionPayload([
            'email' => 'other.en@example.test',
            'locale' => 'en',
            'status' => NewsletterSubscription::STATUS_SUBSCRIBED,
        ]));

        Livewire::actingAs($admin)
            ->test(NewsletterSubscriptionManager::class)
            ->set('search', 'target')
            ->set('status', NewsletterSubscription::STATUS_SUBSCRIBED)
            ->set('locale', 'en')
            ->assertSee('target.en@example.test')
            ->assertDontSee('target.hr@example.test')
            ->assertDontSee('other.en@example.test');
    }

    public function test_manager_uses_admin_pagination_setting(): void
    {
        $admin = $this->makeUserWithRole('admin');
        app(SystemSettingsService::class)->put('admin_items_per_page', 5);

        foreach (range(1, 6) as $index) {
            NewsletterSubscription::query()->create($this->subscriptionPayload([
                'email' => "subscriber-{$index}@example.test",
                'last_attempt_at' => now()->subMinutes($index),
            ]));
        }

        Livewire::actingAs($admin)
            ->test(NewsletterSubscriptionManager::class)
            ->assertViewHas('rows', static fn ($rows): bool => $rows->perPage() === 5
                && $rows->total() === 6
                && $rows->count() === 5
                && $rows->hasPages());
    }

    private function makeUserWithRole(string $role): User
    {
        $user = User::factory()->create();
        Bouncer::assign($role)->to($user);

        return $user;
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function subscriptionPayload(array $overrides = []): array
    {
        return array_merge([
            'email' => 'subscriber@example.test',
            'locale' => 'hr',
            'provider' => 'mailchimp',
            'status' => NewsletterSubscription::STATUS_CONFIRMATION_PENDING,
            'provider_member_id' => md5('subscriber@example.test'),
            'subscriber_hash' => md5('subscriber@example.test'),
            'attempts' => 1,
            'last_attempt_at' => now(),
            'last_synced_at' => now(),
            'payload' => ['source' => 'footer'],
        ], $overrides);
    }
}
