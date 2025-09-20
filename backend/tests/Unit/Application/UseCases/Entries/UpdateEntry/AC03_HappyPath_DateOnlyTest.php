<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\UpdateEntry;

use Daylog\Domain\Models\Entries\Entry;
use Daylog\Tests\Support\Assertion\UpdateEntryDateOnlyAssertions;
use Daylog\Tests\Support\Datasets\Entries\UpdateEntryDataset;

/**
 * UC-5 / AC-03 — Happy path (date-only) — Unit.
 *
 * Purpose:
 * Ensure that when only the date is provided with a valid id, the use case
 * updates the date, preserves other fields, refreshes updatedAt per BR-2,
 * and returns a response DTO holding a valid domain Entry snapshot.
 *
 * Mechanics:
 * - Build deterministic dataset via UpdateEntryDataset::ac03DateOnly();
 * - Seed repository with dataset rows;
 * - Use request object directly from the dataset;
 *
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry::execute
 * @group UC-UpdateEntry
 */
final class AC03_HappyPath_DateOnlyTest extends BaseUpdateEntryUnitTest
{
    use UpdateEntryDateOnlyAssertions;

    /**
     * Validate date-only update behavior and response DTO integrity.
     *
     * @return void
     */
    public function testHappyPathUpdatesDateOnlyAndReturnsResponseDto(): void
    {
        // Arrange
        $repo    = $this->makeRepo();
        $dataset = UpdateEntryDataset::ac03DateOnly();
        $this->seedFromDataset($repo, $dataset);

        $validator = $this->makeValidatorOk();
        $useCase   = $this->makeUseCase($repo, $validator);

        // Act
        $request  = $dataset['request'];
        $response = $useCase->execute($request);
        
        // Assert
        $newDate = $dataset['payload']['date'];

        $expectedEntry = $dataset['rows'][0];
        $expectedEntry = Entry::fromArray($expectedEntry);
        $actualEntry   = $response->getEntry();

        $this->assertDateOnlyUpdated($expectedEntry, $actualEntry, $newDate);
    }
}
