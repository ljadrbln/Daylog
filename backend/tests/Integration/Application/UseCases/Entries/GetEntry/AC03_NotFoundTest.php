<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\GetEntry;

use Daylog\Tests\Support\Assertion\EntryValidationAssertions;
use Daylog\Tests\Support\Factory\GetEntryTestRequestFactory;

/**
 * AC-03 Not found: ensures that a valid UUID v4 which does not exist
 * leads to a domain-level "ENTRY_NOT_FOUND" error.
 *
 * Mechanics:
 * - Build a request DTO with another (valid v4) UUID that is not present in DB.
 * - Execute the GetEntry use case.
 * - Expect DomainValidationException; verify the error list contains only "ENTRY_NOT_FOUND".
 *
 * Notes:
 * - The repository is legitimately touched in this scenario (unlike AC-2),
 *   but it returns null for the missing id; the use case translates this to a domain error.
 *
 * @covers \Daylog\Application\UseCases\Entries\GetEntry
 *
 * @group UC-GetEntry
 */
final class AC03_NotFoundTest extends BaseGetEntryIntegrationTest
{
    use EntryValidationAssertions;

    /**
     * Verifies that a valid-but-absent UUID triggers "ENTRY_NOT_FOUND".
     *
     * @return void
     */
    public function testValidAbsentUuidTriggersEntryNotFound(): void
    {
        // Arrange
        $request = GetEntryTestRequestFactory::notFound();

        // Expect
        $this->expectEntryNotFound();

        // Act
        $this->useCase->execute($request);
    }
}
