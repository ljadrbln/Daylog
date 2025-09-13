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
 * Provide focused builders for UC-4 scenarios (AC1–AC3).
 *
 * Mechanics:
 * - For AC-1 (happy path) caller provides an existing valid UUID.
 * - For AC-2 builds request with invalid id (non-UUID).
 * - For AC-3 builds request with valid but absent UUID.
 */
final class DeleteEntryTestRequestFactory
{
    /**
     * Build a happy-path request using given entry id.
     *
     * @param string $entryId Existing entry UUID.
     * @return DeleteEntryRequestInterface
     */
    public static function happy(string $entryId): DeleteEntryRequestInterface
    {
        $payload = ['id' => $entryId];
        $request = DeleteEntryRequest::fromArray($payload);

        return $request;
    }

    /**
     * Build request with an invalid id (non-UUID).
     *
     * @return DeleteEntryRequestInterface
     */
    public static function invalidId(): DeleteEntryRequestInterface
    {
        $payload = ['id' => 'not-a-uuid'];
        $request = DeleteEntryRequest::fromArray($payload);

        return $request;
    }

    /**
     * Build request with a valid but absent UUID.
     *
     * @return DeleteEntryRequestInterface
     */
    public static function notFound(): DeleteEntryRequestInterface
    {
        $uuid    = UuidGenerator::generate();
        $request = DeleteEntryRequest::fromArray(['id' => $uuid]);

        return $request;
    }
}
