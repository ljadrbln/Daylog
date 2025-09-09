<?php

declare(strict_types=1);

namespace Daylog\Domain\Services;

use DateTimeImmutable;
use InvalidArgumentException;

/**
 * DateService provides strict ISO local date ('Y-m-d') validation.
 *
 * Keep it simple, reusable across Entry/Tag/Attachment.
 */
final class DateService
{
    private const ISO_UTC_FORMAT = 'Y-m-d\TH:i:sP';

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

    /**
     * Return the later of two ISO-8601 UTC instants.
     *
     * Preconditions:
     *  - $a and $b MUST be strict ISO-8601 with '+00:00' offset and pass isValidIsoUtcDateTime().
     *
     * @param string $a ISO-8601 UTC datetime ('Y-m-d\TH:i:sP')
     * @param string $b ISO-8601 UTC datetime ('Y-m-d\TH:i:sP')
     * @return string Later instant, formatted as ISO-8601 UTC.
     *
     * @throws InvalidArgumentException When either input is not strict ISO-8601 UTC.
     */
    public static function maxIsoUtc(string $a, string $b): string
    {
        $format = self::ISO_UTC_FORMAT;

        $da = DateTimeImmutable::createFromFormat($format, $a);
        $db = DateTimeImmutable::createFromFormat($format, $b);

        $isAValid = (
            $da !== false 
            && $da->format($format) === $a 
            && $da->getOffset() === 0
        );

        $isBValid = (
            $db !== false 
            && $db->format($format) === $b 
            && $db->getOffset() === 0
        );

        if (!$isAValid || !$isBValid) {
            $message = sprintf('Expected ISO-8601 UTC strings (+00:00) in format %s', $format);

            throw new InvalidArgumentException($message);
        }

        $max    = $da >= $db ? $da : $db;        
        $result = $max->format($format);

        return $result;
    }    
}
