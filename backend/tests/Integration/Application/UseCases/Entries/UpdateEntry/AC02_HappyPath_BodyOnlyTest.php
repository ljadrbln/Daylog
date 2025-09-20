<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\UpdateEntry;

use Daylog\Domain\Models\Entries\Entry;
use Daylog\Tests\Support\Assertion\UpdateEntryBodyOnlyAssertions;
use Daylog\Tests\Support\Datasets\Entries\UpdateEntryDataset;

/**
 * AC-2 (happy path — body): Given a valid id and a non-empty body within limits,
 * when updating, then the system persists the new body and refreshes updatedAt.
 *
 * Purpose:
 * Verify that UpdateEntry changes only the provided 'body' and refreshes 'updatedAt'
 * while keeping 'createdAt' intact, using real wiring (Provider + SqlFactory).
 *
 * Mechanics:
 * - Seed a single row via EntriesSeeding::intoDb() from UpdateEntryScenario;
 * - Build a body-only request for the seeded id;
 * - Execute the real use case (prepared in the base class);
 * - Assert via shared trait that only body changes and updatedAt increases.
 *
 * @covers \Daylog\Configuration\Providers\Entries\UpdateEntryProvider
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry
 * @group UC-UpdateEntry
 */
final class AC02_HappyPath_BodyOnlyTest extends BaseUpdateEntryIntegrationTest
{
    use UpdateEntryBodyOnlyAssertions;

    /**
     * AC-02 Happy path (body only): persists new body and refreshes updatedAt.
     *
     * @return void
     */
    public function testHappyPathUpdatesBodyAndRefreshesUpdatedAt(): void
    {
        // Arrange
        $dataset = UpdateEntryDataset::ac02BodyOnly();
        $this->seedFromDataset($dataset);

        // Act
        $request  = $dataset['request'];
        $response = $this->useCase->execute($request);

        // Assert
        $newBody = $dataset['payload']['body'];

        $expectedEntry = $dataset['rows'][0];
        $expectedEntry = Entry::fromArray($expectedEntry);
        $actualEntry   = $response->getEntry();

        $this->assertBodyOnlyUpdated($expectedEntry, $actualEntry, $newBody);
    }
}
