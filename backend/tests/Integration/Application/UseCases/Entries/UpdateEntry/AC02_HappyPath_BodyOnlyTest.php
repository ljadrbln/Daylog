<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\UpdateEntry;

use Daylog\Domain\Models\Entries\Entry;
use Daylog\Tests\Support\Factory\UpdateEntryTestRequestFactory;
use Daylog\Tests\Support\Helper\EntriesSeeding;
use Daylog\Tests\Support\Scenarios\Entries\UpdateEntryScenario;
use Daylog\Tests\Support\Assertion\UpdateEntryBodyOnlyAssertions;

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
        $dataset = UpdateEntryScenario::ac02BodyOnly();

        $rows    = $dataset['rows'];
        $id      = $dataset['targetId'];
        $newBody = $dataset['newBody'];

        $request = UpdateEntryTestRequestFactory::bodyOnly($id, $newBody);
        EntriesSeeding::intoDb($rows);

        // Act
        $response = $this->useCase->execute($request);
        $actual   = $response->getEntry();

        // Assert
        $expected = Entry::fromArray($rows[0]);
        $this->assertBodyOnlyUpdated($expected, $actual, $newBody);
    }
}
