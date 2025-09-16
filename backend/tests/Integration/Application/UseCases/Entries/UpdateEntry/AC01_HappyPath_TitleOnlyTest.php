<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\UpdateEntry;

use Daylog\Domain\Models\Entries\Entry;
use Daylog\Tests\Support\Factory\UpdateEntryTestRequestFactory;
use Daylog\Tests\Support\Helper\EntriesSeeding;
use Daylog\Tests\Support\Scenarios\Entries\UpdateEntryScenario;
use Daylog\Tests\Support\Assertion\UpdateEntryTitleOnlyAssertions;

/**
 * AC-01 (happy path — title): Given a valid id and a non-empty title within limits,
 * when updating, then the system persists the new title and refreshes updatedAt.
 *
 * Purpose:
 * Verify that UpdateEntry changes only the provided 'title' and refreshes 'updatedAt'
 * while keeping 'createdAt' intact, using real wiring (Provider + SqlFactory).
 *
 * Mechanics:
 * - Seed a single row via EntriesSeeding::intoDb() from UpdateEntryScenario;
 * - Build a title-only request for the seeded id;
 * - Execute the real use case (prepared in the base class);
 * - Assert via shared trait that only title changes and updatedAt increases.
 *
 * @covers \Daylog\Configuration\Providers\Entries\UpdateEntryProvider
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry
 * @group UC-UpdateEntry
 */
final class AC01_HappyPath_TitleOnlyTest extends BaseUpdateEntryIntegrationTest
{
    use UpdateEntryTitleOnlyAssertions;

    /**
     * AC-01 Happy path (title only): persists new title and refreshes updatedAt.
     *
     * @return void
     */
    public function testHappyPathUpdatesTitleAndRefreshesUpdatedAt(): void
    {
        // Arrange
        $dataset  = UpdateEntryScenario::ac01TitleOnly();
        $rows     = $dataset['rows'];
        $targetId = $dataset['targetId'];
        $newTitle = $dataset['newTitle'];

        $request = UpdateEntryTestRequestFactory::titleOnly($targetId, $newTitle);
        EntriesSeeding::intoDb($rows);

        // Act
        $response = $this->useCase->execute($request);
        $actual   = $response->getEntry();

        // Assert
        $expected = Entry::fromArray($rows[0]);
        $this->assertTitleOnlyUpdated($expected, $actual, $newTitle);
    }
}
