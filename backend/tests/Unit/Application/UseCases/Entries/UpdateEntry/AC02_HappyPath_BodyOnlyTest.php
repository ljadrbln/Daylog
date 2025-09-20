<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\UpdateEntry;

use Daylog\Domain\Models\Entries\Entry;
use Daylog\Tests\Support\Assertion\UpdateEntryBodyOnlyAssertions;
use Daylog\Tests\Support\Datasets\Entries\UpdateEntryDataset;

/**
 * UC-5 / AC-02 — Happy path (body-only) — Unit.
 *
 * Purpose:
 * Ensure that when only the body is provided with a valid id, the use case
 * updates the body, preserves other fields, refreshes updatedAt per BR-2,
 * and returns a response DTO holding a valid domain Entry snapshot.
 *
 * Mechanics:
 * - Build deterministic rows via UpdateEntryScenario::ac02BodyOnly();
 * - Seed a Fake repository through EntriesSeeding::intoFakeRepo();
 * - Build a body-only request via UpdateEntryTestRequestFactory;
 * - Validator is expected to run exactly once (domain rules tested elsewhere).
 *
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry::execute
 * @group UC-UpdateEntry
 */
final class AC02_HappyPath_BodyOnlyTest extends BaseUpdateEntryUnitTest
{
    use UpdateEntryBodyOnlyAssertions;

    /**
     * Validate body-only update behavior and response DTO integrity.
     *
     * @return void
     */
    public function testHappyPathUpdatesBodyOnlyAndReturnsResponseDto(): void
    {
        // Arrange
        $repo    = $this->makeRepo();
        $dataset = UpdateEntryDataset::ac02BodyOnly();
        $this->seedFromDataset($repo, $dataset);

        $validator = $this->makeValidatorOk();
        $useCase   = $this->makeUseCase($repo, $validator);

        // Act
        $request  = $dataset['request'];
        $response = $useCase->execute($request);
        
        // Assert
        $newBody = $dataset['payload']['body'];

        $expectedEntry = $dataset['rows'][0];
        $expectedEntry = Entry::fromArray($expectedEntry);
        $actualEntry   = $response->getEntry();

        $this->assertBodyOnlyUpdated($expectedEntry, $actualEntry, $newBody);
    }
}
