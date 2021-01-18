## Test booking

A Főváros Önkormányzata által létrehozott, koronavírus gyorstesztelés menedzseléséhez használt backend alkalmazás telepítési útmutatója.

#### Követelmények
- Docker (minimum 17.12.0+ Docker Engine release)

#### Üzemeltetés
Az alkalmazás közvetlen függőségeit a `docker/config/webapp/Dockerfile.production` írja le, a futtatását Dockerrel javasoljuk. Amennyiben nem Dockerrel fog futni, úgy a fájlban lévő összes csomagot, beállításokat manuálisan kell elvégezni. Továbbá külön figyelmet kell fordítani a `cronjob`-ra beállítására, az `utf-8`-as Arial betűtípus telepítésére.

A szükséges `cronjob` beállítás a `bash/crontab` fájlban található.

A szoftver működéséhez elengethetetlen egy
- MySQL / MariaDB adatbázis szerver,
- Nginx proxy,
- SMTP levelező szerver

A jelenlegi konfiguráció feltételezi, hogy csak ez az egy alkalmazás fut a szerveren. Amennyiben több van használatban, úgy az adott környezetnek megfelelően kell alakítani a konfigurációt.

#### Telepítés
A projekt letöltése után, szükséges a `.env.example` fájl másolása, a másolat neve `.env` legyen. Ebben a fájlban konfigurálható az alkalmazás paraméterei, például:
- adatbázis-, levelező szerver kapcsolat,
- titkosító kulcsok,
- az alkalmazás által használt nevezéktanok

A `config/development.config.php.dist` fájlt is le kell másolni, a másolat nevéből ki kell venni a `.dist` kifejezést és a fájlban pedig a `debug` kulcsot `false` értékre kell állítani.

A Docker környezetnek futnia kell, utána pedig az alábbi paranccsal lehet elinditani. Ez automatikusan meghívja indulás után a `docker/config/webapp/setup.sh`-ban lévő sciptet. Ezzel fel fognak települni a PHP csomagok és a PDF generáláshoz szükséges betűkészlet.
```
docker-compose up --build -d
```

Amennyiben `éles környezetben` telepítjük, úgy az alábbi parancsot kell kiadni. Ebből hiányoznak a fejlesztéshez szükséges toolok, illetve rendelkezik már optimalizációval is.
```
docker-compose up --build -f docker-compose.production.yml -d
```

Az adatbázisban le kell futtatni a `migration/shemas.sql` fájlt. És ki kell adni Docker konténeren belűl az adatbázis létrehozásához és frissítéséhez szükséges parancsot.

```
docker exec -it test_booking_webapp composer db-update
```

#### Licence
A szoftver az [MIT License](/LICENSE)-et használja.
