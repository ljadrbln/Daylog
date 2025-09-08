<?php
declare(strict_types=1);

namespace Daylog\Presentation\Requests\Entries\UpdateEntry;

/**
 * Sanitizer for UC-5 Update Entry transport input.
 *
 * Purpose:
 * - Apply BR-1 (Trimming) consistently to all string fields present in payload.
 * - Keep UpdateEntryRequestFactory focused on type checks and DTO creation.
 *
 * Mechanics:
 * - Called inside UpdateEntryRequestFactory::fromArray().
 * - Does not perform business validation (only normalization).
 * - Ignores absent optional fields; trims only those provided.
 */
final class UpdateEntrySanitizer
{
    /**
     * Apply BR-1 trimming to raw params.
     *
     * @param array<string,mixed> $params Raw input from transport.
     *
     * @return array<string,mixed> Normalized payload with trimmed strings.
     */
    public static function sanitize(array $params): array
    {
        $clean = [];

        foreach ($params as $key => $value) {
            if (is_string($value)) {
                $clean[$key] = trim($value);
                continue;
            }

            // Preserve non-string values as-is;
            // type validation happens elsewhere.
            $clean[$key] = $value;
        }

        return $clean;
    }
}
