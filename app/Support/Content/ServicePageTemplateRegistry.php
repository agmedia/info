<?php

namespace App\Support\Content;

use Illuminate\Support\Str;

class ServicePageTemplateRegistry
{
    public const FINANCE = 'finance';

    public const AUDIT = 'audit';

    public const FAMILY_BUSINESS = 'family_business';

    /**
     * @return array<string, string>
     */
    public static function labels(): array
    {
        return [
            self::FINANCE => 'Financije',
            self::AUDIT => 'Revizija',
            self::FAMILY_BUSINESS => 'Family Business',
        ];
    }

    public static function label(string $templateKey): string
    {
        return self::labels()[$templateKey]
            ?? (string) Str::of($templateKey)->replace(['_', '-'], ' ')->title();
    }

    public static function defaultCode(string $templateKey): string
    {
        return match ($templateKey) {
            self::FINANCE => 'finance',
            self::AUDIT => 'audit',
            self::FAMILY_BUSINESS => 'family-business',
            default => Str::of($templateKey)->replace('_', '-')->lower()->value(),
        };
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaultPagePayload(string $templateKey): array
    {
        return match ($templateKey) {
            self::FINANCE => [
                'blog_source' => [
                    'mode' => 'auto_category',
                    'category_id' => null,
                    'post_ids' => [],
                    'limit' => 5,
                ],
            ],
            self::AUDIT => [
                'blog_source' => [
                    'mode' => 'auto_category',
                    'category_id' => null,
                    'post_ids' => [],
                    'limit' => 6,
                ],
            ],
            self::FAMILY_BUSINESS => [
                'blog_source' => [
                    'mode' => 'auto_category',
                    'category_id' => null,
                    'post_ids' => [],
                    'limit' => 6,
                ],
                'faq_source' => [
                    'mode' => 'auto_group',
                    'group_code' => '',
                    'faq_ids' => [],
                ],
                'team_source' => [
                    'mode' => 'auto',
                    'member_ids' => [],
                ],
                'brochure_url' => '',
            ],
            default => [],
        };
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaultTranslationPayload(string $templateKey, ?string $locale = null): array
    {
        return match ($templateKey) {
            self::FINANCE => FinanceServicePageDefaults::defaultsForLocale(
                $locale ?: (string) config('app.locale', 'en')
            ),
            self::AUDIT => AuditServicePageDefaults::defaultsForLocale(
                $locale ?: (string) config('app.locale', 'en')
            ),
            self::FAMILY_BUSINESS => [
                'hero' => [
                    'brand_title' => 'ALPHA CAPITALIS',
                    'subtitle_lead' => 'Savjetnici za',
                    'subtitle_accent' => 'obiteljski biznis',
                    'intro' => 'U ALPHA CAPITALIS-u svjesni smo složenosti vašeg obiteljskog biznisa i jedinstvenosti vaše poduzetničke obitelji. Upravo zato vam na jednom mjestu pružamo cjelovitu podršku. Kroz holistički pristup stvaramo siguran prostor i posvećujemo vrijeme vašem poslovnom putu, osiguravajući stabilnost i razvoj kroz sve faze rasta.',
                    'cta_label' => 'Pružamo vam podršku',
                    'cta_url' => '#family-business-publika',
                ],
                'audience' => [
                    'headline' => 'ALPHA CAPITALIS pruža vam podršku u razvoju dugoročne održivosti poslovanja i obitelji.',
                    'cards' => [
                        [
                            'title' => 'Osnivači',
                            'icon' => 'founders',
                            'text' => 'Suradujemo s vama na očuvanju vaše ostavštine, izvornih vrijednosti i vizije obiteljskog poduzeća. Pružamo vam podršku u procesu prijenosa vlasništva, vođenja i upravljanja na sljedeću generaciju te zadržavanja doprinosa kroz mentorstvo. Pratimo vas i vodimo kroz suočavanje s emocionalnim izazovima gubitka kontrole i važnosti, straha od isključenja i neizvjesnosti te prilagodbe osobnog identiteta uslijed povlačenja.',
                        ],
                        [
                            'title' => 'Nasljednici',
                            'icon' => 'successors',
                            'text' => 'Pružamo vam podršku u razvoju liderskih sposobnosti za preuzimanje ključnih odgovornosti obiteljske tvrtke u tranziciji. Surađujemo s vama na uvođenju inovacija bez ugrožavanja tradicije te promjena i transformacija uz očuvanje nasljeđa. Radimo na definiranju vašeg profesionalnog identiteta i stjecanju autoriteta te suočavanju s emocionalnim izazovima unutarnjih konflikata, strahom od grešaka i otpora te sumnjom u sebe.',
                        ],
                        [
                            'title' => 'Članovi obitelji',
                            'icon' => 'family',
                            'text' => 'Radimo s vama na definiranju vaših profesionalnih uloga, odgovornosti i prava u obiteljskoj firmi te etici i kodeksu ponašanja. Pružamo vam podršku u kreiranju upravljačkih i komunikacijskih struktura kako biste izgradili povjerenje i usklađenost obiteljskih vrijednosti sa strateškim poslovnim ciljevima. Pratimo vas i vodimo kroz izazove u suočavanju s prisutnim psihološkim, obiteljskim i širim društvenim dinamikama.',
                        ],
                        [
                            'title' => 'Neobiteljski menadžeri',
                            'icon' => 'managers',
                            'text' => 'Suradujemo s vama kako biste se uspješno integrirali u obiteljsku kompaniju te potpuno iskoristili svoje znanje i iskustvo. Pružamo vam podršku u definiranju profesionalnih upravljačkih struktura i procedura koje osiguravaju transparentnost i učinkovito poslovanje, što doprinosi dugoročnoj održivosti i rastu. Radimo na suočavanju sa psihološkim i organizacijskim dinamikama, uslijed specifičnosti obiteljskog biznisa.',
                        ],
                    ],
                ],
                'ffi' => [
                    'title' => 'ALPHA CAPITALIS je član Family Firm Institute (FFI)',
                    'body' => [
                        'FFI je najutjecajnija globalna mreža lidera u području obiteljskog biznisa. Pružaju učenje temeljeno na istraživanju i pripadajuće alate za savjetnike, edukatore i dionike obiteljskih poduzeća.',
                    ],
                    'logo_alt' => 'FFI GEN ACFBA logo',
                ],
                'what_we_do' => [
                    'kicker' => 'ŠTO RADIMO?',
                    'title' => 'Dugi niz godina savjetujemo obiteljska poduzeća i poduzetničke obitelji.',
                    'intro' => 'Svjesni smo međuovisnosti izazova obitelji, vlasništva i poslovanja s kojima se suočavate, kao i drugačijih zakonitosti koje njima vladaju jer zajedno s vama radimo na ključnim temama i ostvarenju vaših zajedničkih ciljeva. Kao multidisciplinarni tim stručnjaka omogućujemo stjecanje i prijenos znanja, iskustva i mudrosti u kritičnim situacijama razvoja mnogih obiteljskih biznisa.',
                ],
                'advisory' => [
                    'kicker' => 'ŠTO MOŽEMO UČINITI ZA VAS',
                    'title' => 'Tu smo kako biste zadobili uvid u cjelovitu perspektivu.',
                    'intro' => 'Razumijemo koliko je zahtjevno donositi odluke, istovremeno dobre za biznis i obitelj jer vas pratimo i vodimo kroz ključne izazove i probleme vašeg obiteljskog poslovanja. Upravo iz tih razloga njegujemo potpuno personaliziran pristup svakoj obitelji i poslovanju, omogućujući dolazak do vlastitih zajedničkih rješenja, koja su prihvatljiva i koja traju.',
                    'box_title' => 'KAKO VAS SAVJETUJEMO',
                    'items' => [
                        [
                            'lead' => 'Uvažavamo vaša proživljena iskustva',
                            'body' => 'i stvorena značenja oko njih. To omogućuje usredotočenost na vaše specifične teme i situacije, na način koji upravo za vas ima smisla.',
                        ],
                        [
                            'lead' => 'Bavimo se cjelinom i međuovisnošću',
                            'body' => 'vašeg obiteljskog, vlasničkog i poslovnog sistema. To omogućuje otkrivanje, osvještavanje i procesiranje naizgled nepovezanih uzroka i posljedica, što je temelj za održive promjene i transformacije.',
                        ],
                        [
                            'lead' => 'Naglašavamo sposobnosti učenja',
                            'body' => 'i samo-obnavljanja, urođene pojedincima, ali također obiteljima i organizacijama. To omogućuje kontinuirano prilagođavanje i uspješno nošenje s vanjskim i unutarnjim promjenama.',
                        ],
                        [
                            'lead' => 'Kreiramo viziju poželjne budućnosti',
                            'body' => 'vašeg obiteljskog poslovanja te vas usmjeravamo na resurse koji ju čine mogućom. To omogućuje vaše ujedinjenje u definiranju zajedničke svrhe, ciljeva i planova.',
                        ],
                        [
                            'lead' => 'Uključujemo sve obiteljske dionike',
                            'body' => 'prisutne u vlasništvu i upravljanju u proces. To omogućuje bogatstvo različitih perspektiva, kolektivnu inteligenciju te unutarnje vlasništvo nad donesenim i provedenim odlukama.',
                        ],
                        [
                            'lead' => 'Podršci pristupamo multidisciplinarno,',
                            'body' => 'koristeći stručna znanja iz različitih, ali međusobno nadopunjujućih područja. To omogućuje cjelovito zadovoljenje vaših potreba.',
                        ],
                        [
                            'lead' => 'Neovisni smo,',
                            'body' => 'što nam omogućuje pružanje podrške svim uključenim stranama, a ne samo pojedinim dijelovima, interesnim skupinama ili pojedincima.',
                        ],
                        [
                            'lead' => 'Njegujemo punu transparentnost',
                            'body' => 'u razmjeni informacija s vama. Na taj način zajednički kreiramo presudan okvir za stvaranje ključnih vrijednosti i rješenja.',
                        ],
                    ],
                ],
                'capabilities' => [
                    [
                        'title' => 'Upravljanje',
                        'icon' => 'governance',
                        'intro' => 'Naš pristup obiteljskom upravljanju temelji se na uspostavljanju jasnog i održivog modela upravljanja koji usklađuje odluke obitelji, vlasništva i poslovanja te osigurava dugoročnu stabilnost sustava.',
                        'help' => 'Pomažemo vam postaviti okvir upravljanja koji donosi veću transparentnost, učinkovitost i dugoročnu stabilnost te olakšava donošenje odluka u ključnim trenucima.',
                        'items' => [
                            [
                                'title' => 'Razvoj krovne misije',
                                'text' => 'Definiramo zajedničku svrhu, vrijednosti i prioritete koji povezuju generacije i daju jasan smjer budućem razvoju obitelji i poslovanja.',
                            ],
                            [
                                'title' => 'Upoznavanje obiteljske dinamike',
                                'text' => 'Prepoznajemo obrasce odnosa, očekivanja i komunikacije kako bi se potencijalni prijepori razumjeli, obradili i spriječili prije eskalacije.',
                            ],
                            [
                                'title' => 'Prilagođena upravljačka struktura',
                                'text' => 'Kreiramo model uloga, foruma i pravila odlučivanja koji odgovara vašoj fazi razvoja, veličini poslovanja i vlasničkoj strukturi.',
                            ],
                        ],
                    ],
                    [
                        'title' => 'Tranzicija',
                        'icon' => 'transition',
                        'intro' => 'Planiranje nasljeđa ključno je za očuvanje stabilnosti i dugoročne održivosti poslovanja. Strukturirano vodimo prijenos vlasništva, odgovornosti i autoriteta na sljedeću generaciju.',
                        'help' => 'Vodimo tranziciju od pripreme i dijaloga do implementacije rješenja koja čuvaju kontinuitet poslovanja, odnose među članovima obitelji i stabilnost vlasničke strukture.',
                        'items' => [
                            [
                                'title' => 'Odabir i evaluacija nasljednika',
                                'text' => 'Procjenjujemo kompetencije, motivaciju i razvojni potencijal budućih nositelja ključnih odgovornosti kako bi izbor bio promišljen i održiv.',
                            ],
                            [
                                'title' => 'Transparentan proces',
                                'text' => 'Postavljamo jasan slijed koraka, kriterija i odluka kako bi prijenos bio razumljiv, prihvatljiv i transparentan svim uključenim stranama.',
                            ],
                            [
                                'title' => 'Mentorski program za nasljednike',
                                'text' => 'Razvijamo prijenos znanja, autoriteta i odgovornosti kroz strukturiranu pripremu nove generacije i podršku u preuzimanju liderske uloge.',
                            ],
                        ],
                    ],
                    [
                        'title' => 'Dinamika odnosa',
                        'icon' => 'relations',
                        'intro' => 'Kod obiteljskih tvrtki poslovne odluke često su usko povezane s odnosima. Zato radimo na prevenciji konflikata, zdravijoj komunikaciji i većoj jasnoći u međusobnim očekivanjima.',
                        'help' => 'Kroz strukturiran dijalog i podršku u komunikaciji pomažemo očuvati povjerenje, stabilnost odnosa i prostor za donošenje zajedničkih odluka.',
                        'items' => [
                            [
                                'title' => 'Prevencija',
                                'text' => 'Pomažemo rano prepoznati tenzije i nesporazume kako bi se otvorio prostor za konstruktivan razgovor i pravovremeno rješavanje izazova.',
                            ],
                            [
                                'title' => 'Kodeks komunikacije',
                                'text' => 'Gradimo pravila komunikacije koja podržavaju jasnoću, uvažavanje i donošenje odluka bez nepotrebnog zastoja ili međusobnog iscrpljivanja.',
                            ],
                            [
                                'title' => 'Normalizacija procesa',
                                'text' => 'Uspostavljamo ritam sastanaka i procesa koji smanjuju neizvjesnost, grade sigurnost i donose više predvidljivosti unutar obitelji i tvrtke.',
                            ],
                        ],
                    ],
                ],
                'capability_cta' => [
                    'kicker' => 'KAKO VAM MOŽEMO POMOĆI',
                    'label' => 'Zatražite konzultacije',
                ],
                'team_section' => [
                    'kicker' => 'TIM',
                    'title' => 'Naš tim za obiteljsko savjetovanje',
                    'intro' => 'Stručnjaci koji rade s obiteljskim poduzećima u temama tranzicije, upravljanja, odnosa i dugoročne održivosti poslovanja.',
                ],
                'meeting' => [
                    'kicker' => 'SASTANAK',
                    'title' => 'Ugovorite sastanak',
                    'intro' => 'U ALPHA CAPITALIS-u svjesni smo složenosti vašeg obiteljskog biznisa i jedinstvenosti vaše poduzetničke obitelji. Upravo zato vam na jednom mjestu pružamo cjelovitu podršku. Kroz holistički pristup stvaramo siguran prostor i posvećujemo vrijeme vašem poslovnom putu, osiguravajući stabilnost i razvoj kroz sve faze rasta.',
                    'visit_title' => 'Posjetite nas',
                    'visit_lines' => [
                        'Ul. Roberta Frangeša Mihanovića 9,',
                        '10110 Zagreb / Sky Office, 19. kat',
                    ],
                    'contact_title' => 'Kontaktirajte nas',
                    'direct_phone_label' => 'Telefon',
                    'direct_email_label' => 'Email',
                    'form_labels' => [
                        'first_name' => 'Ime',
                        'last_name' => 'Prezime',
                        'company' => 'Tvrtka',
                        'phone' => 'Broj telefona',
                        'email' => 'Email',
                        'subject' => 'Naslov poruke',
                        'message' => 'Poruka',
                    ],
                    'submit' => 'Pošalji',
                ],
                'blog_section' => [
                    'kicker' => 'BLOG',
                    'title' => 'Najnovije objave iz kategorije :category',
                    'intro' => 'Novosti, članci i stručni uvidi vezani uz tranziciju, upravljanje i razvoj obiteljskih poduzeća.',
                ],
                'brochure_label' => 'Preuzmite brošuru',
            ],
            default => [],
        };
    }

    /**
     * @param  array<string, mixed>|null  $payload
     * @return array<string, mixed>
     */
    public static function mergePagePayload(string $templateKey, ?array $payload): array
    {
        return self::deepMerge(self::defaultPagePayload($templateKey), is_array($payload) ? $payload : []);
    }

    /**
     * @param  array<string, mixed>|null  $payload
     * @return array<string, mixed>
     */
    public static function mergeTranslationPayload(string $templateKey, ?array $payload, ?string $locale = null): array
    {
        return self::deepMerge(
            self::defaultTranslationPayload($templateKey, $locale),
            is_array($payload) ? $payload : []
        );
    }

    /**
     * @param  array<string, mixed>  $defaults
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private static function deepMerge(array $defaults, array $overrides): array
    {
        foreach ($overrides as $key => $value) {
            if (
                isset($defaults[$key])
                && is_array($defaults[$key])
                && is_array($value)
                && ! array_is_list($defaults[$key])
                && ! array_is_list($value)
            ) {
                $defaults[$key] = self::deepMerge($defaults[$key], $value);
                continue;
            }

            $defaults[$key] = $value;
        }

        return $defaults;
    }
}
