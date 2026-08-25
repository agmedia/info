<?php

namespace App\Support\Content;

class CareerPageDefaults
{
    /**
     * @return array<string, mixed>
     */
    public static function merge(mixed $payload, string $locale): array
    {
        $isCroatian = self::isCroatian($locale);
        $defaults = $isCroatian
            ? self::croatianDefaults()
            : self::blankEditorStructure(self::englishDefaults());
        $source = is_array($payload) ? $payload : [];

        $merged = self::mergeValues($defaults, $source);
        $sourceIntro = is_array($source['intro'] ?? null) ? $source['intro'] : [];
        $sourceProcess = is_array($source['process'] ?? null) ? $source['process'] : [];
        $sourceApplication = is_array($source['application'] ?? null) ? $source['application'] : [];

        $merged['intro']['hero_body_html'] = array_key_exists('hero_body_html', $sourceIntro)
            ? trim((string) $sourceIntro['hero_body_html'])
            : StructuredRichText::fromParagraphs(array_slice((array) data_get($merged, 'intro.body', []), 1));

        $merged['process']['title'] = array_key_exists('title', $sourceProcess)
            ? trim((string) $sourceProcess['title'])
            : trim(implode(' ', array_filter([
                data_get($merged, 'process.title_line_one'),
                data_get($merged, 'process.title_line_two'),
            ])));

        $merged['application']['body_html'] = array_key_exists('body_html', $sourceApplication)
            ? trim((string) $sourceApplication['body_html'])
            : StructuredRichText::fromParagraphs((array) data_get($merged, 'application.paragraphs', []));

        if (array_key_exists('values_text', $source)) {
            $merged['values'] = StructuredRichText::itemsFromLines($source['values_text']);
        }
        $merged['values_text'] = StructuredRichText::lines((array) ($merged['values'] ?? []));

        foreach ((array) ($merged['stories'] ?? []) as $storyIndex => $story) {
            if (! is_array($story)) {
                continue;
            }

            $sourceStory = is_array(data_get($source, 'stories.'.$storyIndex))
                ? data_get($source, 'stories.'.$storyIndex)
                : [];

            $merged['stories'][$storyIndex]['body_html'] = array_key_exists('body_html', $sourceStory)
                ? trim((string) $sourceStory['body_html'])
                : StructuredRichText::fromParagraphs((array) ($story['paragraphs'] ?? []));

            if (array_key_exists('list_text', $sourceStory)) {
                $merged['stories'][$storyIndex]['list'] = StructuredRichText::itemsFromLines($sourceStory['list_text']);
            }

            $merged['stories'][$storyIndex]['list_text'] = StructuredRichText::lines(
                (array) data_get($merged, 'stories.'.$storyIndex.'.list', []),
            );
        }

        return $merged;
    }

    /**
     * @return array<string, mixed>
     */
    private static function croatianDefaults(): array
    {
        return [
            'intro' => [
                'section_title' => 'Karijera u ALPHA CAPITALISU',
                'title' => 'Mjesto gdje ljudi i karijere rastu',
                'highlight' => 'Tražimo ljude, ne samo životopise.',
                'kicker' => 'Rastemo zajedno',
                'body' => [
                    'Tražimo znatiželjne ljude koji žele učiti, preuzimati odgovornost i zajedno s nama graditi nešto dugoročno.',
                    'ALPHA CAPITALIS danas okuplja 75 stručnjaka iz područja računovodstva, financija, revizije, EU fondova i savjetovanja. U našem Sky Officeu znanje dijelimo otvoreno, pomažemo jedni drugima i ozbiljan posao gradimo u atmosferi u kojoj ljudi mogu biti svoji.',
                    'Od prvog dana radit ćeš na stvarnim poslovnim izazovima, uz mentora, podršku iskusnih kolega i prostor da predložiš bolji način rada. Želimo da posao bude mjesto na kojem rasteš i kojem se rado vraćaš.',
                ],
                'values_label' => 'Što nudimo',
                'button_label' => 'OTVORENE POZICIJE',
                'image_alt' => '',
                'stat_value' => '75',
                'stat_label' => 'stručnjaka iz računovodstva, financija, revizije i savjetovanja',
            ],
            'process' => [
                'kicker' => 'Zašto ALPHA CAPITALIS?',
                'title_line_one' => 'Razvoj koji nije',
                'title_line_two' => 'samo fraza',
                'intro' => 'Vjerujemo da se potencijal razvija kroz iskustvo, mentorstvo i prilike. Zato naši zaposlenici od prvog dana aktivno sudjeluju u projektima, surađuju s klijentima i razvijaju stručna znanja kroz rad s različitim industrijama i poslovnim izazovima.',
                'steps' => [
                    [
                        'step' => '01',
                        'title' => 'Povjerenje',
                        'description' => 'Od prvog dana dobivaš prostor sudjelovati u stvarnim projektima i preuzimati odgovornost uz jasnu podršku tima.',
                    ],
                    [
                        'step' => '02',
                        'title' => 'Podrška',
                        'description' => 'Učiš kroz mentorstvo, suradnju s iskusnim stručnjacima i otvoreno dijeljenje znanja unutar tima.',
                    ],
                    [
                        'step' => '03',
                        'title' => 'Prilika za razvoj',
                        'description' => 'Radiš s različitim industrijama i poslovnim izazovima, bez čekanja godinama da pokažeš što znaš.',
                    ],
                    [
                        'step' => '04',
                        'title' => 'Prostor za ideje',
                        'description' => 'Cijenimo proaktivnost, nova rješenja i ljude koji žele aktivno graditi bolji način rada.',
                    ],
                ],
            ],
            'application' => [
                'kicker' => 'Prijave',
                'title' => 'Otvorene pozicije',
                'highlight' => 'Pronađi svoje mjesto u našem timu',
                'paragraphs' => [
                    'Tražimo ambiciozne, odgovorne i proaktivne ljude koji žele razvijati svoje znanje i karijeru u okruženju koje potiče rast.',
                    'Ne vidiš otvorenu poziciju?',
                    'Uvijek smo otvoreni za kvalitetne ljude. Ako vjeruješ da bi bio dobar dio ALPHA CAPITALIS tima, pošalji nam svoj životopis i predstavi se. Možda upravo ti budeš naše sljedeće veliko pojačanje.',
                ],
            ],
            'form' => [
                'title' => 'Pošalji nam svoj životopis',
                'intro' => 'Ispunite osnovne podatke i učitajte životopis kako bismo vas mogli kontaktirati kada prepoznamo podudaranje s otvorenim pozicijama.',
                'first_name' => 'Ime',
                'last_name' => 'Prezime',
                'email' => 'Email',
                'message' => 'Poruka (opcionalno)',
                'cv' => 'Upload CV-a',
                'cv_button' => 'Odaberi datoteku',
                'cv_empty' => 'Datoteka nije odabrana.',
                'cv_help' => 'Podržani formati: PDF, DOC i DOCX. Maksimalna veličina datoteke je 5 MB.',
                'accept_terms' => 'Slažem se s obradom osobnih podataka za potrebe selekcijskog postupka.',
                'submit' => 'Pošalji prijavu',
            ],
            'stories_section' => [
                'title' => 'Život u ALPHA CAPITALISU',
                'intro' => 'Više od radnog mjesta',
            ],
            'values' => [
                'povjerenje',
                'podršku',
                'priliku za razvoj',
                'prostor za ideje',
                'tim koji ih gura naprijed',
            ],
            'stories' => [
                [
                    'kicker' => 'Tim',
                    'title' => 'Ljudi zbog kojih ostaješ',
                    'paragraphs' => [
                        'Možeš imati odličan posao, ali bez dobrog tima ništa nema smisla.',
                        'U ALPHA CAPITALISU gradimo kulturu međusobnog poštovanja, suradnje i otvorene komunikacije. Vjerujemo u dijeljenje znanja, podršku među kolegama i atmosferu u kojoj ljudi mogu biti profesionalni, ali i svoji.',
                        'Ozbiljni smo u poslu, ali vjerujemo da dobra atmosfera i kvalitetni odnosi čine veliku razliku.',
                    ],
                    'list' => [],
                ],
                [
                    'kicker' => 'Izazovi',
                    'title' => 'Okruženje koje te potiče na više',
                    'paragraphs' => [
                        'Radimo s poduzetnicima, obiteljskim tvrtkama i kompanijama koje rastu i razvijaju se. Zato ni naš posao nije rutinski.',
                        'Svaki projekt donosi nove izazove, nova znanja i priliku da razvijaš širu poslovnu perspektivu.',
                        'Ako voliš dinamiku, odgovornost i kontinuirani razvoj - osjećat ćeš se kao doma.',
                    ],
                    'list' => [],
                ],
                [
                    'kicker' => 'Rast',
                    'title' => 'Rastemo zajedno',
                    'paragraphs' => [
                        'ALPHA CAPITALIS nije mjesto gdje samo dolaziš odraditi posao.',
                        'Naš cilj je stvoriti okruženje u kojem ljudi dugoročno žele ostati, razvijati se i biti ponosni na ono što zajedno gradimo.',
                    ],
                    'list' => [
                        'znanje',
                        'iskustvo',
                        'odnose',
                        'samostalnost',
                        'karijeru',
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function englishDefaults(): array
    {
        return [
            'intro' => [
                'section_title' => 'A career at ALPHA CAPITALIS',
                'title' => 'A place where people and careers grow',
                'highlight' => 'We are looking for people, not just resumes.',
                'kicker' => 'Growing together',
                'body' => [
                    'We are looking for people who want to learn, develop, take responsibility and build something long-term with us.',
                    'Today, ALPHA CAPITALIS brings together 75 experts in accounting, finance, audit, EU funds and advisory. In our Sky Office, we share knowledge openly, help one another and build serious work in an atmosphere where people can be themselves.',
                    'With us, you will work on real business challenges, collaborate with experienced professionals and have the opportunity to grow faster than in a classic corporate environment.',
                ],
                'values_label' => 'What we offer',
                'button_label' => 'OPEN POSITIONS',
                'image_alt' => '',
                'stat_value' => '75',
                'stat_label' => 'experts in accounting, finance, audit and advisory',
            ],
            'process' => [
                'kicker' => 'Why ALPHA CAPITALIS?',
                'title_line_one' => 'Development that is',
                'title_line_two' => 'more than a phrase',
                'intro' => 'We believe potential grows through experience, mentorship and opportunity. From day one, our people actively participate in projects, collaborate with clients and build expertise through work across industries and business challenges.',
                'steps' => [
                    [
                        'step' => '01',
                        'title' => 'Trust',
                        'description' => 'From day one, you get the space to participate in real projects and take responsibility with clear team support.',
                    ],
                    [
                        'step' => '02',
                        'title' => 'Support',
                        'description' => 'You learn through mentorship, collaboration with experienced professionals and open knowledge sharing.',
                    ],
                    [
                        'step' => '03',
                        'title' => 'Growth opportunity',
                        'description' => 'You work with different industries and business challenges without waiting years to show what you can do.',
                    ],
                    [
                        'step' => '04',
                        'title' => 'Room for ideas',
                        'description' => 'We value proactivity, new solutions and people who want to actively build better ways of working.',
                    ],
                ],
            ],
            'application' => [
                'kicker' => 'Applications',
                'title' => 'Open positions',
                'highlight' => 'Find your place in our team',
                'paragraphs' => [
                    'We are looking for ambitious, responsible and proactive people who want to develop their knowledge and career in an environment that encourages growth.',
                    'Do not see an open position?',
                    'We are always open to quality people. If you believe you would be a good part of the ALPHA CAPITALIS team, send us your CV and introduce yourself.',
                ],
            ],
            'form' => [
                'title' => 'Send us your CV',
                'intro' => 'Fill in your basic details and upload your resume so we can contact you when your profile matches an open position.',
                'first_name' => 'First name',
                'last_name' => 'Last name',
                'email' => 'Email',
                'message' => 'Message (optional)',
                'cv' => 'CV upload',
                'cv_button' => 'Choose file',
                'cv_empty' => 'No file selected.',
                'cv_help' => 'Supported formats: PDF, DOC, and DOCX. Maximum file size is 5 MB.',
                'accept_terms' => 'I agree to the processing of personal data for recruitment purposes.',
                'submit' => 'Send application',
            ],
            'stories_section' => [
                'title' => 'Life at ALPHA CAPITALIS',
                'intro' => 'More than a workplace',
            ],
            'values' => [
                'trust',
                'support',
                'development opportunities',
                'room for ideas',
                'a team that pushes them forward',
            ],
            'stories' => [
                [
                    'kicker' => 'Team',
                    'title' => 'People who make you stay',
                    'paragraphs' => [
                        'You can have a great job, but without a good team it does not mean much.',
                        'At ALPHA CAPITALIS, we build a culture of mutual respect, collaboration and open communication. We believe in knowledge sharing, support among colleagues and an atmosphere where people can be professional and still be themselves.',
                        'We are serious about work, but we believe a good atmosphere and quality relationships make a real difference.',
                    ],
                    'list' => [],
                ],
                [
                    'kicker' => 'Challenges',
                    'title' => 'An environment that pushes you further',
                    'paragraphs' => [
                        'We work with entrepreneurs, family businesses and growing companies. That is why our work is not routine.',
                        'Every project brings new challenges, new knowledge and an opportunity to develop a broader business perspective.',
                        'If you like dynamics, responsibility and continuous development, you will feel at home.',
                    ],
                    'list' => [],
                ],
                [
                    'kicker' => 'Growth',
                    'title' => 'We grow together',
                    'paragraphs' => [
                        'ALPHA CAPITALIS is not a place where you only come to finish tasks.',
                        'Our goal is to create an environment where people want to stay long-term, develop and be proud of what we build together.',
                    ],
                    'list' => [
                        'knowledge',
                        'experience',
                        'relationships',
                        'independence',
                        'career',
                    ],
                ],
            ],
        ];
    }

    /**
     * @param  array<string|int, mixed>  $defaults
     * @param  array<string|int, mixed>  $source
     * @return array<string|int, mixed>
     */
    private static function mergeValues(array $defaults, array $source): array
    {
        $merged = $defaults;

        foreach ($source as $key => $value) {
            if (array_key_exists($key, $defaults) && is_array($defaults[$key])) {
                if (is_array($value)) {
                    $merged[$key] = self::mergeValues($defaults[$key], $value);
                }

                continue;
            }

            if (array_key_exists($key, $defaults) && is_array($value)) {
                continue;
            }

            if (is_array($value)) {
                $merged[$key] = self::mergeValues([], $value);

                continue;
            }

            if (is_scalar($value) || $value === null) {
                $merged[$key] = trim((string) $value);
            }
        }

        return $merged;
    }

    /**
     * Non-Croatian CMS locales use the established editor shape, but never
     * receive hardcoded copy. Text may only come from that locale's payload.
     *
     * @param  array<string|int, mixed>  $values
     * @return array<string|int, mixed>
     */
    private static function blankEditorStructure(array $values): array
    {
        foreach ($values as $key => $value) {
            if (is_array($value)) {
                $values[$key] = self::blankEditorStructure($value);

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

    private static function isCroatian(string $locale): bool
    {
        return str_starts_with(strtolower($locale), 'hr');
    }
}
