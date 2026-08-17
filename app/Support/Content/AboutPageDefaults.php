<?php

namespace App\Support\Content;

class AboutPageDefaults
{
    /**
     * @return array<string, mixed>
     */
    public static function merge(mixed $payload, string $locale): array
    {
        return self::mergeValues(
            self::forLocale($locale),
            is_array($payload) ? $payload : [],
        );
    }

    /**
     * @return array<string, mixed>
     */
    public static function forLocale(string $locale): array
    {
        return self::isCroatian($locale)
            ? self::croatianDefaults()
            : self::englishDefaults();
    }

    /**
     * @return array<string, mixed>
     */
    private static function croatianDefaults(): array
    {
        return [
            'hero' => [
                'eyebrow' => 'O nama',
                'title' => 'Naša priča',
                'lead' => 'Od stručnosti i iskustva do dugoročnih odnosa s klijentima.',
                'image_alt' => '',
                'stat_value' => '600+',
                'stat_label' => 'klijenata kojima svakodnevno pružamo podršku',
            ],
            'story' => [
                'kicker' => 'Naša priča',
                'title' => 'Partner za sigurne poslovne odluke',
                'paragraphs' => [
                    'ALPHA CAPITALIS nastao je iz želje da poduzetnicima pružimo više od standardne poslovne podrške. Od samih početaka gradimo tvrtku koja spaja stručnost, iskustvo i razumijevanje stvarnih izazova s kojima se susreću poduzetnici, obiteljske tvrtke i organizacije u razvoju.',
                    'Kroz godine rada razvili smo multidisciplinarni tim stručnjaka iz područja računovodstva, financija, poreza, revizije i EU fondova s ciljem pružanja cjelovitih i dugoročnih rješenja za naše klijente.',
                    'Danas ALPHA CAPITALIS posluje kao partner koji svojim klijentima pruža sigurnost u donošenju poslovnih odluka, stabilnost u poslovanju i podršku u svim fazama razvoja - od svakodnevnog operativnog poslovanja do strateških odluka i prijenosa poslovanja na nove generacije.',
                    'Naša priča temelji se na povjerenju, stručnosti i dugoročnim odnosima koje gradimo s klijentima. Upravo zato mnogi od njih s nama rastu već godinama.',
                ],
            ],
            'values' => [
                'kicker' => 'Naše vrijednosti',
                'label' => 'Naše vrijednosti',
                'title' => 'Jednostavni principi koji vode svaki dan',
                'intro' => 'U ALPHA CAPITALISU vrijednosti nisu samo riječi - one određuju kako razmišljamo, kako radimo i kako gradimo odnose. One su prisutne u svakodnevnim odlukama, u načinu na koji surađujemo unutar tima i u odnosu koji gradimo s klijentima.',
                'items' => [
                    [
                        'title' => 'Learn fast',
                        'lead' => 'Volimo ljude koji žele učiti, pitati, istraživati i brzo se razvijati.',
                        'paragraphs' => [
                            'Radimo u okruženju koje se stalno mijenja - tržište, zakoni, tehnologija, potrebe klijenata. Zato vjerujemo da je sposobnost brzog učenja jedna od najvažnijih stvari koje možemo imati kao tim.',
                            'Kod nas nije problem ne znati. Problem je ne htjeti naučiti.',
                            'Zato dijelimo znanje, učimo jedni od drugih, razvijamo se kroz praksu i ne čekamo savršeni trenutak da preuzmemo odgovornost. Učimo brzo jer želimo biti bolji - za sebe, za tim i za klijente.',
                        ],
                    ],
                    [
                        'title' => 'Work smart, not hard',
                        'lead' => 'Ne vjerujemo u kulturu "ostani duže pa će izgledati da puno radiš".',
                        'paragraphs' => [
                            'Vjerujemo u pametan rad. To znači da razmišljamo unaprijed, postavljamo prioritete, tražimo bolja rješenja i ne radimo stvari samo zato što su se uvijek tako radile.',
                            'Volimo ljude koji prepoznaju problem, ali još više one koji predlažu rješenje. Za nas produktivnost nije kaos, nego fokus. Nije više sati, nego bolji način rada.',
                            'Želimo stvarati rezultate bez nepotrebne kompleksnosti - kvalitetno, odgovorno i s jasnim ciljem.',
                        ],
                    ],
                    [
                        'title' => 'Relationship over transaction',
                        'lead' => 'Ljudi su uvijek važniji od procesa.',
                        'paragraphs' => [
                            'Ne gradimo odnose koji traju jedan projekt ili jedan e-mail. Gradimo partnerstva.',
                            'To vrijedi za klijente, ali i za tim. Vjerujemo da se povjerenje gradi dostupnošću, iskrenošću, kvalitetnom komunikacijom i spremnošću da budemo tu kada je važno.',
                            'Volimo dugoročne odnose jer vjerujemo da najbolje stvari nastaju kada postoji povjerenje i kada ljudi stvarno razumiju jedni druge. Na kraju dana, brojke jesu važne, ali ljudi su razlog zašto posao ima smisla.',
                        ],
                    ],
                ],
            ],
            'why' => [
                'kicker' => 'Zašto postojimo',
                'title' => 'Podrška za sigurno, kvalitetno i održivo poslovanje',
                'quote' => 'Vjerujemo da uspješno poslovanje ne počiva samo na brojkama, već i na kvalitetnim odnosima, jasnoj strategiji i pravovremenim odlukama.',
                'paragraphs' => [
                    'Postojimo kako bismo poduzetnicima omogućili sigurnije, kvalitetnije i održivije poslovanje.',
                    'Naša je misija biti pouzdan partner koji klijentima pruža stručnu podršku u svim ključnim poslovnim područjima - od financija i računovodstva do strateškog razvoja, revizije i EU fondova.',
                    'Zato klijentima pristupamo individualno, razumijemo njihove izazove i zajedno stvaramo rješenja prilagođena njihovim ciljevima.',
                    'Naš cilj nije samo pratiti poslovanje klijenata, već aktivno doprinositi njihovom rastu i dugoročnoj stabilnosti.',
                ],
            ],
            'team' => [
                'kicker' => 'TIM',
                'label' => 'Naš tim',
                'title' => 'Ljudi koji stoje iza ALPHA CAPITALISA',
                'intro' => 'ALPHA CAPITALIS danas čini snažan i multidisciplinaran tim stručnjaka koji svakodnevno pruža podršku klijentima iz različitih industrija i poslovnih sektora.',
                'body' => 'Naš tim čine stručnjaci iz područja računovodstva, revizije i poslovnog savjetovanja koji zajednički rade na pružanju kvalitetnih, pravovremenih i prilagođenih rješenja.',
                'stats' => [
                    ['value' => '70+', 'label' => 'stručnjaka'],
                    ['value' => '9', 'label' => 'članova uprave'],
                    ['value' => '600+', 'label' => 'klijenata'],
                    ['value' => '3', 'label' => 'ureda u Zagrebu, Vinkovcima i Rijeci'],
                ],
                'button_label' => 'Upoznaj cijeli tim',
            ],
            'culture' => [
                'kicker' => 'Naša kultura',
                'title' => 'Kvalitetno poslovanje počinje kvalitetnim odnosima',
                'quote' => 'U ALPHA CAPITALISU vjerujemo da kvalitetno poslovanje počinje kvalitetnim odnosima.',
                'paragraphs' => [
                    'Gradimo kulturu koja potiče suradnju, profesionalni razvoj, otvorenu komunikaciju i međusobno poštovanje.',
                    'Naš tim čine ljudi različitih iskustava i stručnosti koje povezuje zajednički cilj - pružiti klijentima najbolju moguću podršku.',
                    'Potičemo kontinuirano učenje, razmjenu znanja i razvoj novih ideja jer vjerujemo da upravo ljudi čine najveću razliku.',
                    'Uz profesionalnost, jednako nam je važna pozitivna radna atmosfera, osjećaj pripadnosti i zajednički rast.',
                ],
            ],
            'responsibility' => [
                'kicker' => 'Društveno odgovorno poslovanje',
                'title' => 'AUXILIUM CAPITALIS - ulaganje u budućnost',
                'quote' => 'Vjerujemo da uspjeh ima najveću vrijednost kada stvara prilike za druge.',
                'paragraphs' => [
                    'Zato smo pokrenuli AUXILIUM CAPITALIS - inicijativu usmjerenu na stipendiranje učenika i pružanje podrške mladima kroz obrazovanje, razvoj i financijsku pismenost.',
                    'Naš cilj je pomoći talentiranim i perspektivnim mladim ljudima da lakše ostvare svoj potencijal, bez obzira na okolnosti iz kojih dolaze.',
                    'Vjerujemo da ulaganje u znanje, prilike i mlade generacije dugoročno mijenja zajednicu na bolje.',
                    'AUXILIUM CAPITALIS nije samo projekt - to je način na koji želimo vraćati zajednici i stvarati konkretan, dugoročan utjecaj.',
                ],
                'cta_intro' => 'Želite biti dio ove priče?',
                'cta_text' => 'Pozivamo pojedince, partnere i tvrtke da nam se pridruže u stvaranju novih prilika za mlade i zajedno pomognemo graditi bolju budućnost.',
                'cta_button_label' => 'Kontaktirajte nas',
                'cta_card_title' => 'Zajedno možemo više.',
                'cta_status' => 'Otvoreni smo za razgovor i nova partnerstva.',
            ],
            'references' => [
                'kicker' => 'Reference',
                'label' => 'Naše reference',
                'title' => 'Povjerenje klijenata potvrđuje kvalitetu našeg rada',
                'paragraphs' => [
                    'Povjerenje više od 600 klijenata iz različitih industrija i sektora potvrda je kvalitete i stručnosti koju svakodnevno pružamo.',
                    'Surađujemo s malim, srednjim i velikim poduzećima kojima pružamo podršku u području računovodstva, revizije i poslovnog savjetovanja.',
                    'Naši dugoročni odnosi s klijentima temelje se na povjerenju, dostupnosti, stručnosti i razumijevanju njihovih poslovnih ciljeva.',
                    'Uspjeh naših klijenata ujedno je i najveća potvrda našeg rada.',
                ],
                'button_label' => 'Sve reference',
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function englishDefaults(): array
    {
        return [
            'hero' => [
                'eyebrow' => 'About us',
                'title' => 'Our story',
                'lead' => 'From expertise and experience to long-term client relationships.',
                'image_alt' => '',
                'stat_value' => '600+',
                'stat_label' => 'clients supported by our team',
            ],
            'story' => [
                'kicker' => 'Our story',
                'title' => 'A partner for confident business decisions',
                'paragraphs' => [
                    'ALPHA CAPITALIS was created from the desire to give entrepreneurs more than standard business support. From the beginning, we have built a company that combines expertise, experience and an understanding of the real challenges faced by entrepreneurs, family businesses and growing organisations.',
                    'Over the years, we have developed a multidisciplinary team of specialists in accounting, finance, tax, audit and EU funds, focused on providing complete and long-term solutions for our clients.',
                    'Today, ALPHA CAPITALIS works as a partner that gives clients confidence in decision-making, stability in operations and support across all stages of development - from daily operations to strategic decisions and business succession.',
                    'Our story is built on trust, expertise and long-term relationships with clients. That is why many of them have been growing with us for years.',
                ],
            ],
            'values' => [
                'kicker' => 'Our values',
                'label' => 'Our values',
                'title' => 'Simple principles that guide our work',
                'intro' => 'At ALPHA CAPITALIS, values are not only words. They define how we think, work and build relationships with each other and with our clients.',
                'items' => [
                    [
                        'title' => 'Learn fast',
                        'lead' => 'We value people who want to learn, ask, explore and develop quickly.',
                        'paragraphs' => [
                            'We work in an environment that keeps changing - markets, laws, technology and client needs. The ability to learn quickly is one of the most important strengths we can have as a team.',
                            'Not knowing is not the problem. Not wanting to learn is.',
                            'That is why we share knowledge, learn from one another, grow through practice and take responsibility without waiting for a perfect moment.',
                        ],
                    ],
                    [
                        'title' => 'Work smart, not hard',
                        'lead' => 'We do not believe in a culture where staying late is the measure of work.',
                        'paragraphs' => [
                            'We believe in thoughtful work. That means planning ahead, setting priorities, looking for better solutions and avoiding habits that exist only because they have always been done that way.',
                            'We value people who recognise problems, and even more those who propose solutions. Productivity is not chaos, but focus.',
                            'We want to create results without unnecessary complexity - responsibly, clearly and with quality.',
                        ],
                    ],
                    [
                        'title' => 'Relationship over transaction',
                        'lead' => 'People are always more important than process.',
                        'paragraphs' => [
                            'We do not build relationships that last one project or one email. We build partnerships.',
                            'That applies to clients and to our team. Trust is built through availability, honesty, quality communication and being present when it matters.',
                            'Numbers matter, but people are the reason work has meaning.',
                        ],
                    ],
                ],
            ],
            'why' => [
                'kicker' => 'Why we exist',
                'title' => 'Support for secure, quality and sustainable business',
                'quote' => 'We believe successful business is not built only on numbers, but also on quality relationships, clear strategy and timely decisions.',
                'paragraphs' => [
                    'We exist to help entrepreneurs build more secure, higher-quality and more sustainable businesses.',
                    'Our mission is to be a reliable partner that provides expert support in key business areas - from finance and accounting to strategic development, audit and EU funds.',
                    'Our goal is not only to follow our clients business, but to actively contribute to their growth and long-term stability.',
                ],
            ],
            'team' => [
                'kicker' => 'TEAM',
                'label' => 'Our team',
                'title' => 'The people behind ALPHA CAPITALIS',
                'intro' => 'ALPHA CAPITALIS is a strong multidisciplinary team of experts supporting clients from different industries and business sectors every day.',
                'body' => 'Our team brings together specialists in accounting, audit and business advisory who work together to provide quality, timely and tailored solutions.',
                'stats' => [
                    ['value' => '70+', 'label' => 'experts'],
                    ['value' => '9', 'label' => 'management board members'],
                    ['value' => '600+', 'label' => 'clients'],
                    ['value' => '3', 'label' => 'offices in Zagreb, Vinkovci and Rijeka'],
                ],
                'button_label' => 'Meet the full team',
            ],
            'culture' => [
                'kicker' => 'Our culture',
                'title' => 'Quality business starts with quality relationships',
                'quote' => 'At ALPHA CAPITALIS, we believe quality business starts with quality relationships.',
                'paragraphs' => [
                    'We build a culture that encourages collaboration, professional development, open communication and mutual respect.',
                    'We encourage continuous learning, knowledge sharing and the development of new ideas because we believe people make the biggest difference.',
                    'Alongside professionalism, a positive working atmosphere, belonging and shared growth are equally important to us.',
                ],
            ],
            'responsibility' => [
                'kicker' => 'Social responsibility',
                'title' => 'AUXILIUM CAPITALIS - investing in the future',
                'quote' => 'We believe success has the greatest value when it creates opportunities for others.',
                'paragraphs' => [
                    'That is why we launched AUXILIUM CAPITALIS - an initiative focused on scholarships and supporting young people through education, development and financial literacy.',
                    'Our goal is to help talented and promising young people reach their potential, regardless of their circumstances.',
                    'AUXILIUM CAPITALIS is not only a project. It is the way we want to give back to the community and create concrete, long-term impact.',
                ],
                'cta_intro' => 'Would you like to be part of this story?',
                'cta_text' => 'We invite individuals, partners and companies to join us in creating new opportunities for young people and helping build a better future.',
                'cta_button_label' => 'Contact us',
                'cta_card_title' => 'Together, we can do more.',
                'cta_status' => 'We are open to conversations and new partnerships.',
            ],
            'references' => [
                'kicker' => 'References',
                'label' => 'Our references',
                'title' => 'Client trust confirms the quality of our work',
                'paragraphs' => [
                    'The trust of more than 600 clients across different industries and sectors confirms the quality and expertise we provide every day.',
                    'We work with small, medium-sized and large companies, supporting them in accounting, audit and business advisory.',
                    'Our long-term client relationships are based on trust, availability, expertise and an understanding of their business goals.',
                    'The success of our clients is also the strongest confirmation of our work.',
                ],
                'button_label' => 'All references',
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

    private static function isCroatian(string $locale): bool
    {
        return str_starts_with(strtolower($locale), 'hr');
    }
}
