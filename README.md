# Abo-Tool

Programm um Lieferungen, Abos und Kunden zu verwalten. Entwickelt für die [Gartenbauschule Hünibach](https://gartenbauschule-huenibach.ch). Die Produktivversion läuft unter [gemueseabo.gsh.ch](https://gemueseabo.gsh.ch). Eine Testversion unter [abo.webtheke.ch](http://abo.webtheke.ch).

## Development

### Requirements

- composer -> [Anleitung](https://getcomposer.org/doc/00-intro.md)
- npm -> [Anleitung](https://nodejs.org/en/download)
- mysql8 -> [Anleitung](https://dev.mysql.com/doc/refman/8.0/en/installing.html)
- php8.1
- MailHog -> Installation siehe [github](https://github.com/mailhog/MailHog) `~/go/bin/MailHog` (Optional, für Mail Testing)

Je nach Umgebung Windows / Linux / MacOS unterscheidet sich der Weg wie man installiert.

### Installation
Das Starten von Laravel wird in der [Dokumentation](https://laravel.com/docs/) erklärt. Für diese Anwendungen braucht es zusätzliche Einstellungen.

1. Herunterladen von Dependencies durch `composer install`
   1. Falls Requirements fehlen gibt es hier Fehlermeldungen
2. Datenbank mit Mysql [erstellen](https://dev.mysql.com/doc/refman/8.0/en/creating-database.html) inklusive [User mit vollem Zugriff](https://www.digitalocean.com/community/tutorials/how-to-create-a-new-user-and-grant-permissions-in-mysql).
3. Erstellen der Datei ".env"
   1. Kopieren von ".env.example" im Root Verzeichnis und umbenennen
   2. Setzen von "DB_DATABASE", "DB_USERNAME", "DB_PASSWORD" anhand Einstellung von Mysql
4. Herunterladen von weiteren Dependencies mittels `npm install`
5. Durch `composer run setup` wird die Datenbank migriert, geseeded und das Programm auf dem Localhost gestartet.

### Tailwind
[CSS Framework](https://tailwindcss.com/). Muss mittels `npm run build` komiliert werden.

### Github

| Branch   | Einsatz             | Umgebung                                                         |
|----------|---------------------|------------------------------------------------------------------|
| main     | Produktivversion    | [Gartenbauschule Hünibach](https://gartenbauschule-huenibach.ch) |
| develop  | Entwicklungsversion | [gemueseabo.gsh.ch](https://gemueseabo.gsh.ch)                   |

Bei Push auf Branch wird die Version auf dem Server aktualisiert. Falls nicht gewünscht, kann in den Commit "[skip ci]" geschrieben werden.

## Rollen
Funktioniert mit [Laravel Permission von Spatie](https://spatie.be/docs/laravel-permission/v5/introduction)

| Rolle    | Beschreibung  | Account           | PW    |
|----------|---------------|-------------------|-------|
| admin    |               | admin@webtheke.ch | admin |
| office   | Intern        |                   |       |
| customer | Kundenaccount | kunde@webtheke.ch | kunde |

## Testing

### Aufsetzen

1. ".env" kopieren in ".env.testing" und neue Datenbank erstellen.
2. `php artisan test` oder falls mit Coverage Report `composer tests-cover`.
