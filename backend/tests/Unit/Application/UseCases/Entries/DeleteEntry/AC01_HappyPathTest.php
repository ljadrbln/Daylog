<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\DeleteEntry;

use Daylog\Domain\Models\Entries\Entry;
use Daylog\Tests\Support\Assertion\UpdateEntryTitleOnlyAssertions;
use Daylog\Tests\Support\Datasets\Entries\DeleteEntryDataset;

/**
 * UC-4 / AC-01 — Happy path — Unit.
 *
 * Purpose:
 *   Verify that DeleteEntry validates the request, removes the targeted entity from the repository,
 *   and returns a response DTO that echoes the same Entry id (UUID v4).
 *
 * Mechanics:
 *   - Build a deterministic dataset via DeleteEntryScenario::ac01HappyPath();
 *   - Seed a Fake repository with a single row;
 *   - Build a request DTO for the seeded id via the test factory;
 *   - Run the use case with a validator that succeeds exactly once;
 *   - Assert: the entity is no longer present and the response echoes the id.
 *
 * @covers \Daylog\Application\UseCases\Entries\DeleteEntry\DeleteEntry::execute
 * @group UC-DeleteEntry
 */
final class AC01_HappyPathTest extends BaseDeleteEntryUnitTest
{
    /**
     * AC-01: Deleting an existing entry removes it from the repository and echoes the same id in response DTO.
     *
     * @return void
     */
    public function testHappyPathDeletesEntryAndReturnsResponseDto(): void
    {
        // Arrange
        $repo    = $this->makeRepo();
        $dataset = DeleteEntryDataset::ac01ExistingId();
        $this->seedFromDataset($repo, $dataset);

        $validator = $this->makeValidatorOk();
        $useCase   = $this->makeUseCase($repo, $validator);
        
        // Act
        $request  = $dataset['request'];
        $response = $useCase->execute($request);        

        // Assert
        $targetId   = $dataset['payload']['id'];
        $foundAfter = $repo->findById($targetId);
        $this->assertNull($foundAfter);

        // Assert
        $entry    = $response->getEntry();
        $actualId = $entry->getId();

        $this->assertSame($targetId, $actualId);
    }
}