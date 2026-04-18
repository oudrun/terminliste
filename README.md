# Terminliste for hundeprøver

En PHP- og MariaDB-app som nå er strukturert slik at en senere overgang til Laravel blir enklere.

## Funksjoner

- vise kommende hundeprøver
- registrere prøver og klasser
- ta imot påmeldinger fra deltakere
- holde oversikt over status på påmeldinger

## Laravel-forberedt struktur

- `public/` som webrot
- `app/Http/Controllers/` for kontrollere
- `app/Repositories/` for databaseadgang
- `app/Models/` som forberedelse til Eloquent-modeller
- `resources/views/` for visninger
- `routes/web.php` for senere ruteflytting
- `database/migrations/` med Laravel-migreringsutkast

## Start i DDEV

1. Kjør `ddev restart`
2. Kjør `ddev composer dump-autoload`
3. Åpne prosjektet med `ddev launch /`

## Sider

- `/` viser terminlisten og ledige klasser
- `/apply.php?class_id=1` lar brukeren melde seg på en klasse
- `/admin.php` gir enkel vedlikehold av prøver, klasser og påmeldinger

## Database

Datamodellen er beskrevet i `database/schema.sql`, og et Laravel-klart migreringsutkast ligger i `database/migrations/`.

Standard DDEV-oppsett:

- databasevert: `db`
- databasenavn: `db`
- brukernavn: `db`
- passord: `db`
