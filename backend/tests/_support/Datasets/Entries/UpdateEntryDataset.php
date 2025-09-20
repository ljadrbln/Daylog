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
     *   payload: array{id: string, body: string},
     *   request: UpdateEntryRequestInterface
     * }
     */
    public static function ac02BodyOnly(): array
    {
        $row  = self::buildBaselineRowWithPastTimestamps();
        $rows = [$row];

        $payload = [
            'id'   => $row['id'],
            'body' => 'Updated body'
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
     *   payload: array{id: string, date: string},
     *   request: UpdateEntryRequestInterface
     * }
     */
    public static function ac03DateOnly(): array
    {
        $row  = self::buildBaselineRowWithPastTimestamps();
        $rows = [$row];

        $payload = [
            'id'   => $row['id'],
            'date' => '1999-12-01'
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
     *   payload: array{id: string, title: string, body: string},
     *   request: UpdateEntryRequestInterface
     * }
     */
    public static function ac04TitleAndBody(): array
    {
        $row  = self::buildBaselineRowWithPastTimestamps();
        $rows = [$row];

        $payload = [
            'id'    => $row['id'],
            'title' => 'Updated title',
            'body'  => 'Updated body'
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
     * AC-05 (missing id) dataset.
     *
     * Purpose:
     * Validate transport-level failure when "id" is missing/empty.
     * Used by functional/integration tests to ensure the contract returns
     * the correct HTTP status and semantic code (expected 422 ID_REQUIRED).
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
    public static function ac05MissingId(): array
    {
        $row  = self::buildBaselineRowWithPastTimestamps();
        $rows = [$row];

        // Build invalid payload with missing/empty id
        $payload = [
            'id'    => '',
            'title' => 'Updated title',
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
     * AC-06 (invalid id) dataset.
     *
     * Purpose:
     * Validate transport-level failure when "id" is not a valid UUID v4.
     * Used by functional/integration tests to ensure the contract returns
     * the correct HTTP status and semantic code (expected 422 ID_INVALID).
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
    public static function ac06InvalidId(): array
    {
        $row  = self::buildBaselineRowWithPastTimestamps();
        $rows = [$row];

        // Build invalid payload with non-UUID id
        $payload = [
            'id'    => 'not-a-uuid',
            'title' => 'Updated title',
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
     * AC-07 (not found) dataset.
     *
     * Purpose:
     * Validate behavior when a valid UUID is provided,
     * but no matching entry exists in storage.
     * Expected contract: 404 NOT_FOUND with ENTRY_NOT_FOUND code.
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
    public static function ac07NotFound(): array
    {
        // Build dataset with a baseline row but target id that doesn't exist
        $row  = self::buildBaselineRowWithPastTimestamps();
        $rows = [$row];

        $missingId = UuidGenerator::generate();

        $payload = [
            'id'    => $missingId,
            'title' => 'Updated title',
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
     * AC-08 (id only, no updatable fields) dataset.
     *
     * Purpose:
     * Validate transport-level failure when request contains only an "id"
     * without any mutable fields (title/body/date).
     * Expected contract: 422 UNPROCESSABLE_ENTITY with a specific error code
     * (e.g., NO_FIELDS_TO_UPDATE).
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
     *   payload: array{id: string},
     *   request: UpdateEntryRequestInterface
     * }
     */
    public static function ac08IdOnly(): array
    {
        $row  = self::buildBaselineRowWithPastTimestamps();
        $rows = [$row];

        $entryId = UuidGenerator::generate();

        $payload = [
            'id' => $entryId,
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
     * AC-09 (empty title after trimming) dataset.
     *
     * Purpose:
     * Validate domain-level failure when the provided "title"
     * becomes empty after trimming whitespace.
     * Expected contract: 422 UNPROCESSABLE_ENTITY with TITLE_REQUIRED code.
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
    public static function ac09EmptyTitle(): array
    {
        $row  = self::buildBaselineRowWithPastTimestamps();
        $rows = [$row];

        $entryId = UuidGenerator::generate();

        $payload = [
            'id'    => $entryId,
            'title' => '   ', // becomes empty after trim
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
     * AC-10 (too long title) dataset.
     *
     * Purpose:
     * Validate domain-level failure when the provided "title"
     * exceeds EntryConstraints::TITLE_MAX length.
     * Expected contract: 422 UNPROCESSABLE_ENTITY with TITLE_TOO_LONG code.
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
    public static function ac10TooLongTitle(): array
    {
        $row  = self::buildBaselineRowWithPastTimestamps();
        $rows = [$row];

        $entryId   = UuidGenerator::generate();
        $length    = EntryConstraints::TITLE_MAX + 1;
        $longTitle = str_repeat('A', $length);

        $payload = [
            'id'    => $entryId,
            'title' => $longTitle,
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
     * AC-11 (empty body after trimming) dataset.
     *
     * Purpose:
     * Validate domain-level failure when the provided "body"
     * becomes empty after trimming whitespace.
     * Expected contract: 422 UNPROCESSABLE_ENTITY with BODY_REQUIRED code.
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
     *   payload: array{id: string, body: string},
     *   request: UpdateEntryRequestInterface
     * }
     */
    public static function ac11EmptyBody(): array
    {
        $row  = self::buildBaselineRowWithPastTimestamps();
        $rows = [$row];

        $entryId = UuidGenerator::generate();

        $payload = [
            'id'   => $entryId,
            'body' => '   ', // becomes empty after trim
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
     * AC-12 (too long body) dataset.
     *
     * Purpose:
     * Validate domain-level failure when the provided "body"
     * exceeds EntryConstraints::BODY_MAX length.
     * Expected contract: 422 UNPROCESSABLE_ENTITY with BODY_TOO_LONG code.
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
     *   payload: array{id: string, body: string},
     *   request: UpdateEntryRequestInterface
     * }
     */
    public static function ac12TooLongBody(): array
    {
        $row  = self::buildBaselineRowWithPastTimestamps();
        $rows = [$row];

        $entryId  = UuidGenerator::generate();
        $length   = EntryConstraints::BODY_MAX + 1;
        $longBody = str_repeat('A', $length);

        $payload = [
            'id'   => $entryId,
            'body' => $longBody,
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
     * AC-13 (invalid date) dataset.
     *
     * Purpose:
     * Validate domain-level failure when the provided "date"
     * is syntactically correct but semantically invalid
     * (e.g., impossible calendar date like 2025-02-30).
     * Expected contract: 422 UNPROCESSABLE_ENTITY with DATE_INVALID code.
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
     *   payload: array{id: string, date: string},
     *   request: UpdateEntryRequestInterface
     * }
     */
    public static function ac13InvalidDate(): array
    {
        $row  = self::buildBaselineRowWithPastTimestamps();
        $rows = [$row];

        $entryId = UuidGenerator::generate();

        $payload = [
            'id'   => $entryId,
            'date' => '2025-02-30', // invalid date
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
     * - Use the same values from the row as payload/request.
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
     *   payload: array{id: string, title: string, body: string, date: string},
     *   request: UpdateEntryRequestInterface
     * }
     */
    public static function ac14NoOp(): array
    {
        $row  = self::buildBaselineRowWithPastTimestamps();
        $rows = [$row];

        $payload = [
            'id'    => $row['id'],
            'title' => $row['title'],
            'body'  => $row['body'],
            'date'  => $row['date'],
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
