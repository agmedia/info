<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Message\CareerApplicationManager;
use App\Livewire\Admin\Message\CollaborationAssessmentMessageManager;
use App\Livewire\Admin\Message\ContactMessageManager;
use App\Livewire\Admin\Message\EuFundsQuestionnaireManager;
use App\Livewire\Admin\Message\ResourceDownloadRequestManager;
use App\Livewire\Admin\MessageNotifications;
use App\Models\Content\Resource\ResourceDownloadRequest;
use App\Models\Content\Support\CareerApplication;
use App\Models\Content\Support\ContactMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Tests\TestCase;

class AdminMessageNotificationsFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_header_groups_new_items_and_displays_the_total_badge(): void
    {
        $admin = $this->makeUserWithRole('admin');

        $this->createContactMessage(ContactMessage::FORM_TYPE_CONTACT);
        $this->createContactMessage(ContactMessage::FORM_TYPE_SERVICE_CONTACT);
        $this->createContactMessage(ContactMessage::FORM_TYPE_COLLABORATION_ASSESSMENT);
        $this->createContactMessage(ContactMessage::FORM_TYPE_EU_FUNDS_QUESTIONNAIRE);
        $this->createContactMessage(ContactMessage::FORM_TYPE_CONTACT, ContactMessage::STATUS_READ);
        $this->createCareerApplication();
        $this->createDownloadRequest();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('data-admin-message-notifications', false)
            ->assertSee('data-admin-message-notification-count="6"', false)
            ->assertSee('data-admin-message-group="contact"', false)
            ->assertSee('data-admin-message-group="collaboration-assessment"', false)
            ->assertSee('data-admin-message-group="career"', false)
            ->assertSee('data-admin-message-group="download-requests"', false)
            ->assertSee('data-admin-message-group="eu-funds-questionnaire"', false)
            ->assertSee(__('admin.layout.notifications.title'));
    }

    public function test_header_only_exposes_message_groups_the_user_may_view(): void
    {
        $viewerRole = Bouncer::role()->firstOrCreate([
            'name' => 'contact-message-viewer',
        ], [
            'title' => 'Contact Message Viewer',
        ]);

        Bouncer::allow($viewerRole)->to('admin.access');
        Bouncer::allow($viewerRole)->to('dashboard.view');
        Bouncer::allow($viewerRole)->to('messages.contact.view');

        $viewer = User::factory()->create();
        Bouncer::assign($viewerRole)->to($viewer);
        Bouncer::refreshFor($viewer);

        $this->createContactMessage(ContactMessage::FORM_TYPE_CONTACT);
        $this->createContactMessage(ContactMessage::FORM_TYPE_COLLABORATION_ASSESSMENT);
        $this->createCareerApplication();

        $this->actingAs($viewer)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('data-admin-message-notification-count="1"', false)
            ->assertSee('data-admin-message-group="contact"', false)
            ->assertDontSee('data-admin-message-group="collaboration-assessment"', false)
            ->assertDontSee('data-admin-message-group="career"', false)
            ->assertDontSee('data-admin-message-group="download-requests"', false)
            ->assertDontSee('data-admin-message-group="eu-funds-questionnaire"', false);
    }

    public function test_header_hides_message_notifications_without_an_inquiry_view_ability(): void
    {
        $contentOnlyRole = Bouncer::role()->firstOrCreate([
            'name' => 'content-only',
        ], [
            'title' => 'Content Only',
        ]);
        Bouncer::allow($contentOnlyRole)->to('admin.access');
        Bouncer::allow($contentOnlyRole)->to('dashboard.view');

        $contentOnlyUser = User::factory()->create();
        Bouncer::assign($contentOnlyRole)->to($contentOnlyUser);
        Bouncer::refreshFor($contentOnlyUser);
        $this->createContactMessage(ContactMessage::FORM_TYPE_CONTACT);

        $this->actingAs($contentOnlyUser)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertDontSee('class="admin-message-notifications" data-admin-message-notifications', false);
    }

    public function test_notification_component_recalculates_the_count_when_refresh_event_is_received(): void
    {
        $admin = $this->makeUserWithRole('admin');
        $firstMessage = $this->createContactMessage(ContactMessage::FORM_TYPE_CONTACT);
        $this->createContactMessage(ContactMessage::FORM_TYPE_SERVICE_CONTACT);

        $component = Livewire::actingAs($admin)
            ->test(MessageNotifications::class)
            ->assertSee('data-admin-message-notification-count="2"', false);

        $firstMessage->update([
            'status' => ContactMessage::STATUS_READ,
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
        ]);

        $component
            ->dispatch(MessageNotifications::REFRESH_EVENT)
            ->assertSee('data-admin-message-notification-count="1"', false);
    }

    public function test_each_message_manager_dispatches_the_header_refresh_event_after_a_status_change(): void
    {
        $admin = $this->makeUserWithRole('admin');
        $contact = $this->createContactMessage(ContactMessage::FORM_TYPE_CONTACT);
        $assessment = $this->createContactMessage(ContactMessage::FORM_TYPE_COLLABORATION_ASSESSMENT);
        $career = $this->createCareerApplication();
        $downloadRequest = $this->createDownloadRequest();
        $euFundsQuestionnaire = $this->createContactMessage(ContactMessage::FORM_TYPE_EU_FUNDS_QUESTIONNAIRE);

        Livewire::actingAs($admin)
            ->test(ContactMessageManager::class)
            ->call('markAsRead', $contact->id)
            ->assertDispatched(MessageNotifications::REFRESH_EVENT);

        Livewire::actingAs($admin)
            ->test(CollaborationAssessmentMessageManager::class)
            ->call('markAsRead', $assessment->id)
            ->assertDispatched(MessageNotifications::REFRESH_EVENT);

        Livewire::actingAs($admin)
            ->test(CareerApplicationManager::class)
            ->call('markAsRead', $career->id)
            ->assertDispatched(MessageNotifications::REFRESH_EVENT);

        Livewire::actingAs($admin)
            ->test(ResourceDownloadRequestManager::class)
            ->call('markAsRead', $downloadRequest->id)
            ->assertDispatched(MessageNotifications::REFRESH_EVENT);

        Livewire::actingAs($admin)
            ->test(EuFundsQuestionnaireManager::class)
            ->call('markAsRead', $euFundsQuestionnaire->id)
            ->assertDispatched(MessageNotifications::REFRESH_EVENT);
    }

    private function makeUserWithRole(string $role): User
    {
        $user = User::factory()->create();
        Bouncer::assign($role)->to($user);
        Bouncer::refreshFor($user);

        return $user;
    }

    private function createContactMessage(string $formType, string $status = ContactMessage::STATUS_NEW): ContactMessage
    {
        return ContactMessage::query()->create([
            'name' => 'Test User',
            'email' => uniqid('message-', true).'@example.test',
            'subject' => 'Test inquiry',
            'message' => 'Test message body.',
            'status' => $status,
            'form_type' => $formType,
            'payload' => [
                'form_type' => $formType,
            ],
        ]);
    }

    private function createCareerApplication(): CareerApplication
    {
        return CareerApplication::query()->create([
            'first_name' => 'Career',
            'last_name' => 'Applicant',
            'email' => uniqid('career-', true).'@example.test',
            'message' => 'Career application.',
            'cv_path' => 'career/test-cv.pdf',
            'cv_original_name' => 'test-cv.pdf',
            'status' => CareerApplication::STATUS_NEW,
        ]);
    }

    private function createDownloadRequest(): ResourceDownloadRequest
    {
        return ResourceDownloadRequest::query()->create([
            'document_title' => 'Test document',
            'name' => 'Download User',
            'email' => uniqid('download-', true).'@example.test',
            'status' => ResourceDownloadRequest::STATUS_NEW,
        ]);
    }
}
