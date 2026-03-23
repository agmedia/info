<?php

use App\Models\Content\Page\InfoPage;
use App\Models\Content\Support\Comment;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * @var array<int, array{author_name:string,company:string,body:string}>
     */
    private array $comments = [
        [
            'author_name' => 'Andrea Dokmanović',
            'company' => 'HPB',
            'body' => 'Jako sam bila zadovoljna edukacijom: cjelovita, jezgrovita i detaljna. Hvala Vam na ovom iskustvu. Topla preporuka svima - ako se želite educirati kvalitetno, na pravom ste mjestu.',
        ],
        [
            'author_name' => 'Krešimir Renić',
            'company' => 'Onda arhitektura',
            'body' => 'Edukacijom o analizi financijskih izvještaja dobili smo kompletan uvid i pregled vezano na izvještaje u trgovačkim društvima. Predavanja su bila opširna, detaljno pripremljena i interesantna, a predavači stručni i profesionalni.',
        ],
        [
            'author_name' => 'Dalibor Crnić',
            'company' => 'DOK-ING d.o.o.',
            'body' => 'Apsolutno sam zadovoljan, osobito Danijelovom prezentacijom. Kompletan uvid i pregled vezano za financijske izvještaje.',
        ],
        [
            'author_name' => 'Maja Sić',
            'company' => 'Smit d.o.o.',
            'body' => 'Iznimno zadovoljna održanom edukacijom i puno sam iz nje naučila. Tema je vrlo opširna i zanimljiva. Predavači su bili rječiti s navođenjem puno primjera što je osiguralo onome tko sluša da bolje razumije i shvati ono što je predavač želio reći.',
        ],
        [
            'author_name' => 'Morana Plejić',
            'company' => 'Zagrebačka burza d.d.',
            'body' => 'Vrlo sažeta i dobro koncipirana edukacija, s puno praktičnih primjera. Predavači znaju kako na jednostavan i razumljiv način prenijeti znanje i iskustvo.',
        ],
        [
            'author_name' => 'Dubravka Tomljanovic',
            'company' => 'ARONDA ADRIANA TRAVEL d.o.o.',
            'body' => 'Edukacijom o analizi financijskih izvještaja dobila sam kompletan uvid u financijske izvještaje. Predavanje je bilo vrlo opširno i zanimljivo.',
        ],
        [
            'author_name' => 'Martin Morin',
            'company' => 'student',
            'body' => 'Izuzetno zadovoljan održanom edukacijom i puno sam iz nje naučio. Vrlo sažeta i dobro koncipirana edukacija, s puno primjera.',
        ],
        [
            'author_name' => 'Ivan Bašić',
            'company' => '',
            'body' => 'Vrlo sažeta i dobro koncipirana edukacija, s puno primjera.',
        ],
        [
            'author_name' => 'Nikolina Čergar',
            'company' => 'In kapital',
            'body' => 'Predavanje je bilo vrlo zanimljivo i opširno. Kvalitetno odrađena edukacija od strane svih predavača.',
        ],
        [
            'author_name' => 'Dražena Kreković',
            'company' => '',
            'body' => 'Iznimno kvalitetna edukacija o analizi financijskih izvještaja. Već dugo nisam osjetila tako snažnu i inspirativnu energiju kao nakon edukacije koju je organiziralo društvo ALPHA CAPITALIS d.o.o.',
        ],
        [
            'author_name' => 'Sanja Noršić',
            'company' => 'Petrokov',
            'body' => 'Tema je bila vrlo zanimljiva. Predavači su bili rječiti s navođenjem puno primjera. Svakako preporučujem tim ALPHA CAPITALIS d.o.o.',
        ],
        [
            'author_name' => 'Žarko Jakovljević',
            'company' => '',
            'body' => 'Kompletan uvid i pregled vezano za financijske izvještaje. Svaka preporuka za edukacije i tim ALPHA CAPITALIS d.o.o.',
        ],
        [
            'author_name' => 'Nikolina Dević',
            'company' => 'Tekstilpromet d.d',
            'body' => 'Kvalitetno odrađena edukacija o financijama. Svaka preporuka za edukacije i tim ALPHA CAPITALIS d.o.o.',
        ],
        [
            'author_name' => 'Ana Belas',
            'company' => 'Petrokov',
            'body' => 'Kompletan uvid u izvještaje u trgovačkim društvima. Predavanja su bila opširna i vrlo detaljno pripremljena.',
        ],
        [
            'author_name' => 'Željka Damjanović',
            'company' => 'NImco',
            'body' => 'Izrazito cijenim edukativni karakter kojeg njegujete u sklopu svojeg poslovanja. Stručnost Alpha Capitalis tima je neupitna i zato sam sigurna da će buduće edukacije biti bolje segmentirane u smislu profila polaznika i odabranih tema.',
        ],
    ];

    /**
     * @var array<int, array{file:string,caption:string}>
     */
    private array $galleryFiles = [
        ['file' => 'alphacapitalis-akademija-001.jpg', 'caption' => 'ALPHA CAPITALIS Akademija 001'],
        ['file' => 'alphacapitalis-akademija-003.jpg', 'caption' => 'ALPHA CAPITALIS Akademija 003'],
        ['file' => 'alphacapitalis-akademija-005.jpg', 'caption' => 'ALPHA CAPITALIS Akademija 005'],
        ['file' => 'alphacapitalis-akademija-007.jpg', 'caption' => 'ALPHA CAPITALIS Akademija 007'],
        ['file' => 'alphacapitalis-akademija-002.jpg', 'caption' => 'ALPHA CAPITALIS Akademija 002'],
        ['file' => 'alphacapitalis-akademija-007-b.jpg', 'caption' => 'ALPHA CAPITALIS Akademija 007'],
        ['file' => 'alphacapitalis-akademija-004.jpg', 'caption' => 'ALPHA CAPITALIS Akademija 004'],
        ['file' => 'alphacapitalis-akademija-008.jpg', 'caption' => 'ALPHA CAPITALIS Akademija 008'],
        ['file' => 'alphacapitalis-akademija-009.jpg', 'caption' => 'ALPHA CAPITALIS Akademija 009'],
    ];

    public function up(): void
    {
        $pageId = $this->academyPageId();

        if (! $pageId) {
            return;
        }

        $this->upsertComments($pageId);
        $this->seedGallery($pageId);
    }

    public function down(): void
    {
        $pageId = $this->academyPageId();

        if (! $pageId) {
            return;
        }

        foreach ($this->comments as $comment) {
            DB::table('content_comments')
                ->where('commentable_type', InfoPage::class)
                ->where('commentable_id', $pageId)
                ->where('locale', 'hr')
                ->where('author_name', $comment['author_name'])
                ->where('body', $comment['body'])
                ->delete();
        }

        $page = InfoPage::query()->find($pageId);

        if (! $page || ! method_exists($page, 'getMedia')) {
            return;
        }

        $fileNames = array_map(
            static fn (array $item): string => (string) $item['file'],
            $this->galleryFiles
        );

        $page->getMedia('academy_gallery')
            ->filter(fn ($media): bool => in_array((string) $media->file_name, $fileNames, true))
            ->each(function ($media): void {
                $media->delete();
            });
    }

    private function academyPageId(): ?int
    {
        $pageId = DB::table('content_info_pages')
            ->where('code', 'academy')
            ->value('id');

        if (! $pageId) {
            $pageId = DB::table('content_info_page_translations')
                ->where('locale', 'hr')
                ->where('slug', 'akademija')
                ->value('page_id');
        }

        return $pageId ? (int) $pageId : null;
    }

    private function upsertComments(int $pageId): void
    {
        $now = now();

        foreach ($this->comments as $comment) {
            $existingId = DB::table('content_comments')
                ->where('commentable_type', InfoPage::class)
                ->where('commentable_id', $pageId)
                ->where('locale', 'hr')
                ->where('author_name', $comment['author_name'])
                ->where('body', $comment['body'])
                ->value('id');

            $payload = trim((string) $comment['company']) !== ''
                ? json_encode(['company' => $comment['company']], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                : null;

            $data = [
                'commentable_type' => InfoPage::class,
                'commentable_id' => $pageId,
                'user_id' => null,
                'parent_id' => null,
                'author_name' => $comment['author_name'],
                'author_email' => null,
                'locale' => 'hr',
                'body' => $comment['body'],
                'rating' => 5,
                'status' => Comment::STATUS_APPROVED,
                'is_featured' => true,
                'reviewed_by' => null,
                'reviewed_at' => $now,
                'payload' => $payload,
                'updated_at' => $now,
                'deleted_at' => null,
            ];

            if ($existingId) {
                DB::table('content_comments')
                    ->where('id', $existingId)
                    ->update($data);

                continue;
            }

            DB::table('content_comments')->insert($data + [
                'created_at' => $now,
            ]);
        }
    }

    private function seedGallery(int $pageId): void
    {
        $page = InfoPage::query()->find($pageId);

        if (! $page || ! method_exists($page, 'getMedia')) {
            return;
        }

        $existingFileNames = $page->getMedia('academy_gallery')
            ->map(fn ($media): string => (string) $media->file_name)
            ->all();

        foreach ($this->galleryFiles as $galleryFile) {
            $fileName = (string) $galleryFile['file'];

            if (in_array($fileName, $existingFileNames, true)) {
                continue;
            }

            $path = public_path('assets/academy/gallery/'.$fileName);

            if (! is_file($path)) {
                continue;
            }

            $caption = trim((string) $galleryFile['caption']);

            $page->addMedia($path)
                ->usingName(pathinfo($fileName, PATHINFO_FILENAME))
                ->usingFileName($fileName)
                ->withCustomProperties([
                    'alt' => ['hr' => $caption],
                    'caption' => ['hr' => $caption],
                ])
                ->toMediaCollection('academy_gallery');
        }
    }
};
