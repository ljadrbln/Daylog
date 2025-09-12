<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\UpdateEntry;

use Daylog\Tests\Support\Factory\UpdateEntryTestRequestFactory;
use Daylog\Tests\Support\Assertion\EntryValidationAssertions;

/**
 * UC-5 / AC-14 — No-op update.
 *
 * Purpose:
 *   Verify that UpdateEntry detects a no-op update (all provided values
 *   equal to current stored values) and responds with DomainValidationException
 *   carrying NO_CHANGES_APPLIED. The Entry must remain unchanged in DB,
 *   including an intact updatedAt timestamp.
 *
 * Mechanics:
 *   - Seed a single entry via EntryFixture with known values.
 *   - Build a request with the same id, title, body, and date as stored.
 *   - Execute the real use case obtained in BaseUpdateEntryIntegrationTest.
 *   - Assert: DomainValidationException with NO_CHANGES_APPLIED, and updatedAt
 *     remains equal to the original timestamp.
 *
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry
 * @group UC-UpdateEntry
 */
final class AC14_NoOpTest extends BaseUpdateEntryIntegrationTest
{
    use EntryValidationAssertions;

    /**
     * AC-14: Identical values trigger NO_CHANGES_APPLIED.
     *
     * @return void
     */
    public function testNoOpUpdateReportsNoChangesApplied(): void
    {
        // Arrange
        $row = $this->insertEntryWithPastTimestamps();
        $request = UpdateEntryTestRequestFactory::noOp($row);

        // Expect
        $this->expectNoChangesApplied();

        // Act
        $this->useCase->execute($request);
    }
}
