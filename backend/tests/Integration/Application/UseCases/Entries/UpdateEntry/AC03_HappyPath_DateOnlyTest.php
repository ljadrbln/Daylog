<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\UpdateEntry;

use Daylog\Domain\Models\Entries\Entry;
use Daylog\Tests\Support\Assertion\UpdateEntryDateOnlyAssertions;
use Daylog\Tests\Support\Datasets\Entries\UpdateEntryDataset;

/**
 * AC-3 (happy path — date): Given a valid id and a valid date,
 * when updating, then the system persists the new date and refreshes updatedAt.
 *
 * Purpose:
 * Verify that UpdateEntry changes only the provided 'date' and refreshes 'updatedAt'
 * while keeping 'createdAt' intact, using real wiring (Provider + SqlFactory).
 *
 * Mechanics:
 * - Seed a single row via EntriesSeeding::intoDb() from UpdateEntryScenario;
 * - Build a date-only request for the seeded id;
 * - Execute the real use case (prepared in the base class);
 * - Assert via shared trait that only date changes and updatedAt increases.
 *
 * @covers \Daylog\Configuration\Providers\Entries\UpdateEntryProvider
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry
 * @group UC-UpdateEntry
 */
final class AC03_HappyPath_DateOnlyTest extends BaseUpdateEntryIntegrationTest
{
    use UpdateEntryDateOnlyAssertions;

    /**
     * AC-03 Happy path (date only): persists new date and refreshes updatedAt.
     *
     * @return void
     */
    public function testHappyPathUpdatesDateAndRefreshesUpdatedAt(): void
    {
        // Arrange
        $dataset = UpdateEntryDataset::ac03DateOnly();
        $this->seedFromDataset($dataset);

        // Act
        $request  = $dataset['request'];
        $response = $this->useCase->execute($request);

        // Assert
        $newDate = $dataset['payload']['date'];

        $expectedEntry = $dataset['rows'][0];
        $expectedEntry = Entry::fromArray($expectedEntry);
        $actualEntry   = $response->getEntry();

        $this->assertDateOnlyUpdated($expectedEntry, $actualEntry, $newDate);
    }
}
