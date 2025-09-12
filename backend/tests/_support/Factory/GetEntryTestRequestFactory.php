<?php
declare(strict_types=1);

namespace Daylog\Tests\Support\Factory;

use Daylog\Application\DTO\Entries\GetEntry\GetEntryRequest;
use Daylog\Application\DTO\Entries\GetEntry\GetEntryRequestInterface;
use Daylog\Domain\Services\UuidGenerator;
use Daylog\Tests\Support\Helper\EntryTestData;

/**
 * Factory for building GetEntry test requests.
 *
 * Purpose:
 * Provide focused builders for UC-3 scenarios (AC1–AC3).
 *
 * Mechanics:
 * - Baseline comes from EntryTestData::getOne() to ensure a valid id.
 * - Overrides id for negative cases (invalid / not found).
 */
final class GetEntryTestRequestFactory
{
    /**
     * Build a happy-path request using fixture id.
     * 
     * @param string $entryId
     *
     * @return GetEntryRequestInterface
     */
    public static function happy($entryId): GetEntryRequestInterface
    {
        $payload = ['id' => $entryId];
        $request = GetEntryRequest::fromArray($payload);

        return $request;
    }

    /**
     * Build request with an invalid id (non-UUID).
     *
     * @return GetEntryRequestInterface
     */
    public static function invalidId(): GetEntryRequestInterface
    {
        $payload = ['id' => 'not-a-uuid'];
        $request = GetEntryRequest::fromArray($payload);

        return $request;
    }

    /**
     * Build request with a valid but absent UUID.
     *
     * @return GetEntryRequestInterface
     */
    public static function notFound(): GetEntryRequestInterface
    {
        $uuid = UuidGenerator::generate();
        $request = GetEntryRequest::fromArray(['id' => $uuid]);

        return $request;
    }
}
