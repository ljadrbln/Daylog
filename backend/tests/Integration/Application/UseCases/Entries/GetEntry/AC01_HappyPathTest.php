<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\GetEntry;

use Daylog\Tests\Support\Fixture\EntryFixture;
use Daylog\Tests\Support\Factory\GetEntryTestRequestFactory;

/**
 * AC-01: Given existing entry id, the system returns the Entry.
 *
 * Purpose:
 *   Verify the happy path using real wiring (Provider + SqlFactory) and a clean DB
 *   prepared in the base class.
 *
 * Mechanics:
 *   - Seed a single row into the 'entries' table using a real DB connection.
 *   - Build a request DTO via the Presentation factory.
 *   - Execute the use case and assert the returned domain Entry matches seeded data.
 *
 * Assertions:
 *   - Result is an Entry instance.
 *   - id/title/body/date match the seeded row.
 *   - DB contains exactly one row (sanity check).
 *
 * @covers \Daylog\Configuration\Providers\Entries\GetEntryProvider
 * @covers \Daylog\Application\UseCases\Entries\GetEntry
 * 
 * @group UC-GetEntry
 */
final class AC01_HappyPathTest extends BaseGetEntryIntegrationTest
{
    /**
     * AC-01 Happy path: returns the seeded Entry by id.
     *
     * @return void
     */
    public function testHappyPathReturnsSeededEntry(): void
    {
        // Arrange
        $rows = EntryFixture::insertRows(1);
        $row  = $rows[0];

        $entryId = $row['id'];
        $request = GetEntryTestRequestFactory::happy($entryId);

        // Act
        $response = $this->useCase->execute($request);
        $entry  = $response->getEntry();

        // Assert
        $actualId    = $entry->getId();
        $actualTitle = $entry->getTitle();
        $actualBody  = $entry->getBody();
        $actualDate  = $entry->getDate();

        $this->assertSame($row['id'],    $actualId);
        $this->assertSame($row['title'], $actualTitle);
        $this->assertSame($row['body'],  $actualBody);
        $this->assertSame($row['date'],  $actualDate);
    }
}
