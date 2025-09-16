<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\UpdateEntry;

use Daylog\Domain\Models\Entries\Entry;
use Daylog\Tests\Support\Helper\EntriesSeeding;
use Daylog\Tests\Support\Factory\UpdateEntryTestRequestFactory;
use Daylog\Tests\Support\Scenarios\Entries\UpdateEntryScenario;
use Daylog\Tests\Support\Assertion\UpdateEntryTitleAndBodyAssertions;

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
        $dataset  = UpdateEntryScenario::ac04TitleAndBody();

        $rows     = $dataset['rows'];
        $targetId = $dataset['targetId'];
        $newTitle = $dataset['newTitle'];
        $newBody  = $dataset['newBody'];

        $repo = $this->makeRepo();
        EntriesSeeding::intoFakeRepo($repo, $rows);

        $request   = UpdateEntryTestRequestFactory::titleAndBody($targetId, $newTitle, $newBody);
        $validator = $this->makeValidatorOk();
        $useCase   = $this->makeUseCase($repo, $validator);

        // Act
        $response = $useCase->execute($request);
        $actual   = $response->getEntry();

        // Assert
        $expected = Entry::fromArray($rows[0]);
        $this->assertTitleAndBodyUpdated($expected, $actual, $newTitle, $newBody);
    }
}
