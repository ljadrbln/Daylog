<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\UpdateEntry;

use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequest;
use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface;

/**
 * AC-3 (happy path — date): Given a valid id and a valid date,
 * when updating, then the system persists the new date and refreshes updatedAt.
 *
 * Purpose:
 *   Verify that UpdateEntry changes only the provided 'date' field and refreshes
 *   the 'updatedAt' timestamp while keeping 'createdAt' intact, using real wiring
 *   (Provider + SqlFactory) and a clean DB prepared by the base class.
 *
 * Mechanics:
 *   - Seed a single entry via EntryFixture and capture its timestamps and fields.
 *   - Build a request containing the same id and a new valid logical date (YYYY-MM-DD).
 *   - Execute the real use case obtained in BaseUpdateEntryIntegrationTest.
 *   - Assert: DB row has the new date, title/body are unchanged, and updatedAt changed.
 *
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry
 *
 * @group UC-UpdateEntry
 */
final class AC03_HappyPath_DateOnlyTest extends BaseUpdateEntryIntegrationTest
{
    /**
     * AC-03 Happy path (date only): persists new date and refreshes updatedAt.
     *
     * @return void
     */
    public function testHappyPathUpdatesDateAndRefreshesUpdatedAt(): void
    {
        // Arrange: insert one entry with past timestamps
        $actualData = $this->insertEntryWithPastTimestamps();

        // Build a request with a new valid date only
        $newDate = '2025-09-11';

        /** @var array<string,string> $payload */
        $payload = [
            'id'   => $actualData['id'],
            'date' => $newDate,
        ];

        /** @var UpdateEntryRequestInterface $request */
        $request = UpdateEntryRequest::fromArray($payload);

        // Act: execute the real use case
        $response = $this->useCase->execute($request);
        $entry    = $response->getEntry();

        // Assert: DB row changed as expected
        $this->assertSame($actualData['title'],     $entry->getTitle());
        $this->assertSame($actualData['body'],      $entry->getBody());
        $this->assertSame($newDate,                 $entry->getDate());
        $this->assertSame($actualData['createdAt'], $entry->getCreatedAt());

        $this->assertNotSame($actualData['updatedAt'], $entry->getUpdatedAt());
    }
}
