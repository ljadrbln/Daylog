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
}
