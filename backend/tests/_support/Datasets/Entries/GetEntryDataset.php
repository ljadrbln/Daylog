<?php
declare(strict_types=1);

namespace Daylog\Tests\Support\Datasets\Entries;

use DateTimeImmutable;
use Daylog\Domain\Services\Clock;
use Daylog\Domain\Services\UuidGenerator;
use Daylog\Tests\Support\Helper\EntryTestData;
use Daylog\Application\DTO\Entries\GetEntry\GetEntryRequest;
use Daylog\Application\DTO\Entries\GetEntry\GetEntryRequestInterface;

/**
 * Centralized datasets for UC-3 GetEntry (AC-01…AC-04).
 *
 * Purpose:
 * Provide deterministic, uniform datasets for Unit / Integration / Functional tests
 * of the GetEntry use case. Each AC method prepares initial storage rows and a
 * request payload (id only), then builds a GetEntryRequest DTO to keep tests DRY.
 *
 * Mechanics:
 * - Baseline row has createdAt/updatedAt shifted into the past to resemble real data;
 * - Happy path (AC-01) seeds a single row and requests its id for retrieval;
 * - Negative paths cover: invalid UUID, valid-but-absent id (not found), and empty id;
 * - All ACs return the same shape: { rows, payload, request } to simplify test wiring.
 *
 * @phpstan-type Row array{
 *   id:string, title:string, body:string, date:string, createdAt:string, updatedAt:string
 * }
 * @phpstan-type Rows array<int, Row>
 * @phpstan-type Payload array{id:string}
 */
final class GetEntryDataset
{
    /**
     * AC-01 — Happy path (existing id).
     *
     * Returns a dataset where storage contains exactly one entry, and the payload id
     * matches that entry. Intended to assert successful fetching and proper response.
     *
     * @return array{
     *   rows: Rows,
     *   payload: Payload,
     *   request: GetEntryRequestInterface
     * }
     */
    public static function ac01ExistingId(): array
    {
        $row  = self::buildBaselineRowWithPastTimestamps();
        $rows = [$row];

        /** @var Payload $payload */
        $payload = [
            'id' => $row['id'],
        ];

        $dataset = self::getDataset($rows, $payload);

        return $dataset;
    }

    /**
     * AC-02 — Invalid id (malformed UUID).
     *
     * Returns a dataset with a valid stored row, but the request payload contains
     * a non-UUID string. Intended to assert transport/domain validation.
     *
     * @return array{
     *   rows: Rows,
     *   payload: Payload,
     *   request: GetEntryRequestInterface
     * }
     */
    public static function ac02InvalidId(): array
    {
        $row  = self::buildBaselineRowWithPastTimestamps();
        $rows = [$row];

        /** @var Payload $payload */
        $payload = [
            'id' => 'not-a-uuid',
        ];

        $dataset = self::getDataset($rows, $payload);

        return $dataset;
    }

    /**
     * AC-03 — Not found (valid UUID absent in storage).
     *
     * Returns a dataset with one stored row, while the request payload contains a
     * different (valid) UUID that does not exist in storage. Intended to assert
     * "not found" flow and response mapping.
     *
     * @return array{
     *   rows: Rows,
     *   payload: Payload,
     *   request: GetEntryRequestInterface
     * }
     */
    public static function ac03NotFound(): array
    {
        $row  = self::buildBaselineRowWithPastTimestamps();
        $rows = [$row];

        $id = UuidGenerator::generate();

        /** @var Payload $payload */
        $payload = [
            'id' => $id,
        ];

        $dataset = self::getDataset($rows, $payload);

        return $dataset;
    }

    /**
     * AC-04 — Empty id.
     *
     * Returns a dataset with a valid stored row, but the request payload contains
     * an empty id. Intended to assert transport validation for missing identifier.
     *
     * @return array{
     *   rows: Rows,
     *   payload: Payload,
     *   request: GetEntryRequestInterface
     * }
     */
    public static function ac04EmptyId(): array
    {
        $row  = self::buildBaselineRowWithPastTimestamps();
        $rows = [$row];

        /** @var Payload $payload */
        $payload = [
            'id' => '',
        ];

        $dataset = self::getDataset($rows, $payload);

        return $dataset;
    }

    /**
     * Build a canonical Entry row with past timestamps.
     *
     * Purpose:
     * Create a deterministic baseline where timestamps look realistic but are
     * strictly in the past. Realistic rows help reuse fixtures consistently
     * across use cases and layers.
     *
     * @param string $shiftSpec Relative modification spec for DateTimeImmutable::modify(), e.g., "-1 hour".
     * @return Row
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
     * Pack prepared rows and request payload into a unified dataset and create the DTO.
     *
     * Purpose:
     * DRY helper used by AC methods to ensure identical dataset shape and to
     * instantiate the GetEntryRequest DTO from the provided payload.
     *
     * @param Rows $rows
     * @param Payload $payload
     * @return array{
     *   rows: Rows,
     *   payload: Payload,
     *   request: GetEntryRequestInterface
     * }
     */
    private static function getDataset(array $rows, array $payload): array
    {
        $request = GetEntryRequest::fromArray($payload);

        $dataset = [
            'rows'    => $rows,
            'payload' => $payload,
            'request' => $request,
        ];

        return $dataset;
    }
}
