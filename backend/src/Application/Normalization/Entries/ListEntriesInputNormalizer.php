<?php

declare(strict_types=1);

namespace Daylog\Application\Normalization\Entries;

/**
 * UC-2 input normalizer (RED skeleton).
 *
 * Purpose:
 * Provide a compilable class with the target API so that unit tests can run
 * and fail on assertions rather than on autoload/class-not-found errors.
 *
 * Notes:
 * - This is an intentionally incorrect RED implementation for TDD.
 * - It returns placeholder values with all expected keys present.
 * - Business validation and real normalization will be implemented at GREEN.
 */
final class ListEntriesInputNormalizer
{
    /**
     * Normalize raw transport input for UC-2 List Entries (RED).
     *
     * This RED version only ensures the output shape exists:
     * - keys are present, but values are deliberately incorrect,
     *   so assertions in tests will fail (as intended in TDD Red step).
     *
     * @param array<string,mixed> $input Raw map (ignored in RED).
     * @return array{
     *     page:int,
     *     perPage:int,
     *     sortField:string,
     *     sortDir:'ASC'|'DESC'|string,
     *     date:?string,
     *     dateFrom:?string,
     *     dateTo:?string,
     *     query:?string
     * }
     */
    public function normalize(array $input): array
    {
        $result = [
            'page'      => 0,       // wrong on purpose (should be >= 1)
            'perPage'   => 0,       // wrong on purpose (should be clamped/default)
            'sortField' => '',      // wrong on purpose (should be default like 'date')
            'sortDir'   => '',      // wrong on purpose (should be 'ASC'|'DESC', default 'DESC')
            'date'      => '',      // wrong on purpose (should be null on empty)
            'dateFrom'  => '',      // wrong on purpose (should be null on empty)
            'dateTo'    => '',      // wrong on purpose (should be null on empty)
            'query'     => '',      // wrong on purpose (should be trimmed or null)
        ];

        return $result;
    }
}
