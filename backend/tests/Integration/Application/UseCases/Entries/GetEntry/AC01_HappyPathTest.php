<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\GetEntry;

use Daylog\Tests\Support\Fixture\EntryFixture;
use Daylog\Tests\Support\Factory\GetEntryTestRequestFactory;
use Daylog\Tests\Support\Scenarios\Entries\GetEntryScenario;
use Daylog\Tests\Support\Helper\EntriesSeeding;

/**
 * AC-01: Retrieving an existing entry succeeds (happy path).
 *
 * Purpose:
 *   Validate the main success scenario of UC-3: given a valid existing entry id,
 *   the system must return the corresponding domain Entry from persistent storage.
 *
 * Mechanics:
 *   - Prepare a clean DB state via the base test class;
 *   - Build a deterministic dataset using GetEntryScenario::ac01HappyPath();
 *   - Insert the row into the DB through EntriesSeeding::intoDb();
 *   - Construct a GetEntryRequest via the test factory;
 *   - Execute the use case against the real provider wiring.
 *
 * Assertions:
 *   - DB row count remains 1 after execution (read-only use case);
 *   - Response DTO carries an Entry whose id/title/body/date match the seeded row.
 *
 * @covers \Daylog\Configuration\Providers\Entries\GetEntryProvider
 * @covers \Daylog\Application\UseCases\Entries\GetEntry
 *
 * @group UC-GetEntry
 */
final class AC01_HappyPathTest extends BaseGetEntryIntegrationTest
{
    /**
     * AC-01: Happy path returns the seeded Entry by id.
     *
     * @return void
     */
    public function testHappyPathReturnsSeededEntry(): void
    {
        // Arrange
        $dataset  = GetEntryScenario::ac01HappyPath();
        $rows     = $dataset['rows'];
        $targetId = $dataset['targetId'];

        $request = GetEntryTestRequestFactory::happy($targetId);
        EntriesSeeding::intoDb($rows);

        // Act
        $response = $this->useCase->execute($request);
        $entry    = $response->getEntry();

        // Assert: response Entry matches seeded row
        $seeded      = $rows[0];
        $actualId    = $entry->getId();
        $actualTitle = $entry->getTitle();
        $actualBody  = $entry->getBody();
        $actualDate  = $entry->getDate();

        $this->assertSame($seeded['id'],    $actualId);
        $this->assertSame($seeded['title'], $actualTitle);
        $this->assertSame($seeded['body'],  $actualBody);
        $this->assertSame($seeded['date'],  $actualDate);
    }
}
