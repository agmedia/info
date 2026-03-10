<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Content\Comment\Manager as CommentManager;
use App\Livewire\Admin\Content\Faq\Form as FaqForm;
use App\Models\Content\Support\Comment;
use App\Models\Content\Support\Faq;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Tests\TestCase;

class ContentSupportFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_faq(): void
    {
        $user = $this->makeAdminUser();

        Livewire::actingAs($user)
            ->test(FaqForm::class)
            ->set('form.code', 'faq-test-1')
            ->set('groupCodeSelection', '__custom__')
            ->set('customGroupCode', 'support')
            ->set('form.is_active', true)
            ->set('form.is_featured', true)
            ->set('form.sort_order', 5)
            ->set('form.locale', 'en')
            ->set('form.question', 'How do I contact support?')
            ->set('form.slug', 'how-do-i-contact-support')
            ->set('form.answer_html', '<p>Use support form.</p>')
            ->call('save')
            ->assertRedirect(route('admin.content.faqs.index', ['locale' => 'en']));

        $faq = Faq::query()->where('code', 'faq-test-1')->first();
        $this->assertNotNull($faq);
        $this->assertSame('support', $faq->group_code);
        $this->assertTrue((bool) $faq->is_featured);
        $this->assertSame(
            'How do I contact support?',
            (string) $faq->translation('en')->first()?->question
        );
    }

    public function test_admin_faq_form_offers_existing_groups_and_allows_creating_new_one(): void
    {
        $user = $this->makeAdminUser();

        Faq::query()->create([
            'code' => 'faq-existing-group',
            'group_code' => 'obiteljski-biznis',
            'is_active' => true,
        ]);

        Livewire::actingAs($user)
            ->test(FaqForm::class)
            ->assertSet('existingGroupCodes.0', 'general')
            ->assertSee('obiteljski-biznis')
            ->set('groupCodeSelection', '__custom__')
            ->set('customGroupCode', 'Nova Grupa 2026')
            ->set('form.code', 'faq-test-2')
            ->set('form.is_active', true)
            ->set('form.locale', 'en')
            ->set('form.question', 'Can we define a new FAQ group?')
            ->set('form.slug', 'can-we-define-a-new-faq-group')
            ->call('save')
            ->assertRedirect(route('admin.content.faqs.index', ['locale' => 'en']));

        $faq = Faq::query()->where('code', 'faq-test-2')->first();

        $this->assertNotNull($faq);
        $this->assertSame('nova-grupa-2026', $faq->group_code);
    }

    public function test_admin_can_create_homepage_comment(): void
    {
        $user = $this->makeAdminUser();

        Livewire::actingAs($user)
            ->test(CommentManager::class)
            ->set('form.locale', 'hr')
            ->set('form.author_name', 'Ivana Test')
            ->set('form.company', 'Palma D.O.O.')
            ->set('form.body', 'Odlicna suradnja i vrlo jasna komunikacija.')
            ->set('form.rating', 5)
            ->set('form.is_featured', true)
            ->call('createComment');

        $comment = Comment::query()->latest('id')->first();

        $this->assertNotNull($comment);
        $this->assertNull($comment->commentable_type);
        $this->assertNull($comment->commentable_id);
        $this->assertSame('Ivana Test', $comment->author_name);
        $this->assertNull($comment->author_email);
        $this->assertSame('hr', $comment->locale);
        $this->assertSame(Comment::STATUS_APPROVED, $comment->status);
        $this->assertSame(5, $comment->rating);
        $this->assertTrue((bool) $comment->is_featured);
        $this->assertSame('Palma D.O.O.', $comment->payload['company'] ?? null);
        $this->assertSame($user->id, $comment->reviewed_by);
        $this->assertNotNull($comment->reviewed_at);
    }

    public function test_admin_can_moderate_comment_status(): void
    {
        $user = $this->makeAdminUser();

        $comment = Comment::query()->create([
            'user_id' => $user->id,
            'author_name' => 'Tester',
            'author_email' => 'tester@example.test',
            'locale' => 'en',
            'body' => 'Pending moderation.',
            'status' => Comment::STATUS_PENDING,
        ]);

        Livewire::actingAs($user)
            ->test(CommentManager::class)
            ->call('approve', $comment->id);

        $comment->refresh();
        $this->assertSame(Comment::STATUS_APPROVED, $comment->status);
        $this->assertSame($user->id, $comment->reviewed_by);
        $this->assertNotNull($comment->reviewed_at);
    }

    private function makeAdminUser(): User
    {
        $user = User::factory()->create();

        Bouncer::role()->firstOrCreate(['name' => 'admin']);
        Bouncer::assign('admin')->to($user);

        return $user;
    }
}
