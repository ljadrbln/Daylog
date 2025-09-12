<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\UpdateEntry;

use Daylog\Domain\Models\Entries\Entry;
use Daylog\Tests\Support\Factory\UpdateEntryTestRequestFactory;
use Daylog\Tests\Support\Assertion\UpdateEntryBodyOnlyAssertions;

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
 *   - Assert: DB row has the new body, body/date are unchanged, and updatedAt changed.
 *
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry
 * @group UC-UpdateEntry
 */
final class AC02_HappyPath_BodyOnlyTest extends BaseUpdateEntryIntegrationTest
{
    use UpdateEntryBodyOnlyAssertions;

    /**
     * AC-02 Happy path (body only): persists new body and refreshes updatedAt.
     *
     * @return void
     */
    public function testHappyPathUpdatesBodyAndRefreshesUpdatedAt(): void
    {
        // Arrange
        $data = $this->insertEntryWithPastTimestamps();
        $expected = Entry::fromArray($data);

        $id      = $data['id'];
        $newBody = 'Updated body';

        $request = UpdateEntryTestRequestFactory::bodyOnly($id, $newBody);

        // Act
        $response = $this->useCase->execute($request);
        $actual   = $response->getEntry();

        // Assert
        $this->assertBodyOnlyUpdated($expected, $actual, $newBody);
    }
}