<?php
declare(strict_types=1);

namespace Daylog\Presentation\Requests\Entries\ListEntries;

/**
 * Sanitizer for UC-2 List Entries transport input (pure, non-expanding).
 *
 * Purpose:
 * Apply BR-1 (Trimming) only to existing string fields without introducing
 * new keys or altering types of non-string values.
 *
 * Mechanics:
 * - Iterates over a known whitelist of UC-2 fields.
 * - If a field exists in input and its value is a string, trims it; otherwise leaves as-is.
 * - Does not add missing keys, does not perform business validation.
 *
 * Typical scenarios:
 * - "  dateFrom  " → "dateFrom"
 * - query with spaces → trimmed
 * - numeric page/perPage remain untouched
 */
final class ListEntriesSanitizer
{
    /**
     * Trim only present string fields, preserve everything else as-is.
     *
     * @param array<string,mixed> $params Raw transport map.
     * @return array<string,mixed> Sanitized map containing only keys present in input.
     */
    public static function sanitize(array $params): array
    {
        /** @var string[] $knownKeys */
        $knownKeys = [
            'page',
            'perPage',
            'sortField',
            'sortDir',
            'dateFrom',
            'dateTo',
            'date',
            'query',
        ];

        /** @var array<string,mixed> $result */
        $result = [];

        foreach ($knownKeys as $key) {
            if (array_key_exists($key, $params)) {
                $value = $params[$key];

                if (is_string($value)) {
                    $value = trim($value);
                }

                $result[$key] = $value;
            }
        }

        return $result;
    }
}
