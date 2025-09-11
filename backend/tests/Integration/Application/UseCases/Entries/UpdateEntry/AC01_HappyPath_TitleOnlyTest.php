<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\UpdateEntry;

use Daylog\Domain\Models\Entries\Entry;
use Daylog\Tests\Support\Factory\UpdateEntryTestRequestFactory;
use Daylog\Tests\Support\Assertion\UpdateEntryTitleOnlyAssertions;

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
    use UpdateEntryTitleOnlyAssertions;

    /**
     * AC-01 Happy path (title only): persists new title and refreshes updatedAt.
     *
     * @return void
     */
    public function testHappyPathUpdatesTitleAndRefreshesUpdatedAt(): void
    {
        // Arrange
        $data = $this->insertEntryWithPastTimestamps();
        $expectedEntry = Entry::fromArray($data);

        $id       = $data['id'];
        $newTitle = 'Updated title';

        /** @var \Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface $request */
        $request = UpdateEntryTestRequestFactory::titleOnly($id, $newTitle);

        // Act
        $response = $this->useCase->execute($request);
        $actualEntry = $response->getEntry();

        // Assert
        $this->assertTitleOnlyUpdated($expectedEntry, $actualEntry, $newTitle);
    }
}