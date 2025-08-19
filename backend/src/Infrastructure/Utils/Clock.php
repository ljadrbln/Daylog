<?php

declare(strict_types=1);

namespace Daylog\Infrastructure\Utils;

use DateTimeZone;
use DateTimeImmutable;

/**
 * Provides a single source of current time in UTC.
 *
 * Used across the system to satisfy BR-4 (timestamps consistency).
 */
final class Clock
{
    private const FORMAT = 'c';

    /**
     * Return current UTC time in ISO-8601 format.
     *
     * Example: 2025-08-19T11:30:45+00:00
     *
     * @return string
     */
    public static function now(): string
    {
        $tz      = new DateTimeZone('UTC');
        $now     = new DateTimeImmutable('now', $tz);
        $result  = $now->format(self::FORMAT);

        return $result;
    }
}
