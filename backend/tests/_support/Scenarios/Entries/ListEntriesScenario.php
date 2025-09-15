<?php
declare(strict_types=1);

namespace Daylog\Tests\Support\Scenarios\Entries;

use Daylog\Tests\Support\Helper\EntryTestData;

/**
 * Scenario data for ListEntries use case.
 *
 * Purpose:
 *   Provide a single source of truth for AC-07 (single-date exact match) across Unit, Integration, and Functional tests.
 *
 * Mechanics:
 *   - Build N rows with a fixed day step so dates differ predictably.
 *   - Choose the target date from the first row.
 *   - Compute expected IDs that MUST be returned by the UC.
 */
final class ListEntriesScenario
{
    /**
     * AC-03: query matches title OR body, case-insensitive.
     *
     * @return array{
     *   rows: array<int, array{
     *     id: string,
     *     title: string,
     *     body: string,
     *     date: string,
     *     createdAt?: string|null,
     *     updatedAt?: string|null
     *   }>,
     *   query: string,
     *   expectedIds: array<int, string>
     * }
     */
    public static function ac03QueryTitleOrBodyCaseInsensitive(): array
    {
        $query = 'alpha';

        $rows = EntryTestData::getMany(3, 1);
        $rows[0]['title'] = 'Alpha note';
        $rows[1]['body']  = 'This body has aLpHa inside';

        $id0 = $rows[0]['id'];
        $id1 = $rows[1]['id'];
        $expectedIds = [$id1, $id0];

        $dataset = [
            'rows'        => $rows,
            'query'       => $query,
            'expectedIds' => $expectedIds,
        ];

        return $dataset;
    }

    /**
     * AC-07: date=YYYY-MM-DD returns only exact logical-date matches.
     *
     * @param int $count
     * @param int $stepDays
     * @return array{
     *   rows: array<int, array{
     *     id: string,
     *     title: string,
     *     body: string,
     *     date: string,
     *     createdAt?: string|null,
     *     updatedAt?: string|null
     *   }>,
     *   targetDate: string,
     *   expectedIds: array<int, string>
     * }
     */
    public static function ac07SingleDateExact(int $count = 5, int $stepDays = 1): array
    {
        $rows = EntryTestData::getMany($count, $stepDays);
        $rows[1]['date'] = $rows[0]['date'];
        
        $targetDate = $rows[0]['date'];

        $id0 = $rows[0]['id'];
        $id1 = $rows[1]['id'];
        $expectedIds = [$id0, $id1];

        $dataset = [
            'rows'       => $rows,
            'targetDate' => $targetDate,
            'expectedIds'=> $expectedIds,
        ];

        return $dataset;
    }
}
