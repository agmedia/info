<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Content\Page\Form as PageForm;
use App\Livewire\Admin\Content\Service\Form as ServiceForm;
use App\Livewire\Admin\Media\Manager as MediaManager;
use App\Models\Content\Call\CallPost;
use App\Models\Content\ContentBlock;
use App\Models\Content\Page\InfoPage;
use App\Models\Content\Service\ServicePage;
use App\Models\User;
use App\Support\Content\ServicePageTemplateRegistry;
use App\Support\Media\MediaProfileRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Tests\TestCase;

class MediaUploadsFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        config()->set('media-library.disk_name', 'public');
        config()->set('media-library.queue_conversions_by_default', false);
    }

    public function test_home_content_block_media_uploads_save_metadata_and_delete_cleanly(): void
    {
        $user = $this->makeAdminUser();

        foreach ([
            ['home-alpha-hero-test', 'home_hero'],
            ['home-alpha-stats-test', 'home_stats'],
            ['home-alpha-services-test', 'home_services'],
        ] as $index => [$code, $type]) {
            $block = ContentBlock::query()->create([
                'code' => $code,
                'name' => $code,
                'type' => $type,
                'is_active' => true,
                'payload' => null,
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);

            Livewire::actingAs($user)
                ->test(MediaManager::class, [
                    'modelClass' => ContentBlock::class,
                    'modelId' => $block->id,
                    'locale' => 'en',
                ])
                ->set('uploads.block_slides', [
                    UploadedFile::fake()->image("home-block-$index.jpg", 1600, 900),
                ])
                ->call('uploadCollection', 'block_slides')
                ->assertHasNoErrors();

            $media = $block->fresh()->getFirstMedia('block_slides');

            $this->assertNotNull($media);
            Storage::disk('public')->assertExists($media->getPathRelativeToRoot());

            Livewire::actingAs($user)
                ->test(MediaManager::class, [
                    'modelClass' => ContentBlock::class,
                    'modelId' => $block->id,
                    'locale' => 'en',
                ])
                ->set("meta.$media->id.name", "Homepage block $index")
                ->set("meta.$media->id.alt", "English homepage block image $index")
                ->set("meta.$media->id.caption", "English caption $index")
                ->call('saveMeta', $media->id)
                ->assertHasNoErrors();

            $media->refresh();
            $this->assertSame("English homepage block image $index", data_get($media->custom_properties, 'alt.en'));
            $this->assertSame("English caption $index", data_get($media->custom_properties, 'caption.en'));

            $storedPath = $media->getPathRelativeToRoot();

            Livewire::actingAs($user)
                ->test(MediaManager::class, [
                    'modelClass' => ContentBlock::class,
                    'modelId' => $block->id,
                    'locale' => 'en',
                ])
                ->call('delete', $media->id)
                ->assertHasNoErrors();

            $this->assertNull($block->fresh()->getFirstMedia('block_slides'));
            Storage::disk('public')->assertMissing($storedPath);
        }
    }

    public function test_advisory_and_eu_funds_direct_hero_upload_fields_save_and_restore(): void
    {
        $user = $this->makeAdminUser();
        $advisory = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::ADVISORY)
            ->firstOrFail();
        $euFunds = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::EU_FUNDS)
            ->firstOrFail();

        Livewire::withQueryParams(['section' => 'main'])
            ->actingAs($user)
            ->test(ServiceForm::class, ['servicePageId' => $advisory->id])
            ->set('advisoryHeroImageUpload', UploadedFile::fake()->image('advisory-hero.jpg', 1920, 1080))
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.content.services.index', ['locale' => 'hr']));

        Livewire::withQueryParams(['section' => 'main'])
            ->actingAs($user)
            ->test(ServiceForm::class, ['servicePageId' => $advisory->id])
            ->set('advisoryPandeaLogoUpload', UploadedFile::fake()->image('pandea-logo.png', 600, 300))
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.content.services.index', ['locale' => 'hr']));

        $advisory->refresh();
        $this->assertNotNull($advisoryHero = $advisory->getFirstMedia('service_hero_image'));
        $this->assertNotNull($advisoryLogo = $advisory->getFirstMedia('service_logo'));
        Storage::disk('public')->assertExists($advisoryHero->getPathRelativeToRoot());
        Storage::disk('public')->assertExists($advisoryLogo->getPathRelativeToRoot());

        Livewire::actingAs($user)
            ->test(ServiceForm::class, ['servicePageId' => $advisory->id])
            ->call('removeAdvisoryHeroImage')
            ->call('removeAdvisoryPandeaLogo')
            ->assertHasNoErrors();

        $this->assertNull($advisory->fresh()->getFirstMedia('service_hero_image'));
        $this->assertNull($advisory->fresh()->getFirstMedia('service_logo'));

        Livewire::actingAs($user)
            ->test(ServiceForm::class, ['servicePageId' => $euFunds->id])
            ->set('euFundsHeroImageUpload', UploadedFile::fake()->image('eu-funds-hero.jpg', 1920, 1080))
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.content.services.index', ['locale' => 'hr']));

        $euFunds->refresh();
        $this->assertNotNull($euFundsHero = $euFunds->getFirstMedia('service_hero_image'));
        Storage::disk('public')->assertExists($euFundsHero->getPathRelativeToRoot());

        Livewire::actingAs($user)
            ->test(ServiceForm::class, ['servicePageId' => $euFunds->id])
            ->call('removeEuFundsHeroImage')
            ->assertHasNoErrors();

        $this->assertNull($euFunds->fresh()->getFirstMedia('service_hero_image'));
    }

    public function test_services_index_all_three_card_upload_fields_save_and_restore(): void
    {
        $user = $this->makeAdminUser();
        $services = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::SERVICES_INDEX)
            ->firstOrFail();

        $component = Livewire::actingAs($user)
            ->test(ServiceForm::class, ['servicePageId' => $services->id]);

        foreach (['audit', 'accounting', 'advisory'] as $index => $key) {
            $component->set(
                "landingImageUploads.$key",
                UploadedFile::fake()->image("$key-card.jpg", 1080, 1350)
            );
        }

        $component
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.content.services.index', ['locale' => 'hr']));

        foreach (ServicePageTemplateRegistry::SERVICES_INDEX_CARD_MEDIA_COLLECTIONS as $key => $collection) {
            $media = $services->fresh()->getFirstMedia($collection);
            $this->assertNotNull($media, "The $key card image was not stored.");
            Storage::disk('public')->assertExists($media->getPathRelativeToRoot());

            Livewire::actingAs($user)
                ->test(ServiceForm::class, ['servicePageId' => $services->id])
                ->call('removeServicesIndexCardImage', $key)
                ->assertHasNoErrors();

            $this->assertNull($services->fresh()->getFirstMedia($collection));
        }
    }

    public function test_eu_funds_nested_pdf_upload_fields_save_to_the_selected_links(): void
    {
        $user = $this->makeAdminUser();
        $euFunds = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::EU_FUNDS)
            ->firstOrFail();

        Livewire::withQueryParams(['locale' => 'en'])
            ->actingAs($user)
            ->test(ServiceForm::class, ['servicePageId' => $euFunds->id])
            ->set('form.translation_payload.resources.cards.0.primary_link.type', 'pdf')
            ->set('form.translation_payload.laws.cards.0.secondary_link.type', 'pdf')
            ->set('assetUploads.resources_cards_0_primary_link_path', UploadedFile::fake()->create('questionnaire.pdf', 80, 'application/pdf'))
            ->set('assetUploads.laws_cards_0_secondary_link_path', UploadedFile::fake()->create('brochure.pdf', 90, 'application/pdf'))
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.content.services.index', ['locale' => 'en']));

        $payload = $euFunds->fresh()->translation('en')->firstOrFail()->payload;

        foreach ([
            'resources.cards.0.primary_link.path',
            'laws.cards.0.secondary_link.path',
        ] as $path) {
            $storedPath = (string) data_get($payload, $path, '');
            $this->assertStringStartsWith('service-assets/eu-funds/', $storedPath);
            Storage::disk('public')->assertExists($storedPath);
        }
    }

    public function test_about_and_career_hero_uploads_save_english_alt_text_and_restore(): void
    {
        $user = $this->makeAdminUser();

        foreach ([
            ['about-us', 'about_hero_image', 'form.about_content.hero.image_alt', 'English About hero'],
            ['career', 'career_hero_image', 'form.career_content.intro.image_alt', 'English Career hero'],
        ] as [$code, $collection, $altPath, $alt]) {
            $page = InfoPage::query()->where('code', $code)->firstOrFail();
            $page->clearMediaCollection($collection);

            Livewire::withQueryParams(['locale' => 'en'])
                ->actingAs($user)
                ->test(PageForm::class, ['pageId' => $page->id])
                ->set($altPath, $alt)
                ->set('pageHeroImageUpload', UploadedFile::fake()->image("$code-hero.jpg", 1440, 1059))
                ->call('save')
                ->assertHasNoErrors()
                ->assertRedirect(route('admin.content.pages.index', ['locale' => 'en']));

            $media = $page->fresh()->getFirstMedia($collection);
            $this->assertNotNull($media);
            $this->assertSame($alt, data_get($media->custom_properties, 'alt.en'));
            Storage::disk('public')->assertExists($media->getPathRelativeToRoot());

            Livewire::withQueryParams(['locale' => 'en'])
                ->actingAs($user)
                ->test(PageForm::class, ['pageId' => $page->id])
                ->call('removePageHeroImage')
                ->assertHasNoErrors();

            $this->assertNull($page->fresh()->getFirstMedia($collection));
        }
    }

    public function test_career_gallery_and_call_media_manager_uploads_are_valid(): void
    {
        $user = $this->makeAdminUser();
        $career = InfoPage::query()->where('code', 'career')->firstOrFail();
        $originalCareerMediaIds = $career->getMedia('career_gallery_images')->pluck('id')->all();
        $call = CallPost::query()->create([
            'code' => 'media-upload-test-call',
            'is_active' => true,
            'is_featured' => false,
            'published_at' => now(),
            'sort_order' => 0,
            'payload' => null,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        Livewire::actingAs($user)
            ->test(MediaManager::class, [
                'modelClass' => InfoPage::class,
                'modelId' => $career->id,
                'locale' => 'en',
                'onlyCollections' => ['career_gallery_images'],
            ])
            ->set('uploads.career_gallery_images', [
                UploadedFile::fake()->image('career-gallery.jpg', 1200, 800),
            ])
            ->call('uploadCollection', 'career_gallery_images')
            ->assertHasNoErrors();

        $careerMedia = $career->fresh()
            ->getMedia('career_gallery_images')
            ->first(fn ($media) => str_starts_with($media->file_name, 'career-gallery-'));
        $this->assertNotNull($careerMedia);

        Livewire::actingAs($user)
            ->test(MediaManager::class, [
                'modelClass' => InfoPage::class,
                'modelId' => $career->id,
                'locale' => 'en',
                'onlyCollections' => ['career_gallery_images'],
            ])
            ->call('delete', $careerMedia->id)
            ->assertHasNoErrors();

        Livewire::actingAs($user)
            ->test(MediaManager::class, [
                'modelClass' => CallPost::class,
                'modelId' => $call->id,
                'locale' => 'en',
            ])
            ->set('uploads.call_cover', UploadedFile::fake()->image('call-cover.jpg', 1440, 900))
            ->call('uploadCollection', 'call_cover')
            ->assertHasNoErrors();

        $callMedia = $call->fresh()->getFirstMedia('call_cover');
        $this->assertNotNull($callMedia);
        Storage::disk('public')->assertExists($callMedia->getPathRelativeToRoot());

        Livewire::actingAs($user)
            ->test(MediaManager::class, [
                'modelClass' => CallPost::class,
                'modelId' => $call->id,
                'locale' => 'en',
            ])
            ->call('delete', $callMedia->id)
            ->assertHasNoErrors();

        $this->assertSame(
            $originalCareerMediaIds,
            $career->fresh()->getMedia('career_gallery_images')->pluck('id')->all()
        );
        $this->assertNull($call->fresh()->getFirstMedia('call_cover'));
    }

    public function test_service_media_manager_accepts_the_svg_format_advertised_by_the_cms(): void
    {
        $user = $this->makeAdminUser();
        $advisory = ServicePage::query()
            ->where('template_key', ServicePageTemplateRegistry::ADVISORY)
            ->firstOrFail();

        $svg = UploadedFile::fake()->createWithContent(
            'service-logo.svg',
            '<svg xmlns="http://www.w3.org/2000/svg" width="240" height="120" viewBox="0 0 240 120"><rect width="240" height="120" fill="#082032"/><circle cx="120" cy="60" r="32" fill="#c9a45c"/></svg>'
        );

        Livewire::actingAs($user)
            ->test(MediaManager::class, [
                'modelClass' => ServicePage::class,
                'modelId' => $advisory->id,
                'locale' => 'en',
                'onlyCollections' => ['service_logo'],
            ])
            ->set('uploads.service_logo', $svg)
            ->call('uploadCollection', 'service_logo')
            ->assertHasNoErrors();

        $media = $advisory->fresh()->getFirstMedia('service_logo');
        $this->assertNotNull($media);
        $this->assertSame('image/svg+xml', $media->mime_type);
        $this->assertTrue($media->hasGeneratedConversion('thumb_100x100'));
        $this->assertTrue($media->hasGeneratedConversion('detail_960x960'));
        Storage::disk('public')->assertExists($media->getPathRelativeToRoot());
    }

    public function test_content_block_media_manager_handles_avif_capability_without_a_conversion_crash(): void
    {
        if (! class_exists(\Imagick::class) || \Imagick::queryFormats('AVIF') === []) {
            $this->markTestSkipped('The local ImageMagick build cannot create an AVIF fixture.');
        }

        $user = $this->makeAdminUser();
        $block = ContentBlock::query()->create([
            'code' => 'avif-upload-test',
            'name' => 'AVIF upload test',
            'type' => 'home_hero',
            'is_active' => true,
            'payload' => null,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $image = new \Imagick;
        $image->newImage(320, 180, new \ImagickPixel('#082032'));
        $image->setImageFormat('avif');
        $avif = UploadedFile::fake()->createWithContent('block-background.avif', $image->getImagesBlob());
        $image->clear();

        $component = Livewire::actingAs($user)
            ->test(MediaManager::class, [
                'modelClass' => ContentBlock::class,
                'modelId' => $block->id,
                'locale' => 'en',
                'onlyCollections' => ['block_background'],
            ])
            ->set('uploads.block_background', $avif)
            ->call('uploadCollection', 'block_background');

        if (! MediaProfileRegistry::supportsAvif()) {
            $component->assertHasErrors(['uploads.block_background']);
            $this->assertNotContains(
                'image/avif',
                MediaProfileRegistry::collectionForModel(ContentBlock::class, 'block_background')['accept_mime_types']
            );
            $this->assertNull($block->fresh()->getFirstMedia('block_background'));

            return;
        }

        $component->assertHasNoErrors();

        $media = $block->fresh()->getFirstMedia('block_background');
        $this->assertNotNull($media);
        $this->assertSame('image/avif', $media->mime_type);
        Storage::disk('public')->assertExists($media->getPathRelativeToRoot());
    }

    private function makeAdminUser(): User
    {
        $user = User::factory()->create();

        Bouncer::role()->firstOrCreate(['name' => 'admin']);
        Bouncer::assign('admin')->to($user);

        return $user;
    }
}
