<?php
declare(strict_types=1);

namespace Daylog\Tests\Support\Datasets\Entries;

use Daylog\Domain\Models\Entries\EntryConstraints;
use Daylog\Application\DTO\Entries\AddEntry\AddEntryRequest;
use Daylog\Application\DTO\Entries\AddEntry\AddEntryRequestInterface;
use Daylog\Tests\Support\Helper\EntryTestData;

/**
 * Centralized datasets for UC-1 AddEntry (AC-01…AC-08).
 *
 * Purpose:
 * Provide deterministic, uniform datasets for Unit / Integration / Functional tests.
 * Each AC method differs only by the request payload; shared scaffolding is DRY'ed
 * via a single helper getDataset() that constructs AddEntryRequest DTOs.
 *
 * Mechanics:
 * - No prior state is required for AddEntry, so we do not pass/return rows.
 * - Each AC builds a small payload (title/body/date).
 * - “Missing” variants reuse the corresponding “Empty” variants and unset the key
 *   to guarantee identical context aside from the absence of the field.
 *
 * Return shape:
 *   { payload: TPayload, request: AddEntryRequestInterface }
 *
 * @phpstan-type PayloadBase array{
 *   title?: string,
 *   body?: string,
 *   date?: string
 * }
 */
final class AddEntryDataset
{
    /**
     * AC-01 — Happy path.
     *
     * @return array{
     *   payload: array{title:string, body:string, date:string},
     *   request: AddEntryRequestInterface
     * }
     */
    public static function ac01HappyPath(): array
    {
        $payload = EntryTestData::getOne();
        $dataset = self::getDataset($payload);

        return $dataset;
    }

    /**
     * AC-02 — Empty title (after trimming).
     *
     * @return array{
     *   payload: array{title:string, body:string, date:string},
     *   request: AddEntryRequestInterface
     * }
     */
    public static function ac02EmptyTitle(): array
    {
        $payload = EntryTestData::getOne(title: '   ');
        $dataset = self::getDataset($payload);

        return $dataset;
    }

    /**
     * AC-02 — Missing title (delegates to Empty → unset).
     *
     * @return array{
     *   payload: array{body:string, date:string},
     *   request: AddEntryRequestInterface
     * }
     */
    public static function ac02MissingTitle(): array
    {
        $dataset = self::ac02EmptyTitle();

        $payload = $dataset['payload'];
        unset($payload['title']);

        $dataset = self::getDataset($payload);

        return $dataset;
    }

    /**
     * AC-03 — Title too long (TITLE_MAX + 1).
     *
     * @return array{
     *   payload: array{title:string, body:string, date:string},
     *   request: AddEntryRequestInterface
     * }
     */
    public static function ac03TooLongTitle(): array
    {
        $length = EntryConstraints::TITLE_MAX + 1;
        $title  = str_repeat('A', $length);

        $payload = EntryTestData::getOne(title: $title);
        $dataset = self::getDataset($payload);

        return $dataset;
    }

    /**
     * AC-04 — Empty body (after trimming).
     *
     * @return array{
     *   payload: array{title:string, body:string, date:string},
     *   request: AddEntryRequestInterface
     * }
     */
    public static function ac04EmptyBody(): array
    {
        $payload = EntryTestData::getOne(body: '   ');
        $dataset = self::getDataset($payload);

        return $dataset;
    }

    /**
     * AC-04 — Missing body (delegates to Empty → unset).
     *
     * @return array{
     *   payload: array{title:string, date:string},
     *   request: AddEntryRequestInterface
     * }
     */
    public static function ac04MissingBody(): array
    {
        $dataset = self::ac04EmptyBody();

        $payload = $dataset['payload'];
        unset($payload['body']);

        $dataset = self::getDataset($payload);

        return $dataset;
    }

    /**
     * AC-05 — Body too long (BODY_MAX + 1).
     *
     * @return array{
     *   payload: array{title:string, body:string, date:string},
     *   request: AddEntryRequestInterface
     * }
     */
    public static function ac05TooLongBody(): array
    {
        $length = EntryConstraints::BODY_MAX + 1;
        $body   = str_repeat('B', $length);

        $payload = EntryTestData::getOne(body: $body);
        $dataset = self::getDataset($payload);

        return $dataset;
    }

    /**
     * AC-06 — Empty date (after trimming).
     *
     * @return array{
     *   payload: array{title:string, body:string, date:string},
     *   request: AddEntryRequestInterface
     * }
     */
    public static function ac06EmptyDate(): array
    {
        $payload = EntryTestData::getOne(date: '   ');
        $dataset = self::getDataset($payload);

        return $dataset;
    }

    /**
     * AC-06 — Missing date (delegates to Empty → unset).
     *
     * @return array{
     *   payload: array{title:string, body:string},
     *   request: AddEntryRequestInterface
     * }
     */
    public static function ac06MissingDate(): array
    {
        $dataset = self::ac06EmptyDate();

        $payload = $dataset['payload'];
        unset($payload['date']);

        $dataset = self::getDataset($payload);

        return $dataset;
    }

    /**
     * AC-07 — Invalid date format (not strict YYYY-MM-DD).
     *
     * @return array{
     *   payload: array{title:string, body:string, date:string},
     *   request: AddEntryRequestInterface
     * }
     */
    public static function ac07InvalidDateFormat(): array
    {
        $payload = EntryTestData::getOne(date: '12/31/2024');
        $dataset = self::getDataset($payload);

        return $dataset;
    }

    /**
     * AC-08 — Invalid calendar date (e.g., 2025-02-30).
     *
     * @return array{
     *   payload: array{title:string, body:string, date:string},
     *   request: AddEntryRequestInterface
     * }
     */
    public static function ac08InvalidCalendarDate(): array
    {
        $payload = EntryTestData::getOne(date: '2025-02-30');
        $dataset = self::getDataset($payload);

        return $dataset;
    }

    /**
     * Helper to build final dataset shape.
     *
     * Removes id/createdAt/updatedAt before constructing AddEntryRequest.
     *
     * @phpstan-param array<string, mixed> $raw
     * @phpstan-return array{
     *   payload: array{title?: string, body?: string, date?: string},
     *   request: AddEntryRequestInterface
     * }
     */
    private static function getDataset(array $raw): array
    {
        unset($raw['id'], $raw['createdAt'], $raw['updatedAt']);
        $request = AddEntryRequest::fromArray($raw);

        $dataset = [
            'payload' => $raw,
            'request' => $request,
        ];

        return $dataset;
    }
}
