<?php
declare(strict_types=1);

namespace Daylog\Tests\Support\Datasets\Entries;

use DateTimeImmutable;
use Daylog\Domain\Services\Clock;
use Daylog\Tests\Support\Helper\EntryTestData;
use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequest;
use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface;

/**
 * Centralized scenarios for UC-5 UpdateEntry.
 *
 * Purpose:
 * Provide deterministic datasets reused in Unit and Integration tests.
 * Each AC method specifies which fields change; time shifting and baseline
 * row generation are encapsulated in a common helper.
 *
 * Mechanics:
 * - Shift createdAt/updatedAt into the past to guarantee BR-2 monotonicity;
 * - Build a canonical Entry payload via EntryTestData::getOne();
 * - Each dataset returns:
 *     - `rows`: array with one baseline row,
 *     - `targetId`: id of that row,
 *     - plus new field values (title/body/date) depending on the case.
 */
final class UpdateEntryDataset
{
    /**
     * AC-01 (title-only) happy path dataset.
     *
     * @return array{
     *   rows: array<int,array{
     *     id: string,
     *     title: string,
     *     body: string,
     *     date: string,
     *     createdAt: string,
     *     updatedAt: string
     *   }>,
     *   payload: array{id: string, title: string},
     *   request: UpdateEntryRequestInterface
     * }
     */
    public static function ac01TitleOnly(): array
    {
        $row  = self::buildBaselineRowWithPastTimestamps();
        $rows = [$row];

        $payload = [
            'id'    => $row['id'],
            'title' => 'Updated title'
        ];

        $request = UpdateEntryRequest::fromArray($payload);

        $dataset = [
            'rows'    => $rows,
            'payload' => $payload,
            'request' => $request,
        ];

        return $dataset;
    }

    /**
     * AC-02 (body-only) happy path dataset.
     *
     * @return array{
     *   rows: array<int,array{
     *     id: string,
     *     title: string,
     *     body: string,
     *     date: string,
     *     createdAt: string,
     *     updatedAt: string
     *   }>,
     *   targetId: string,
     *   newBody: string
     * }
     */
    public static function ac02BodyOnly(): array
    {
        $row  = self::buildBaselineRowWithPastTimestamps();
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
     *   rows: array<int,array{
     *     id: string,
     *     title: string,
     *     body: string,
     *     date: string,
     *     createdAt: string,
     *     updatedAt: string
     *   }>,
     *   targetId: string,
     *   newDate: string
     * }
     */
    public static function ac03DateOnly(): array
    {
        $row  = self::buildBaselineRowWithPastTimestamps();
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
     *   rows: array<int,array{
     *     id: string,
     *     title: string,
     *     body: string,
     *     date: string,
     *     createdAt: string,
     *     updatedAt: string
     *   }>,
     *   targetId: string,
     *   newTitle: string,
     *   newBody: string
     * }
     */
    public static function ac04TitleAndBody(): array
    {
        $row  = self::buildBaselineRowWithPastTimestamps();
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

    /**
     * AC-14 (no-op) dataset: request equals stored values.
     *
     * Purpose:
     * Provide a single-row dataset where the incoming request repeats the stored
     * values (id/title/body/date). The use case must detect no effective changes
     * and raise NO_CHANGES_APPLIED without touching updatedAt.
     *
     * Mechanics:
     * - Build a baseline row with past timestamps to make any accidental update
     *   detectable by monotonicity checks (should not change in this case).
     *
     * @return array{
     *   rows: array<int,array{
     *     id: string,
     *     title: string,
     *     body: string,
     *     date: string,
     *     createdAt: string,
     *     updatedAt: string
     *   }>
     * }
     */
    public static function ac14NoOp(): array
    {
        $row  = self::buildBaselineRowWithPastTimestamps();
        $rows = [$row];

        $dataset = [
            'rows' => $rows,
        ];

        return $dataset;
    }

    /**
     * Build a canonical Entry row with past timestamps.
     *
     * Purpose:
     * Create a deterministic Entry payload where both `createdAt` and `updatedAt`
     * are shifted into the past. This guarantees that any subsequent use case
     * execution at Clock::now() produces a strictly newer `updatedAt`,
     * thus maintaining BR-2 monotonicity.
     *
     * Mechanics:
     * - Take the current UTC timestamp via Clock::now();
     * - Convert to DateTimeImmutable and shift by $shiftSpec (default "-1 hour");
     * - Format as ISO-8601 and assign to both `createdAt` and `updatedAt`;
     * - Call EntryTestData::getOne() with named parameters for these timestamps.
     *
     * @param string $shiftSpec Relative modification spec for DateTimeImmutable::modify(),
     *                          e.g. "-1 hour", "-2 days".
     * @return array{
     *   id: string,
     *   title: string,
     *   body: string,
     *   date: string,
     *   createdAt: string,
     *   updatedAt: string
     * }
     */
    private static function buildBaselineRowWithPastTimestamps(string $shiftSpec = '-1 hour'): array
    {
        $nowIso = Clock::now();
        $nowObj = new DateTimeImmutable($nowIso);

        $pastObj = $nowObj->modify($shiftSpec);
        $pastIso = $pastObj->format(DATE_ATOM);

        $createdAt = $pastIso;
        $updatedAt = $pastIso;

        $row = EntryTestData::getOne(createdAt: $createdAt, updatedAt: $updatedAt);

        return $row;
    }
}
