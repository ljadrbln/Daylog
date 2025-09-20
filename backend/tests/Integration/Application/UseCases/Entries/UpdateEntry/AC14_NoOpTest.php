<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\UpdateEntry;

use Daylog\Tests\Support\Assertion\EntryValidationAssertions;
use Daylog\Tests\Support\Datasets\Entries\UpdateEntryDataset;

/**
 * UC-5 / AC-14 — No-op update.
 *
 * Purpose:
 * Verify that UpdateEntry detects a no-op (all provided values are identical to
 * the stored ones) and raises DomainValidationException with NO_CHANGES_APPLIED.
 * The stored Entry must remain unchanged, including an intact updatedAt.
 *
 * Mechanics:
 * - Seed a single row via EntriesSeeding::intoDb() from UpdateEntryScenario;
 * - Build a no-op request containing the same id/title/body/date as stored;
 * - Execute the real use case (prepared in the base class);
 * - Expect NO_CHANGES_APPLIED via EntryValidationAssertions helper.
 *
 * @covers \Daylog\Configuration\Providers\Entries\UpdateEntryProvider
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
        $dataset = UpdateEntryDataset::ac14NoOp();
        $this->seedFromDataset($dataset);
        
        $request = $dataset['request'];
        
        // Expect
        $this->expectNoChangesApplied();

        // Act
        $this->useCase->execute($request);
    }
}
