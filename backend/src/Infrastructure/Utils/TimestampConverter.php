<?php

declare(strict_types=1);

namespace Daylog\Infrastructure\Utils;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use InvalidArgumentException;

/**
 * TimestampConverter converts between Domain ISO-8601 UTC and SQL DATETIME.
 *
 * Domain <-> Infrastructure boundary:
 * - Domain works with ISO-8601 UTC strings: 'Y-m-d\TH:i:sP' with '+00:00'.
 * - DB stores UTC DATETIME strings: 'Y-m-d H:i:s' (no time zone).
 */
final class TimestampConverter
{
    /** @var string ISO-8601 output format. */
    private const ISO_FORMAT = DateTimeInterface::ATOM; // Y-m-d\TH:i:sP

    /** @var string SQL DATETIME storage format (UTC). */
    private const SQL_FORMAT = 'Y-m-d H:i:s';

    /**
     * Convert ISO-8601 UTC into SQL DATETIME (UTC).
     *
     * @param string $isoUtc ISO-8601 UTC, e.g. '2025-09-10T19:18:42+00:00'.
     * @return string SQL DATETIME 'Y-m-d H:i:s' (UTC).
     *
     * @throws InvalidArgumentException When input is not a valid ISO-8601 datetime.
     */
    public static function isoToSqlUtc(string $isoUtc): string
    {
        $parsed = new DateTimeImmutable($isoUtc);
        $isUtc  = $parsed->getOffset() === 0;

        if (!$isUtc) {
            $message = 'Expected ISO-8601 UTC with +00:00 offset.';
            throw new InvalidArgumentException($message);
        }

        $utc    = new DateTimeZone('UTC');
        $dt     = $parsed->setTimezone($utc);
        $result = $dt->format(self::SQL_FORMAT);

        return $result;
    }

    /**
     * Convert SQL DATETIME (UTC) into ISO-8601 UTC.
     *
     * @param string $sqlDatetime SQL 'Y-m-d H:i:s' stored in UTC.
     * @return string ISO-8601 'Y-m-d\TH:i:sP' with '+00:00'.
     *
     * @throws InvalidArgumentException When input cannot be parsed.
     */
    public static function sqlToIsoUtc(string $sqlDatetime): string
    {
        $utc    = new DateTimeZone('UTC');
        $dt     = DateTimeImmutable::createFromFormat(self::SQL_FORMAT, $sqlDatetime, $utc);

        if ($dt === false) {
            $message = sprintf('Invalid SQL DATETIME, expected %s.', self::SQL_FORMAT);
            throw new InvalidArgumentException($message);
        }

        $result = $dt->format(self::ISO_FORMAT);
        
        return $result;
    }
}
