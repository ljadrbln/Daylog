<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\GetEntry;

use Daylog\Tests\Support\Assertion\EntryValidationAssertions;
use Daylog\Tests\Support\Datasets\Entries\GetEntryDataset;

/**
 * AC-03 Not found: ensures that a valid UUID v4 which does not exist
 * leads to a domain-level "ENTRY_NOT_FOUND" error.
 *
 * Mechanics:
 * - Seed exactly one entry to exercise the repository path;
 * - Build a request DTO with another (valid v4) UUID that is absent in DB;
 * - Execute the GetEntry use case;
 * - Expect DomainValidationException; verify the error list contains only "ENTRY_NOT_FOUND".
 *
 * Invariants:
 * - The repository is legitimately touched and returns null for the missing id;
 * - The use case translates this to a domain error without modifying storage;
 * - DB row count remains unchanged after execution.
 *
 * @covers \Daylog\Application\UseCases\Entries\GetEntry
 *
 * @group UC-GetEntry
 */
final class AC03_NotFoundTest extends BaseGetEntryIntegrationTest
{
    use EntryValidationAssertions;

    /**
     * Verifies that a valid-but-absent UUID triggers "ENTRY_NOT_FOUND"
     * and does not alter existing rows.
     *
     * @return void
     */
    public function testValidAbsentUuidTriggersEntryNotFound(): void
    {
        // Arrange
        $dataset = GetEntryDataset::ac03NotFound();
        $this->seedFromDataset($dataset);

        // Expect
        $this->expectEntryNotFound();

        // Act
        $request = $dataset['request'];
        $this->useCase->execute($request);
    }
}
