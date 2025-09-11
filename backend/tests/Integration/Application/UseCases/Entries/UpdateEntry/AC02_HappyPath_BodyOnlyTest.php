<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\UpdateEntry;

use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequest;
use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface;

/**
 * AC-2 (happy path — body): Given a valid id and a non-empty body within limits,
 * when updating, then the system persists the new body and refreshes updatedAt.
 *
 * Purpose:
 *   Verify that UpdateEntry changes only the provided 'body' field and refreshes
 *   the 'updatedAt' timestamp while keeping 'createdAt' intact, using real wiring
 *   (Provider + SqlFactory) and a clean DB prepared by the base class.
 *
 * Mechanics:
 *   - Insert a single entry via BaseUpdateEntryIntegrationTest helper with past timestamps.
 *   - Build a request containing the same id and a new valid body.
 *   - Execute the real use case obtained in BaseUpdateEntryIntegrationTest.
 *   - Assert: DB row has the new body, title/date are unchanged, and updatedAt changed.
 *
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry
 * @group UC-UpdateEntry
 */
final class AC02_HappyPath_BodyOnlyTest extends BaseUpdateEntryIntegrationTest
{
    /**
     * AC-02 Happy path (body only): persists new body and refreshes updatedAt.
     *
     * @return void
     */
    public function testHappyPathUpdatesBodyAndRefreshesUpdatedAt(): void
    {
        // Arrange: insert one entry with past timestamps
        $actualData = $this->insertEntryWithPastTimestamps();

        // Build a request with a new valid body only
        $newBody = 'Updated body';

        /** @var array<string,string> $payload */
        $payload = [
            'id'   => $actualData['id'],
            'body' => $newBody,
        ];

        /** @var UpdateEntryRequestInterface $request */
        $request = UpdateEntryRequest::fromArray($payload);

        // Act: execute the real use case
        $response = $this->useCase->execute($request);
        $entry    = $response->getEntry();

        // Assert: DB row changed as expected
        $this->assertSame($actualData['title'],     $entry->getTitle());
        $this->assertSame($newBody,                 $entry->getBody());
        $this->assertSame($actualData['date'],      $entry->getDate());
        $this->assertSame($actualData['createdAt'], $entry->getCreatedAt());

        $this->assertNotSame($actualData['updatedAt'], $entry->getUpdatedAt());
    }
}
