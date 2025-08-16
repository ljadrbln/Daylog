# Configuration

## Database configuration (single source of truth)

We do **not** hardcode DSNs. All DB settings come from environment
variables and are parsed centrally.

-   Env vars are read once (via `Variables`)
-   DSN strings are parsed into components (via `DSNParser`)

### Pattern

1)  Read env:

``` php
$dsn = \Daylog\Infrastructure\Utils\Variables::getDB(); 
// e.g. "pgsql:host=...;dbname=...;user=...;password=..."
```

2)  Parse once, reuse:

``` php
[$pdoDsn, $user, $pass] = \Daylog\Infrastructure\Utils\DSNParser::toPdoTriple($dsn);
$pdo = new \PDO($pdoDsn, $user, $pass);
```

This keeps app code and tests consistent and avoids ad-hoc DSN handling.

------------------------------------------------------------------------

## Environment variables

Daylog requires database connection URLs to be set as environment
variables.

### Setup on Linux

Create a shell script under `/etc/profile.d/`:

``` bash
sudo nano /etc/profile.d/daylog_env.sh
```

Add variables:

``` bash
export DAYLOG_DEV_DATABASE_URL="mysql://user:pass@127.0.0.1/daylog_dev"
export DAYLOG_TEST_DATABASE_URL="mysql://user:pass@127.0.0.1/daylog_test"
```

Reload your shell session or run:

``` bash
source /etc/profile.d/daylog_env.sh
```

Verify:

``` bash
echo $DAYLOG_DEV_DATABASE_URL
```

These variables are now automatically loaded for all shell sessions.