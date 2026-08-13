# Standard za stranice usluga

Ovaj dokument je trajna radna smjernica za izradu glavnih stranica usluga ALPHA CAPITALISA. Vizualni i interakcijski autoritet je stranica `/revizija`, pregledana 13. kolovoza 2026. na širinama 1440 px, 1280 px i 390 px. Tekstualne smjernice i odobrene formulacije preuzete su iz dokumenta `WEB 10.08.(1).docx`.

Prije rada na stranici usluge obavezno pregledati:

- `resources/views/front/desktop/pages/audit.blade.php`
- `public/front-theme/styles/pages/audit.css`
- `public/front-theme/styles/alpha-redesign.css`
- `public/front-theme/scripts/alpha-redesign.js`
- odgovarajući kontroler i klasu zadanog sadržaja (`*ServicePageDefaults.php`)

## 1. Što znači "identičan princip"

Sve glavne stranice usluga trebaju imati isti sustav, ritam i ponašanje kao `/revizija`, ali ne moraju imati iste specifične module. Osnovni redoslijed je:

1. globalni header preko hero fotografije
2. hero s nazivom usluge i snažnom hook rečenicom
3. svijetli 50/50 uvod s naslovom „Zašto Vam je [usluga] bitna?”
4. sadržaj specifičan usluzi, ako postoji
5. pregled usluga u karticama
6. tamna sekcija „Naš pristup”
7. povezane stručne objave, samo kada postoje
8. standardni tamni `contact-cta`
9. neizmijenjeni globalni newsletter i footer

Specifični moduli, primjerice obveznici revizije ili Pandea Global M&A, smiju se zadržati. Moraju koristiti isti navy/cream/gold sustav, tipografiju, širine, kutove i animacije te se uključuju između uvoda/usluga i pristupa. Ne stvarati zaseban vizualni sustav za svaku uslugu.

## 2. Hero

- Hero je full-bleed i koristi stvarnu fotografiju ili odobrenu servisnu ilustraciju, s tamnim navy overlayem.
- Desktop visina slijedi `/revizija`: približno `65dvh`, uz sigurne minimalne i maksimalne granice. Mobilno približno `70dvh`.
- Header je apsolutno položen preko hero sekcije i koristi postojeću globalnu navigaciju.
- Jedini `h1` sadrži dva semantička reda: naziv usluge i hook.
- Naziv usluge koristi `Bodoni Moda Variable`; hook koristi `Instrument Sans Variable`.
- Hook je sadržajni fokus. Kod duljih naziva, posebno „Računovodstvo”, naziv se smije razmjerno smanjiti kako ne bi potisnuo hook ili se loše prelomio.
- Fotografija koristi `object-fit: cover`, namjerno odabran `object-position` za desktop i mobilni prikaz te lagani ulazni zoom kao na `/revizija`.
- Slika mora imati smislen alt tekst. Overlay i ukrasni elementi su skriveni od čitača ekrana.

## 3. Uvod „Zašto Vam je ... bitno?”

- Na desktopu su dvije potpuno jednake kolone bez grida između njih, tako da razdjelnik ostaje na stvarnom centru stranice. Razmak se stvara unutarnjim paddingom.
- Lijevo je veliki sans-serif naslov. Zadnja ključna riječ je zlatna, italic i u fontu `Bodoni Moda Variable`.
- Desno su dva kratka odlomka. Prvi objašnjava poslovnu korist, drugi pojačava sigurnost, partnerstvo ili stručnu vrijednost.
- Drugi odlomak je naglašen. Ako brief izričito označava kraći dio teksta, koristiti semantički `<strong>` samo oko tog dijela.
- Na mobilnom se kolone pretvaraju u jedan stupac, a vertikalni razdjelnik postaje vodoravna linija između naslova i teksta.
- Naslov, copy i razdjelnik moraju zadržati isti ritam i hijerarhiju kao na `/revizija`.

## 4. Vizualni sustav

- Glavne boje: cream `#f2efe7`, duboki navy `#03121f`, navy `#061a2b`, svijetlo zlato `#d8b96f` i zlato `#c6a35a`. Preferirati postojeće CSS varijable.
- Standardna široka ljuska: `min(1600px, calc(100% - 136px))`; na 1280 px koristiti bočne margine od oko 40 px; na mobilnom 20 px.
- Naslovi sekcija koriste `Instrument Sans Variable`, težinu oko 420, čvrst line-height i negativni letter-spacing. Editorial naglasci koriste `Bodoni Moda Variable`, italic.
- Tekst kartica i body copy koriste `Instrument Sans Variable`; Bodoni je rezerviran za postojeće editorial uloge, naslove kartica i citate.
- Vertikalni razmak sekcija prati clamped raspon `/revizija`; desktop približno 5,5–8,2 rem, mobilno približno 4–5,5 rem.
- Kartice koriste tanki zlatni/navy obrub i postojeći asimetrični uzorak kutova. Ne dodavati pill oblik ili potpuno zaobljene kartice.
- Font Awesome Pro duotone/thin ikone su zadani izbor. Ikone moraju biti tematski povezane s uslugom i označene `aria-hidden="true"` kada su dekorativne.

## 5. Kartice usluga i specifični sadržaj

- Sekcija „Naše [naziv] usluge” je na cream podlozi s centriranim naslovom na desktopu i lijevo poravnanim naslovom na mobilnom.
- Kartice: 3 stupca na širokom desktopu, 2 do 1024 px, 1 na mobilnom.
- Sve kartice u istom redu trebaju izgledati uravnoteženo. Sadržaj ostaje dovoljno kratak da kartice ne postanu zidovi teksta.
- Redoslijed u kartici: ikona, `h3`, opis, pa `Opširnije` samo ako kartica vodi na zasebnu stranicu.
- `Opširnije` koristi postojeći editorial hover-line i strelicu udesno.
- Ne uklanjati postojeće stručne sadržaje ispod uvoda samo zato što se mijenja hero/uvod. Dokument izričito traži da se „naše usluge i ostalo” zadrži.
- Stari blok tima sa starim fotografijama ne prenosi se u novi standard. Ukloniti sve od Anite Kutleše Osmanović nadalje, uključujući Anitu, kada se taj stari blok pojavljuje na stranici usluge.

## 6. Pristup, objave i CTA

- „Naš pristup” je tamna navy sekcija. Desktop koristi užu kolonu za naslov i širu za quote karticu; mobilno se slaže u jedan stupac.
- Quote kartica koristi Bodoni za tekst, zlatni Font Awesome navodnik, tanki obrub i asimetrične kutove.
- Objave se dohvaćaju dinamički iz odgovarajuće kategorije. Prikazati najviše tri kartice kao na `/revizija`; cijelu sekciju izostaviti kada nema objava.
- Završni CTA mora ponovno koristiti zajednički `contact-cta`: naslov lijevo, kontakt kartica desno, gold gumb i statusna napomena. Ne raditi servisnu varijantu izgleda.
- Newsletter, header i footer ostaju globalni i ne smiju se prebojati ili prelomiti iz CSS-a pojedine usluge.

## 7. Motion i pristupačnost

- Hero se otkriva nakon što je font/slika spremna, preko postojeće klase `intro-ready`.
- Naslovi koriste `data-words-slide-from-right`, riječi s klasama `animation-index-*` i postojeći `IntersectionObserver`.
- Copy, kartice i CTA koriste `content-reveal` i `data-image-reveal`; sadržaj se otkriva kada uđe u viewport, a ne sav odjednom pri učitavanju.
- Stagger je kratak i ujednačen. Ne dodavati inline custom properties za kašnjenja.
- `prefers-reduced-motion: reduce` mora ukloniti opacity/transform animacije i odmah prikazati sav sadržaj.
- Očuvati jedan `h1`, urednu `h2`/`h3` hijerarhiju, `aria-labelledby` na sekcijama, vidljiv fokus tipkovnice, smislene alt tekstove i dovoljno velike tap mete.

## 8. Odobreni hrvatski tekstovi iz briefa

Kapitalizaciju `Vam`, `Vaše` i `Vas` u sljedećim blokovima treba sačuvati točno kako je napisana.

### Revizija

Hero hook:

> Povjerenje u financijske informacije počinje neovisnom i stručnom revizijom.

Uvodni naslov:

> Zašto Vam je revizija bitna?

Uvodni tekst:

> Revizija pruža neovisnu i objektivnu procjenu financijskih informacija, povećava transparentnost i pouzdanost poslovanja te pomaže u prepoznavanju potencijalnih rizika.
>
> Neovisna revizija daje Vam sigurnost da odluke donosite na temelju pouzdanih informacija. Uz stručan i objektivan pristup, Vaše poslovanje sagledavamo šire od samih brojki.

Revizija je već odobreni referentni prikaz. Ostali njezini tekstovi i sadržaji ostaju kakvi jesu, osim ako stigne novi brief.

### Računovodstvo

Naziv usluge:

> Računovodstvo

Hero hook:

> Vi vodite poslovanje. Mi brinemo da Vaše brojke budu točne, pravovremene i spremne za svaku odluku.

Uvodni naslov:

> Zašto Vam je računovodstvo bitno?

Uvodni tekst:

> Mirnije poslovanje počinje jasnim i pouzdanim brojkama. Ažurne financijske informacije daju Vam kontrolu nad poslovanjem, pomažu prepoznati prilike i rizike te donijeti sigurnije odluke.
>
> Uz ALPHA CAPITALIS ne dobivate samo računovodstvenu uslugu, već pouzdanog partnera koji razumije Vaše poslovanje i prati Vas kroz svakodnevne izazove i planove rasta.

U briefu je prva rečenica prvog odlomka posebno naglašena, a drugi odlomak u cijelosti. Hero mora vizualno dati prednost hooku, a naziv „Računovodstvo” ne smije biti prevelik u odnosu na njega. Ispod uvoda zadržati postojeće računovodstvene usluge i ostali aktualni sadržaj.

### Savjetovanje

Naziv usluge:

> Savjetovanje

Hero hook:

> Budućnost poslovanja oblikuju odluke koje donosite danas. Zato Vam pružamo stručnu financijsku i stratešku perspektivu koja pomaže prepoznati prilike, upravljati rizicima i stvarati dugoročnu vrijednost.

Uvodni naslov:

> Zašto Vam je savjetovanje bitno?

Uvodni tekst:

> Važne poslovne odluke rijetko imaju jednostavne odgovore. Financijske, porezne i strateške odluke mogu imati dugoročan utjecaj na poslovanje, zbog čega je važno imati stručnu perspektivu na koju se možete osloniti.
>
> Naše savjetovanje povezuje stručnost iz različitih područja kako bismo Vam pomogli sagledati širu sliku, prepoznati prilike, upravljati rizicima i donositi odluke s većom sigurnošću.

U briefu su posebno naglašene prva rečenica hooka i prva rečenica prvog uvodnog odlomka. Ispod uvoda zadržati aktualne usluge, Pandea sadržaj i ostale relevantne servisne module, ali ih uklopiti u standard `/revizija`.

## 9. Implementacija i podaci

- Blade je zadužen za strukturu i podatke. Bez `<style>`, `style=""` i inline `<script>` blokova.
- Svaka stranica ima vanjski stylesheet u `public/front-theme/styles/pages/`, učitan preko `asset(...)` i `filemtime(...)`.
- Dodatno ponašanje ide u vanjski page script u `public/front-theme/scripts/`, također verzioniran.
- Prije dupliciranja markup/CSS-a provjeriti može li se hero, uvod, services grid, approach, news ili CTA izdvojiti u zajedničku Blade komponentu/partial i zajedničke servisne klase.
- Zadržati postojeći CMS tok: `ServicePageTemplateRegistry` + payload iz baze + locale fallback + `*ServicePageDefaults.php`.
- Novi odobreni tekst mora biti dosljedan u defaultima, kontrolerskom kompatibilnom fallbacku i ciljanoj migraciji podataka. Ne prepisivati sadržaj koji je urednik već prilagodio.
- Blog kategorija i servisne slike ostaju dinamičke. Ne hardkodirati URL produkcijske domene ni objave.

## 10. Obavezna završna provjera

- Vizualno usporediti s `/revizija` na 1440 × 900, 1280 × 800 i 390 × 844.
- Proći stranicu scrollanjem kako bi se aktivirala i provjerila svaka reveal animacija.
- Provjeriti hero, 50/50 intro, servisne kartice, specifične module, pristup, objave, CTA, newsletter i footer.
- Provjeriti hover/focus, tipkovnicu, reduced motion, hrvatske prijelome i izostanak horizontalnog overflowa.
- Pokrenuti `php -l` za uređene PHP/Blade datoteke, `node --check` za uređeni JavaScript, `git diff --check` i najuži relevantni feature test.
- U završnom diffu ne smije biti inline CSS/JS-a, dupliciranih globalnih komponenti, cache viewova, privremenih slika ni nepovezanih izmjena.
