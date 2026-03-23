<?php

namespace Tests\Feature\Front;

use App\Mail\ResourceDownloadLinkMail;
use App\Models\Content\Resource\ResourceDocument;
use App\Models\Content\Resource\ResourceDocumentTranslation;
use App\Models\Content\Resource\ResourceDownloadRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ResourceDownloadsFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_resources_index_and_detail_pages_are_available(): void
    {
        $document = $this->createResourceDocument();

        $this->get(route('resources.index'))
            ->assertOk()
            ->assertSee(__('resources.heading'))
            ->assertSee($document->translations->first()->title);

        $this->get(route('resources.show', ['slug' => 'analiza-sektora-proizvodnja-tekstila']))
            ->assertOk()
            ->assertSee('Analiza sektora: Proizvodnja tekstila')
            ->assertSee(__('resources.form.submit'));
    }

    public function test_resource_request_stores_download_request_and_sends_mail(): void
    {
        Mail::fake();

        $this->createResourceDocument();

        $this->post(route('resources.request', ['slug' => 'analiza-sektora-proizvodnja-tekstila']), [
            'name' => 'Ivana Horvat',
            'company' => 'Alpha Test d.o.o.',
            'email' => 'ivana@example.test',
            'phone' => '+38591111222',
            'accept_terms' => '1',
        ])->assertRedirect(route('resources.show', ['slug' => 'analiza-sektora-proizvodnja-tekstila']).'#resource-request-form');

        $request = ResourceDownloadRequest::query()->latest('id')->first();

        $this->assertNotNull($request);
        $this->assertSame('Ivana Horvat', $request->name);
        $this->assertSame('ivana@example.test', $request->email);
        $this->assertSame('Alpha Test d.o.o.', $request->company);
        $this->assertSame(ResourceDownloadRequest::STATUS_NEW, $request->status);
        $this->assertSame('Analiza sektora: Proizvodnja tekstila', $request->document_title);

        Mail::assertSent(ResourceDownloadLinkMail::class, function (ResourceDownloadLinkMail $mail): bool {
            return $mail->hasTo('ivana@example.test')
                && $mail->downloadRequest->document_title === 'Analiza sektora: Proizvodnja tekstila';
        });
    }

    private function createResourceDocument(): ResourceDocument
    {
        $document = ResourceDocument::query()->create([
            'code' => 'analiza-sektora-proizvodnja-tekstila',
            'group_code' => 'sector-analysis',
            'is_active' => true,
            'sort_order' => 1,
            'download_url' => 'https://alphacapitalis.com/wp-content/uploads/2024/01/Analiza-sektora_C13_2024.pdf',
        ]);

        ResourceDocumentTranslation::query()->create([
            'document_id' => $document->id,
            'locale' => 'hr',
            'title' => 'Analiza sektora: Proizvodnja tekstila',
            'slug' => 'analiza-sektora-proizvodnja-tekstila',
            'excerpt' => 'Sektorska analiza tekstilne proizvodnje s ključnim financijskim uvidima.',
        ]);

        return $document->load('translations');
    }
}
