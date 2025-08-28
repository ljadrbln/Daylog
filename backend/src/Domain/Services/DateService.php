<?php

declare(strict_types=1);

namespace Daylog\Domain\Services;

use DateTimeImmutable;

/**
 * DateService provides strict ISO local date ('Y-m-d') validation.
 *
 * Keep it simple, reusable across Entry/Tag/Attachment.
 */
final class DateService
{
    /**
     * Validate strict 'Y-m-d' date using DateTime with a round-trip check.
     *
     * @param string $dateString Input date string
     * @return bool True when valid; false otherwise
     */
    public static function isValidLocalDate(string $dateString): bool
    {
        $format = 'Y-m-d';
        $dt     = DateTimeImmutable::createFromFormat($format, $dateString);

        $isValid = $dt !== false && $dt->format($format) === $dateString;
        return $isValid;
    }

    /**
     * Validate strict ISO-8601 UTC datetime: 'Y-m-d\TH:i:sP' with '+00:00' offset.
     *
     * Purpose:
     * Ensures timestamps like '2025-08-28T16:55:11+00:00' are strictly formatted and in UTC.
     * The check is two-step: exact format round-trip + zero offset (UTC).
     *
     * @param string $dateTimeString Input datetime string.
     * @return bool True when strictly ISO-8601 with '+00:00' and round-trip-stable; false otherwise.
     */
    public static function isValidIsoUtcDateTime(string $dateTimeString): bool
    {
        $format = 'Y-m-d\TH:i:sP';
        $dt     = DateTimeImmutable::createFromFormat($format, $dateTimeString);

        $isStrictFormat = $dt !== false && $dt->format($format) === $dateTimeString;
        if (!$isStrictFormat) {
            return false;
        }

        $isUtc  = $dt->getOffset() === 0; // requires '+00:00'
        
        return $isUtc;
    }    
}
