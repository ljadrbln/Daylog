<?php
declare(strict_types=1);

namespace Daylog\Tests\Support\DataProviders;

/**
 * Centralized invalid date ranges for UC-2 AC-10.
 *
 * Purpose:
 * Provide reversed ranges where dateFrom > dateTo, which must trigger
 * DATE_RANGE_INVALID in validation.
 *
 * Scenarios:
 * - Adjacent days reversed (same month).
 * - Cross-month reversed.
 * - Cross-year reversed.
 */
trait ListEntriesDateRangeDataProvider
{
    /**
     * @return array<string, array{string,string}>
     */
    public function provideInvalidDateRanges(): array
    {
        $cases = [
            'adjacent days reversed' => ['2025-08-12', '2025-08-11'],
            'cross month reversed'   => ['2025-09-01', '2025-08-31'],
            'cross year reversed'    => ['2026-01-01', '2025-12-31'],
        ];

        return $cases;
    }
}
