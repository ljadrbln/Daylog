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
     * AC-01: happy path returns boundary items, ordered by date DESC.
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
     *   expectedIds: array<int,string>
     * }
     */
    public static function ac01HappyPath(): array
    {
        $rows = EntryTestData::getMany(3, 1);

        $id0  = $rows[0]['id'];
        $id1  = $rows[1]['id'];
        $id2  = $rows[2]['id'];

        $expectedIds = [$id2, $id1, $id0];

        $dataset = [
            'rows'        => $rows,
            'expectedIds' => $expectedIds
        ];

        return $dataset;
    }

    /**
     * AC-02: dateFrom..dateTo returns boundary items, ordered by date DESC.
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
     *   from: string,
     *   to: string,
     *   expectedIds: array<int,string>
     * }
     */
    public static function ac02DateRangeInclusive(): array
    {
        $rows = EntryTestData::getMany(3, 1);
        $r0   = $rows[0];
        $r1   = $rows[1];

        $id0  = $r0['id'];
        $id1  = $r1['id'];

        $from = $r0['date'];
        $to   = $r1['date'];

        $expectedIds = [$id1, $id0];

        $dataset = [
            'rows'        => $rows,
            'from'        => $from,
            'to'          => $to,
            'expectedIds' => $expectedIds
        ];

        return $dataset;
    }

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
     * AC-04: perPage clamping and valid empty pages. 
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
     *   expectedIds: array<int, string>
     * }
     */
    public static function ac04PaginationBoundsClamp(): array {
        $rows = EntryTestData::getMany(5, 1);

        $id0  = $rows[0]['id'];
        $id1  = $rows[1]['id'];
        $id2  = $rows[2]['id'];
        $id3  = $rows[3]['id'];
        $id4  = $rows[4]['id'];

        $expectedIds = [$id4, $id3, $id2, $id1, $id0];

        $dataset = [
            'rows'        => $rows,
            'expectedIds' => $expectedIds
        ];

        return $dataset;
    }
    
    /**
     * AC-05: Three rows with the same logical date but distinct created/updated timestamps.
     *
     * @return array{
     *   rows: array<int, array{
     *     id: string,
     *     title: string,
     *     body: string,
     *     date: string,
     *     createdAt?: string|null,
     *     updatedAt?: string|null
     *   }>
     * }
     */
    public static function ac05SortingByTimestamps(): array
    {
        $rows = EntryTestData::getMany(3, 0);

        $startDate = $rows[0]['date'];
        $dateBase  = new \DateTimeImmutable($startDate);

        $baseCreated = $dateBase->setTime(10, 0, 0);
        $baseUpdated = $dateBase->setTime(10, 5, 0);

        $stepHours = 1;

        for ($i = 0; $i < count($rows); $i++) {
            $shiftHour = sprintf('+%s hours', $i * $stepHours);
            $createdAt = $baseCreated->modify($shiftHour);
            $updatedAt = $baseUpdated->modify($shiftHour);

            $rows[$i]['createdAt'] = $createdAt->format('Y-m-d H:i:s');
            $rows[$i]['updatedAt'] = $updatedAt->format('Y-m-d H:i:s');
        }

        $dataset = [
            'rows' => $rows
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
