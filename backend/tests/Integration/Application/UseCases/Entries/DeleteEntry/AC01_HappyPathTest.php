<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\DeleteEntry;

use Daylog\Tests\Support\Fixture\EntryFixture;
use Daylog\Tests\Support\Factory\DeleteEntryTestRequestFactory;
use Daylog\Tests\Support\Scenarios\Entries\DeleteEntryScenario;
use Daylog\Tests\Support\Helper\EntriesSeeding;

/**
 * AC-01: Deleting an existing entry succeeds (happy path).
 *
 * Purpose:
 *   Validate the main success scenario of UC-4: given a valid existing entry id,
 *   the system must remove the corresponding row from persistent storage.
 *
 * Mechanics:
 *   - Prepare a clean DB state via the base test class;
 *   - Build a deterministic dataset using DeleteEntryScenario::ac01HappyPath();
 *   - Insert the row into the DB through EntriesSeeding::intoDb();
 *   - Construct a DeleteEntryRequest via the test factory;
 *   - Execute the use case against the real provider wiring.
 *
 * Assertions:
 *   - Row count decreases from 1 to 0 after execution;
 *   - Exactly one row is removed (no collateral deletions).
 *
 * @covers \Daylog\Configuration\Providers\Entries\DeleteEntryProvider
 * @covers \Daylog\Application\UseCases\Entries\DeleteEntry
 *
 * @group UC-DeleteEntry
 */
final class AC01_HappyPathTest extends BaseDeleteEntryIntegrationTest
{
    /**
     * AC-01: Happy path removes the seeded Entry by id.
     *
     * @return void
     */
    public function testHappyPathDeletesSeededEntry(): void
    {
        // Arrange
        $dataset  = DeleteEntryScenario::ac01HappyPath();
        $rows     = $dataset['rows'];
        $targetId = $dataset['targetId'];

        $request = DeleteEntryTestRequestFactory::happy($targetId);
        EntriesSeeding::intoDb($rows);

        $rowsCountBefore = EntryFixture::countRows();

        // Act
        $this->useCase->execute($request);

        // Assert
        $rowsCountAfter      = EntryFixture::countRows();
        $numberOfDeletedRows = $rowsCountBefore - $rowsCountAfter;

        $this->assertSame(1, $numberOfDeletedRows);
    }
}