<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Message\CollaborationAssessmentMessageManager;
use App\Models\Content\Support\ContactMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Tests\TestCase;

class CollaborationAssessmentMessagesFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_collaboration_assessment_page(): void
    {
        $user = $this->makeAdminUser();

        ContactMessage::query()->create($this->messagePayload([
            'name' => 'Alpha Test d.o.o.',
            'email' => 'assessment@example.test',
            'payload' => [
                'form_type' => ContactMessage::FORM_TYPE_COLLABORATION_ASSESSMENT,
                'answers' => [
                    'company_name' => 'Alpha Test d.o.o.',
                    'company_oib' => '12345678901',
                    'activity' => 'Financijsko savjetovanje',
                    'incoming_invoices_monthly' => '24',
                    'outgoing_invoices_monthly' => '18',
                    'bank_accounts_monthly' => '2',
                    'payroll_calculations_monthly' => '6',
                    'inventory_bookkeeping' => 'no',
                    'cost_centers_tracking' => 'yes',
                    'monthly_reporting' => 'yes',
                ],
            ],
        ]));

        $this->actingAs($user)
            ->get(route('admin.messages.collaboration-assessment.index'))
            ->assertOk()
            ->assertSee(__('admin.messages.collaboration_assessment.manager.title'))
            ->assertSee('Alpha Test d.o.o.')
            ->assertSee('12345678901');
    }

    public function test_admin_can_mark_collaboration_assessment_as_read(): void
    {
        $user = $this->makeAdminUser();

        $message = ContactMessage::query()->create($this->messagePayload());

        Livewire::actingAs($user)
            ->test(CollaborationAssessmentMessageManager::class)
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
            'name' => 'Alpha Test d.o.o.',
            'email' => 'assessment@example.test',
            'phone' => '+38591111222',
            'subject' => 'Procjena suradnje',
            'message' => "Ulazni računi mjesečno: 24\nIzlazni računi mjesečno: 18",
            'status' => ContactMessage::STATUS_NEW,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit',
            'payload' => [
                'form_type' => ContactMessage::FORM_TYPE_COLLABORATION_ASSESSMENT,
                'answers' => [
                    'company_name' => 'Alpha Test d.o.o.',
                    'company_oib' => '12345678901',
                    'activity' => 'Financijsko savjetovanje',
                    'incoming_invoices_monthly' => '24',
                    'outgoing_invoices_monthly' => '18',
                    'bank_accounts_monthly' => '2',
                    'payroll_calculations_monthly' => '6',
                    'inventory_bookkeeping' => 'no',
                    'cost_centers_tracking' => 'yes',
                    'monthly_reporting' => 'yes',
                ],
            ],
        ], $overrides);
    }
}
