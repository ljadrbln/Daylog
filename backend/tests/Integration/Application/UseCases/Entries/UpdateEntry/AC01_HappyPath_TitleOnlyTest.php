<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\UpdateEntry;

use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequest;
use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface;

/**
 * AC-1 (happy path — title): Given a valid id and a non-empty title within limits,
 * when updating, then the system persists the new title and refreshes updatedAt.
 *
 * Purpose:
 *   Verify that UpdateEntry changes only the provided 'title' field and refreshes
 *   the 'updatedAt' timestamp while keeping 'createdAt' intact, using real wiring
 *   (Provider + SqlFactory) and a clean DB prepared by the base class.
 *
 * Mechanics:
 *   - Seed a single entry via EntryFixture and capture its timestamps.
 *   - Build a request containing the same id and a new valid title.
 *   - Execute the real use case obtained in BaseUpdateEntryIntegrationTest.
 *   - Assert: DB row has the new title, body/date are unchanged, and updatedAt changed.
 *
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry
 *
 * @group UC-UpdateEntry
 */
final class AC01_HappyPath_TitleOnlyTest extends BaseUpdateEntryIntegrationTest
{
    /**
     * AC-01 Happy path (title only): persists new title and refreshes updatedAt.
     *
     * @return void
     */
    public function testHappyPathUpdatesTitleAndRefreshesUpdatedAt(): void
    {
        // Arrange: insert one entry with past timestamps
        $actualData = $this->insertEntryWithPastTimestamps();

        // Build a request with a new valid title only
        $newTitle = 'Updated title';

        /** @var array<string,string> $payload */
        $payload = [
            'id'    => $actualData['id'],
            'title' => $newTitle,
        ];

        /** @var UpdateEntryRequestInterface $request */
        $request = UpdateEntryRequest::fromArray($payload);

        // Act: execute the real use case
        usleep(1000);
        $response = $this->useCase->execute($request);
        $entry    = $response->getEntry();

        // Assert: DB row changed as expected
        $this->assertSame($newTitle,          $entry->getTitle());
        $this->assertSame($actualData['body'],      $entry->getBody());
        $this->assertSame($actualData['date'],      $entry->getDate());
        $this->assertSame($actualData['createdAt'], $entry->getCreatedAt());

        $this->assertNotSame($actualData['updatedAt'], $entry->getUpdatedAt());
    }
}
