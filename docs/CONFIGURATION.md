# Configuration

## Database configuration (single source of truth)
We do **not** hardcode DSNs. All DB settings come from environment variables and are parsed centrally.

- Env vars are read once (via `Variables`)
- DSN strings are parsed into components (via `DSNParser`)

### Pattern

1) Read env:
```php
$dsn = \Daylog\Infrastructure\Utils\Variables::getDB(); 
// e.g. "pgsql:host=...;dbname=...;user=...;password=..."
```

2) Parse once, reuse:
```php
[$pdoDsn, $user, $pass] = \Daylog\Infrastructure\Utils\DSNParser::toPdoTriple($dsn);
$pdo = new \PDO($pdoDsn, $user, $pass);
```

This keeps app code and tests consistent and avoids ad-hoc DSN handling.

---
