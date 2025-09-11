<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\UpdateEntry;

use Daylog\Domain\Models\Entries\Entry;
use Daylog\Tests\Support\Factory\UpdateEntryTestRequestFactory;
use Daylog\Tests\Support\Assertion\UpdateEntryDateOnlyAssertions;

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
    use UpdateEntryDateOnlyAssertions;

    /**
     * AC-03 Happy path (date only): persists new date and refreshes updatedAt.
     *
     * @return void
     */
    public function testHappyPathUpdatesDateAndRefreshesUpdatedAt(): void
    {
        // Arrange
        $data = $this->insertEntryWithPastTimestamps();
        $expectedEntry = Entry::fromArray($data);

        $id      = $data['id'];
        $newDate = '1999-12-01';

        /** @var \Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface $request */
        $request = UpdateEntryTestRequestFactory::dateOnly($id, $newDate);

        // Act
        $response = $this->useCase->execute($request);
        $actualEntry = $response->getEntry();

        // Assert
        $this->assertDateOnlyUpdated($expectedEntry, $actualEntry, $newDate);
    }
}