<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Message\CollaborationAssessmentMessageManager;
use App\Models\Content\Support\ContactMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
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

    public function test_admin_displays_advisory_answers_and_can_download_a_private_attachment(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('contact-message-attachments/2026/09/brief.pdf', 'proposal brief');

        $user = $this->makeAdminUser();
        $message = ContactMessage::query()->create($this->messagePayload([
            'name' => 'Ivana Horvat, direktorica',
            'email' => 'advisory@example.test',
            'subject' => 'Zahtjev za ponudu',
            'message' => "Usluga: Savjetovanje\nOdabir savjetodavne usluge: Prodaja poduzeća",
            'payload' => [
                'form_type' => ContactMessage::FORM_TYPE_COLLABORATION_ASSESSMENT,
                'service' => 'advisory',
                'company' => 'Savjetovanje Klijent d.o.o.',
                'answers' => [
                    'service' => 'advisory',
                    'advisory_contact_person' => 'Ivana Horvat, direktorica',
                    'advisory_contact_email' => 'advisory@example.test',
                    'advisory_contact_phone' => '+385991234567',
                    'advisory_quote_company_name' => 'Savjetovanje Klijent d.o.o.',
                    'advisory_quote_company_activity' => 'Proizvodnja',
                    'advisory_quote_company_revenue' => '2_5m',
                    'advisory_quote_company_employees' => '10_50',
                    'advisory_service' => 'company_sale',
                    'advisory_reason' => 'Priprema za prodaju.',
                    'advisory_deadline' => 'three_to_six_months',
                    'advisory_sale_share' => '75',
                    'advisory_sale_buyer_identified' => 'no',
                    'advisory_sale_closing_timeline' => 'Do kraja godine',
                    'advisory_target_relation' => 'same',
                ],
                'attachments' => [[
                    'disk' => 'local',
                    'path' => 'contact-message-attachments/2026/09/brief.pdf',
                    'name' => 'brief.pdf',
                    'mime' => 'application/pdf',
                    'size' => 14,
                ]],
            ],
        ]));

        $this->actingAs($user)
            ->get(route('admin.messages.collaboration-assessment.index'))
            ->assertOk()
            ->assertSee(__('assessment.services.advisory'))
            ->assertSee('Savjetovanje Klijent d.o.o.')
            ->assertSee(__('assessment.values.advisory_services.company_sale'))
            ->assertSee('Priprema za prodaju.')
            ->assertSee('brief.pdf');

        $this->actingAs($user)
            ->get(route('admin.messages.collaboration-assessment.attachment', [
                'contactMessage' => $message,
                'attachment' => 0,
            ]))
            ->assertOk()
            ->assertDownload('brief.pdf');
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
