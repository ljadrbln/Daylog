<?php
declare(strict_types=1);

namespace Daylog\Tests\Support\Factory;

use Daylog\Application\DTO\Entries\GetEntry\GetEntryRequest;
use Daylog\Application\DTO\Entries\GetEntry\GetEntryRequestInterface;
use Daylog\Domain\Services\UuidGenerator;

/**
 * Factory for building GetEntry test requests.
 *
 * Purpose:
 * Provide focused builders for UC-3 scenarios (AC1–AC3) with a single source of truth:
 * payload methods return arrays for transport/HTTP, while DTO methods wrap them via fromArray().
 *
 * Usage:
 * - Functional (HTTP): use *Payload() to send JSON (allows field omission if needed).
 * - Unit/Integration: use DTO methods (happy(), invalidId(), notFound()).
 */
final class GetEntryTestRequestFactory
{
    /**
     * Build the canonical happy-path payload.
     *
     * @param string $entryId
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
     * @param string $entryId
     * @return GetEntryRequestInterface
     */
    public static function happy(string $entryId): GetEntryRequestInterface
    {
        $payload = self::happyPayload($entryId);
        $request = GetEntryRequest::fromArray($payload);

        return $request;
    }

    /**
     * Build the payload with an invalid id (non-UUID).
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
     * @return GetEntryRequestInterface
     */
    public static function invalidId(): GetEntryRequestInterface
    {
        $payload = self::invalidIdPayload();
        $request = GetEntryRequest::fromArray($payload);

        return $request;
    }

    /**
     * Build the payload with a valid but absent UUID.
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
     * @return GetEntryRequestInterface
     */
    public static function notFound(): GetEntryRequestInterface
    {
        $payload = self::notFoundPayload();
        $request = GetEntryRequest::fromArray($payload);

        return $request;
    }
}
