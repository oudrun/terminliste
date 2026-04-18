# Terminliste for hundeprøver

En enkel PHP- og MariaDB-app for å:

- vise kommende hundeprøver
- registrere prøver og klasser
- ta imot påmeldinger fra deltakere
- holde oversikt over status på påmeldinger

## Start i DDEV

1. Kjør `ddev start`
2. Åpne prosjektet i nettleser med `ddev launch`
3. Databasen opprettes automatisk ved første sidevisning

## Sider

- `/` viser terminlisten og ledige klasser
- `/apply.php?class_id=1` lar brukeren melde seg på en klasse
- `/admin.php` gir enkel vedlikehold av prøver, klasser og påmeldinger

## Database

Datamodellen er beskrevet i `database/schema.sql`.

Appen bruker standard DDEV-oppsett:

- databasevert: `db`
- databasenavn: `db`
- brukernavn: `db`
- passord: `db`
