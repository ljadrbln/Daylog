<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\DeleteEntry;

use Daylog\Tests\Support\Fixture\EntryFixture;
use Daylog\Tests\Support\Factory\DeleteEntryTestRequestFactory;
use Daylog\Tests\Support\Scenarios\Entries\DeleteEntryScenario;
use Daylog\Tests\Support\Helper\EntriesSeeding;
use Daylog\Tests\Support\Assertion\EntryValidationAssertions;

use Daylog\Tests\Support\Datasets\Entries\DeleteEntryDataset;


/**
 * AC-03 Not found: ensures that a valid UUID v4 which does not exist
 * leads to a domain-level "ENTRY_NOT_FOUND" error.
 *
 * Mechanics:
 * - Seed exactly one entry via EntryFixture to ensure the repository path is exercised.
 * - Build a request DTO with another (valid v4) UUID that is not present in DB.
 * - Execute the DeleteEntry use case.
 * - Expect DomainValidationException; verify the error list contains only "ENTRY_NOT_FOUND".
 *
 * Invariants:
 * - The repository is legitimately touched in this scenario and returns null for the missing id.
 * - The use case translates this to a domain error without side effects on other rows.
 * - DB row count remains unchanged after execution.
 *
 * @covers \Daylog\Application\UseCases\Entries\DeleteEntry
 *
 * @group UC-DeleteEntry
 */
final class AC03_NotFoundTest extends BaseDeleteEntryIntegrationTest
{
    use EntryValidationAssertions;

    /**
     * Verifies that a valid-but-absent UUID triggers "ENTRY_NOT_FOUND"
     * and does not delete existing rows.
     *
     * @return void
     */
    public function testValidAbsentUuidTriggersEntryNotFound(): void
    {
        // Arrange
        $dataset  = DeleteEntryDataset::ac03NotFound();
        $this->seedFromDataset($dataset);

        $rowsCountBefore = EntryFixture::countRows();

        // Expect
        $this->expectEntryNotFound();

        // Act
        $request = $dataset['request'];
        $this->useCase->execute($request);

        // Assert
        $rowsCountAfter = EntryFixture::countRows();
        $this->assertSame($rowsCountBefore, $rowsCountAfter);
    }
}