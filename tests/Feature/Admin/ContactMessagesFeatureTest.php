<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Message\ContactMessageManager;
use App\Models\Content\Support\ContactMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Tests\TestCase;

class ContactMessagesFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_contact_messages_page(): void
    {
        $user = $this->makeAdminUser();

        ContactMessage::query()->create($this->messagePayload([
            'name' => 'Ivana Horvat',
            'email' => 'ivana@example.test',
            'subject' => 'Dogovor sastanka',
            'message' => 'Želim dogovoriti sastanak za prezentaciju usluga.',
            'payload' => [
                'form_type' => ContactMessage::FORM_TYPE_CONTACT,
                'company' => 'Alpha Test d.o.o.',
                'locale' => 'hr',
                'url' => 'https://info.test/contact',
                'source_page' => '/contact',
                'redirect_to' => null,
            ],
        ]));

        ContactMessage::query()->create($this->messagePayload([
            'name' => 'Procjena primjer',
            'email' => 'assessment@example.test',
            'payload' => [
                'form_type' => ContactMessage::FORM_TYPE_COLLABORATION_ASSESSMENT,
                'answers' => [
                    'company_name' => 'Assessment d.o.o.',
                ],
            ],
        ]));

        ContactMessage::query()->create($this->messagePayload([
            'name' => 'Usluga primjer',
            'email' => 'service@example.test',
            'payload' => [
                'form_type' => ContactMessage::FORM_TYPE_SERVICE_CONTACT,
                'company' => 'Service d.o.o.',
                'locale' => 'hr',
                'url' => 'https://info.test/contact',
                'source_page' => '/obiteljski-biznis',
                'redirect_to' => '/obiteljski-biznis#family-business-sastanak',
            ],
        ]));

        $this->actingAs($user)
            ->get(route('admin.messages.contact.index'))
            ->assertOk()
            ->assertSee(__('admin.messages.contact.manager.title'))
            ->assertSee('Ivana Horvat')
            ->assertSee('Dogovor sastanka')
            ->assertDontSee('Procjena primjer')
            ->assertDontSee('Usluga primjer');
    }

    public function test_admin_can_mark_contact_message_as_read(): void
    {
        $user = $this->makeAdminUser();

        $message = ContactMessage::query()->create($this->messagePayload());

        Livewire::actingAs($user)
            ->test(ContactMessageManager::class)
            ->call('markAsRead', $message->id);

        $message->refresh();

        $this->assertSame(ContactMessage::STATUS_READ, $message->status);
        $this->assertSame($user->id, $message->reviewed_by);
        $this->assertNotNull($message->reviewed_at);
    }

    private function makeAdminUser(): User
    {
        $user = User::factory()->create();

        Bouncer::role()->firstOrCreate(['name' => 'admin']);
        Bouncer::assign('admin')->to($user);

        return $user;
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function messagePayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Front Tester',
            'email' => 'front@example.test',
            'phone' => '+38591000000',
            'subject' => 'Kontakt upit',
            'message' => 'Molim povratnu informaciju o vašim uslugama.',
            'status' => ContactMessage::STATUS_NEW,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit',
            'payload' => [
                'form_type' => ContactMessage::FORM_TYPE_CONTACT,
                'company' => 'Alpha Test d.o.o.',
                'locale' => 'hr',
                'url' => 'https://info.test/contact',
                'source_page' => '/contact',
                'redirect_to' => null,
            ],
        ], $overrides);
    }
}
