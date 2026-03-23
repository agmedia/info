<?php

namespace App\Support\Content;

class AcademyPageDefaults
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public static function programs(): array
    {
        return [
            [
                'title' => 'Seminari za male i srednje poduzetnike',
                'icon' => 'growth',
                'accent' => 'gold',
                'intro' => 'Edukacija je namijenjena poduzetnicima koji imaju interes izraditi poslovni plan, godišnji budžet, pribaviti financiranje, pristupiti investitoru, prodati poslovne udjele i/ili napraviti prijenos poslovanja na mlađe generacije ili na menadžment.',
                'items' => [
                    [
                        'title' => 'Pribavljanje kapitala',
                        'text' => 'Struktura kapitala predstavlja omjer dužničkog i vlasničkog kapitala društva, iz čega se spoznaje način na koji društvo financira imovinu s kojom generira prihode. Edukacija će polaznicima omogućiti usvajanje znanja o: vrstama izvora financiranja, modelima financiranja, namjeni pribavljenih sredstava te poslovnim procesom pribavljanja kapitala.',
                    ],
                    [
                        'title' => 'Business Transfer',
                        'text' => 'Podrazumijeva prijenos vlasništva društva na mlađu generaciju, drugu osobu ili drugo društvo, čime se osigurava kontinuitet postojanja i poslovne aktivnosti društva. Polaznici će moći opisati koncept prijenosa poslovanja, diskutirati o različitim opcijama izlaska iz obiteljskog poslovanja, kritički razlagati o pravovremenom planiranju prijenosa poslovanja i sl.',
                    ],
                    [
                        'title' => 'Procjena vrijednosti',
                        'text' => 'Procjena vrijednosti kompleksan je proces koji se koristi u mnogim situacijama: od prijenosa udjela u vlasništvu na odabrane nasljednike, inicijalne javne ponude, dokapitalizacije, davanje udjela u vlasništvu kao nagrada menadžmentu za ostvarene rezultate i dr. Sve teme vezane uz procjenu vrijednosti bit će potkrijepljene stvarnim primjerima iz prakse.',
                    ],
                    [
                        'title' => 'Računovodstvo za male poduzetnike',
                        'text' => 'Program edukacije namijenjen je vlasnicima malih poduzeća koji žele konkretne odgovore kroz praktične primjere, a ne teoriju. Kroz seminare polaznici će naučiti kako samostalno razumjeti financijske izvještaje, te porezne, računovodstvene i financijske poslove. Naši stručnjaci prenose konkretne slučajeve, te Vas upućuju na koje stvari trebate paziti da izbjegnete najčešće greške u poslovanju.',
                    ],
                ],
            ],
            [
                'title' => 'Specijalistički seminari',
                'icon' => 'insight',
                'accent' => 'blue',
                'intro' => 'Edukacija o temama iz područja financija i računovodstva namijenjena je vlasnicima i menadžmentu, stručnjacima iz odjela financija, djelatnicima nefinancijskih odjela, početnicima u kontrolingu, reviziji, financijama i računovodstvu te korporativnim pravnicima koji trebaju više znanja i iskustva iz područja računovodstva i financija.',
                'items' => [
                    [
                        'title' => 'Financije za nefinancijaše',
                        'text' => 'Kroz teorijsko predavanje, te radom na stvarnim primjerima sudionici će se upoznati s osnovnim financijskim izvještajima i njihovom analizom, upravljanjem kapitalom društva, ekonomskom profitabilnošću te financijskom vrijednošću društva. Polaznici će naučiti pravilno tumačiti informacije iz financijskih izvještaja što je iznimno važno za opstanak društva. Seminar je namijenjen djelatnicima nefinancijskih odjela, početnicima u kontrolingu, reviziji, financijama, računovodstvu te vlasnicima i menadžmentu.',
                    ],
                    [
                        'title' => 'Financije za odvjetnike',
                        'text' => 'Edukacija je namijenjena korporativnim pravnicima koji sve više trebaju znanja i iskustva iz područja računovodstva i financija. Kroz teorijsko predavanje i praktične primjere iz prakse detaljnije ćemo vas upoznati s osnovnim načelima računovodstva i financija s kojima se korporativni pravnici i odvjetnici svakodnevno susreću u svom radu. Također, kroz seminar polaznici će naučiti tumačiti i analizirati financijske izvještaje i pokazatelje.',
                    ],
                    [
                        'title' => 'Analiza financijskih izvještaja',
                        'text' => 'Obuhvaća vrednovanje prethodnog financijskog poslovanja društva i njegovog budućeg poslovanja. Polaznici će biti upoznati s pojmom financijskih izvještaja, horizontalnom i vertikalnom analizom istih, te značenjem financijskih omjera i indikatora prikazanim na stvarnim primjerima iz prakse. Postupak analize financijskih izvještaja bit će prikazan na stvarnim primjerima iz prakse.',
                    ],
                    [
                        'title' => 'Manipulacije financijskim izvještajima',
                        'text' => 'Kroz primjere iz prakse, seminar će omogućiti polaznicima da brže uoče neuobičajene odnose i sumnjive transakcije te spriječe ili barem umanje posljedice prijevare. Na seminaru će se prezentirati i pojasniti otkrivanje i upravljanje rizicima poslovnih prijevara. Objasnit će se dva osnovna pristupa pomoću kojih se može manipulirati financijskim izvještajima te prikazati tehnike manipulacije financijskim izvještajima.',
                    ],
                ],
            ],
            [
                'title' => 'Računovodstveni seminari',
                'icon' => 'ledger',
                'accent' => 'sand',
                'intro' => 'Edukacija razvija vještine potrebne za osiguravanje pouzdanih i usporedivih informacija, razumijevanje manipulacija financijskim izvještajima te razumijevanje složenijih poslovnih aktivnosti poput spajanja i preuzimanja.',
                'items' => [
                    [
                        'title' => 'Forenzičko računovodstvo',
                        'text' => 'Polaznici seminara će se upoznati s mogućim manipulacijama financijskih izvještaja, ciljevima, tehnikama i posljedicama istih. Na seminaru prolazimo kroz računovodstvena načela, politike i procjene koje su usklađene s najnovijim promjenama u računovodstvenim standardima.',
                    ],
                    [
                        'title' => 'Menadžersko računovodstvo / Kontroling',
                        'text' => 'Edukacija iz područja menadžerskog računovodstva i kontrolinga polaznicima omogućuje razvijanje vještina kojim će se osigurati posjedovanje pouzdanih i usporedivih informacija u očekivanim ili ostvarenim vrijednosno izraženim ciljevima. Glavni cilj je razumijevanje prošlosti, kontrola sadašnjosti i planiranje budućnosti.',
                    ],
                    [
                        'title' => 'Poslovne kombinacije – financijski, porezni i pravni aspekti',
                        'text' => 'Edukacija iz područja poslovnih kombinacija pružit će znanja o poslovnim aktivnostima poput spajanja i preuzimanja kao i ostalim aktivnostima koje su obuhvaćene navedenim procesima. Sukladno tome polaznicima će se prezentirati osnove procjene vrijednosti i metode kojima se ona provodi, Due Diligence proces te završetak same aktivnosti. Poseban naglasak bit će na povezanim osobama i društvima, kao i na kontroli kroz upravljačku moć.',
                    ],
                    [
                        'title' => 'Poslovne kombinacije – financijski, porezni i pravni aspekti',
                        'text' => 'Edukacija iz područja poslovnih kombinacija pružit će znanja o poslovnim aktivnostima poput spajanja i preuzimanja kao i ostalim aktivnostima koje su obuhvaćene navedenim procesima. Sukladno tome polaznicima će se prezentirati osnove procjene vrijednosti i metode kojima se ona provodi, Due Diligence proces te završetak same aktivnosti. Poseban naglasak bit će na povezanim osobama i društvima, kao i na kontroli kroz upravljačku moć.',
                    ],
                ],
            ],
            [
                'title' => 'Porezni seminari',
                'icon' => 'compliance',
                'accent' => 'slate',
                'intro' => 'Edukacija polaznicima pruža jasan uvid u načela i metodologiju transfernih cijena te u osnove poreznog nadzora uz primjenu na konkretnim primjerima iz prakse.',
                'items' => [
                    [
                        'title' => 'Transferne cijene',
                        'text' => 'Transfernim cijenama vrednuju se transakcije između povezanih osoba te bi trebale biti u skladu s uobičajenim tržišnim cijenama. Polaznici će se upoznati s načelima i metodologijom transfernih cijena, zahtjevima OECD-ovih Smjernica o transfernim cijenama te s primjenom transfernih cijena na određene specifične transakcije između povezanih društava, uključujući njihovu primjenu u praksi. Na edukaciji će na temelju primjera iz prakse biti prikazan odabir pojedine metode utvrđivanja transfernih cijena, posebnosti transfernih cijena i njihov utjecaj na osnovicu poreza na dobit.',
                    ],
                    [
                        'title' => 'Porezni nadzor',
                        'text' => 'Edukacija će polaznicima omogućiti uvid u osnove faza poreznog nadzora koji započinje odabirom subjekta za porezni nadzor, zatim slijedi obavijest o istome, zaključno do poreznog rješenja i mogućnosti žalbe na njega. Unutar procesa pregledava se dokumentacija, prikupljaju dodatne informacije, odnosno pojašnjenja ukoliko postoje nejasnoće. Nakon pregleda dokumentacije i prikupljanja dodatnih informacija izrađuje se zapisnik te prigovor. Navedeni segmenti pojasnit će se teoretski, ali i na praktičnim primjerima kako bi se polaznicima što kvalitetnije približio ovaj segment poslovanja.',
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function mergePrograms(mixed $payloadPrograms): array
    {
        $sourcePrograms = is_array($payloadPrograms) ? array_values($payloadPrograms) : [];
        $mergedPrograms = [];

        foreach (self::programs() as $programIndex => $defaultProgram) {
            $sourceProgram = is_array($sourcePrograms[$programIndex] ?? null) ? $sourcePrograms[$programIndex] : [];
            $sourceItems = is_array($sourceProgram['items'] ?? null) ? array_values($sourceProgram['items']) : [];
            $mergedItems = [];

            foreach ($defaultProgram['items'] as $itemIndex => $defaultItem) {
                $sourceItem = is_array($sourceItems[$itemIndex] ?? null) ? $sourceItems[$itemIndex] : [];

                $mergedItems[] = [
                    'title' => self::valueOrDefault($sourceItem, 'title', $defaultItem['title']),
                    'text' => self::valueOrDefault($sourceItem, 'text', $defaultItem['text']),
                ];
            }

            $mergedPrograms[] = [
                'title' => self::valueOrDefault($sourceProgram, 'title', $defaultProgram['title']),
                'icon' => $defaultProgram['icon'],
                'accent' => $defaultProgram['accent'],
                'intro' => self::valueOrDefault($sourceProgram, 'intro', $defaultProgram['intro']),
                'items' => $mergedItems,
            ];
        }

        return $mergedPrograms;
    }

    private static function valueOrDefault(array $values, string $key, string $default): string
    {
        if (! array_key_exists($key, $values)) {
            return $default;
        }

        return trim((string) $values[$key]);
    }
}
