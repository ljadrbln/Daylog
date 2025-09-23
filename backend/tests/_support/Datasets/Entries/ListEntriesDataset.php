<?php
declare(strict_types=1);

namespace Daylog\Tests\Support\Datasets\Entries;

use DateTimeImmutable;
use Daylog\Tests\Support\Helper\EntryTestData;
use Daylog\Tests\Support\Helper\ListEntriesHelper;
use Daylog\Application\DTO\Entries\ListEntries\ListEntriesRequest;
use Daylog\Application\DTO\Entries\ListEntries\ListEntriesRequestInterface;

/**
 * Centralized datasets for UC-2 ListEntries (AC-01…AC-08).
 *
 * Purpose:
 * Provide deterministic, uniform datasets for Unit / Integration / Functional tests.
 * Each AC prepares storage rows and optional payload overrides; a single helper
 * getDataset() applies overrides to the baseline, builds the ListEntriesRequest DTO,
 * and returns a stable dataset shape used across all test layers.
 *
 * Mechanics:
 * - Rows are produced via EntryTestData::getMany($count, $stepDays) to keep dates predictable;
 * - Filters (date/dateFrom/dateTo/query/page/perPage) are set only through payload overrides;
 * - expectedIds reflects order by primary sort (date DESC) and any secondary rules where relevant.
 *
 * Return shape (uniform for all ACs):
 *   {
 *     rows: array<int, TRow>,
 *     payload: TPayload,
 *     request: ListEntriesRequestInterface,
 *     expectedIds: array<int,string>
 *   }
 *
 * @phpstan-type TRow array{
 *   id: string,
 *   title: string,
 *   body: string,
 *   date: string,
 *   createdAt?: string|null,
 *   updatedAt?: string|null
 * }
 * @phpstan-type TRows array<int, TRow>
 * @phpstan-type TPayload array{
 *   page?: int,
 *   perPage?: int,
 *   date?: string,
 *   dateFrom?: string,
 *   dateTo?: string,
 *   query?: string
 * }
 * @phpstan-type TDataset array{
 *   rows: TRows,
 *   payload: TPayload,
 *   request: ListEntriesRequestInterface,
 *   expectedIds: array<int,string>
 * }
 */
final class ListEntriesDataset
{
    /**
     * AC-01 — Happy path: default listing ordered by date DESC.
     *
     * @return TDataset
     */
    public static function ac01HappyPath(): array
    {
        $rows = EntryTestData::getMany(3, 1);

        $id0 = $rows[0]['id'];
        $id1 = $rows[1]['id'];
        $id2 = $rows[2]['id'];

        $expectedIds = [$id2, $id1, $id0];

        $overrides = [];
        $dataset   = self::getDataset($rows, $overrides, $expectedIds);

        return $dataset;
    }

    /**
     * AC-02 — Inclusive range [dateFrom..dateTo] returns boundary items (date DESC).
     *
     * @return TDataset
     */
    public static function ac02DateRangeInclusive(): array
    {
        $rows = EntryTestData::getMany(3, 1);

        $from = $rows[0]['date'];
        $to   = $rows[1]['date'];

        $id0 = $rows[0]['id'];
        $id1 = $rows[1]['id'];

        $expectedIds = [$id1, $id0];

        $overrides = [
            'dateFrom' => $from,
            'dateTo'   => $to,
        ];

        $dataset = self::getDataset($rows, $overrides, $expectedIds);

        return $dataset;
    }

    /**
     * AC-03 — query matches title OR body, case-insensitive (keeps date DESC within matches).
     *
     * @return TDataset
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

        $overrides = [
            'query' => $query,
        ];

        $dataset = self::getDataset($rows, $overrides, $expectedIds);

        return $dataset;
    }

    /**
     * AC-04 — Pagination bounds clamp.
     *
     * Purpose:
     * Build a 5-row dataset with deterministic dates, then inject pagination parameters
     * directly into the payload so tests don’t mutate payloads manually.
     *
     * Mechanics:
     * - Rows: 5 items with a 1-day step (D1..D5).
     * - Expected total order by date DESC (for convenience/consistency).
     * - perPage/page are passed in and included in the request DTO.
     *
     * @param int $perPage Raw perPage from client (may be out of bounds).
     * @param int $page    Requested page number.
     * @phpstan-return TDataset
     */
    public static function ac04PaginationBoundsClamp(int $perPage, int $page): array
    {
        $rows = EntryTestData::getMany(5, 1);

        $id0 = $rows[0]['id'];
        $id1 = $rows[1]['id'];
        $id2 = $rows[2]['id'];
        $id3 = $rows[3]['id'];
        $id4 = $rows[4]['id'];

        $expectedIds = [$id4, $id3, $id2, $id1, $id0];

        $overrides = [
            'perPage' => $perPage,
            'page'    => $page,
        ];

        $dataset = self::getDataset($rows, $overrides, $expectedIds);
        return $dataset;
    }

    /**
     * AC-05 — Three rows share the same logical date; createdAt/updatedAt differ to exercise timestamp-based sorting.
     * sortField/sortDir are injected into payload so the ListEntriesRequest is fully prepared by the dataset.
     *
     * @param string       $sortField One of: 'createdAt'|'updatedAt'
     * @param 'ASC'|'DESC' $sortDir   Sorting direction
     * @return array{
     *   rows: array<int, array{
     *     id: string,
     *     title: string,
     *     body: string,
     *     date: string,
     *     createdAt?: string|null,
     *     updatedAt?: string|null
     *   }>,
     *   payload: array{
     *     page?: int,
     *     perPage?: int,
     *     date?: string,
     *     dateFrom?: string,
     *     dateTo?: string,
     *     query?: string,
     *     sortField?: string,
     *     sortDir?: 'ASC'|'DESC'
     *   },
     *   request: ListEntriesRequestInterface,
     *   expectedIds: array<int,string>
     * }
     */
    public static function ac05SortingByTimestamps(string $sortField, string $sortDir): array
    {
        $rows = \Daylog\Tests\Support\Helper\EntryTestData::getMany(3, 0);

        $startDate = $rows[0]['date'];
        $base      = new DateTimeImmutable($startDate);

        $baseCreated = $base->setTime(10, 0, 0);
        $baseUpdated = $base->setTime(10, 5, 0);

        $stepHours = 1;

        for ($i = 0; $i < count($rows); $i++) {
            $shiftSpec             = sprintf('+%d hours', $i * $stepHours);
            $createdAtObj          = $baseCreated->modify($shiftSpec);
            $updatedAtObj          = $baseUpdated->modify($shiftSpec);
            $rows[$i]['createdAt'] = $createdAtObj->format('Y-m-d\TH:i:s+00:00');
            $rows[$i]['updatedAt'] = $updatedAtObj->format('Y-m-d\TH:i:s+00:00');
        }

        // We compute expectations in the test at runtime, so keep this empty by design.
        $expectedIds = [];

        $overrides = [
            'sortField' => $sortField,
            'sortDir'   => $sortDir,
        ];

        $dataset = self::getDataset($rows, $overrides, $expectedIds);

        return $dataset;
    }

    /**
     * AC-06 — Build a request with an invalid date-like field.
     *
     * Purpose:
     * Provide a minimal dataset focused on validation: no rows are required,
     * only a payload where exactly one of {date, dateFrom, dateTo} is invalid.
     *
     * Mechanics:
     * - Start from ListEntriesHelper::getData() baseline;
     * - Override $field with $value; keep others valid/empty;
     * - Build ListEntriesRequest inside the dataset to keep shape uniform.
     *
     * @param string $field One of: date|dateFrom|dateTo.
     * @param string $value Invalid raw string.
     * @phpstan-return TDataset
     */
    public static function ac06InvalidDateInput(string $field, string $value): array
    {
        $rows = []; // Not needed: validation must fail before repo access.

        $overrides = [
            $field => $value,
        ];

        $dataset = self::getDataset($rows, $overrides, []);
        return $dataset;
    }

    /**
     * AC-07 — date=YYYY-MM-DD returns only exact logical-date matches (date DESC within the day).
     *
     * @param int $count    Number of rows to generate.
     * @param int $stepDays Step in days between generated rows.
     * @return TDataset
     */
    public static function ac07SingleDateExact(int $count = 5, int $stepDays = 1): array
    {
        $rows = EntryTestData::getMany($count, $stepDays);

        $rows[1]['date'] = $rows[0]['date'];
        $targetDate      = $rows[0]['date'];

        $id0 = $rows[0]['id'];
        $id1 = $rows[1]['id'];

        $expectedIds = [$id1, $id0];

        $overrides = [
            'date' => $targetDate,
        ];

        $dataset = self::getDataset($rows, $overrides, $expectedIds);

        return $dataset;
    }

    /**
     * AC-08 — Stable secondary order when primary keys are equal: prefer createdAt DESC (example).
     * All rows share the same logical date; createdAt differs, updatedAt is constant.
     *
     * @return TDataset
     */
    public static function ac08StableSecondaryOrder(): array
    {
        $rows = EntryTestData::getMany(3, 0);

        $startDate = $rows[0]['date'];
        $base      = new DateTimeImmutable($startDate);

        $baseCreated = $base->setTime(10, 0, 0);
        $baseUpdated = $base->setTime(10, 5, 0);

        $stepHours = 1;

        for ($i = 0; $i < count($rows); $i++) {
            $shiftSpec            = sprintf('+%d hours', $i * $stepHours);
            $createdObj           = $baseCreated->modify($shiftSpec);
            $rows[$i]['createdAt'] = $createdObj->format('Y-m-d\TH:i:s+00:00');
            $rows[$i]['updatedAt'] = $baseUpdated->format('Y-m-d\TH:i:s+00:00');
        }

        $id0 = $rows[0]['id'];
        $id1 = $rows[1]['id'];
        $id2 = $rows[2]['id'];

        $expectedIds = [$id2, $id1, $id0];

        $overrides = [];
        $dataset   = self::getDataset($rows, $overrides, $expectedIds);

        return $dataset;
    }

    /**
     * AC-9 — Build a request with an overlong query (>30 after trim).
     *
     * Purpose:
     * Provide a minimal dataset focused on validation: rows are not required.
     * The payload contains only the 'query' field with an overlong value.
     *
     * Mechanics:
     * - Start from ListEntriesHelper::getData() baseline;
     * - Override 'query' with the provided raw value (may include whitespace);
     * - Build ListEntriesRequest so tests can use $dataset['request'] directly.
     *
     * @param string $rawQuery Overlong query string (raw, possibly with spaces).
     * @phpstan-return TDataset
     */
    public static function ac09QueryTooLong(string $rawQuery): array
    {
        $rows = [];

        $overrides = [
            'query' => $rawQuery,
        ];

        $dataset = self::getDataset($rows, $overrides, []);
        return $dataset;
    }    

    /**
     * Build dataset with reversed inclusive range (dateFrom > dateTo).
     *
     * @param string $from Later date to place into 'dateFrom'.
     * @param string $to   Earlier date to place into 'dateTo'.
     * @return array{
     *   rows: array<int, array<string,mixed>>,
     *   payload: array<string,mixed>,
     *   request: ListEntriesRequestInterface,
     *   expectedIds: array<int,string>
     * }
     */
    public static function ac10DateRangeOrderInvalid(string $from, string $to): array
    {
        $rows = [];

        $overrides = [
            'dateFrom' => $from,
            'dateTo'   => $to,
        ];

        $expectedIds = [];

        $dataset = self::getDataset($rows, $overrides, $expectedIds);

        return $dataset;
    }    

    /**
     * TR-01: page is non-numeric (array).
     *
     * @return array{query: array<string,mixed>}
     */
    public static function tr01PageMustBeNumeric(): array
    {
        $payload = ['page' => ['not-numeric']];
        $dataset = ['payload' => $payload];

        return $dataset;
    }

    /**
     * TR-02: perPage is non-numeric (array).
     *
     * @return array{query: array<string,mixed>}
     */
    public static function tr02PerPageMustBeNumeric(): array
    {
        $payload = ['perPage' => ['not-numeric']];
        $dataset = ['payload' => $payload];

        return $dataset;
    }

    /**
     * TR-03: sortField is non-string (array).
     *
     * @return array{query: array<string,mixed>}
     */
    public static function tr03SortFieldMustBeString(): array
    {
        $payload = ['sortField' => ['createdAt']];
        $dataset = ['payload' => $payload];

        return $dataset;
    }

    /**
     * TR-04: sortDir is non-string (array).
     *
     * @return array{query: array<string,mixed>}
     */
    public static function tr04SortDirMustBeString(): array
    {
        $payload = ['sortDir' => ['ASC']];
        $dataset = ['payload' => $payload];

        return $dataset;
    }

    /**
     * TR-05: dateFrom is non-string (array).
     *
     * @return array{query: array<string,mixed>}
     */
    public static function tr05DateFromMustBeString(): array
    {
        $payload = ['dateFrom' => ['2025-09-01']];
        $dataset = ['payload' => $payload];

        return $dataset;
    }

    /**
     * TR-06: dateTo is non-string (array).
     *
     * @return array{query: array<string,mixed>}
     */
    public static function tr06DateToMustBeString(): array
    {
        $payload = ['dateTo' => ['2025-09-30']];
        $dataset = ['payload' => $payload];

        return $dataset;
    }

    /**
     * TR-07: date is non-string (array).
     *
     * @return array{query: array<string,mixed>}
     */
    public static function tr07DateMustBeString(): array
    {
        $payload = ['date' => ['2025-09-23']];
        $dataset = ['payload' => $payload];

        return $dataset;
    }

    /**
     * TR-08: query is non-string (array).
     *
     * @return array{query: array<string,mixed>}
     */
    public static function tr08QueryMustBeString(): array
    {
        $payload = ['query' => ['hello']];
        $dataset = ['payload' => $payload];

        return $dataset;
    }

    /**
     * Helper to build the final dataset shape.
     *
     * Starts from ListEntriesHelper::getData() baseline, applies payload overrides,
     * and constructs ListEntriesRequest DTO. Use this from all AC methods to keep
     * shape identical.
     *
     * @phpstan-param TRows $rows
     * @phpstan-param TPayload $overrides
     * @phpstan-param array<int,string> $expectedIds
     * @phpstan-return TDataset
     */
    private static function getDataset(array $rows, array $overrides, array $expectedIds): array
    {
        $payload = ListEntriesHelper::getData();

        foreach ($overrides as $key => $value) {
            $payload[$key] = $value;
        }

        $request = ListEntriesRequest::fromArray($payload);

        $dataset = [
            'rows'        => $rows,
            'payload'     => $payload,
            'request'     => $request,
            'expectedIds' => $expectedIds,
        ];

        return $dataset;
    }
}
