<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\DeleteEntry;

use Daylog\Application\DTO\Entries\DeleteEntry\DeleteEntryRequestInterface;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Presentation\Requests\Entries\DeleteEntryRequestFactory;

/**
 * AC-2: Invalid id: ensures that non-UUID input is rejected.
 *
 * Mechanics:
 * - Build payload with invalid id (not a valid UUID v4).
 * - Create request DTO via DeleteEntryRequestFactory.
 * - Execute use case, expecting DomainValidationException with error code `ID_INVALID`.
 *
 * Invariants:
 * - Repository must not be touched (validation blocks execution).
 * - Error codes list contains only `ID_INVALID`.
 *
 * @covers \Daylog\Application\UseCases\Entries\DeleteEntry
 *
 * @group UC-DeleteEntry
 */
final class AC2_InvalidIdTest extends BaseDeleteEntryIntegrationTest
{
    /**
     * Verifies that a non-UUID id triggers validation error `ID_INVALID`.
     *
     * @return void
     */
    public function testInvalidIdTriggersValidationError(): void
    {
        // Arrange
        $payload = ['id' => 'not-a-uuid'];

        /** @var DeleteEntryRequestInterface $request */
        $request = DeleteEntryRequestFactory::fromArray($payload);

        // Assert
        $this->expectException(DomainValidationException::class);
        $this->expectExceptionMessage('ID_INVALID');

        // Act
        $this->useCase->execute($request);
    }
}
