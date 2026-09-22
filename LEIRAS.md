# Yii2 Labors feladat – leírás az elvégzett munkáról

Mind a négy részfeladatot megoldottam, egyetlen Yii2 (basic template) projektben:

| Terület | Állapot |
|---|---|
| Backend – adattábla / modul (migráció, mock-data feltöltés, Gii CRUD) | kész |
| Dependency (Composer, Kartik bővítmények) | kész |
| Frontend (Kartik widgetek, link szín, fordítás) | kész |
| Backend – API (`POST /api/works`) | kész |

A beküldött csomag tartalma:

- a teljes munkakönyvtár `vendor/` mappa nélkül (a függőségek `composer install` paranccsal telepíthetők, a pontos verziókat a `composer.lock` rögzíti);
- `yii2_labors_db.sql` – az adatbázis SQL szkriptje (tábla + 1000 rekord + migrációs napló);
- ez a leírás.

---

## 1. Backend – adattábla / modul

### Migráció – `labors` tábla

`migrations/m260918_154708_create_labors_table.php`

A `mock-data` szerkezete alapján: `id` (PK), `first_name`, `last_name` (kötelező), `email`, `ip_address` (45 karakter,
hogy IPv6 cím is elférjen), `need_work` (boolean, alapértelmezés `false`), `working_minutes`, `working_date` (DATETIME).
MySQL esetén a tábla kifejezetten `utf8mb4` karakterkészlettel és InnoDB motorral jön létre, hogy az ékezetes
nevek és a tranzakciók biztosan működjenek.

Egy későbbi migráció (`m260921_120000_harden_labors_table.php`) finomítja a sémát:

- `working_minutes` előjel nélküli (negatív munkaidő az adatbázis szintjén sem menthető);
- a `need_work` egyoszlopos indexe helyett összetett `(need_work, working_date)` index készül, mert az API pontosan
  így szűr és rendez. `EXPLAIN` alapján a lekérdezés így indexet használ, külön rendezési lépés (filesort) nélkül.

Azért külön migrációban, mert a már lefutott migrációt nem illik módosítani – így egy meglévő adatbázis is
`php yii migrate` paranccsal frissíthető.

### Mock-data feltöltés

`migrations/m260918_154833_mockupload.php` – a kért elnevezéssel, `batchInsert` metódussal, 250-es csomagokban tölti
be a `data/mock-data.json` 1000 rekordját. A betöltés után az id-szekvenciát a legnagyobb id utánra állítja
(`resetSequence`), így az új rekordok nem ütköznek a betöltött id-kkal. A visszagörgetés (`safeDown`) `DELETE`-et
használ `TRUNCATE` helyett, mert a `TRUNCATE` MySQL-ben automatikusan lezárja a tranzakciót, így nem vonható vissza.

Alternatívaként a `yii2_labors_db.sql` szkript is ugyanezt az állapotot hozza létre.

### Gii – CRUD

A `labors` táblához Gii-vel generáltam a modellt (`models/Labors.php`) és a CRUD-ot
(`controllers/LaborsController.php`, `views/labors/`: index, view, create, update, delete). A generált kódot utána
bővítettem:

- **validáció**: szóközök levágása, kötelező nevek (csak szóközből álló név nem fogadható el), e-mail és IP-cím
  formátum, `working_minutes` 0–1440 perc (egy nap), `need_work` logikai érték, szigorú dátumellenőrzés;
- **keresőmodell** (`models/LaborsSearch.php`): a listaoldal minden oszlopa szűrhető, rendezhető, 20 elem/oldal;
- `getFullName()` a teljes név előállítására (a nézetek és az API is ezt használja);
- sikeres mentés/törlés után magyar nyelvű visszajelzés.

## 2. Dependency – Composer

A Kartik bővítményeket Composerrel telepítettem:

```bash
composer require kartik-v/yii2-widget-datepicker kartik-v/yii2-checkbox-x
```

A `config/params.php`-ban a `bsVersion => '5.x'` beállítással a Kartik widgetek Bootstrap 5 jelölést használnak,
összhangban a projekt Bootstrap 5 alapjával.

## 3. Frontend

| Követelmény | Megoldás |
|---|---|
| Minden link színe `rgb(20, 96, 130)` | `web/css/site.css` – saját stíluslap, világos és sötét témában is ez a szín |
| Törlés ikon eltávolítása a listáról | `views/labors/index.php` – `ActionColumn` sablon: `{view} {update}`; törölni csak a részletező oldalon lehet, megerősítés után |
| Alap widgetek cseréje Kartikra | `views/labors/_form.php` – `working_date`: Kartik DatePicker, `need_work`: Kartik Checkbox-X (a create és az update ugyanazt a formot használja) |
| Dátum angol formátumban: `23-Feb-1982` | DatePicker `dd-M-yyyy` formátummal; a lista és a részletező oldal is `23-Feb-1982 13:23:05` alakban mutatja |
| `need_work` igen/nem checkbox | Checkbox-X kétállapotú (`threeState => false`) módban; a listán és a részletezőn „Igen/Nem” |
| Címkék `Yii::t('app')`-pal, magyar fordítással | a modell `attributeLabels()` metódusa `Yii::t('app', …)`-t használ, a fordítások a `messages/hu-HU/app.php` fájlban vannak, az alkalmazás nyelve `hu-HU` |

A címkék fordítását a modellben oldottam meg, nem a formban: így a form, a lista fejléce, a részletező oldal és a
validációs hibaüzenetek (pl. „E-mail cím nem valódi e-mail cím.”) is magyarul, egységesen jelennek meg. A menü, a
gombok, a visszajelzések és a hibaoldal szövegei is fordítva vannak (37/37 kulcs).

A sablon kezdőlapját (Yii keretrendszer-bemutató és bővítmény-ajánlók) eltávolítottam, mert nem kapcsolódott a
feladathoz: a kezdőlap most a munkavállalók listája (`defaultRoute`).

## 4. Backend – API

`POST {host}/api/works` – `controllers/ApiController.php`, az összesítés a `models/WorksReport.php` osztályban.

1. Az adatokat ActiveRecorddal kérdezem le (`Labors::find()`): csak a `need_work = true` rekordokat, a munkakezdés
   (`working_date`) szerint növekvő sorrendben. A `findAll()` helyett azért `find()`, mert így a szűrés és a rendezés
   az adatbázisban történik, és a sorok 500-as csomagokban érkeznek, nem egyszerre az egész tábla.
2. A csoportosítás kulcsa a munkanap (`YYYY-MM-DD`), azon belül a munkavállaló teljes neve.
3. Ha ugyanazon a napon ugyanaz a név többször szerepel, a `working_minutes` összeadódik; a hiányzó (`null`) perc
   0-nak számít.

Példa a valós adatokból (a feladatleírás példájával egyezik):

```json
{
  "2021-05-19": {
    "Basile Seedhouse": { "name": "Basile Seedhouse", "working_minutes": 301 }
  }
}
```

További viselkedés:

- nem POST kérésre `405 Method Not Allowed` JSON válasz jön;
- CORS engedélyezve van, így böngészőből, más domainről is hívható;
- ha az adatbázis nem érhető el, a hibát naplózom, és a válasz a mock adatokból készül ugyanazokkal a szabályokkal és
  ugyanabban a sorrendben. A feladat a `mock-data.php`-t említi; én ugyanezen adatok JSON változatát olvasom be,
  mert az adatként dolgozódik fel – egy PHP fájl `require`-rel történő betöltése kódot futtatna;
- opcionálisan tokenes védelem kapcsolható be (`API_TOKEN` környezeti változó); alapértelmezésben a végpont nyilvános,
  ahogy a feladat leírja.

Ellenőrzésként a végpont kimenetét egy tőle független, egyszerű implementációval vetettem össze a teljes adathalmazon
(276 nap, 503 munkavállaló) – az eredmény egyezik.

---

## Mik okoztak nehézséget

1. **Dátumformátum a form és az adatbázis között.** A DatePicker `23-Feb-1982` alakú szöveget küld, a DATETIME
   oszlop `1982-02-23 00:00:00`-t vár. A generált modell ezt nem alakította át, és mivel a helyi MariaDB nem szigorú
   (non-strict) módban fut, a mentés nem adott hibát – csendben `0000-00-00 00:00:00` került az adatbázisba. A
   megoldás egy saját validátor, amely mindkét formátumot szigorúan értelmezi (a `31-Feb-2021`-et is elutasítja), és
   adatbázis-formátumra alakít. Szerkesztésnél, ha csak a dátumot módosítják, a munkakezdés tárolt időpontja
   megmarad, mert a dátumválasztó időt nem kezel.

2. **Magyar nyelv kontra angol dátumformátum.** Mivel az alkalmazás nyelve `hu-HU`, a DatePicker automatikusan a
   magyar nyelvi fájlt töltötte be, és a hónapnevek magyarul jelentek volna meg (`23-febr.-1982`). A widgetnél ezért
   kifejezetten angol nyelvet állítottam be. Ugyanez a listánál: a Yii Formatter magyar hónapneveket írt, és az
   időpontot UTC-ből helyi időre váltotta, így minden rekord 2 órával eltolva jelent meg (13:23 helyett 15:23). A
   dátumokat ezért PHP `DateTime`-mal formázom, átváltás nélkül.

3. **Kartik widgetek Bootstrap 5 alatt.** A DatePicker alapértelmezett ikonjai Font Awesome ikonok, amelyet a projekt
   nem tölt be, így a naptár gomb üres volt – beágyazott SVG ikonokra cseréltem őket. A Bootstrap 5-ben megszűnt
   `.input-group-addon` osztályra írt CSS sem érvényesült, ezt az új `.input-group-text` jelöléshez igazítottam. A
   lapozó és a form validációs jelölése is Bootstrap 3-as stílusú volt; a Bootstrap 5-ös `ActiveForm`-ra és
   `LinkPager`-re váltottam.

4. **Link szín és sötét téma.** A kötelező `rgb(20, 96, 130)` sötét háttéren gyenge kontrasztú (kb. 2,2:1, míg a WCAG
   AA 4,5:1-et ír elő). A követelményt pontosan betartottam, a szín mindkét témában ez; az alapértelmezett a világos
   téma, ahol a kontraszt megfelelő (kb. 6,9:1).

5. **Tesztkörnyezet.** A tesztek eredetileg a fejlesztői adatbázison futottak volna, és hiányzott a Codeception
   konfiguráció. Külön tesztadatbázist (`yii2_labors_test`) vezettem be, a tesztkonfiguráció a webes konfiguráció
   útvonalait, fordításait és beállításait használja, az adatbázist módosító tesztek tranzakcióban futnak és
   visszagörgetnek.

## Tesztelés

- 48 unit teszt (160 ellenőrzés): a modell validációja és dátumkezelése, a keresőmodell, az API összesítési szabályai
  (több műszak egy napon, `null` perc, kizárt rekordok, érvénytelen dátum, éjfél körüli műszakok), az adatbázis nélküli
  tartalék működés, a 405/401 válaszok, a flash üzenet widget;
- 1 acceptance teszt: a kezdőlapon a munkavállalók listája jelenik meg, és onnan elérhető a létrehozás oldal;
- kézi ellenőrzés böngészőben: rekord létrehozása a dátumválasztóval, szerkesztés, szűrés, lapozás, világos/sötét téma.

## Futtatás

```bash
composer install
cp .env.example .env            # adatbázis-hozzáférés beállítása
mysql -u root -e "CREATE DATABASE yii2_labors_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
php yii migrate --interactive=0 # vagy: mysql -u root yii2_labors_db < yii2_labors_db.sql
php yii serve --port=8080
```

- Lista: `http://localhost:8080/labors`
- API: `curl -X POST http://localhost:8080/api/works`

Részletes dokumentáció (környezeti változók, tesztek futtatása): `README.md`.
