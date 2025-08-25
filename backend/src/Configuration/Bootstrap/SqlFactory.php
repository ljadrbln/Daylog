<?php

declare(strict_types=1);

namespace Daylog\Configuration\Bootstrap;

use Daylog\Infrastructure\Utils\Variables;
use Daylog\Infrastructure\Utils\DSNParser;
use DB\SQL;

/**
 * SQL connection factory (shared DB\SQL instance).
 *
 * Purpose:
 * Provide a single, lazily-initialized DB\SQL connection per process/request.
 * Reads environment via Variables and converts a database URL into a PDO triple
 * via DSNParser::parse(), keeping DSN handling centralized.
 *
 * Usage scenarios:
 * - Application wiring at bootstrap time.
 * - Any infrastructure component that depends on Fat-Free's DB\SQL wrapper.
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
     */
    public static function get(): SQL
    {
        if (self::$instance instanceof SQL) {
            $existing = self::$instance;

            return $existing;
        }

        $databaseUrl = Variables::getDB();
        [$pdoDsn, $username, $password] = DSNParser::parse($databaseUrl);

        /** @var SQL $connection */
        $connection = new SQL($pdoDsn, $username, $password);

        self::$instance = $connection;

        return $connection;
    }

    /**
     * Reset the shared connection instance (useful for tests).
     *
     * @return void
     */
    public static function reset(): void
    {
        self::$instance = null;
    }
}
