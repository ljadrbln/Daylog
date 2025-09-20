<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\UpdateEntry;

use Daylog\Domain\Models\Entries\Entry;
use Daylog\Tests\Support\Assertion\UpdateEntryTitleAndBodyAssertions;
use Daylog\Tests\Support\Datasets\Entries\UpdateEntryDataset;

/**
 * UC-5 / AC-04 — Partial update (title+body) — Unit.
 *
 * Purpose:
 * Given a valid id and a subset of fields (title+body), only provided fields must change
 * while others remain intact. The updatedAt timestamp is refreshed per BR-2.
 *
 * Mechanics:
 * - Build deterministic rows via UpdateEntryScenario::ac04TitleAndBody();
 * - Seed a Fake repository through EntriesSeeding::intoFakeRepo();
 * - Build a request via UpdateEntryTestRequestFactory::titleAndBody();
 * - Execute the use case and assert via shared trait.
 *
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry::execute
 * @group UC-UpdateEntry
 */
final class AC04_PartialUpdateTest extends BaseUpdateEntryUnitTest
{
    use UpdateEntryTitleAndBodyAssertions;

    /**
     * Validate that only provided fields (title+body) change; date remains intact.
     *
     * @return void
     */
    public function testPartialUpdateChangesOnlyProvidedFields(): void
    {
        // Arrange
        $repo    = $this->makeRepo();
        $dataset = UpdateEntryDataset::ac04TitleAndBody();
        $this->seedFromDataset($repo, $dataset);

        $validator = $this->makeValidatorOk();
        $useCase   = $this->makeUseCase($repo, $validator);

        // Act
        $request  = $dataset['request'];
        $response = $useCase->execute($request);
        
        // Assert
        $newTitle = $dataset['payload']['title'];
        $newBody  = $dataset['payload']['body'];

        $expectedEntry = $dataset['rows'][0];
        $expectedEntry = Entry::fromArray($expectedEntry);
        $actualEntry   = $response->getEntry();

        $this->assertTitleAndBodyUpdated($expectedEntry, $actualEntry, $newTitle, $newBody);
    }
}