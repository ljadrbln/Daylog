<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\UpdateEntry;

use Daylog\Domain\Models\Entries\Entry;
use Daylog\Tests\Support\Factory\UpdateEntryTestRequestFactory;
use Daylog\Tests\Support\Helper\EntriesSeeding;
use Daylog\Tests\Support\Scenarios\Entries\UpdateEntryScenario;
use Daylog\Tests\Support\Assertion\UpdateEntryTitleOnlyAssertions;

/**
 * UC-5 / AC-01 — Happy path (title-only) — Unit.
 *
 * Purpose:
 * Verify that only the title changes, other fields are preserved,
 * and timestamps remain valid/monotonic in the response DTO.
 *
 * Mechanics:
 * - Build deterministic rows via UpdateEntryScenario::ac01TitleOnly();
 * - Seed a Fake repository through EntriesSeeding::intoFakeRepo();
 * - Build a title-only request via UpdateEntryTestRequestFactory;
 * - Execute the use case and assert with shared trait.
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
        $dataset  = UpdateEntryScenario::ac01TitleOnly();

        $rows     = $dataset['rows'];
        $targetId = $dataset['targetId'];
        $newTitle = $dataset['newTitle'];

        $repo = $this->makeRepo();
        EntriesSeeding::intoFakeRepo($repo, $rows);

        $request   = UpdateEntryTestRequestFactory::titleOnly($targetId, $newTitle);
        $validator = $this->makeValidatorOk();
        $useCase   = $this->makeUseCase($repo, $validator);

        // Act
        $response = $useCase->execute($request);
        $actualEntry = $response->getEntry();

        // Assert
        $expectedEntry = Entry::fromArray($rows[0]);
        $this->assertTitleOnlyUpdated($expectedEntry, $actualEntry, $newTitle);
    }
}