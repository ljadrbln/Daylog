<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\UpdateEntry;

use Daylog\Tests\Support\Factory\UpdateEntryTestRequestFactory;
use Daylog\Tests\Support\Assertion\EntryValidationAssertions;

/**
 * UC-5 / AC-08 — No fields to update.
 *
 * Purpose:
 *   Ensure that providing only an id without any updatable fields (title/body/date)
 *   fails early with DomainValidationException('NO_FIELDS_TO_UPDATE') before touching storage.
 *
 * Mechanics:
 *   - Generate a valid UUID v4 that may or may not exist — irrelevant for this validation branch.
 *   - Build a request via the shared test factory with only the id (no title/body/date).
 *   - Execute the real use case from the base integration class.
 *   - Expect DomainValidationException with the message 'NO_FIELDS_TO_UPDATE'.
 *
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry
 * @group UC-UpdateEntry
 */
final class AC08_NoFieldsTest extends BaseUpdateEntryIntegrationTest
{
    use EntryValidationAssertions;

    /**
     * AC-08: Only id provided → NO_FIELDS_TO_UPDATE.
     *
     * @return void
     */
    public function testNoFieldsToUpdateFailsValidationWithNoFieldsToUpdate(): void
    {
        // Arrange
        $request = UpdateEntryTestRequestFactory::idOnly();

        // Expect
        $this->expectNoFieldsToUpdate();

        // Act
        $this->useCase->execute($request);
    }
}
