<?php

namespace Daylog\Infrastructure\Utils;

class Variables
{
    /**
     * Get the database DSN from the appropriate environment variable,
     * depending on whether the application is in test mode.
     *
     * @return string|null The database DSN or null if not set
     */
    public static function getDB(): ?string
    {
        $name = self::isTestEnv()
            ? 'DIARY_TEST_DATABASE_URL'
            : 'DIARY_DATABASE_URL';

        $dsn = self::getEnv($name);

        return $dsn;
    }

    /**
     * Determine if the application is running in a test environment
     * based on the HTTP host.
     *
     * @return bool True if the host ends with '.test', false otherwise
     */
    private static function isTestEnv(): bool
    {
        $host = $_SERVER['HTTP_HOST'] ?? '';
        $isTest = str_ends_with($host, '.test');

        return $isTest;
    }

    /**
     * Retrieve the value of an environment variable or null if not set.
     *
     * @param string $name Name of the environment variable to retrieve
     * @return string|null The value of the environment variable or null
     */
    private static function getEnv(string $name): ?string
    {
        $value = getenv($name) ?: null;

        return $value;
    }    
}