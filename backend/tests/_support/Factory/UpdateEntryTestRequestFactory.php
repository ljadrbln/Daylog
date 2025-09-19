<?php
declare(strict_types=1);

namespace Daylog\Tests\Support\Factory;

use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequest;
use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface;
use Daylog\Domain\Models\Entries\EntryConstraints;
use Daylog\Domain\Services\UuidGenerator;

/**
 * Factory for building UpdateEntry test payloads and DTOs.
 *
 * Purpose:
 * Provide a single source of truth for UC-5 (UpdateEntry) inputs:
 * - *Payload() methods return associative arrays suitable for transport/HTTP (JSON);
 * - DTO methods wrap payloads via UpdateEntryRequest::fromArray().
 *
 * Mechanics:
 * - Each method prepares a clearly scoped payload (title/body/date subsets, invalid cases, etc.);
 * - DTO methods call their corresponding *Payload() method to avoid duplication;
 * - All literals are assigned to variables before use to keep code intent explicit.
 *
 * Usage:
 * - Functional tests (HTTP): use *Payload() for JSON bodies;
 * - Unit/Integration tests: use DTO builders (titleOnly(), bodyOnly(), ...).
 */
final class UpdateEntryTestRequestFactory
{
    /**
     * Build the canonical payload for "title only" update.
     *
     * @param string $id    UUID v4 identifier.
     * @param string $title New title value.
     * @return array{id:string,title:string}
     */
    public static function titleOnlyPayload(string $id, string $title): array
    {
        $payload = [
            'id'    => $id,
            'title' => $title,
        ];

        return $payload;
    }

    /**
     * Build the DTO for "title only" update using the canonical payload.
     *
     * @param string $id    UUID v4 identifier.
     * @param string $title New title value.
     * @return UpdateEntryRequestInterface
     */
    public static function titleOnly(string $id, string $title): UpdateEntryRequestInterface
    {
        $payload = self::titleOnlyPayload($id, $title);
        $request = UpdateEntryRequest::fromArray($payload);

        return $request;
    }

    /**
     * Build the canonical payload for "body only" update.
     *
     * @param string $id   UUID v4 identifier.
     * @param string $body New body text.
     * @return array{id:string,body:string}
     */
    public static function bodyOnlyPayload(string $id, string $body): array
    {
        $payload = [
            'id'   => $id,
            'body' => $body,
        ];

        return $payload;
    }

    /**
     * Build the DTO for "body only" update using the canonical payload.
     *
     * @param string $id   UUID v4 identifier.
     * @param string $body New body text.
     * @return UpdateEntryRequestInterface
     */
    public static function bodyOnly(string $id, string $body): UpdateEntryRequestInterface
    {
        $payload = self::bodyOnlyPayload($id, $body);
        $request = UpdateEntryRequest::fromArray($payload);

        return $request;
    }

    /**
     * Build the canonical payload for "date only" update.
     *
     * @param string $id   UUID v4 identifier.
     * @param string $date Logical date in YYYY-MM-DD.
     * @return array{id:string,date:string}
     */
    public static function dateOnlyPayload(string $id, string $date): array
    {
        $payload = [
            'id'   => $id,
            'date' => $date,
        ];

        return $payload;
    }

    /**
     * Build the DTO for "date only" update using the canonical payload.
     *
     * @param string $id   UUID v4 identifier.
     * @param string $date Logical date in YYYY-MM-DD.
     * @return UpdateEntryRequestInterface
     */
    public static function dateOnly(string $id, string $date): UpdateEntryRequestInterface
    {
        $payload = self::dateOnlyPayload($id, $date);
        $request = UpdateEntryRequest::fromArray($payload);

        return $request;
    }

    /**
     * Build the canonical payload for partial update: title + body (date omitted).
     *
     * @param string $id
     * @param string $title
     * @param string $body
     * @return array{id:string,title:string,body:string}
     */
    public static function titleAndBodyPayload(string $id, string $title, string $body): array
    {
        $payload = [
            'id'    => $id,
            'title' => $title,
            'body'  => $body,
        ];

        return $payload;
    }

    /**
     * Build the DTO for partial update: title + body (date omitted).
     *
     * @param string $id
     * @param string $title
     * @param string $body
     * @return UpdateEntryRequestInterface
     */
    public static function titleAndBody(string $id, string $title, string $body): UpdateEntryRequestInterface
    {
        $payload = self::titleAndBodyPayload($id, $title, $body);
        $request = UpdateEntryRequest::fromArray($payload);

        return $request;
    }

    /**
     * Build invalid payload: missing id (empty after trimming).
     *
     * @return array{id:string,title:string}
     */
    public static function missingIdPayload(): array
    {
        $id      = '';
        $title   = 'Updated title';
        $payload = [
            'id'    => $id,
            'title' => $title,
        ];

        return $payload;
    }

    /**
     * Build invalid DTO: missing id (empty after trimming).
     *
     * @return UpdateEntryRequestInterface
     */
    public static function missingId(): UpdateEntryRequestInterface
    {
        $payload = self::missingIdPayload();
        $request = UpdateEntryRequest::fromArray($payload);

        return $request;
    }

    /**
     * Build invalid payload: id only (no updatable fields).
     *
     * @return array{id:string}
     */
    public static function idOnlyPayload(): array
    {
        $entryId = UuidGenerator::generate();

        $payload = [
            'id' => $entryId,
        ];

        return $payload;
    }

    /**
     * Build invalid DTO: id only (no updatable fields).
     *
     * @return UpdateEntryRequestInterface
     */
    public static function idOnly(): UpdateEntryRequestInterface
    {
        $payload = self::idOnlyPayload();
        $request = UpdateEntryRequest::fromArray($payload);

        return $request;
    }

    /**
     * Build invalid payload: empty title (after trimming).
     *
     * @return array{id:string,title:string}
     */
    public static function emptyTitlePayload(): array
    {
        $entryId = UuidGenerator::generate();
        $title   = '   ';

        $payload = [
            'id'    => $entryId,
            'title' => $title,
        ];

        return $payload;
    }

    /**
     * Build invalid DTO: empty title (after trimming).
     *
     * @return UpdateEntryRequestInterface
     */
    public static function emptyTitle(): UpdateEntryRequestInterface
    {
        $payload = self::emptyTitlePayload();
        $request = UpdateEntryRequest::fromArray($payload);

        return $request;
    }

    /**
     * Build invalid payload: title too long (> TITLE_MAX).
     *
     * @return array{id:string,title:string}
     */
    public static function tooLongTitlePayload(): array
    {
        $entryId   = UuidGenerator::generate();
        $length    = EntryConstraints::TITLE_MAX + 1;
        $longTitle = str_repeat('A', $length);

        $payload = [
            'id'    => $entryId,
            'title' => $longTitle,
        ];

        return $payload;
    }

    /**
     * Build invalid DTO: title too long (> TITLE_MAX).
     *
     * @return UpdateEntryRequestInterface
     */
    public static function tooLongTitle(): UpdateEntryRequestInterface
    {
        $payload = self::tooLongTitlePayload();
        $request = UpdateEntryRequest::fromArray($payload);

        return $request;
    }

    /**
     * Build invalid payload: body empty after trimming.
     *
     * @return array{id:string,body:string}
     */
    public static function emptyBodyPayload(): array
    {
        $entryId = UuidGenerator::generate();
        $body    = '   ';

        $payload = [
            'id'   => $entryId,
            'body' => $body,
        ];

        return $payload;
    }

    /**
     * Build invalid DTO: body empty after trimming.
     *
     * @return UpdateEntryRequestInterface
     */
    public static function emptyBody(): UpdateEntryRequestInterface
    {
        $payload = self::emptyBodyPayload();
        $request = UpdateEntryRequest::fromArray($payload);

        return $request;
    }

    /**
     * Build invalid payload: body too long (> BODY_MAX).
     *
     * @return array{id:string,body:string}
     */
    public static function tooLongBodyPayload(): array
    {
        $entryId  = UuidGenerator::generate();
        $length   = EntryConstraints::BODY_MAX + 1;
        $longBody = str_repeat('A', $length);

        $payload = [
            'id'   => $entryId,
            'body' => $longBody,
        ];

        return $payload;
    }

    /**
     * Build invalid DTO: body too long (> BODY_MAX).
     *
     * @return UpdateEntryRequestInterface
     */
    public static function tooLongBody(): UpdateEntryRequestInterface
    {
        $payload = self::tooLongBodyPayload();
        $request = UpdateEntryRequest::fromArray($payload);

        return $request;
    }

    /**
     * Build invalid payload: invalid date (malformed or impossible calendar date).
     *
     * @return array{id:string,date:string}
     */
    public static function invalidDatePayload(): array
    {
        $entryId = UuidGenerator::generate();
        $date    = '2025-02-30';

        $payload = [
            'id'   => $entryId,
            'date' => $date,
        ];

        return $payload;
    }

    /**
     * Build invalid DTO: invalid date (malformed or impossible calendar date).
     *
     * @return UpdateEntryRequestInterface
     */
    public static function invalidDate(): UpdateEntryRequestInterface
    {
        $payload = self::invalidDatePayload();
        $request = UpdateEntryRequest::fromArray($payload);

        return $request;
    }

    /**
     * Build payload that results in a no-op (values identical to current entry).
     *
     * @param array{
     *   id:string,
     *   title?:string,
     *   body?:string,
     *   date?:string
     * } $data Existing entry data (id, title, body, date).
     * @return array{id:string,title?:string,body?:string,date?:string}
     */
    public static function noOpPayload(array $data): array
    {
        // Keep the given data as-is: the use case should detect no effective changes.
        $payload = $data;

        return $payload;
    }

    /**
     * Build DTO that results in a no-op (values identical to current entry).
     *
     * @param array{
     *   id:string,
     *   title?:string,
     *   body?:string,
     *   date?:string
     * } $data Existing entry data (id, title, body, date).
     * @return UpdateEntryRequestInterface
     */
    public static function noOp(array $data): UpdateEntryRequestInterface
    {
        $payload = self::noOpPayload($data);
        $request = UpdateEntryRequest::fromArray($payload);

        return $request;
    }

    /**
     * Build invalid payload: non-UUID id with a valid title.
     *
     * @return array{id:string,title:string}
     */
    public static function invalidIdPayload(): array
    {
        $id      = 'not-a-uuid';
        $title   = 'Updated title';
        $payload = [
            'id'    => $id,
            'title' => $title,
        ];

        return $payload;
    }

    /**
     * Build invalid DTO: non-UUID id with a valid title.
     *
     * @return UpdateEntryRequestInterface
     */
    public static function invalidId(): UpdateEntryRequestInterface
    {
        $payload = self::invalidIdPayload();
        $request = UpdateEntryRequest::fromArray($payload);

        return $request;
    }

    /**
     * Build payload: valid UUID that is not present in storage.
     *
     * @return array{id:string,title:string}
     */
    public static function notFoundPayload(): array
    {
        $id      = UuidGenerator::generate();
        $title   = 'Updated title';
        $payload = [
            'id'    => $id,
            'title' => $title,
        ];

        return $payload;
    }

    /**
     * Build DTO: valid UUID that is not present in storage.
     *
     * @return UpdateEntryRequestInterface
     */
    public static function notFound(): UpdateEntryRequestInterface
    {
        $payload = self::notFoundPayload();
        $request = UpdateEntryRequest::fromArray($payload);

        return $request;
    }
}
