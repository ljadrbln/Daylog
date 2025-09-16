# Configuration

## Database configuration (single source of truth)

We do **not** hardcode DB connection strings. All settings come from a single
environment variable that holds a **database URL**, and are parsed centrally.

- Env vars are read once (via `Variables::getDB()`)
- The URL is parsed into a PDO DSN + username + password triple (via `DSNParser::parse()`)
- `SqlFactory` constructs and caches a shared `DB\SQL` connection

### Pattern

1) Read env (URL):

```php
$databaseUrl = \Daylog\Infrastructure\Utils\Variables::getDB();
// e.g. "postgres://user:pass@host:5432/daylog?sslmode=require"
```

2) Parse once, reuse:

```php
[$pdoDsn, $user, $pass] = \Daylog\Infrastructure\Utils\DSNParser::parse($databaseUrl);
$pdo = new \PDO($pdoDsn, $user, $pass);
```

This keeps app code and tests consistent and avoids ad‑hoc DSN handling.

### SqlFactory

`Daylog\Configuration\Bootstrap\SqlFactory` provides a shared `DB\SQL` instance.

- Lazily builds the connection on first call.
- Wraps errors in a RuntimeException with a clear message.
- Always returns a valid `DB\SQL` or throws.

### Providers

Providers compose dependencies top‑down using `SqlFactory`. For example, `AddEntryProvider` builds:

- DB URL read via `Variables::getDB()`
- Parsed by `DSNParser::parse()`
- Shared connection from `SqlFactory::get()`
- Injected into `EntryModel → EntryStorage → EntryRepository → AddEntry`

This ensures a single connection source and consistent wiring.

------------------------------------------------------------------------

## Environment variables

Daylog requires database connection URLs to be set as environment
variables.

### Setup on Linux

Create a shell script under `/etc/profile.d/`:

```bash
sudo nano /etc/profile.d/daylog_env.sh
```

Add variables:

```bash
export DAYLOG_DEV_DATABASE_URL="postgres://user:pass@127.0.0.1:5432/daylog_dev"
export DAYLOG_TEST_DATABASE_URL="postgres://user:pass@127.0.0.1:5432/daylog_test"
```

Reload your shell session or run:

```bash
source /etc/profile.d/daylog_env.sh
```

Verify:

```bash
echo $DAYLOG_DEV_DATABASE_URL
```

These variables are now automatically loaded for all shell sessions.

## Apache VirtualHost (Development)

Create `/etc/apache2/sites-available/daylog.localhost.conf`:

```bash
Define DAYLOG_HOST daylog.localhost
Define DAYLOG_ROOT /var/www/html/Daylog/public
Define DAYLOG_LOG  /var/log/apache2/daylog

<VirtualHost *:80>
    ServerName  ${DAYLOG_HOST}
    ServerAlias www.${DAYLOG_HOST} ${DAYLOG_HOST}.test www.${DAYLOG_HOST}.test

    DocumentRoot ${DAYLOG_ROOT}

    # Dev logs
    ErrorLog  ${DAYLOG_LOG}-error.log
    CustomLog ${DAYLOG_LOG}-access.log combined
    LogLevel  warn

    <Directory ${DAYLOG_ROOT}>
        Options +FollowSymLinks -MultiViews -Indexes

        # .htaccess enabled (dev)
        AllowOverride All

        Require all granted
        DirectoryIndex index.php index.html
    </Directory>

    <FilesMatch "^\.">
        Require all denied
    </FilesMatch>
    <FilesMatch "\.(?:ini|env|log|sql|sh)$">
        Require all denied
    </FilesMatch>
</VirtualHost>
```

Enable with:

```bash
sudo a2ensite daylog.localhost.conf
sudo systemctl reload apache2
```