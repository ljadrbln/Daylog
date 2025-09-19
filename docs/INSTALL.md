# Install & Setup

## Requirements
- PHP 8.1+ (CLI). For `db-setup`, the `pdo_mysql` extension must be enabled.
- Composer
- MySQL 8.x
- Git (to register the commit template)

## Environment
Set database URLs (RFC‑3986 style) in your shell or env file. Example:

```bash
DAYLOG_DEV_DATABASE_URL=mysql://daylog:secret@127.0.0.1:3306/daylog_dev
DAYLOG_TEST_DATABASE_URL=mysql://daylog:secret@127.0.0.1:3306/daylog_test
```

## What runs on `composer install` / `composer update`
Composer triggers cross‑platform PHP scripts located under `backend/scripts`:

- **remove-codeception-banner.php**  
  Removes the vendor concat from `vendor/codeception/.../Run.php`. Safe to run; skipped if vendor is not installed yet.

- **prepare-writable-dirs.php**  
  Ensures `backend/tmp` and `backend/logs` exist. On POSIX systems it best‑effort applies `chmod 775` and `chgrp www-data`. On Windows it just creates directories.

- **set-git-commit-template.php**  
  Registers `.gitmessage` as the repository’s commit template. If Git is not available or the file is missing, the step is skipped.

- **db-setup.php**  
  Creates (or recreates) databases from `DAYLOG_DEV_DATABASE_URL` and `DAYLOG_TEST_DATABASE_URL`, then applies SQL files from `backend/database/migrations/*.sql` in natural order.

## Running scripts manually
```bash
composer run prepare-writable-dirs
composer run remove-codeception-banner
composer run set-git-commit-template
composer run db-setup -- --recreate=ask    # options: ask | always | never
```

### `db-setup.php` parameters
- `--recreate=ask|always|never` — behavior when the database already exists (default: `ask`).
- `--charset=<name>` and `--collation=<name>` — database defaults on creation (defaults: `utf8mb4` / `utf8mb4_unicode_ci`).

## Commit template
A conventional commit template is stored at `.gitmessage` in the project root.

Check the current setting:
```bash
git config --get commit.template
```

Set it manually (if needed):
```bash
git config commit.template .gitmessage
```

## Troubleshooting
- **PDO / MySQL driver**: ensure `pdo_mysql` is enabled in the CLI `php.ini`.
- **Directory permissions**: on Windows, `chown/chgrp` are not available — that’s expected; the script will skip them.

## CI note
In CI pipelines run:
```bash
composer install --no-interaction --prefer-dist
composer run db-setup -- --recreate=always
```
Make sure `DAYLOG_DEV_DATABASE_URL` and `DAYLOG_TEST_DATABASE_URL` are provided via CI secrets.
