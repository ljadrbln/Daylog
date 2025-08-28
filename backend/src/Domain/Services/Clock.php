<?php

declare(strict_types=1);

namespace Daylog\Domain\Services;

use DateTimeImmutable;
use DateTimeZone;

/**
 * Provides a single source of current time in UTC.
 *
 * Used across the system to satisfy BR-2 (timestamps consistency): all timestamps
 * must be stored in UTC and formatted as ISO-8601 with an explicit offset.
 * Example:
 *  $nowIso = Clock::now(); // "2025-08-19T11:30:45+00:00"
 *
 * @see DateTimeImmutable
 * @see DateTimeImmutable::format()
 */
final class Clock
{
    /**
     * ISO-8601 format constant used for output.
     *
     * @var string
     */
    private const FORMAT = 'c';

    /**
     * Time zone identifier for UTC.
     *
     * @var string
     */
    private const TIMEZONE = 'UTC';

    /**
     * Return current UTC time in ISO-8601 format with explicit +00:00 offset.
     *
     * @return string Non-empty ISO-8601 string, e.g. "2025-08-19T11:30:45+00:00"
     */
    public static function now(): string
    {
        $timezone = new DateTimeZone(self::TIMEZONE);
        $now      = new DateTimeImmutable('now', $timezone);
        $result   = $now->format(self::FORMAT);

        return $result;
    }
}
