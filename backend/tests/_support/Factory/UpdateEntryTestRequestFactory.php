<?php
declare(strict_types=1);

namespace Daylog\Tests\Support\Factory;

use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequest;
use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface;
use Daylog\Domain\Models\Entries\EntryConstraints;
use Daylog\Domain\Services\UuidGenerator;

/**
 * Test factory for UpdateEntry requests.
 *
 * Purpose:
 * Build DTOs consistently for Unit/Integration AC-tests.
 */
final class UpdateEntryTestRequestFactory
{
    /**
     * Build title-only request.
     *
     * @param string $id UUID v4.
     * @param string $title New title.
     * @return UpdateEntryRequestInterface
     */
    public static function titleOnly(string $id, string $title): UpdateEntryRequestInterface
    {
        $payload = [
            'id'    => $id,
            'title' => $title,
        ];

        /** @var UpdateEntryRequestInterface $request */
        $request = UpdateEntryRequest::fromArray($payload);

        return $request;
    }

    /**
     * Build body-only UpdateEntry request.
     *
     * @param string $id   UUID v4 identifier.
     * @param string $body New body text.
     * @return UpdateEntryRequestInterface
     */
    public static function bodyOnly(string $id, string $body): UpdateEntryRequestInterface
    {
        $payload = [
            'id'   => $id,
            'body' => $body,
        ];

        /** @var UpdateEntryRequestInterface $request */
        $request = UpdateEntryRequest::fromArray($payload);

        return $request;
    }

    /**
     * Build date-only UpdateEntry request.
     *
     * @param string $id   UUID v4 identifier.
     * @param string $date Logical date in YYYY-MM-DD.
     * @return UpdateEntryRequestInterface
     */
    public static function dateOnly(string $id, string $date): UpdateEntryRequestInterface
    {
        $payload = [
            'id'   => $id,
            'date' => $date,
        ];

        /** @var UpdateEntryRequestInterface $request */
        $request = UpdateEntryRequest::fromArray($payload);

        return $request;
    }

    /**
     * Build partial UpdateEntry request: title + body (date omitted).
     *
     * @param string $id
     * @param string $title
     * @param string $body
     * @return UpdateEntryRequestInterface
     */
    public static function titleAndBody(string $id, string $title, string $body): UpdateEntryRequestInterface
    {
        $payload = [
            'id'    => $id,
            'title' => $title,
            'body'  => $body,
        ];

        /** @var UpdateEntryRequestInterface $request */
        $request = UpdateEntryRequest::fromArray($payload);

        return $request;
    }

    /**
     * Build invalid request: missing id (empty after trimming).
     *
     * @return UpdateEntryRequestInterface
     */
    public static function missingId(): UpdateEntryRequestInterface
    {
        $payload = [
            'id'    => '',
            'title' => 'Updated title', // arbitrary valid value
        ];

        /** @var UpdateEntryRequestInterface $request */
        $request = UpdateEntryRequest::fromArray($payload);

        return $request;
    }

    /**
     * Build invalid request: id only (no updatable fields).
     *
     * @return UpdateEntryRequestInterface
     */
    public static function idOnly(): UpdateEntryRequestInterface
    {
        $entryId = UuidGenerator::generate();

        $payload = [
            'id' => $entryId
        ];

        $request = UpdateEntryRequest::fromArray($payload);

        return $request;
    }    

    /**
     * Build invalid request: empty title (after trimming).
     * 
     * @return UpdateEntryRequestInterface
     */
    public static function emptyTitle(): UpdateEntryRequestInterface
    {
        $entryId = UuidGenerator::generate();
        $payload = [
            'id'    => $entryId,
            'title' => '   ', // becomes empty after trimming
        ];

        $request = UpdateEntryRequest::fromArray($payload);

        return $request;
    }    

    /**
     * Build invalid request: title too long (> 200 chars).
     *
     * @return UpdateEntryRequestInterface
     */
    public static function tooLongTitle(): UpdateEntryRequestInterface
    {
        $entryId   = UuidGenerator::generate();
        $length    = EntryConstraints::TITLE_MAX + 1;
        $longTitle = str_repeat('A', $length);

        $payload = [
            'id'    => $entryId,
            'title' => $longTitle,
        ];

        $request = UpdateEntryRequest::fromArray($payload);

        return $request;
    }

    /**
     * Build invalid request: body empty after trimming.
     *
     * @return UpdateEntryRequestInterface
     */
    public static function emptyBody(): UpdateEntryRequestInterface
    {
        $entryId  = UuidGenerator::generate();

        $payload = [
            'id'   => $entryId,
            'body' => '   ', // becomes empty after trimming
        ];

        $request = UpdateEntryRequest::fromArray($payload);

        return $request;
    }

    /**
     * Build invalid request: body too long (> 50000 chars).
     *
     * @return UpdateEntryRequestInterface
     */
    public static function tooLongBody(): UpdateEntryRequestInterface
    {
        $entryId  = UuidGenerator::generate();
        $length   = EntryConstraints::BODY_MAX + 1;
        $longBody = str_repeat('A', $length);

        $payload = [
            'id'   => $entryId,
            'body' => $longBody,
        ];

        $request = UpdateEntryRequest::fromArray($payload);

        return $request;
    }

    /**
     * Build invalid request: invalid date (malformed or impossible calendar date).
     *
     * @return UpdateEntryRequestInterface
     */
    public static function invalidDate(): UpdateEntryRequestInterface
    {
        $entryId = UuidGenerator::generate();

        $payload = [
            'id'   => $entryId,
            'date' => '2025-02-30', // invalid calendar date
        ];

        $request = UpdateEntryRequest::fromArray($payload);

        return $request;
    }

    /**
     * Build no-op UpdateEntry request: values identical to current entry.
     *
     * @param array{
     *  id: string, 
     *  title?: string, 
     *  body?: string, 
     *  date?: string
     * } $data Existing entry data (id, title, body, date).
     * @return UpdateEntryRequestInterface
     */
    public static function noOp(array $data): UpdateEntryRequestInterface
    {
        /** @var UpdateEntryRequestInterface $request */
        $request = UpdateEntryRequest::fromArray($data);

        return $request;
    }

    /**
     * Build invalid request: non-UUID id with valid title.
     *
     * @return UpdateEntryRequestInterface
     */
    public static function invalidId(): UpdateEntryRequestInterface
    {
        $payload = [
            'id'    => 'not-a-uuid',
            'title' => 'Updated title',
        ];

        $request = UpdateEntryRequest::fromArray($payload);

        return $request;
    }

    /**
     * Build invalid request: valid UUID not found in storage.
     *
     * @return UpdateEntryRequestInterface
     */
    public static function notFound(): UpdateEntryRequestInterface
    {
        $payload = [
            'id'    => UuidGenerator::generate(),
            'title' => 'Updated title',
        ];

        $request = UpdateEntryRequest::fromArray($payload);

        return $request;
    }
}
