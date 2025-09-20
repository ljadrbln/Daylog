<?php
declare(strict_types=1);

namespace Daylog\Tests\Support\Datasets\Entries;

use DateTimeImmutable;
use Daylog\Domain\Services\Clock;
use Daylog\Domain\Services\UuidGenerator;
use Daylog\Domain\Models\Entries\EntryConstraints;
use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequest;
use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface;
use Daylog\Tests\Support\Helper\EntryTestData;

/**
 * Centralized datasets for UC-5 UpdateEntry (AC-01…AC-14).
 *
 * Purpose:
 * Provide deterministic, uniform datasets for Unit / Integration / Functional tests.
 * Each AC method differs only by the request payload; common scaffolding is DRY’ed
 * via two helpers: buildBaselineRowWithPastTimestamps() and getDataset().
 *
 * Mechanics:
 * - Baseline row has createdAt/updatedAt shifted to the past (BR-2 monotonicity guard);
 * - Each AC builds a small payload array (id/title/body/date as needed);
 * - getDataset() wraps {rows, payload} and constructs UpdateEntryRequest DTO;
 * - All ACs return the same shape: { rows, payload, request }.
 * 
 * @phpstan-type Row array{
 *   id:string, title:string, body:string, date:string, createdAt:string, updatedAt:string
 * }
 * @phpstan-type Rows array<int, Row>
 * @phpstan-type PayloadBase array{id:string, title?:string, body?:string, date?:string}
 
 */
final class UpdateEntryDataset
{
    /**
     * AC-01 (title-only) happy path dataset.
     *
     * @return array{
     *   rows: Rows,
     *   payload: array{id:string, title:string},
     *   request: UpdateEntryRequestInterface
     * }
     */
    public static function ac01TitleOnly(): array
    {
        $row   = self::buildBaselineRowWithPastTimestamps();
        $rows  = [$row];

        /** @var array{id: string, title: string} $payload */
        $payload = [
            'id'    => $row['id'],
            'title' => 'Updated title',
        ];

        $dataset = self::getDataset($rows, $payload);

        return $dataset;
    }

    /**
     * AC-02 (body-only) happy path dataset.
     *
     * @return array{
     *   rows: Rows,
     *   payload: array{id:string,body:string},
     *   request: UpdateEntryRequestInterface
     * }
     */
    public static function ac02BodyOnly(): array
    {
        $row   = self::buildBaselineRowWithPastTimestamps();
        $rows  = [$row];

        /** @var array{id: string, body: string} $payload */
        $payload = [
            'id'   => $row['id'],
            'body' => 'Updated body',
        ];

        $dataset = self::getDataset($rows, $payload);

        return $dataset;
    }

    /**
     * AC-03 (date-only) happy path dataset.
     *
     * @return array{
     *   rows: Rows,
     *   payload: array{id:string,date:string},
     *   request: UpdateEntryRequestInterface
     * }
     */
    public static function ac03DateOnly(): array
    {
        $row   = self::buildBaselineRowWithPastTimestamps();
        $rows  = [$row];

        /** @var array{id: string, date: string} $payload */
        $payload = [
            'id'   => $row['id'],
            'date' => '1999-12-01',
        ];

        $dataset = self::getDataset($rows, $payload);

        return $dataset;
    }

    /**
     * AC-04 (partial update: title+body) happy path dataset.
     *
     * @return array{
     *   rows: Rows,
     *   payload: array{id:string,title:string,body:string},
     *   request: UpdateEntryRequestInterface
     * }
     */
    public static function ac04TitleAndBody(): array
    {
        $row   = self::buildBaselineRowWithPastTimestamps();
        $rows  = [$row];

        /** @var array{id: string, title: string, body: string} $payload */
        $payload = [
            'id'    => $row['id'],
            'title' => 'Updated title',
            'body'  => 'Updated body',
        ];

        $dataset = self::getDataset($rows, $payload);

        return $dataset;
    }

    /**
     * AC-05 (missing id) dataset.
     *
     * @return array{
     *   rows: Rows,
     *   payload: array{id:string,title:string},
     *   request: UpdateEntryRequestInterface
     * }
     */
    public static function ac05MissingId(): array
    {
        $row   = self::buildBaselineRowWithPastTimestamps();
        $rows  = [$row];

        /** @var array{id: string, title: string} $payload */
        $payload = [
            'id'    => '',
            'title' => 'Updated title',
        ];

        $dataset = self::getDataset($rows, $payload);

        return $dataset;
    }

    /**
     * AC-06 (invalid id) dataset.
     *
     * @return array{
     *   rows: Rows,
     *   payload: array{id:string,title:string},
     *   request: UpdateEntryRequestInterface
     * }
     */
    public static function ac06InvalidId(): array
    {
        $row   = self::buildBaselineRowWithPastTimestamps();
        $rows  = [$row];

        /** @var array{id: string, title: string} $payload */
        $payload = [
            'id'    => 'not-a-uuid',
            'title' => 'Updated title',
        ];

        $dataset = self::getDataset($rows, $payload);

        return $dataset;
    }

    /**
     * AC-07 (not found) dataset — valid UUID that is absent in storage.
     *
     * @return array{
     *   rows: Rows,
     *   payload: array{id:string,title:string},
     *   request: UpdateEntryRequestInterface
     * }
     */
    public static function ac07NotFound(): array
    {
        $row   = self::buildBaselineRowWithPastTimestamps();
        $rows  = [$row];

        /** @var array{id: string, title: string} $payload */
        $payload = [
            'id'    => UuidGenerator::generate(),
            'title' => 'Updated title',
        ];

        $dataset = self::getDataset($rows, $payload);

        return $dataset;
    }

    /**
     * AC-08 (id only, no updatable fields) dataset.
     *
     * @return array{
     *   rows: Rows,
     *   payload: array{id:string},
     *   request: UpdateEntryRequestInterface
     * }
     */
    public static function ac08IdOnly(): array
    {
        $row   = self::buildBaselineRowWithPastTimestamps();
        $rows  = [$row];

        /** @var array{id: string} $payload */
        $payload = [
            'id' => UuidGenerator::generate(),
        ];

        $dataset = self::getDataset($rows, $payload);

        return $dataset;
    }

    /**
     * AC-09 (empty title after trimming) dataset.
     *
     * @return array{
     *   rows: Rows,
     *   payload: array{id:string,title:string},
     *   request: UpdateEntryRequestInterface
     * }
     */
    public static function ac09EmptyTitle(): array
    {
        $row   = self::buildBaselineRowWithPastTimestamps();
        $rows  = [$row];

        /** @var array{id: string, title: string} $payload */
        $payload = [
            'id'    => UuidGenerator::generate(),
            'title' => '   ',
        ];

        $dataset = self::getDataset($rows, $payload);

        return $dataset;
    }

    /**
     * AC-10 (too long title) dataset.
     *
     * @return array{
     *   rows: Rows,
     *   payload: array{id:string,title:string},
     *   request: UpdateEntryRequestInterface
     * }
     */
    public static function ac10TooLongTitle(): array
    {
        $row   = self::buildBaselineRowWithPastTimestamps();
        $rows  = [$row];

        $id      = UuidGenerator::generate();
        $length  = EntryConstraints::TITLE_MAX + 1;
        $title   = str_repeat('A', $length);

        /** @var array{id: string, title: string} $payload */
        $payload = [
            'id'    => $id,
            'title' => $title,
        ];

        $dataset = self::getDataset($rows, $payload);

        return $dataset;
    }

    /**
     * AC-11 (empty body after trimming) dataset.
     *
     * @return array{
     *   rows: Rows,
     *   payload: array{id:string,body:string},
     *   request: UpdateEntryRequestInterface
     * }
     */
    public static function ac11EmptyBody(): array
    {
        $row   = self::buildBaselineRowWithPastTimestamps();
        $rows  = [$row];

        /** @var array{id: string, body: string} $payload */
        $payload = [
            'id'   => UuidGenerator::generate(),
            'body' => '   ',
        ];

        $dataset = self::getDataset($rows, $payload);

        return $dataset;
    }

    /**
     * AC-12 (too long body) dataset.
     *
     * @return array{
     *   rows: Rows,
     *   payload: array{id:string,body:string},
     *   request: UpdateEntryRequestInterface
     * }
     */
    public static function ac12TooLongBody(): array
    {
        $row   = self::buildBaselineRowWithPastTimestamps();
        $rows  = [$row];

        $id      = UuidGenerator::generate();
        $length  = EntryConstraints::BODY_MAX + 1;
        $body    = str_repeat('A', $length);

        /** @var array{id: string, body: string} $payload */
        $payload = [
            'id'   => $id,
            'body' => $body,
        ];

        $dataset = self::getDataset($rows, $payload);

        return $dataset;
    }

    /**
     * AC-13 (invalid date) dataset (e.g., impossible calendar date).
     *
     * @return array{
     *   rows: Rows,
     *   payload: array{id:string,date:string},
     *   request: UpdateEntryRequestInterface
     * }
     */
    public static function ac13InvalidDate(): array
    {
        $row   = self::buildBaselineRowWithPastTimestamps();
        $rows  = [$row];

        /** @var array{id: string, date: string} $payload */
        $payload = [
            'id'   => UuidGenerator::generate(),
            'date' => '2025-02-30',
        ];

        $dataset = self::getDataset($rows, $payload);

        return $dataset;
    }

    /**
     * AC-14 (no-op) dataset: request equals stored values.
     *
     * @return array{
     *   rows: Rows,
     *   payload: array{id: string, title?: string, body?: string, date?: string},
     *   request: UpdateEntryRequestInterface
     * }
     */
    public static function ac14NoOp(): array
    {
        $row   = self::buildBaselineRowWithPastTimestamps();
        $rows  = [$row];

        $id      = $row['id'];
        $title   = $row['title'];
        $body    = $row['body'];
        $date    = $row['date'];

        $payload = [
            'id'    => $id,
            'title' => $title,
            'body'  => $body,
            'date'  => $date,
        ];

        $dataset = self::getDataset($rows, $payload);

        return $dataset;
    }

    /**
     * Build a canonical Entry row with past timestamps.
     *
     * Purpose:
     * Create a deterministic baseline where both createdAt and updatedAt are in the past,
     * ensuring any successful update performed “now” strictly increases updatedAt (BR-2).
     *
     * @param string $shiftSpec Relative modification spec for DateTimeImmutable::modify(), e.g., "-1 hour".
     * @return array{id:string,title:string,body:string,date:string,createdAt:string,updatedAt:string}
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

    /**
     * Construct a unified dataset shape and the corresponding UpdateEntryRequest DTO.
     *
     * Purpose:
     * DRY helper to pack prepared DB rows and request payload into a single
     * dataset used by tests, while also instantiating the request DTO.
     *
     * @template TPayload of PayloadBase
     * @param Rows $rows
     * @param TPayload $payload
     * @return array{
     *   rows: Rows,
     *   payload: TPayload,
     *   request: UpdateEntryRequestInterface
     * }
     */
    private static function getDataset(array $rows, array $payload): array
    {
        $request = UpdateEntryRequest::fromArray($payload);

        $dataset = [
            'rows'    => $rows,
            'payload' => $payload,
            'request' => $request,
        ];

        return $dataset;
    }
}
