<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\UpdateEntry;

use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequest;
use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface;

/**
 * AC-4 (partial update): Given a valid id and any subset of title, body, date,
 * when updating, then only provided fields change; others remain intact.
 *
 * Purpose:
 *   Verify that UpdateEntry performs a selective merge: it updates exactly the
 *   fields present in the request and leaves unspecified fields untouched.
 *   The test uses real wiring (Provider + SqlFactory) and a clean DB prepared
 *   by the base class to ensure end-to-end behavior.
 *
 * Mechanics:
 *   - Seed a single entry via EntryFixture and capture its initial values.
 *   - Build a request with a subset of fields (e.g., title + date) and omit body.
 *   - Execute the real use case obtained in BaseUpdateEntryIntegrationTest.
 *   - Assert: DB row has new title/date, original body preserved, createdAt intact,
 *     and updatedAt refreshed.
 *
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry
 *
 * @group UC-UpdateEntry
 */
final class AC04_PartialUpdateTest extends BaseUpdateEntryIntegrationTest
{
    /**
     * AC-04 Partial update: only provided fields change, others remain intact.
     *
     * @return void
     */
    public function testPartialUpdateChangesOnlyProvidedFields(): void
    {
        // Arrange: insert one entry with past timestamps
        $actualData = $this->insertEntryWithPastTimestamps();

        // Build a request updating only a subset: title + date (omit body)
        $newTitle = 'Updated title';
        $newDate  = '2025-09-11';

        /** @var array<string,string> $payload */
        $payload = [
            'id'    => $actualData['id'],
            'title' => $newTitle,
            'date'  => $newDate,
        ];

        /** @var UpdateEntryRequestInterface $request */
        $request = UpdateEntryRequest::fromArray($payload);

        // Act: execute the real use case
        $response = $this->useCase->execute($request);
        $entry    = $response->getEntry();

        // Assert: only provided fields changed; others preserved
        $this->assertSame($newTitle,                 $entry->getTitle());
        $this->assertSame($actualData['body'],       $entry->getBody());
        $this->assertSame($newDate,                  $entry->getDate());
        $this->assertSame($actualData['createdAt'],  $entry->getCreatedAt());
        $this->assertNotSame($actualData['updatedAt'], $entry->getUpdatedAt());
    }
}
