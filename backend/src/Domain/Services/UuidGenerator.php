<?php

namespace Daylog\Domain\Services;

/**
 * Utility class for generating and validating UUID v4 identifiers.
 */
class UuidGenerator
{
    /**
     * Generates a random UUID (version 4).
     *
     * @return string UUID v4 string in the format xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx
     */
    public static function generate(): string
    {
        $uuid = sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            random_int(0, 0xffff), random_int(0, 0xffff),
            random_int(0, 0xffff),
            random_int(0, 0x0fff) | 0x4000,
            random_int(0, 0x3fff) | 0x8000,
            random_int(0, 0xffff), random_int(0, 0xffff), random_int(0, 0xffff)
        );

        return $uuid;
    }
}
