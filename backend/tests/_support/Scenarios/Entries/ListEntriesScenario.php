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
     * AC-07: date=YYYY-MM-DD returns only exact logical-date matches.
     *
     * @param int $count
     * @param int $stepDays
     * @return array{rows:array<int,array>, targetDate:string, expectedIds:array<int,string>}
     */
    public static function ac07SingleDateExact(int $count = 3, int $stepDays = 1): array
    {
        $rows = EntryTestData::getMany($count, $stepDays);

        $targetDate  = $rows[0]['date'];
        $expectedIds = [$rows[0]['id']];

        $dataset = [
            'rows'       => $rows,
            'targetDate' => $targetDate,
            'expectedIds'=> $expectedIds,
        ];

        return $dataset;
    }
}
