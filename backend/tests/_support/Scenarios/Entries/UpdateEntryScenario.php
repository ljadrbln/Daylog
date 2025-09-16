<?php
declare(strict_types=1);

namespace Daylog\Tests\Support\Scenarios\Entries;

use DateTimeImmutable;
use Daylog\Domain\Models\Entries\Entry;
use Daylog\Domain\Services\Clock;
use Daylog\Tests\Support\Helper\EntryTestData;

/**
 * Centralized scenarios for UC-5 UpdateEntry.
 *
 * Purpose:
 * Provide deterministic datasets shared by Unit and Integration tests.
 * AC-01 (title-only) ensures both timestamps are shifted into the past so that
 * executing the use case at Clock::now() monotonically increases 'updatedAt'.
 *
 * Mechanics:
 * - Take Clock::now() (ISO-8601 UTC) and shift createdAt/updatedAt by "-1 hour";
 * - Build a canonical Entry payload via EntryTestData::getOne();
 * - Return a single-row dataset with explicit 'targetId' and 'newTitle'.
 *
 */
final class UpdateEntryScenario
{
    /**
     * AC-01 (title-only) happy path dataset.
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
     *   targetId: string,
     *   newTitle: string
     * }
     */
    public static function ac01TitleOnly(): array
    {
        $nowIso = Clock::now();

        $nowObj = new DateTimeImmutable($nowIso);
        $pastObj = $nowObj->modify('-1 hour');
        $pastIso = $pastObj->format(DATE_ATOM);

        $title = 'Valid title';
        $body  = 'Valid body';
        $date  = '2025-09-10';

        $createdAt = $pastIso;
        $updatedAt = $pastIso;

        $row  = EntryTestData::getOne($title, $body, $date, $createdAt, $updatedAt);
        $rows = [$row];

        $targetId = $row['id'];
        $newTitle = 'Updated title';

        $dataset = [
            'rows'     => $rows,
            'targetId' => $targetId,
            'newTitle' => $newTitle,
        ];

        return $dataset;
    }

    /**
     * AC-02 (body-only) happy path dataset.
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
     *   targetId: string,
     *   newBody: string
     * }
     */
    public static function ac02BodyOnly(): array
    {
        $nowIso = Clock::now();

        $nowObj = new DateTimeImmutable($nowIso);
        $pastObj = $nowObj->modify('-1 hour');
        $pastIso = $pastObj->format(DATE_ATOM);

        $title = 'Valid title';
        $body  = 'Valid body';
        $date  = '2025-09-10';

        $createdAt = $pastIso;
        $updatedAt = $pastIso;

        $row  = EntryTestData::getOne($title, $body, $date, $createdAt, $updatedAt);
        $rows = [$row];

        $targetId = $row['id'];
        $newBody  = 'Updated body';

        $dataset = [
            'rows'     => $rows,
            'targetId' => $targetId,
            'newBody'  => $newBody,
        ];

        return $dataset;
    }

    /**
     * AC-03 (date-only) happy path dataset.
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
     *   targetId: string,
     *   newDate: string
     * }
     */
    public static function ac03DateOnly(): array
    {
        $nowIso = Clock::now();

        $nowObj = new DateTimeImmutable($nowIso);
        $pastObj = $nowObj->modify('-1 hour');
        $pastIso = $pastObj->format(DATE_ATOM);

        $title = 'Valid title';
        $body  = 'Valid body';
        $date  = '2025-09-10';

        $createdAt = $pastIso;
        $updatedAt = $pastIso;

        $row  = EntryTestData::getOne($title, $body, $date, $createdAt, $updatedAt);
        $rows = [$row];

        $targetId = $row['id'];
        $newDate  = '1999-12-01';

        $dataset = [
            'rows'     => $rows,
            'targetId' => $targetId,
            'newDate'  => $newDate,
        ];

        return $dataset;
    }
    
   /**
     * AC-04 (partial update: title+body) happy path dataset.
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
     *   targetId: string,
     *   newTitle: string,
     *   newBody: string
     * }
     */
    public static function ac04TitleAndBody(): array
    {
        $nowIso = Clock::now();

        $nowObj = new DateTimeImmutable($nowIso);
        $pastObj = $nowObj->modify('-1 hour');
        $pastIso = $pastObj->format(DATE_ATOM);

        $title = 'Valid title';
        $body  = 'Valid body';
        $date  = '2025-09-10';

        $createdAt = $pastIso;
        $updatedAt = $pastIso;

        $row  = EntryTestData::getOne($title, $body, $date, $createdAt, $updatedAt);
        $rows = [$row];

        $targetId = $row['id'];
        $newTitle = 'Updated title';
        $newBody  = 'Updated body';

        $dataset = [
            'rows'     => $rows,
            'targetId' => $targetId,
            'newTitle' => $newTitle,
            'newBody'  => $newBody,
        ];

        return $dataset;
    }    
}
