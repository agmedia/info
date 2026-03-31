<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Message\EuFundsQuestionnaireManager;
use App\Models\Content\Support\ContactMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Tests\TestCase;

class EuFundsQuestionnaireMessagesFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_eu_funds_questionnaire_page(): void
    {
        $user = $this->makeAdminUser();

        ContactMessage::query()->create($this->messagePayload([
            'name' => 'Ivana Horvat',
            'email' => 'ivana@example.test',
            'message' => "Naziv poduzeća: Kreativni studio d.o.o.\nOIB poduzeća: 12345678901",
            'payload' => [
                'form_type' => ContactMessage::FORM_TYPE_EU_FUNDS_QUESTIONNAIRE,
                'answers' => [
                    'company_name' => 'Kreativni studio d.o.o.',
                    'company_oib' => '12345678901',
                    'company_activity' => '90.03 Umjetničko stvaralaštvo',
                    'employee_count' => '10-49',
                    'related_companies' => 'Da',
                    'project_sectors' => ['Kreativne industrije', 'Informacije i komunikacije (ICT)'],
                    'project_sector_other' => null,
                    'investment_location' => 'Zagreb',
                    'planned_costs' => ['Opremanje (strojevi, alati, oprema)', 'Digitalizacija i nabava IKT opreme - softver i hardver'],
                    'investment_amount' => '100.000,00 - 500.000,00 EUR',
                    'interested_services' => ['Kredite s nižom kamatnom stopom i/ili otpisom dijela glavnice'],
                    'additional_notes' => 'Povezano društvo: Studio projekt d.o.o.',
                    'contact_name' => 'Ivana Horvat',
                    'contact_phone' => '+38591111222',
                    'email' => 'ivana@example.test',
                ],
            ],
        ]));

        $this->actingAs($user)
            ->get(route('admin.messages.eu-funds-questionnaire.index'))
            ->assertOk()
            ->assertSee(__('admin.messages.eu_funds_questionnaire.manager.title'))
            ->assertSee('Ivana Horvat')
            ->assertSee('Kreativni studio d.o.o.');
    }

    public function test_admin_can_mark_eu_funds_questionnaire_as_read(): void
    {
        $user = $this->makeAdminUser();

        $message = ContactMessage::query()->create($this->messagePayload());

        Livewire::actingAs($user)
            ->test(EuFundsQuestionnaireManager::class)
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
            'name' => 'Ivana Horvat',
            'email' => 'eu-funds@example.test',
            'phone' => '+38591111222',
            'subject' => ContactMessage::SUBJECT_EU_FUNDS_QUESTIONNAIRE,
            'message' => "Naziv poduzeća: Kreativni studio d.o.o.\nOIB poduzeća: 12345678901\nPrema Vašoj procjeni, kolika bi bila ukupna vrijednost investicije?: 100.000,00 - 500.000,00 EUR",
            'status' => ContactMessage::STATUS_NEW,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit',
            'payload' => [
                'form_type' => ContactMessage::FORM_TYPE_EU_FUNDS_QUESTIONNAIRE,
                'answers' => [
                    'company_name' => 'Kreativni studio d.o.o.',
                    'company_oib' => '12345678901',
                    'company_activity' => '90.03 Umjetničko stvaralaštvo',
                    'employee_count' => '10-49',
                    'related_companies' => 'Da',
                    'project_sectors' => ['Kreativne industrije', 'Informacije i komunikacije (ICT)'],
                    'project_sector_other' => null,
                    'investment_location' => 'Zagreb',
                    'planned_costs' => ['Opremanje (strojevi, alati, oprema)', 'Digitalizacija i nabava IKT opreme - softver i hardver'],
                    'investment_amount' => '100.000,00 - 500.000,00 EUR',
                    'interested_services' => ['Kredite s nižom kamatnom stopom i/ili otpisom dijela glavnice'],
                    'additional_notes' => 'Povezano društvo: Studio projekt d.o.o.',
                    'contact_name' => 'Ivana Horvat',
                    'contact_phone' => '+38591111222',
                    'email' => 'eu-funds@example.test',
                ],
            ],
        ], $overrides);
    }
}
