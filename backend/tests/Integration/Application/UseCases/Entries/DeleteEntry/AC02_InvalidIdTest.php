<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\DeleteEntry;

use Daylog\Tests\Support\Assertion\EntryValidationAssertions;
use Daylog\Tests\Support\Datasets\Entries\DeleteEntryDataset;

/**
 * AC-02 Invalid id: ensures that non-UUID input is rejected.
 *
 * Mechanics:
 * - Build payload with invalid id (not a valid UUID v4).
 * - Create request DTO via DeleteEntryTestRequestFactory.
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
final class AC02_InvalidIdTest extends BaseDeleteEntryIntegrationTest
{
    use EntryValidationAssertions;

    /**
     * Verifies that a non-UUID id triggers validation error `ID_INVALID`.
     *
     * @return void
     */
    public function testInvalidIdTriggersValidationError(): void
    {
        // Arrange
        $dataset = DeleteEntryDataset::ac02InvalidId();

        // Assert
        $this->expectIdInvalid();

        // Act
        $request = $dataset['request'];
        $this->useCase->execute($request);
    }
}
