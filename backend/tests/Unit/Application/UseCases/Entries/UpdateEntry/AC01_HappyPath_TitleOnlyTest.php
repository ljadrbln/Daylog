<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\UpdateEntry;

use Daylog\Domain\Models\Entries\Entry;
use Daylog\Tests\Support\Assertion\UpdateEntryTitleOnlyAssertions;
use Daylog\Tests\Support\Datasets\Entries\UpdateEntryDataset;

/**
 * UC-5 / AC-01 — Happy path (title-only) — Unit.
 *
 * Purpose:
 * Verify that only the title changes, other fields are preserved,
 * and timestamps remain valid/monotonic in the response DTO.
 *
 * Mechanics:
 * - Build deterministic dataset via UpdateEntryDataset::ac01TitleOnly();
 * - Seed repository with dataset rows;
 * - Use request object directly from the dataset;
 *
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry::execute
 * @group UC-UpdateEntry
 */
final class AC01_HappyPath_TitleOnlyTest extends BaseUpdateEntryUnitTest
{
    use UpdateEntryTitleOnlyAssertions;

    /**
     * Validate title-only update behavior and response DTO integrity.
     *
     * @return void
     */
    public function testHappyPathUpdatesTitleOnlyAndReturnsResponseDto(): void
    {
        // Arrange
        $repo    = $this->makeRepo();
        $dataset = UpdateEntryDataset::ac01TitleOnly();
        $this->seedFromDataset($repo, $dataset);

        $validator = $this->makeValidatorOk();
        $useCase   = $this->makeUseCase($repo, $validator);

        // Act
        $request  = $dataset['request'];
        $response = $useCase->execute($request);
        
        // Assert
        $newTitle = $dataset['payload']['title'];

        $expectedEntry = $dataset['rows'][0];
        $expectedEntry = Entry::fromArray($expectedEntry);
        $actualEntry   = $response->getEntry();

        $this->assertTitleOnlyUpdated($expectedEntry, $actualEntry, $newTitle);
    }
}