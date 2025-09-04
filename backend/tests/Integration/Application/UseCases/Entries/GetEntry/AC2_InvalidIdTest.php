<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\GetEntry;

use Daylog\Application\DTO\Entries\GetEntry\GetEntryRequestInterface;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Presentation\Requests\Entries\GetEntryRequestFactory;

/**
 * AC-2 Invalid id: ensures that non-UUID input is rejected.
 *
 * Mechanics:
 * - Build payload with invalid id (not a valid UUID v4).
 * - Create request DTO via GetEntryRequestFactory.
 * - Execute use case, expecting DomainValidationException with error code `ID_INVALID`.
 *
 * Invariants:
 * - Repository must not be touched (validation blocks execution).
 * - Error codes list contains only `ID_INVALID`.
 *
 * @covers \Daylog\Application\UseCases\Entries\GetEntry
 */
final class AC2_InvalidIdTest extends BaseGetEntryIntegrationTest
{
    /**
     * Verifies that a non-UUID id triggers validation error `ID_INVALID`.
     *
     * @return void
     */
    public function testInvalidIdTriggersValidationError(): void
    {
        // Arrange: non-UUID string
        $payload = ['id' => 'not-a-uuid'];

        /** @var GetEntryRequestInterface $request */
        $request = GetEntryRequestFactory::fromArray($payload);

        // Assert: expect DomainValidationException with ID_INVALID
        $this->expectException(DomainValidationException::class);

        $this->useCase->execute($request);        
    }
}
