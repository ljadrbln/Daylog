<?php
declare(strict_types=1);

namespace Daylog\Domain\Services;

/**
 * Class UuidGenerator
 *
 * Stateless helper for creating and validating RFC‑4122 UUID strings.
 * - generate(): creates UUID v4 using only built-in PHP functions.
 * - isValid(): validates UUIDs of versions 1..5 (RFC‑4122).
 */
final class UuidGenerator
{
    /** @var string RFC‑4122 UUID (v1..v5) validation pattern */
    private const RFC4122_PATTERN =
        '/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i';

    /**
     * Generate a random UUID v4 string (RFC‑4122).
     *
     * @return string UUID v4 in canonical 8-4-4-4-12 hexadecimal form.
     */
    public static function generate(): string
    {
        $uuid = sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            random_int(0, 0xffff), random_int(0, 0xffff),
            random_int(0, 0xffff),
            random_int(0, 0x0fff) | 0x4000,   // version 4
            random_int(0, 0x3fff) | 0x8000,   // variant 10xx
            random_int(0, 0xffff), random_int(0, 0xffff), random_int(0, 0xffff)
        );

        return $uuid;
    }

    /**
     * Check that a string is a valid RFC‑4122 UUID (versions 1..5).
     *
     * @param string $uuid Any candidate string.
     * @return bool True when string matches RFC‑4122 UUID format.
     */
    public static function isValid(string $uuid): bool
    {
        $matches = (bool) preg_match(self::RFC4122_PATTERN, $uuid);
        return $matches;
    }
}
