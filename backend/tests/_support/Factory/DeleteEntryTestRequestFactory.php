<?php
declare(strict_types=1);

namespace Daylog\Tests\Support\Factory;

use Daylog\Application\DTO\Entries\DeleteEntry\DeleteEntryRequest;
use Daylog\Application\DTO\Entries\DeleteEntry\DeleteEntryRequestInterface;
use Daylog\Domain\Services\UuidGenerator;

/**
 * Factory for building DeleteEntry test requests.
 *
 * Purpose:
 * Provide focused builders for UC-4 scenarios (AC1–AC3) with a single source of truth:
 * payload methods return arrays for transport/HTTP, while DTO methods wrap them via fromArray().
 *
 * Usage:
 * - Functional (HTTP): use *Payload() to send JSON (allows field omission if needed).
 * - Unit/Integration: use DTO methods (happy(), invalidId(), notFound()).
 */
final class DeleteEntryTestRequestFactory
{
    /**
     * Build the canonical happy-path payload.
     *
     * Mechanics:
     * - Accepts an existing entry UUID and places it into the 'id' field.
     *
     * @param string $entryId Existing entry UUID.
     * @return array{id:string}
     */
    public static function happyPayload(string $entryId): array
    {
        $payload = ['id' => $entryId];

        return $payload;
    }

    /**
     * Build a happy-path request using the canonical payload.
     *
     * @param string $entryId Existing entry UUID.
     * @return DeleteEntryRequestInterface
     */
    public static function happy(string $entryId): DeleteEntryRequestInterface
    {
        $payload = self::happyPayload($entryId);
        $request = DeleteEntryRequest::fromArray($payload);

        return $request;
    }

    /**
     * Build the payload with an invalid id (non-UUID).
     *
     * Mechanics:
     * - Uses a clearly invalid token to trigger validation errors in tests.
     *
     * @return array{id:string}
     */
    public static function invalidIdPayload(): array
    {
        $id      = 'not-a-uuid';
        $payload = ['id' => $id];

        return $payload;
    }

    /**
     * Build request with an invalid id (non-UUID).
     *
     * @return DeleteEntryRequestInterface
     */
    public static function invalidId(): DeleteEntryRequestInterface
    {
        $payload = self::invalidIdPayload();
        $request = DeleteEntryRequest::fromArray($payload);

        return $request;
    }

    /**
     * Build the payload with a valid but absent UUID.
     *
     * Mechanics:
     * - Generates a fresh UUID via UuidGenerator to simulate "not found" cases.
     *
     * @return array{id:string}
     */
    public static function notFoundPayload(): array
    {
        $id      = UuidGenerator::generate();
        $payload = ['id' => $id];

        return $payload;
    }

    /**
     * Build request with a valid but absent UUID.
     *
     * @return DeleteEntryRequestInterface
     */
    public static function notFound(): DeleteEntryRequestInterface
    {
        $payload = self::notFoundPayload();
        $request = DeleteEntryRequest::fromArray($payload);

        return $request;
    }
}
