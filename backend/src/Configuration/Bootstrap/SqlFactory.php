<?php

declare(strict_types=1);

namespace Daylog\Configuration\Bootstrap;

use DB\SQL;
use Daylog\Infrastructure\Utils\Variables;
use Daylog\Infrastructure\Utils\DSNParser;

/**
 * SQL connection factory that provides a shared DB\SQL instance.
 *
 * Purpose:
 *  Resolve DB connection settings from environment once per process/request,
 *  construct a single shared DB\SQL wrapper, and return it on subsequent calls.
 *
 * Mechanics:
 *  - Lazily builds DB\SQL on first call.
 *  - Wraps construction errors into RuntimeException with a clear message.
 *  - Does not expose null: callers always receive a valid SQL instance or an exception.
 */
final class SqlFactory
{
    /** @var SQL|null Shared DB\SQL instance for the current process/request. */
    private static ?SQL $instance = null;

    /**
     * Get a shared DB\SQL connection.
     *
     * Returns the same instance on subsequent calls during the process/request lifetime.
     * The connection parameters are resolved from the environment once, parsed centrally,
     * and passed to DB\SQL without duplicating DSN logic.
     *
     * @return SQL DB\SQL connection wrapper over PDO.
     * @throws \RuntimeException When connection construction fails (invalid DSN, unreachable host, bad credentials).
     */
    public static function get(): SQL
    {
        if (self::$instance instanceof SQL) {
            $existing = self::$instance;
            return $existing;
        }

        $databaseUrl = Variables::getDB();
        [$pdoDsn, $username, $password] = DSNParser::parse($databaseUrl);

        try {
            /** @var SQL $connection */
            $connection = new SQL($pdoDsn, $username, $password);
        } catch (\Throwable $e) {
            $message = 'Unable to create DB\\SQL connection. Check DSN, credentials, and network.';
            throw new \RuntimeException($message, 0, $e);
        }

        self::$instance = $connection;

        return $connection;
    }

    /**
     * Reset the shared connection instance (useful for tests).
     *
     * In test suites, call this to force a fresh connection on next get().
     *
     * @return void
     */
    public static function reset(): void
    {
        self::$instance = null;
        return;
    }
}
