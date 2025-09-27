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

To make environment variables available both in the console and in `phpinfo()`, define them in a single file and connect it to the Apache systemd unit.

### Steps

1. Create a shared environment file:

```bash
sudo nano /etc/webenv/daylog.env
```

Example content:

```
DAYLOG_DEV_DATABASE_URL=mysql://user:pass@127.0.0.1:3306/daylog_dev
DAYLOG_TEST_DATABASE_URL=mysql://user:pass@127.0.0.1:3306/daylog_test
```

2. Load this file for interactive shells:

```bash
sudo nano /etc/profile.d/daylog_env.sh
```

Content:

```sh
# Load Daylog env into interactive shells
file="/etc/webenv/daylog.env"
[ -r "$file" ] || return 0
set -a
. "$file"
set +a
```

If you want to use the variables without restarting your session, run:

```bash
source /etc/profile.d/daylog_env.sh
```

3. Load the same file for Apache:

```bash
sudo systemctl edit apache2
```

Insert in the editor:

```
[Service]
EnvironmentFile=/etc/webenv/daylog.env
```

Save and exit, then reload Apache:

```bash
sudo systemctl daemon-reload
sudo systemctl restart apache2
```

### Verification

- In the console:

```bash
printenv | grep DAYLOG
```

- In PHP (mod_php): open `phpinfo()` and check the **Environment** section or call `getenv("DAYLOG_DEV_DATABASE_URL")`.

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

## Local host mapping

Add the following lines to your `/etc/hosts` file:

```bash
sudo nano /etc/hosts
```

Example content:

```bash
127.0.0.1 daylog.localhost www.daylog.localhost
127.0.0.1 daylog.localhost.test www.daylog.localhost.test
```

## Developer Tools

### Clean local git branches

A helper script is available to remove local branches that have already been merged and deleted on remote.

- Location: `tools/git/clean_stale_branches.sh`  
- Default mode: **dry-run** (only prints which branches would be removed).  
- Protected branches: `main`, `master`, `develop`, and the current branch are never deleted.  

#### Usage

```bash
# Preview branches that would be deleted
./tools/git/clean_stale_branches.sh

# Actually delete stale branches
./tools/git/clean_stale_branches.sh -f
```

This script supports the GitFlow workflow: after merging a feature branch via PR and deleting it on GitHub, run the script locally to prune obsolete branches.
