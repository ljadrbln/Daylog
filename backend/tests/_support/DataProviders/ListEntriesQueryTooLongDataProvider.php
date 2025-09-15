<?php
declare(strict_types=1);

namespace Daylog\Tests\Support\DataProviders;

use Daylog\Domain\Models\Entries\ListEntriesConstraints;

/**
 * Centralized overlong query cases for UC-2 AC-9.
 *
 * Purpose:
 *   Provide >30-char strings that remain too long after trimming.
 *   Covers whitespace and control-char variants to avoid flakiness.
 */
trait ListEntriesQueryTooLongDataProvider
{
    /**
     * Provide overlong query inputs.
     *
     * Cases:
     *  - Exactly 31 ASCII chars.
     *  - 31 chars padded with spaces.
     *  - 31 chars padded with newline/tab.
     *
     * @return array<string,array{string}>
     */
    public static function provideTooLongQueries(): array
    {
        $length = ListEntriesConstraints::QUERY_MAX + 1;

        $thirtyOne = str_repeat('a', $length);
        $withSpaces = '   ' . str_repeat('b', $length) . '   ';
        $withNlTab  = "\n\t" . str_repeat('c', $length) . "\t\n";

        return [
            '31 ascii chars'                   => [$thirtyOne],
            '31 chars with surrounding spaces' => [$withSpaces],
            '31 chars with newline/tab'        => [$withNlTab],
        ];
    }
}
