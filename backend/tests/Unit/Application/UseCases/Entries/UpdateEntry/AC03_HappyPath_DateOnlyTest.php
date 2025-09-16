<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\UpdateEntry;

use Daylog\Domain\Models\Entries\Entry;
use Daylog\Tests\Support\Helper\EntriesSeeding;
use Daylog\Tests\Support\Factory\UpdateEntryTestRequestFactory;
use Daylog\Tests\Support\Scenarios\Entries\UpdateEntryScenario;
use Daylog\Tests\Support\Assertion\UpdateEntryDateOnlyAssertions;

/**
 * UC-5 / AC-03 — Happy path (date-only) — Unit.
 *
 * Purpose:
 * Ensure that when only the date is provided with a valid id, the use case
 * updates the date, preserves other fields, refreshes updatedAt per BR-2,
 * and returns a response DTO holding a valid domain Entry snapshot.
 *
 * Mechanics:
 * - Build deterministic rows via UpdateEntryScenario::ac03DateOnly();
 * - Seed a Fake repository through EntriesSeeding::intoFakeRepo();
 * - Build a date-only request via UpdateEntryTestRequestFactory;
 * - Validator is expected to run exactly once (domain rules tested elsewhere).
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
        $dataset = UpdateEntryScenario::ac03DateOnly();
        $rows    = $dataset['rows'];
        $id      = $dataset['targetId'];
        $newDate = $dataset['newDate'];

        $repo = $this->makeRepo();
        EntriesSeeding::intoFakeRepo($repo, $rows);

        $request = UpdateEntryTestRequestFactory::dateOnly($id, $newDate);
        $validator = $this->makeValidatorOk();

        $useCase = $this->makeUseCase($repo, $validator);

        // Act
        $response = $useCase->execute($request);
        $actual   = $response->getEntry();

        // Assert
        $expected = Entry::fromArray($rows[0]);
        $this->assertDateOnlyUpdated($expected, $actual, $newDate);
    }
}
