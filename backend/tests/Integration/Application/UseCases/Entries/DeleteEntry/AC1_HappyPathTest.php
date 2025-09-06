<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\DeleteEntry;

use Daylog\Presentation\Requests\Entries\DeleteEntry\DeleteEntryRequestFactory;
use Daylog\Presentation\Requests\Entries\DeleteEntry\DeleteEntryRequestInterface;
use Daylog\Tests\Support\Fixture\EntryFixture;

/**
 * AC-1: Given existing entry id, the system deletes the Entry.
 *
 * Purpose:
 *   Verify the happy path using real wiring (Provider + SqlFactory) and a clean DB
 *   prepared in the base class.
 *
 * Mechanics:
 *   - Seed a single row into the 'entries' table using a real DB connection.
 *   - Build a request DTO via the Presentation factory.
 *   - Execute the use case and assert the entry is deleted.
 *
 * Assertions:
 *   - DB row count goes from 1 → 0 after execution.
 *   - No other rows are affected.
 *
 * @covers \Daylog\Configuration\Providers\Entries\DeleteEntryProvider
 * @covers \Daylog\Application\UseCases\Entries\DeleteEntry
 *
 * @group UC-DeleteEntry
 */
final class AC1_HappyPathTest extends BaseDeleteEntryIntegrationTest
{
    /**
     * AC-1 Happy path: deletes the seeded Entry by id.
     *
     * @return void
     */
    public function testHappyPathDeletesSeededEntry(): void
    {
        // Arrange: seed one row
        $rows = EntryFixture::insertRows(1);
        $row  = $rows[0];

        // Sanity: exactly one row present
        $rowsCount = EntryFixture::countRows();
        $this->assertSame(1, $rowsCount);

        // Build request
        $payload = ['id' => $row['id']];

        /** @var DeleteEntryRequestInterface $request */
        $request = DeleteEntryRequestFactory::fromArray($payload);

        // Act
        $this->useCase->execute($request);

        // Assert: DB must be empty
        $rowsCountAfter = EntryFixture::countRows();
        $this->assertSame(0, $rowsCountAfter);
    }
}
