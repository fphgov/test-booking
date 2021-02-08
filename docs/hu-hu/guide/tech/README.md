# Technikai leírás

## Követelmények

Technikailag a legkényelmesebb megoldás egy teljes új Linux alapú VPS (Virtual Private Server) megléte. Mivel nem fut más webes alkalmazás a gépen, nem lesznek ütközések, kevesebb kockázattal jár és a lehető legbiztonságosabb / legfrissebb lesz az operációs rendszer induláskor.

* VPS szerver (várható terhelés függvénye, minimum 2 CPU, 8 GB RAM ajánlott)
* Ubuntu (mindenkori legfrissebb verzió)
* Docker (minimum 17.12.0+ Docker Engine release)
* Aldomain cím (pl. covidteszt.onkormanyzat.hu)
* SMTP levelező szerver (mivel VPS, ezért külső szolgáltatónak tekintendő, konfigurációs szempontból)

*TIPP: Biztonság szempontjából érdemes lehet az adatbázis szervert egy külön VPS-re bekonfigurálni, az összes nem használt portot lezárni és kinyitni az adatbázis kapcsolatot kizárólag az alkalmazás VPS irányában. A `docker-compose.production.yml` fájlban ilyenkor ki kell venni a `db` szekciót és az összes hivatkozási helyet. Az .env fájlban pedig definiálni kell az adatbázis kapcsolathoz szükséges beállításokat.*

## Működési leírások

### Alapvető API működés

A backend és a frontend külön van választva. A kettő közötti kommunikáció API-n keresztül valósul meg. Fontos, hogy ez a kapcsolat titkosított `https` csatornán történjen. A frontend-et külön kell buildelni, a szövegezések, logók mind ott cserélendők. Buildelés után a frontend `public` mappa tartalmát át kell másolni a backend `public` mappájába.

### Adat titkosítási eljárás

Mivel a rendszer bekér személyes adatokat, illetve a jelentkező TAJ kártya számát is (ami egy "különleges" személyes adat), elengedhetetlen az adatbázisban tárolt adatok védelme / titkosítása. Ennek technikai megvalósítása rendelkezésre áll, a titkosítás bonyolultsága szabályozható, de utólag (első regisztráció után) már nem módosítható. A környezeti változókban definiálni kell a titkosító kulcsot és annak kiindulási vektorát. Ezeknek az értékeknek az utólagos módosítása esetén a tárolt adatok végérvényesen visszafejthetetlenek lesznek, így érdemes megfelelően eltárolni a regisztrációs folyamat lezárultáig vagy az adatok törléséig, figyelemmel az adatkezelési szabályzatban foglaltakra.

### Cronjob

A rendszer regisztrációt követően késleltetve küld ki e-maileket, hogy csökkentse a szerver terheltségét. Értesítő kiküldésekor generálódik kettő darab `.pdf` fájl a jelentkező adataival. Ezek csatolmányként e-mail formájában továbbításra kerülnek a regisztrálónak. Ez nagy mennyiségben komolyabb igénybevételt jelent, így alapértelmezetten percenként `20` e-mail kiküldése javasolt.

A rendszer fel van készítve arra, hogy a már nem regisztrálható időpontokat letiltsa. Az erre vonatkozó script perceként vizsgálja ezt.

## Telepítés

A szervert érdemes lefrissíteni `apt-get update && apt-get upgrade` parancsokkal. És ebben a pontban fel kell készíteni az host OS-t biztonsági szempontból, például tűzfal, fail2ban, WAF. Ezt követően fel kell telepíteni a Docker CE (Community Edition)-t.

### Docker CE telepítés

A következő parancsok lefuttatásával telepíthető a Docker.

```
# apt install apt-transport-https ca-certificates curl software-properties-common
# curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo apt-key add -
# add-apt-repository "deb [arch=amd64] https://download.docker.com/linux/ubuntu focal stable"
# apt update
# apt install docker-ce
```

#### Futtatás root jogosultság nélkül (Opcionális)

Biztonsági szempontból jobb, ha a Docker futtatása nem rendszergazdaként történik.

```
# usermod -aG docker ${USER}
# su - ${USER}
# usermod -aG docker username
```

### Környezeti változók

A szoftver a beállításához környezeti változókat használ. Ezt a `.env` fájlból szedi, megtalálható egy minta fájl (`.env.example`), amiben szerepel az összes változó kulcsa, ezekhez pedig értéket kell rendelni.

|Kulcs|Minta érték|Lehetséges értékek|Leírás|
|-----|-----------|------------------|------|
|NODE_ENV|production|production, development|Meghatározza, hogy milyen környezetben fusson a szoftver.|
|DB_DRIVER|Mysqli|[További leírás](https://docs.laminas.dev/tutorials/db-adapter/)|Adatbázis kapcsoló tipusa|
|DB_HOSTNAME|127.0.0.1|-|Adatbázis elérési címe|
|DB_PORT|3306|-|Adatbázis kommunikációs csatornájának száma|
|DB_DATABASE|your-database-name|-|Adatbázis tábla neve|
|DB_USER|your-username|-|Adatbázis kommunikációhoz szükséges felhasználónév|
|DB_PASSWORD|your-password|-|Adatbázis kommunikációhoz szükséges jelszó|
|DB_CHARSET|utf8mb4|-|Adatbázis karakterkódolása|
|MYSQL_RANDOM_ROOT_PASSWORD|1|0, 1|A MySQL szerver létrehozásakor véletlen root jelszó legyen-e|
|MYSQL_DATABASE|your-database-name|-|A MySQL szerver létrehozásakor ezzel a névvel fog létrehozni táblát|
|MYSQL_USER|your-username|-|A MySQL szerver létrehozásakor ezzel a névvel fog létrehozni felhasználót|
|MYSQL_PASSWORD|your-password|-|A MySQL szerver létrehozásakor ezzel a névvel fog létrehozni jelszót|
|MYSQL_ALLOW_EMPTY_PASSWORD|0|0, 1|MySQL szerver beállítás: Engedélyezett-e a jelszó nélküli belépés (local)|
|JWT_ISS|http://localhost|-|JWT token "kibocsátó" azonosítója|
|JWT_AUD|http://localhost|-|JWT token "közönség" azonosítója|
|JWT_JTI|RdzPkZ9pucdV5JJw|-|JWT token egyedi azonosítója (egyedi, véletlenszerűen generált legyen)|
|JWT_NBF|0|-|JWT token kiállítása és használhatósága közötti idő nővelése|
|JWT_EXP|1|-|JWT token lejárati ideje|
|JWT_SECRET|vFG8fqDbNEffk3qr|-|JWT tokenhez tartozó titkosító kulcs (egyedi, véletlenszerűen generált legyen)|
|DOCKER_WITH_XDEBUG|false|true, false|Fejlesztés használható xdebug, ha true, úgy buildelésnél belekerül a docker image-be|
|DOCKER_PHP_IDE_CONFIG|serverName=webapp|-|Az xdebug szerver neve|
|DOCKER_XDEBUG_CONFIG|'remote_enable=1 remote_host=172.2.0.113 remote_port=9001'|-|Az xdebug szerver konfigurációja|
|SMTP_HOST|127.0.0.1|-|Levélküldő szerver elérési címe|
|SMTP_NAME|localhost|-|Levélküldő szerver elérési címe (domain)|
|SMTP_PORT|25|-|Levélküldő szerver kommunikációs csatornájának száma|
|SMTP_CONNECTION_CLASS|plain|plain, login, crammd5|Levélküldő szerver fiók hitelesítési tipusa|
|SMTP_CONNECTION_CONFIG_USERNAME|username|-|Levélküldő szerver fiók hitelesítési felhasználóneve|
|SMTP_CONNECTION_CONFIG_PASSWORD|password|-|Levélküldő szerver fiók hitelesítési jelszava|
|SMTP_CONNECTION_CONFIG_SSL|(semmi), ssl, tls|-|Levélküldő szerver fiók hitelesítési jelszava|
|SMTP_DEFAULTS_ADD_FROM|noreply@onkormanyzat.hu|-|Rendszer által küldött e-mailek feladója|
|SMTP_DEFAULTS_ADD_FROM_NAME|Önkormányzat|-|Rendszer által küldött e-mailek feladó neve|
|SMTP_HEADERS_MESSAGE_ID_DOMAIN|onkormanyzat.hu|-|Rendszer által küldött e-mailek üzenet-azonosító domainje|
|ENCRYPT_SHA_TYPE|sha256|-|Adatbázisban tárolt adatok titkosítási eljárás tipusa|
|ENCRYPT_ENCRYPT_METHOD|AES-256-CBC|[További leírás](https://www.php.net/manual/en/function.openssl-get-cipher-methods.php)|Adatbázisban tárolt adatok titkosításának metódusa|
|ENCRYPT_SECRET_KEY|vhEJt8V732mvxg7MJQGETSD9k3pxQka5HY78EZ7Sve7p|-|Adatbázisban tárolt adatok titkosításának titkosító kulcsa (44 karakter hosszú)|
|ENCRYPT_SECRET_IV|W654sStDcEsNcGbVCdkdr|-|Adatbázisban tárolt adatok titkosításának kiindulási vektora (21 karakter hosszú)|
|APP_PHASE|1|-|Engedélyezett fázisok (<= 1)|
|APP_MUNICIPALITY|Önkormányzat|-|Önkormányzatnak a neve|
|APP_EMAIL|ugyfelszolgalat@onkormanyzat.hu|-|Önkormányzat (ügyfélszolgálatának) e-mail címe|
|APP_PHONE|"06-1-000-0001"|-|Önkormányzat (ügyfélszolgálatának) telefonszáma|
|APP_URL|"https://covidteszt.onkormanyzat.hu"|-|Az alkalmazás URL elérhetősége|
|APP_URL_ADMIN|"https://covidteszt.onkormanyzat.hu/bp-admin"|-|Az alkalmazás admin URL elérhetősége|
|APP_COMPANY_NAME_PART_1|"SAMPLE AMBULANCE"|-|A generált PDF-en megjelenő aláírás első sora|
|APP_COMPANY_NAME_PART_2|"xy Kft."|-|A generált PDF-en megjelenő aláírás második sora|
|APP_COMPANY_FULL_INFO|"Sample Ambulance Kft, székhely: 1111 Budapest, Alma utca 0.; adószám: 00000000000., cégjegyzékszám: 00 00 000000; e-mail: info@samplexykft.hu"|-|A generált PDF-en megjelenő hosszabb céginformáció (adatvédelmi szövegben)|
|APP_NOTIFICATION_FREQUENCY|20|-|Az alkalmazás késleltetve küldi ki az e-maileket, ezzel meghatározható, hogy percenként hány e-mail küldése lehetséges|
|APP_NOTIFICATION_MAIL_TESTTO|"test@onkormanyzat.hu"|-|Az e-mail küldés teszteléséhez használt e-mail cím|
|APP_NOTIFICATION_MAIL_REPLAYTO|ugyfelszolgalat@onkormanyzat.hu|-|Válasz e-mail beállítása|
|APP_APPOINTMENT_EXPIRED_TIME_HOUR|7|UTC+1 (0-24)|A foglalható időpont aznapi lejárata (óra)|
|APP_APPOINTMENT_EXPIRED_TIME_MIN|0|UTC+1 (0-59)|A foglalható időpont aznapi lejárata (perc)|
|APP_ICS_NAME|"Ingyenes gyorsztesztelés"|-|Az e-mailben mellékelt kalendár fájlhoz tartozó esemény neve|
|APP_ICS_DESCRIPTION|"Ingyenes gyorstesztelés bővebb leírása"|-|Az e-mailben mellékelt kalendár fájlhoz tartozó esemény leírása|
|APP_SURVEY_DISABLE|1|0, 1|Felmérő e-mail küldésének be/ki kapcsolása|
|APP_SURVEY_TEMPLATE|"email/survey"|-|Felmérő e-mail sablonja|
|APP_SURVEY_TIME|"18:00"|-|A felmérő e-mail küldésének ideje (szerver idő szerint)|
|APP_SURVEY_URL|"https://forms.office.com/Pages/ResponsePage.aspx?id="|-|Felmérő e-mail szövegében lévő URL|
|APP_SURVEY_MAIL_TESTTO|"test@onkormanyzat.hu"|-|A felmérő e-mail teszteléséhez használt e-mail cím|
|APP_SURVEY_MAIL_SUBJECT|"Mondja el a véleményét az Önkormányzat teszteléséről"|-|A felmérő e-mail tárgya|
|APP_SURVEY_MAIL_REPLAYTO|ugyfelszolgalat@onkormanyzat.hu|-|A felmérő e-mail válasz e-mail cím beállítása|
|RECAPTCHA_SECRET|{Google által generált token}|-|Google reCaptcha v3-as titkos kulcsa|

### Docker-compose elindítása

Docker nem csak 1-1 konténert tud futtatni, ki lehet szervezni egységekben, így külön-külön menedzselhetőek a szolgáltatás függőségek. Ezt a `docker-compose.production.yml` fájl szabályozza. Amennyiben nem kell például adatbázis, mert külön szerveren fut, úgy ki kell törölni a `db` service-t és törölni a hozzá tartozó hivatkozásokat. Ha pedig nem áll rendelkezésre SMTP vagy egyes policy miatt nem lehetséges a használata, úgy felvehető ide és elindítás után rendelkezésre fog állni.

*TIPP: Amennyiben az új VPS-re telepített SMTP szerverről mennének ki a levelek, és használatban van már levelező szerver a DNS-ben fel kell venni egy SPF-es TXT rekordot, hogy ne SPAM-be kerüljenek az e-mailek*

A fájlban definiálva vannak `expose` és `ports` attributomok. Mind a kettő portokkal kapcsolat, de míg az `expose`-zal csak a belső networkon figyel, a `ports` kiengedi az összes network interface-re. Ezt érdemes szem előtt tartani és helyén kezelni.

Az alkalmazás szolgáltatás függősei elindítása, Docker image építése a következő paranccsal történik:

```
docker-compose -f docker-compose.production.yml up --build -d
```

Lehetőség van a naplófájlokat streamelni a konzolra a következő paranccsal:

```
docker-compose logs -f
```

*TIPP: A docker-compose inditásánál lefut a `setup.sh` script, ez hosszabb ideig is eltarthat, akkor végződik (és futott le sikeresen), amikor azt írja a log, hogy `INFO exited: setup (exit status 0; expected)`
Futtatási hiba esetén győződjön meg arról, hogy Unix szabványnak megfelelően `LF` sorvégződést használ a fájlban, `CR+LF` beállításnál a script nem futtatható.*


#### ReCaptcha

Az űrlap a botok kivédéséhez Google reCaptcha v3-at használ. Ehhez való kulcsot a [Google oldalán](https://www.google.com/recaptcha/admin/create) lehet létrehozni. Használata production-ben kötelező, amennyiben mégsem kellene, úgy a `config/routes.php`-ból ki kell venni a `\Middlewares\Recaptcha::class` sort. Továbbá a frontend-et úgy kell buildelni, hogy az `.env` fájlban a `SITE_KEY` változó üres legyen.

#### Frontend buildelése

A test-booking-frontend repository leírásában foglaltak alapján a buildelt kódot `public` mappa tartalmát át kell másolni a test-booking (jelen repository-hoz tartozó) `public` mappába. Ezután lesz képes a szerver kiszolgálni a frontend web applikációját.

#### Admin hozzáférést létrehozása

Az alábbi parancssal létrehozható az admin user.

```
docker exec test_booking_demo_webapp php bin/create-admin.php -f Firstname -l Lastname -e {email} -p {password} -r {role}
```

#### Jogosultsági szintek (ACL - role)

A rendszer rendelkezik jogosultsági szintekkel. A mindenkori legfrissebb verziót a [config/autoload/authorization.global.php](config/autoload/authorization.global.php) fájl tartalmazza.

|Megnevezés     |Kódja    |
|---------------|---------|
|Vendég         |guest    |
|Önkéntes       |voluntary|
|Ügyfélszolgálat|cs       |
|Admin          |admin    |
|Fejlesztői     |developer|

#### Helyszínek létrehozása

Hamarosan...

#### Időpontok generálása

Hamarosan...

### Tesztelés

#### Levelező szerver tesztelése

A kimenő levelek tesztelésére van egy script, ezzel ki lehet próbálni, hogy megfelelően fog-e működni az e-mailek kiküldése.

```
docker exec test_booking_demo_webapp php bin/test/send-email.php
```

#### Pdf generálás tesztelése

Amennyiben az e-mailek sikeresen megérkeznek, szükség van a pdf generálásának tesztelésére is.

```
docker exec test_booking_demo_webapp php bin/test/create-pdf.php
```

### Tanúsítvány és https kapcsolat

Az élesítéshez elengedhetetlen a titkosított kapcsolat. Amennyiben nem rendelkezünk megvásárolt érvényes tanúsítvánnyal, úgy generálhatunk egyet a Let's Encrypt segítségével.

```
docker run -it --rm --name certbot -v "/etc/letsencrypt:/etc/letsencrypt" -v "/var/lib/letsencrypt:/var/lib/letsencrypt" certbot/certbot certonly -d {your_domain} --standalone --preferred-challenges http -m {your_admin_email}
```
