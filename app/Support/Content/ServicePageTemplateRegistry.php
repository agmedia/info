<?php

namespace App\Support\Content;

use Illuminate\Support\Str;

class ServicePageTemplateRegistry
{
    public const SERVICES_INDEX = 'services_index';

    public const SERVICES_INDEX_CARD_MEDIA_COLLECTIONS = [
        'audit' => 'services_index_audit_image',
        'accounting' => 'services_index_accounting_image',
        'advisory' => 'services_index_advisory_image',
    ];

    public const ADVISORY = 'advisory';

    public const FINANCE = 'finance';

    public const ACCOUNTING = 'accounting';

    public const AUDIT = 'audit';

    public const TAX = 'tax';

    public const EU_FUNDS = 'eu_funds';

    public const FAMILY_BUSINESS = 'family_business';

    /**
     * @return array<string, string>
     */
    public static function labels(): array
    {
        return [
            self::SERVICES_INDEX => 'Usluge',
            self::ADVISORY => 'Savjetovanje',
            self::FINANCE => 'Financije',
            self::ACCOUNTING => 'Računovodstvo',
            self::AUDIT => 'Revizija',
            self::TAX => 'Porezi',
            self::EU_FUNDS => 'EU fondovi',
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function primaryServiceTemplateKeys(): array
    {
        return [
            self::AUDIT,
            self::ACCOUNTING,
            self::ADVISORY,
        ];
    }

    /**
     * @return array<string, int>
     */
    public static function adminDisplayOrder(): array
    {
        return [
            self::SERVICES_INDEX => 0,
            self::AUDIT => 10,
            self::ACCOUNTING => 20,
            self::ADVISORY => 30,
            self::FINANCE => 100,
            self::TAX => 110,
            self::EU_FUNDS => 120,
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function adminNestedTemplateKeys(): array
    {
        $templateKeys = [];

        $collect = function (array $pages) use (&$collect, &$templateKeys): void {
            foreach ($pages as $page) {
                if (isset($page['template_key'])) {
                    $templateKeys[] = (string) $page['template_key'];
                }

                $collect((array) ($page['children'] ?? []));
            }
        };

        foreach (self::adminPageTree() as $page) {
            $collect((array) ($page['children'] ?? []));
        }

        return array_values(array_unique($templateKeys));
    }

    public static function label(string $templateKey): string
    {
        return self::labels()[$templateKey]
            ?? (string) Str::of($templateKey)->replace(['_', '-'], ' ')->title();
    }

    public static function defaultCode(string $templateKey): string
    {
        return match ($templateKey) {
            self::SERVICES_INDEX => 'services',
            self::ADVISORY => 'advisory',
            self::FINANCE => 'finance',
            self::ACCOUNTING => 'racunovodstvo',
            self::AUDIT => 'audit',
            self::TAX => 'tax',
            self::EU_FUNDS => 'eu-fondovi',
            self::FAMILY_BUSINESS => 'family-business',
            default => Str::of($templateKey)->replace('_', '-')->lower()->value(),
        };
    }

    public static function canonicalStructuralSlug(string $templateKey, string $locale): ?string
    {
        $locale = strtolower(trim($locale));
        $locale = (string) preg_split('/[-_]/', $locale, 2)[0];

        return match ($locale) {
            'hr' => match ($templateKey) {
                self::SERVICES_INDEX => 'usluge',
                self::AUDIT => 'revizija',
                self::ACCOUNTING => 'racunovodstvo',
                self::ADVISORY => 'savjetovanje',
                self::EU_FUNDS => 'eu-fondovi',
                default => null,
            },
            'en' => match ($templateKey) {
                self::SERVICES_INDEX => 'services',
                self::AUDIT => 'audit',
                self::ACCOUNTING => 'accounting',
                self::ADVISORY => 'advisory',
                self::EU_FUNDS => 'eu-funds',
                default => null,
            },
            default => null,
        };
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaultPagePayload(string $templateKey): array
    {
        return match ($templateKey) {
            self::SERVICES_INDEX => [],
            self::ADVISORY => [
                'blog_source' => [
                    'mode' => 'auto_category',
                    'category_id' => null,
                    'post_ids' => [],
                    'limit' => 6,
                ],
                'video_source' => [
                    'items' => [],
                ],
            ],
            self::FINANCE => [
                'blog_source' => [
                    'mode' => 'auto_category',
                    'category_id' => null,
                    'post_ids' => [],
                    'limit' => 5,
                ],
                'video_source' => [
                    'items' => [],
                ],
            ],
            self::ACCOUNTING => [
                'blog_source' => [
                    'mode' => 'auto_category',
                    'category_id' => null,
                    'post_ids' => [],
                    'limit' => 6,
                ],
                'video_source' => [
                    'items' => [],
                ],
            ],
            self::AUDIT => [
                'blog_source' => [
                    'mode' => 'auto_category',
                    'category_id' => null,
                    'post_ids' => [],
                    'limit' => 6,
                ],
                'video_source' => [
                    'items' => [],
                ],
            ],
            self::TAX => [
                'blog_source' => [
                    'mode' => 'auto_category',
                    'category_id' => null,
                    'post_ids' => [],
                    'limit' => 6,
                ],
                'video_source' => [
                    'items' => [],
                ],
            ],
            self::EU_FUNDS => [
                'blog_source' => [
                    'mode' => 'auto_category',
                    'category_id' => null,
                    'post_ids' => [],
                    'limit' => 5,
                ],
                'video_source' => [
                    'items' => [],
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
                'video_source' => [
                    'items' => [],
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
        $defaults = match ($templateKey) {
            self::SERVICES_INDEX => self::servicesIndexDefaultsForLocale(
                $locale ?: (string) config('app.locale', 'en')
            ),
            self::ADVISORY => AdvisoryServicePageDefaults::defaultsForLocale(
                $locale ?: (string) config('app.locale', 'en')
            ),
            self::FINANCE => FinanceServicePageDefaults::defaultsForLocale(
                $locale ?: (string) config('app.locale', 'en')
            ),
            self::ACCOUNTING => AccountingServicePageDefaults::defaultsForLocale(
                $locale ?: (string) config('app.locale', 'en')
            ),
            self::AUDIT => AuditServicePageDefaults::defaultsForLocale(
                $locale ?: (string) config('app.locale', 'en')
            ),
            self::TAX => TaxServicePageDefaults::defaultsForLocale(
                $locale ?: (string) config('app.locale', 'en')
            ),
            self::EU_FUNDS => EuFundsServicePageDefaults::defaultsForLocale(
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

        if (in_array($templateKey, array_keys(self::labels()), true)) {
            $defaults = self::deepMerge([
                'video_section' => [
                    'title' => '',
                    'intro' => '',
                ],
            ], $defaults);
        }

        return $defaults;
    }

    /**
     * Return only the structural shape required by the CMS editor. This is
     * deliberately copy-free so a missing non-default translation cannot be
     * populated from PHP defaults merely by opening and saving the form.
     *
     * @return array<string, mixed>
     */
    public static function blankTranslationPayload(string $templateKey, ?string $locale = null): array
    {
        return self::blankEditorValues(
            self::defaultTranslationPayload($templateKey, $locale)
        );
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function adminPageTree(): array
    {
        return [
            self::SERVICES_INDEX => [
                'title' => 'Usluge',
                'route' => 'services.index',
                'admin_anchor' => '#services-index-editor',
                'children' => [],
            ],
            self::AUDIT => [
                'title' => 'Revizija',
                'route' => 'audit.show',
                'admin_anchor' => '#audit-overview-admin',
                'children' => [],
            ],
            self::ACCOUNTING => [
                'title' => 'Računovodstvo',
                'route' => 'accounting.show',
                'admin_anchor' => '#accounting-hero-admin',
                'children' => [],
            ],
            self::ADVISORY => [
                'title' => 'Savjetovanje',
                'route' => 'advisory.show',
                'admin_anchor' => '#advisory-main-admin',
                'children' => [
                    [
                        'title' => 'Financijsko savjetovanje',
                        'route' => 'advisory.finance.show',
                        'admin_anchor' => '#advisory-financial-admin',
                        'content_key' => 'financial',
                    ],
                    [
                        'title' => 'Pribavljanje financiranja',
                        'route' => 'advisory.funding.show',
                        'admin_anchor' => '#advisory-funding-admin',
                        'content_key' => 'funding',
                        'children' => [
                            [
                                'title' => 'EU fondovi',
                                'route' => 'eu-funds.show',
                                'template_key' => self::EU_FUNDS,
                                'admin_anchor' => '#eu-funds-about',
                            ],
                            [
                                'title' => 'Bankovni krediti',
                                'route' => 'advisory.bank-loans.show',
                                'admin_anchor' => '#advisory-bank-loans-admin',
                                'content_key' => 'bank_loans',
                            ],
                            [
                                'title' => 'Zakon o poticanju ulaganja',
                                'route' => 'advisory.investment-incentives.show',
                                'admin_anchor' => '#advisory-zopu-admin',
                                'content_key' => 'zopu',
                            ],
                        ],
                    ],
                    [
                        'title' => 'Prodaja i kupnja poduzeća (M&A)',
                        'route' => 'advisory.ma.show',
                        'admin_anchor' => '#advisory-ma-admin',
                        'content_key' => 'ma',
                    ],
                    [
                        'title' => 'Dubinska snimanja (Due Diligence)',
                        'route' => 'advisory.due-diligence.show',
                        'admin_anchor' => '#advisory-due-diligence-admin',
                        'content_key' => 'due_diligence',
                    ],
                    [
                        'title' => 'Procjena vrijednosti društva',
                        'route' => 'advisory.valuations.show',
                        'admin_anchor' => '#advisory-valuations-admin',
                        'content_key' => 'valuations',
                    ],
                    [
                        'title' => 'Porezno savjetovanje',
                        'route' => 'advisory.tax.show',
                        'admin_anchor' => '#advisory-tax-admin',
                        'content_key' => 'tax',
                    ],
                ],
            ],
            self::FINANCE => [
                'title' => 'Financije',
                'route' => 'finance.show',
                'admin_anchor' => '#finance-services-intro',
                'children' => [],
            ],
            self::TAX => [
                'title' => 'Porezi',
                'route' => 'tax.show',
                'admin_anchor' => '#tax-overview-admin',
                'children' => [],
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function adminChildrenForTemplate(string $templateKey): array
    {
        return (array) data_get(self::adminPageTree(), $templateKey.'.children', []);
    }

    /**
     * @return array<int, string>
     */
    public static function templateKeysMatchingAdminSearch(string $search): array
    {
        $needle = Str::of($search)->lower()->ascii()->squish()->value();

        if ($needle === '') {
            return [];
        }

        return collect(self::adminPageTree())
            ->filter(function (array $page, string $templateKey) use ($needle): bool {
                $haystacks = [
                    $templateKey,
                    (string) ($page['title'] ?? ''),
                    (string) ($page['route'] ?? ''),
                    (string) ($page['admin_anchor'] ?? ''),
                ];

                $collectChildHaystacks = function (array $children) use (&$collectChildHaystacks, &$haystacks): void {
                    foreach ($children as $child) {
                        $haystacks[] = (string) ($child['title'] ?? '');
                        $haystacks[] = (string) ($child['route'] ?? '');
                        $haystacks[] = (string) ($child['admin_anchor'] ?? '');
                        $haystacks[] = (string) ($child['content_key'] ?? '');
                        $haystacks[] = (string) ($child['template_key'] ?? '');

                        $collectChildHaystacks((array) ($child['children'] ?? []));
                    }
                };

                $collectChildHaystacks((array) ($page['children'] ?? []));

                return collect($haystacks)
                    ->map(fn (string $value): string => Str::of($value)->lower()->ascii()->squish()->value())
                    ->contains(fn (string $value): bool => str_contains($value, $needle));
            })
            ->keys()
            ->values()
            ->all();
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
        $defaults = self::defaultTranslationPayload($templateKey, $locale);
        $overrides = is_array($payload) ? $payload : [];
        $merged = self::deepMerge($defaults, $overrides);

        if ($templateKey === self::SERVICES_INDEX) {
            $merged['primary_pillars'] = self::mergeServicesIndexPillars(
                (array) ($defaults['primary_pillars'] ?? []),
                (array) ($overrides['primary_pillars'] ?? [])
            );
        }

        if ($templateKey === self::AUDIT) {
            $effectiveLocale = (string) ($locale ?: config('app.locale', 'en'));
            $isCroatian = str_starts_with(strtolower($effectiveLocale), 'hr');
            $legacyBlogTitle = $isCroatian
                ? 'Najnovije objave iz kategorije :category'
                : 'Latest posts from :category';

            if (trim((string) data_get($merged, 'blog_section.title')) === $legacyBlogTitle) {
                data_set($merged, 'blog_section.title', (string) data_get($defaults, 'blog_section.title', ''));
            }
        }

        if ($templateKey === self::ACCOUNTING) {
            $effectiveLocale = (string) ($locale ?: config('app.locale', 'en'));
            $isCroatian = str_starts_with(strtolower($effectiveLocale), 'hr');
            $legacyBlogTitle = $isCroatian
                ? 'Najnovije objave iz kategorije :category'
                : 'Latest posts from :category';

            if (trim((string) data_get($merged, 'blog_section.title')) === $legacyBlogTitle) {
                data_set($merged, 'blog_section.title', (string) data_get($defaults, 'blog_section.title', ''));
            }
        }

        if ($templateKey === self::ADVISORY) {
            $effectiveLocale = (string) ($locale ?: config('app.locale', 'en'));
            $isCroatian = str_starts_with(strtolower($effectiveLocale), 'hr');
            $legacyBlogTitle = $isCroatian ? 'Savjetovanje' : 'Advisory';

            if (trim((string) data_get($merged, 'blog_section.title')) === $legacyBlogTitle) {
                data_set($merged, 'blog_section.title', (string) data_get($defaults, 'blog_section.title', ''));
            }

            $routeCards = [
                'funding' => ['/savjetovanje/pribavljanje-financiranja', 'service_cards'],
                'ma' => ['/savjetovanje/prodaja-i-kupnja-poduzeca', 'service_cards'],
                'due_diligence' => ['/savjetovanje/dubinska-snimanja', 'service_cards'],
                'valuations' => ['/savjetovanje/procjena-vrijednosti-drustva', 'service_cards'],
                'tax' => ['/savjetovanje/porezno-savjetovanje', 'service_cards'],
                'bank_loans' => ['/savjetovanje/pribavljanje-financiranja/bankovni-krediti', 'funding.cards'],
                'zopu' => ['/savjetovanje/pribavljanje-financiranja/zakon-o-poticanju-ulaganja', 'funding.cards'],
            ];

            foreach (['financial', 'funding', 'bank_loans', 'zopu', 'ma', 'due_diligence', 'valuations', 'tax'] as $pageKey) {
                $pageOverrides = is_array($overrides[$pageKey] ?? null) ? $overrides[$pageKey] : [];

                if (! array_key_exists('blog_section', $pageOverrides)) {
                    data_set($merged, $pageKey.'.blog_section', (array) data_get($merged, 'blog_section', []));
                }

                if (! array_key_exists('meeting', $pageOverrides)) {
                    data_set($merged, $pageKey.'.meeting', (array) data_get($merged, 'meeting', []));
                }

                if (! array_key_exists('hero_intro', $pageOverrides)) {
                    $heroIntro = '';

                    if (isset($routeCards[$pageKey])) {
                        [$route, $cardPath] = $routeCards[$pageKey];

                        foreach ((array) data_get($merged, $cardPath, []) as $card) {
                            if (is_array($card) && str_ends_with((string) ($card['url'] ?? ''), $route)) {
                                $heroIntro = trim((string) ($card['text'] ?? ''));
                                break;
                            }
                        }
                    }

                    if ($heroIntro === '') {
                        $heroIntro = $pageKey === 'funding'
                            ? trim((string) data_get($merged, 'funding.intro', ''))
                            : trim((string) data_get($merged, $pageKey.'.overview_body.0', ''));
                    }

                    data_set($merged, $pageKey.'.hero_intro', $heroIntro);
                }
            }

            if (! array_key_exists('approach_title', (array) ($overrides['funding'] ?? []))) {
                data_set($merged, 'funding.approach_title', (string) data_get($merged, 'approach.title', ''));
            }

            if (! array_key_exists('approach_body', (array) ($overrides['funding'] ?? []))) {
                data_set($merged, 'funding.approach_body', (array) data_get($merged, 'approach.body', []));
            }

            if (! array_key_exists('pandea', (array) ($overrides['ma'] ?? []))) {
                data_set($merged, 'ma.pandea', (array) data_get($merged, 'pandea', []));
            }
        }

        $merged = self::applyLatestBriefCopy(
            $templateKey,
            $merged,
            (string) ($locale ?: config('app.locale', 'en')),
        );

        return self::hydrateStructuredEditorFields($templateKey, $merged, $overrides);
    }

    /**
     * Upgrade only known untouched legacy copy before editor fields are
     * materialized. Custom copy and explicitly saved HTML remain authoritative.
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private static function applyLatestBriefCopy(string $templateKey, array $payload, string $locale): array
    {
        $isCroatian = str_starts_with(strtolower($locale), 'hr');

        if ($templateKey === self::AUDIT) {
            $legacyHeroIntro = $isCroatian
                ? 'Neovisna, stručna provjera vaših financijskih izvještaja. Povećavamo povjerenje, smanjujemo rizike i jačamo kredibilitet vašeg poslovanja.'
                : 'Independent, expert review of your financial statements. We increase trust, reduce risk, and strengthen the credibility of your business.';
            $legacyOverviewTitle = $isCroatian ? 'Što je revizija?' : 'What is an audit?';
            $hero = (array) ($payload['hero'] ?? []);
            $overview = (array) ($payload['overview'] ?? []);

            if (trim((string) ($hero['intro'] ?? '')) === $legacyHeroIntro) {
                $hero['intro'] = $isCroatian
                    ? 'Povjerenje u financijske informacije počinje neovisnom i stručnom revizijom.'
                    : 'Trust in financial information begins with an independent and expert audit.';
                $payload['hero'] = $hero;
            }

            if (trim((string) ($overview['title'] ?? '')) === $legacyOverviewTitle) {
                $overview['title'] = $isCroatian ? 'Zašto Vam je revizija bitna?' : 'Why does audit matter to you?';
                $overview['highlight_title'] = $overview['title'];
                $overview['intro'] = '';
                $overview['body'] = $isCroatian
                    ? [
                        'Revizija pruža neovisnu i objektivnu procjenu financijskih informacija, povećava transparentnost i pouzdanost poslovanja te pomaže u prepoznavanju potencijalnih rizika.',
                        'Neovisna revizija daje Vam sigurnost da odluke donosite na temelju pouzdanih informacija. Uz stručan i objektivan pristup, Vaše poslovanje sagledavamo šire od samih brojki.',
                    ]
                    : [
                        'Audit provides an independent and objective assessment of financial information, increases transparency and reliability, and helps identify potential risks.',
                        'An independent audit gives you confidence that your decisions are based on reliable information. Through an expert and objective approach, we look beyond the numbers to understand the wider context of your business.',
                    ];
                $payload['overview'] = $overview;
            }

            return $payload;
        }

        if ($templateKey !== self::ACCOUNTING) {
            return $payload;
        }

        $legacyHeroIntro = $isCroatian
            ? 'Precizno, pravovremeno i transparentno - preuzimamo vođenje vaših poslovnih knjiga kako biste se fokusirali na ono što zaista donosi rast.'
            : 'Precise, timely, and transparent accounting - we take over your books so you can stay focused on what truly drives growth.';
        $legacyOverviewTitle = $isCroatian ? 'Što je računovodstvo?' : 'What is accounting?';
        $legacyOverviewBody = $isCroatian
            ? [
                'Računovodstvo je sustavan zapis poslovnih transakcija koji osigurava točan prikaz financijskog stanja društva. Dobro računovodstvo nije samo zakonska obveza - to je temelj za donošenje kvalitetnih poslovnih odluka.',
            ]
            : [
                'Accounting is the systematic recording of business transactions that provides an accurate view of a company’s financial position. Good accounting is not only a legal obligation - it is the foundation for sound business decisions.',
            ];
        $hero = (array) ($payload['hero'] ?? []);
        $overview = (array) ($payload['overview'] ?? []);

        if (trim((string) ($hero['intro'] ?? '')) === $legacyHeroIntro) {
            $hero['intro'] = $isCroatian
                ? 'Vi vodite poslovanje. Mi brinemo da Vaše brojke budu točne, pravovremene i spremne za svaku odluku.'
                : 'You run the business. We make sure your numbers are accurate, timely, and ready for every decision.';
            $payload['hero'] = $hero;
        }

        $currentOverviewBody = array_values(array_map(
            static fn ($paragraph): string => trim((string) $paragraph),
            (array) ($overview['body'] ?? []),
        ));

        if (
            trim((string) ($overview['title'] ?? '')) === $legacyOverviewTitle
            && $currentOverviewBody === $legacyOverviewBody
        ) {
            $overview['title'] = $isCroatian
                ? 'Zašto Vam je računovodstvo bitno?'
                : 'Why does accounting matter to you?';
            $overview['highlight_title'] = $overview['title'];
            $overview['intro'] = '';
            $overview['body'] = $isCroatian
                ? [
                    'Mirnije poslovanje počinje jasnim i pouzdanim brojkama. Ažurne financijske informacije daju Vam kontrolu nad poslovanjem, pomažu prepoznati prilike i rizike te donijeti sigurnije odluke.',
                    'Uz ALPHA CAPITALIS ne dobivate samo računovodstvenu uslugu, već pouzdanog partnera koji razumije Vaše poslovanje i prati Vas kroz svakodnevne izazove i planove rasta.',
                ]
                : [
                    'Calmer business operations begin with clear and reliable numbers. Up-to-date financial information gives you control over your business, helps you identify opportunities and risks, and supports more confident decisions.',
                    'With ALPHA CAPITALIS, you get more than an accounting service - you get a reliable partner who understands your business and supports you through everyday challenges and growth plans.',
                ];
            $payload['overview'] = $overview;
        }

        return $payload;
    }

    /**
     * Populate the consolidated CMS fields without overwriting a value that was
     * explicitly saved, including an intentionally empty editor.
     *
     * @param  array<string, mixed>  $payload
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    public static function hydrateStructuredEditorFields(string $templateKey, array $payload, array $overrides = []): array
    {
        if ($templateKey === self::AUDIT) {
            $overviewParagraphs = [];
            $overviewIntro = trim((string) data_get($payload, 'overview.intro', ''));

            if ($overviewIntro !== '') {
                $overviewParagraphs[] = $overviewIntro;
            }

            $overviewParagraphs = array_merge(
                $overviewParagraphs,
                (array) data_get($payload, 'overview.body', []),
            );
            $payload = self::hydrateRichTextField(
                $payload,
                $overrides,
                'overview.body_html',
                $overviewParagraphs,
            );

            $approachParagraphs = (array) data_get($payload, 'approach.body', []);
            if (collect($approachParagraphs)->filter(fn ($paragraph): bool => trim((string) $paragraph) !== '')->isEmpty()) {
                $approachIntro = trim((string) data_get($payload, 'approach.intro', ''));
                $approachParagraphs = $approachIntro !== '' ? [$approachIntro] : [];
            }
            $payload = self::hydrateRichTextField(
                $payload,
                $overrides,
                'approach.body_html',
                $approachParagraphs,
            );

            $primaryItems = array_values((array) data_get($payload, 'obligors.primary_items', []));
            foreach ($primaryItems as $index => $item) {
                if (! is_array($item)) {
                    continue;
                }

                $textPath = 'obligors.primary_items.'.$index.'.children_text';
                $itemsPath = 'obligors.primary_items.'.$index.'.children';
                $payload = self::hydrateLineListField($payload, $overrides, $textPath, $itemsPath);
            }
        }

        if ($templateKey === self::ACCOUNTING) {
            $overviewParagraphs = [];
            $overviewIntro = trim((string) data_get($payload, 'overview.intro', ''));
            $legacyOverviewBody = array_values((array) data_get($payload, 'overview.body', []));

            if ($overviewIntro !== '') {
                $overviewParagraphs[] = $overviewIntro;
            }
            if (array_key_exists(0, $legacyOverviewBody)) {
                $overviewParagraphs[] = $legacyOverviewBody[0];
            }

            $payload = self::hydrateRichTextField(
                $payload,
                $overrides,
                'overview.body_html',
                $overviewParagraphs,
            );
            $payload = self::hydrateRichTextField(
                $payload,
                $overrides,
                'overview.partner_body_html',
                array_slice($legacyOverviewBody, 1),
            );

            $approachParagraphs = (array) data_get($payload, 'approach.body', []);
            if (collect($approachParagraphs)->filter(fn ($paragraph): bool => trim((string) $paragraph) !== '')->isEmpty()) {
                $approachIntro = trim((string) data_get($payload, 'approach.intro', ''));
                $approachParagraphs = $approachIntro !== '' ? [$approachIntro] : [];
            }
            $payload = self::hydrateRichTextField(
                $payload,
                $overrides,
                'approach.body_html',
                $approachParagraphs,
            );
        }

        if ($templateKey === self::ADVISORY) {
            foreach ([
                ['overview.body_html', 'overview.body'],
                ['pandea.body_html', 'pandea.body'],
                ['approach.body_html', 'approach.body'],
                ['funding.approach_body_html', 'funding.approach_body'],
                ['ma.pandea.body_html', 'ma.pandea.body'],
            ] as [$htmlPath, $legacyPath]) {
                $payload = self::hydrateRichTextField(
                    $payload,
                    $overrides,
                    $htmlPath,
                    (array) data_get($payload, $legacyPath, []),
                );
            }

            foreach (['financial', 'bank_loans', 'zopu', 'ma', 'due_diligence', 'valuations', 'tax'] as $pageKey) {
                foreach ([
                    ['overview_body_html', 'overview_body'],
                    ['services_body_html', 'services_body'],
                    ['approach_body_html', 'approach_body'],
                ] as [$htmlKey, $legacyKey]) {
                    $payload = self::hydrateRichTextField(
                        $payload,
                        $overrides,
                        $pageKey.'.'.$htmlKey,
                        (array) data_get($payload, $pageKey.'.'.$legacyKey, []),
                    );
                }

                $payload = self::hydrateLineListField(
                    $payload,
                    $overrides,
                    $pageKey.'.help_items_text',
                    $pageKey.'.help_items',
                );
            }
        }

        if ($templateKey === self::EU_FUNDS) {
            foreach ([
                ['overview.body_html', 'overview.body'],
                ['approach.body_html', 'approach.body'],
            ] as [$htmlPath, $legacyPath]) {
                $payload = self::hydrateRichTextField(
                    $payload,
                    $overrides,
                    $htmlPath,
                    (array) data_get($payload, $legacyPath, []),
                );
            }

            foreach (array_values((array) data_get($payload, 'resources.cards', [])) as $cardIndex => $card) {
                $payload = self::hydrateRichTextField(
                    $payload,
                    $overrides,
                    'resources.cards.'.$cardIndex.'.body_html',
                    (array) data_get($card, 'body', []),
                );
            }

            foreach (array_values((array) data_get($payload, 'laws.cards', [])) as $cardIndex => $card) {
                foreach (array_values((array) data_get($card, 'lists', [])) as $listIndex => $list) {
                    $payload = self::hydrateLineListField(
                        $payload,
                        $overrides,
                        'laws.cards.'.$cardIndex.'.lists.'.$listIndex.'.items_text',
                        'laws.cards.'.$cardIndex.'.lists.'.$listIndex.'.items',
                    );
                }
            }
        }

        return $payload;
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, mixed>  $overrides
     * @param  array<int|string, mixed>  $legacyParagraphs
     * @return array<string, mixed>
     */
    private static function hydrateRichTextField(
        array $payload,
        array $overrides,
        string $htmlPath,
        array $legacyParagraphs,
    ): array {
        if (! self::nestedKeyExists($overrides, $htmlPath)) {
            data_set($payload, $htmlPath, StructuredRichText::fromParagraphs($legacyParagraphs));
        }

        data_set(
            $payload,
            $htmlPath,
            StructuredRichText::sanitize(data_get($payload, $htmlPath, '')),
        );

        return $payload;
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private static function hydrateLineListField(
        array $payload,
        array $overrides,
        string $textPath,
        string $itemsPath,
    ): array {
        if (self::nestedKeyExists($overrides, $textPath)) {
            $items = StructuredRichText::itemsFromLines(data_get($payload, $textPath, ''));
            data_set($payload, $itemsPath, $items);
            data_set($payload, $textPath, StructuredRichText::lines($items));

            return $payload;
        }

        data_set(
            $payload,
            $textPath,
            StructuredRichText::lines((array) data_get($payload, $itemsPath, [])),
        );

        return $payload;
    }

    /**
     * Check for a nested key without treating null or an empty string as absent.
     *
     * @param  array<string, mixed>  $payload
     */
    private static function nestedKeyExists(array $payload, string $path): bool
    {
        $current = $payload;

        foreach (explode('.', $path) as $segment) {
            if (! is_array($current) || ! array_key_exists($segment, $current)) {
                return false;
            }

            $current = $current[$segment];
        }

        return true;
    }

    /**
     * @return array<string, mixed>
     */
    private static function servicesIndexDefaultsForLocale(string $locale): array
    {
        if (str_starts_with(strtolower($locale), 'hr')) {
            return [
                'showcase' => [
                    'title_lead' => 'Naše usluge',
                    'intro' => 'Kroz integrirani pristup reviziji, računovodstvu i financijskom savjetovanju stvaramo dodatnu vrijednost pomažući klijentima da posluju sigurnije, transparentnije i učinkovitije.',
                    'card_action_label' => 'SAZNAJTE VIŠE',
                    'outro' => [
                        'Kroz integrirani pristup reviziji, računovodstvu i financijskom savjetovanju stvaramo dodatnu vrijednost za klijente koji žele stabilan rast, jasnije odluke i pouzdanu podršku u ključnim poslovnim trenucima.',
                        'Naša podrška omogućuje bolje upravljanje financijama, kvalitetnije strateško planiranje i sigurnije donošenje odluka.',
                    ],
                ],
                'primary_pillars' => [
                    [
                        'key' => 'audit',
                        'title' => 'Revizija',
                        'subtitle' => 'sigurnost i povjerenje u brojke',
                        'text' => 'Neovisna provjera financijskih izvještaja koja povećava povjerenje vlasnika, investitora i partnera.',
                        'image_alt' => 'Potpisivanje poslovnog dokumenta za stolom',
                        'bullets' => [
                            'Pomažemo vlasnicima, investitorima i upravi da imaju potpunu sigurnost u financijske izvještaje.',
                            'Revizija smanjuje rizik pogrešnih odluka jer potvrđuje da su podaci točni, potpuni i u skladu s propisima.',
                            'Kroz neovisnu provjeru dobivate jasnu sliku stvarnog financijskog stanja poduzeća, što jača povjerenje banaka, partnera i regulatora.',
                        ],
                        'url' => '/revizija',
                        'action_label' => 'Detaljnije',
                    ],
                    [
                        'key' => 'accounting',
                        'title' => 'Računovodstvo',
                        'subtitle' => 'kontrola i jasnoća poslovanja',
                        'text' => 'Precizno vođenje knjiga i pravovremeno izvještavanje koje oslobađa menadžment za strateške odluke.',
                        'image_alt' => 'Rad na financijskim podacima na prijenosnom računalu',
                        'bullets' => [
                            'Omogućujemo da vaše poslovanje bude financijski uredno, pregledno i uvijek spremno za odluke.',
                            'To znači da u svakom trenutku imate točne podatke o prihodima, troškovima i rezultatu, bez kašnjenja i nejasnoća.',
                            'Umjesto da reagirate na probleme, možete upravljati poslovanjem na temelju pouzdanih informacija.',
                        ],
                        'url' => '/racunovodstvo',
                        'action_label' => 'Detaljnije',
                    ],
                    [
                        'key' => 'advisory',
                        'title' => 'Savjetovanje',
                        'subtitle' => 'rast, optimizacija i bolji financijski izbor',
                        'text' => 'Financijsko i porezno savjetovanje te pribavljanje kapitala - sve na jednom mjestu.',
                        'image_alt' => 'Poslovni razgovor tijekom savjetovanja',
                        'bullets' => [
                            'Pomažemo društvima, investitorima i poduzetnicima u donošenju kvalitetnih odluka, upravljanju rizicima i stvaranju dugoročne vrijednosti.',
                            'Pružamo podršku u procjenama vrijednosti, due diligence postupcima, M&A procesima i strukturiranju financiranja.',
                            'EU fondovi, bankovni krediti i porezne olakšice povezani su u okviru pribavljanja financiranja.',
                        ],
                        'url' => '/savjetovanje',
                        'action_label' => 'Detaljnije',
                    ],
                ],
            ];
        }

        return [
            'showcase' => [
                'title_lead' => 'Our services',
                'intro' => 'Through an integrated approach to audit, accounting, and financial advisory, we create value by helping clients operate with more confidence, transparency, and efficiency.',
                'card_action_label' => 'LEARN MORE',
                'outro' => [
                    'Our support helps companies manage finance more clearly, plan strategically, and make safer decisions in key business moments.',
                ],
            ],
            'primary_pillars' => [
                [
                    'key' => 'audit',
                    'title' => 'Audit',
                    'subtitle' => 'assurance and confidence in the numbers',
                    'text' => 'Independent review of financial statements that increases confidence for owners, investors, and partners.',
                    'image_alt' => 'Signing a business document at a desk',
                    'bullets' => [
                        'We help owners, investors, and management gain confidence in financial statements.',
                        'Audit reduces the risk of wrong decisions by confirming that data is accurate, complete, and compliant.',
                        'Through independent review you gain a clear view of the company financial position, strengthening trust with banks, partners, and regulators.',
                    ],
                    'url' => '/revizija',
                    'action_label' => 'Learn more',
                ],
                [
                    'key' => 'accounting',
                    'title' => 'Accounting',
                    'subtitle' => 'control and clarity of operations',
                    'text' => 'Precise bookkeeping and timely reporting that frees management for strategic decisions.',
                    'image_alt' => 'Working with financial data on a laptop',
                    'bullets' => [
                        'We help keep your business financially organized, transparent, and ready for decisions.',
                        'That means accurate data on revenue, costs, and results at any moment, without delays or uncertainty.',
                        'Instead of reacting to problems, you can manage the business based on reliable information.',
                    ],
                    'url' => '/racunovodstvo',
                    'action_label' => 'Learn more',
                ],
                [
                    'key' => 'advisory',
                    'title' => 'Advisory',
                    'subtitle' => 'growth, optimization and better financial choices',
                    'text' => 'Financial and tax advisory plus capital raising - all in one place.',
                    'image_alt' => 'Business conversation during an advisory meeting',
                    'bullets' => [
                        'We help companies, investors, and entrepreneurs make better decisions, manage risk, and create long-term value.',
                        'We support valuations, due diligence, M&A processes, and financing structuring.',
                        'EU funds, bank loans, and tax incentives are connected within the capital raising framework.',
                    ],
                    'url' => '/savjetovanje',
                    'action_label' => 'Learn more',
                ],
            ],
        ];
    }

    /**
     * Keep the three fixed landing cards in their frontend order while filling in
     * newly introduced fields for payloads saved before those fields existed.
     *
     * @param  array<int, mixed>  $defaults
     * @param  array<int, mixed>  $overrides
     * @return array<int, array<string, mixed>>
     */
    private static function mergeServicesIndexPillars(array $defaults, array $overrides): array
    {
        $overridesByKey = collect($overrides)
            ->filter(fn ($item): bool => is_array($item) && trim((string) ($item['key'] ?? '')) !== '')
            ->keyBy(fn (array $item): string => trim((string) $item['key']));

        return collect($defaults)
            ->map(function ($default, int $index) use ($overrides, $overridesByKey): array {
                $default = is_array($default) ? $default : [];
                $key = trim((string) ($default['key'] ?? ''));
                $override = $key !== '' ? $overridesByKey->get($key) : null;

                if (! is_array($override)) {
                    $override = is_array($overrides[$index] ?? null) ? $overrides[$index] : [];
                }

                return self::deepMerge($default, $override);
            })
            ->values()
            ->all();
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

    /**
     * @param  array<string|int, mixed>  $values
     * @return array<string|int, mixed>
     */
    private static function blankEditorValues(array $values): array
    {
        foreach ($values as $key => $value) {
            if (is_array($value)) {
                $values[$key] = self::blankEditorValues($value);

                continue;
            }

            // Stable card keys are editor structure, not translatable copy.
            if ($key === 'key') {
                continue;
            }

            $values[$key] = match (true) {
                is_bool($value) => false,
                is_int($value), is_float($value) => 0,
                $value === null => null,
                default => '',
            };
        }

        return $values;
    }
}
