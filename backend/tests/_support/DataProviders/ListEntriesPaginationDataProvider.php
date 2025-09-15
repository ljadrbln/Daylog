<?php
declare(strict_types=1);

namespace Daylog\Tests\Support\DataProviders;

use Daylog\Domain\Models\Entries\ListEntriesConstraints;

/**
 * Data provider for UC-2 pagination bounds (AC-04).
 *
 * Purpose:
 * Centralize test cases for perPage clamping and empty page handling,
 * so both Unit and Integration test suites can reuse them.
 */
trait ListEntriesPaginationDataProvider
{
    /**
     * Provide cases for pagination bounds.
     *
     * Cases:
     *  - perPage below minimum => clamped to 1; page 1 returns exactly 1 item; total 5 => 5 pages
     *  - perPage above maximum => clamped to 100; all items fit on page 1; total pages = 1
     *  - page beyond range => empty items, but metadata (pagesCount) stays consistent
     *
     * @return array<string, array{int,int,int,int,int}>
     */
    public function providePaginationBoundsCases(): array
    {
        $minPerPage = ListEntriesConstraints::PER_PAGE_MIN;
        $maxPerPage = ListEntriesConstraints::PER_PAGE_MAX;
        $minPage    = ListEntriesConstraints::PAGE_MIN;

        $cases = [
            // perPage below minimum => clamped to 1; page 1 returns exactly 1 item; total 5 => 5 pages
            'perPage below minimum is clamped to 1'
                => [0, $minPerPage, $minPage, 1, 5],

            // perPage above maximum => clamped to 100; all 5 items fit on page 1; total pages = 1
            'perPage above maximum is clamped to 100'
                => [200, $maxPerPage, $minPage, 5, 1],

            // page beyond boundary => empty items; with perPage=2 and total=5 => pagesCount = ceil(5/2) = 3
            'requesting page beyond range yields empty items with valid meta'
                => [2, 2, 10, 0, 3],
        ];

        return $cases;
    }
}