<?php
declare(strict_types=1);

/**
 * Cross-platform DB setup: creates (optionally recreates) databases and loads SQL migrations.
 *
 * Env:
 *   DAYLOG_DEV_DATABASE_URL  (required)  e.g. mysql://user:pass@localhost:3306/daylog_dev
 *   DAYLOG_TEST_DATABASE_URL (optional)  same format
 *
 * Args:
 *   --recreate=ask|always|never   default: ask
 *   --charset=utf8mb4             default: utf8mb4
 *   --collation=utf8mb4_unicode_ci default: utf8mb4_unicode_ci
 */

ini_set('memory_limit', '-1');

final class DbUrl
{
    public string $driver;
    public string $user;
    public string $pass;
    public string $host;
    public int $port;
    public string $dbname;

    public static function parse(string $url): self
    {
        $parts = parse_url($url);
        if ($parts === false || !isset($parts['scheme'], $parts['host'], $parts['user'], $parts['path'])) {
            throw new RuntimeException("Invalid DB URL: {$url}");
        }
        $self = new self();
        $self->driver = $parts['scheme'];
        if ($self->driver !== 'mysql') {
            throw new RuntimeException("Only mysql:// is supported, got: {$self->driver}");
        }
        $self->user   = $parts['user'];
        $self->pass   = $parts['pass'] ?? '';
        $self->host   = $parts['host'];
        $self->port   = isset($parts['port']) ? (int)$parts['port'] : 3306;

        $path = ltrim($parts['path'] ?? '', '/');
        $dbname = preg_replace('~\?.*$~', '', $path) ?? '';
        if ($dbname === '') {
            throw new RuntimeException("Missing database name in URL: {$url}");
        }
        $self->dbname = $dbname;

        return $self;
    }
}

final class DbSetup
{
    private const DEFAULT_CHARSET   = 'utf8mb4';
    private const DEFAULT_COLLATION = 'utf8mb4_unicode_ci';

    private string $charset = self::DEFAULT_CHARSET;
    private string $collation = self::DEFAULT_COLLATION;
    private string $recreate = 'ask'; // ask|always|never

    public function __construct(array $argv)
    {
        foreach ($argv as $arg) {
            if (str_starts_with($arg, '--recreate=')) {
                $val = substr($arg, 11);
                if (!in_array($val, ['ask', 'always', 'never'], true)) {
                    throw new RuntimeException("Invalid --recreate value: {$val}");
                }
                $this->recreate = $val;
            } elseif (str_starts_with($arg, '--charset=')) {
                $this->charset = substr($arg, 10);
            } elseif (str_starts_with($arg, '--collation=')) {
                $this->collation = substr($arg, 12);
            }
        }
    }

    public function run(): void
    {
        $dev  = getenv('DAYLOG_DEV_DATABASE_URL');
        if (!$dev) {
            fwrite(STDERR, "Missing DAYLOG_DEV_DATABASE_URL. Abort.\n");
            exit(1);
        }
        $test = getenv('DAYLOG_TEST_DATABASE_URL');

        $this->process(DbUrl::parse($dev));
        if ($test) {
            $this->process(DbUrl::parse($test));
        } else {
            echo "DAYLOG_TEST_DATABASE_URL not set. Test DB is skipped.\n";
        }
    }

    private function process(DbUrl $url): void
    {
        $sqlDir = dirname(__DIR__) . '/database/migrations';
        if (!is_dir($sqlDir)) {
            throw new RuntimeException("Migrations directory not found: {$sqlDir}");
        }

        $files = glob($sqlDir . '/*.sql') ?: [];
        sort($files, SORT_NATURAL | SORT_FLAG_CASE);

        echo "Checking database '{$url->dbname}'...\n";

        $server = $this->connectServer($url);
        $exists = $this->databaseExists($server, $url->dbname);

        if ($exists) {
            if ($this->shouldRecreate($url->dbname)) {
                $this->dropDatabase($server, $url->dbname);
                $this->createDatabase($server, $url->dbname);
                $this->loadMigrations($url, $files);
                echo "Done with {$url->dbname}.\n";
            } else {
                echo "Skip recreation of {$url->dbname} (no migrations applied).\n";
            }
        } else {
            $this->createDatabase($server, $url->dbname);
            $this->loadMigrations($url, $files);
            echo "Done with {$url->dbname}.\n";
        }
    }

    private function connectServer(DbUrl $u): PDO
    {
        $dsn = "mysql:host={$u->host};port={$u->port};charset={$this->charset}";
        $pdo = new PDO($dsn, $u->user, $u->pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        return $pdo;
    }

    private function connectDb(DbUrl $u): PDO
    {
        $dsn = "mysql:host={$u->host};port={$u->port};dbname={$u->dbname};charset={$this->charset}";
        $pdo = new PDO($dsn, $u->user, $u->pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        $pdo->setAttribute(PDO::MYSQL_ATTR_MULTI_STATEMENTS, true);
        return $pdo;
    }

    private function databaseExists(PDO $server, string $dbname): bool
    {
        $stmt = $server->prepare('SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?');
        $stmt->execute([$dbname]);
        $exists = (bool)$stmt->fetchColumn();
        return $exists;
    }

    private function createDatabase(PDO $server, string $dbname): void
    {
        echo "Creating {$dbname}...\n";
        $sql = sprintf(
            'CREATE DATABASE `%s` CHARACTER SET %s COLLATE %s',
            str_replace('`', '``', $dbname),
            $this->charset,
            $this->collation
        );
        $server->exec($sql);
    }

    private function dropDatabase(PDO $server, string $dbname): void
    {
        echo "Dropping {$dbname}...\n";
        $sql = sprintf('DROP DATABASE `%s`', str_replace('`', '``', $dbname));
        $server->exec($sql);
    }

    private function shouldRecreate(string $dbname): bool
    {
        if ($this->recreate === 'always') {
            return true;
        }
        if ($this->recreate === 'never') {
            return false;
        }
        fwrite(STDOUT, "Database {$dbname} exists. Recreate? (y/N) ");
        $answer = trim(strtolower((string)fgets(STDIN)));
        $result = $answer === 'y';
        return $result;
    }

    private function loadMigrations(DbUrl $url, array $files): void
    {
        echo "Loading migrations into {$url->dbname}...\n";
        $pdo = $this->connectDb($url);

        foreach ($files as $f) {
            echo "  -> {$f}\n";
            $sql = file_get_contents($f);
            if ($sql === false) {
                throw new RuntimeException("Cannot read SQL file: {$f}");
            }
            $pdo->exec($sql);
        }
    }
}

try {
    $app = new DbSetup(array_slice($argv, 1));
    $app->run();
} catch (Throwable $e) {
    fwrite(STDERR, $e->getMessage() . "\n");
    exit(1);
}
