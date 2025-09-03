<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\GetEntry;

use Daylog\Domain\Models\Entries\Entry;
use Daylog\Presentation\Requests\Entries\GetEntryRequestFactory;
use Daylog\Presentation\Requests\Entries\GetEntryRequestInterface;
use Daylog\Tests\Support\Fixture\EntryFixture;

/**
 * AC-1: Given existing entry id, the system returns the Entry.
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
final class AC1_HappyPathTest extends BaseGetEntryIntegrationTest
{
    /**
     * AC-1 Happy path: returns the seeded Entry by id.
     *
     * @return void
     */
    public function testHappyPathReturnsSeededEntry(): void
    {
        // Arrange: seed one row
        $rows = EntryFixture::insertRows(1);

        // Sanity: exactly one row present
        $rowsCount = EntryFixture::countRows();
        $this->assertSame(1, $rowsCount);

        // Build request
        $payload = ['id' => $rows[0]['id']];

        /** @var GetEntryRequestInterface $request */
        $request = GetEntryRequestFactory::fromArray($payload);

        // Act
        $result = $this->useCase->execute($request);

        // Assert
        $this->assertInstanceOf(Entry::class, $result);

        $actualId    = $result->getId();
        $actualTitle = $result->getTitle();
        $actualBody  = $result->getBody();
        $actualDate  = $result->getDate();

        $this->assertSame($id,    $actualId);
        $this->assertSame($title, $actualTitle);
        $this->assertSame($body,  $actualBody);
        $this->assertSame($date,  $actualDate);
    }
}
