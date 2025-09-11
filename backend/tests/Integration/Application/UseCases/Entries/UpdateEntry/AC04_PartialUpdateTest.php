<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\UpdateEntry;

use Daylog\Domain\Models\Entries\Entry;
use Daylog\Tests\Support\Factory\UpdateEntryTestRequestFactory;
use Daylog\Tests\Support\Assertion\UpdateEntryTitleAndBodyAssertions;

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
    use UpdateEntryTitleAndBodyAssertions;

    /**
     * AC-04: only provided fields (title+body) change; date remains intact.
     *
     * @return void
     */
    public function testPartialUpdateChangesOnlyProvidedFields(): void
    {
        // Arrange
        $data = $this->insertEntryWithPastTimestamps();
        $expected = Entry::fromArray($data);

        $id        = $data['id'];
        $newTitle  = 'Updated title';
        $newBody   = 'Updated body';

        /** @var \Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface $request */
        $request = UpdateEntryTestRequestFactory::titleAndBody($id, $newTitle, $newBody);

        // Act
        $response = $this->useCase->execute($request);
        $actual   = $response->getEntry();

        // Assert
        $this->assertTitleAndBodyUpdated($expected, $actual, $newTitle, $newBody);
    }
}
