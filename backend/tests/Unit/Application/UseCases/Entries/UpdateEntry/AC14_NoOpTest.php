<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\UpdateEntry;

use Daylog\Tests\Support\Helper\EntriesSeeding;
use Daylog\Tests\Support\Datasets\Entries\UpdateEntryDataset;
use Daylog\Tests\Support\Assertion\EntryValidationAssertions;

/**
 * UC-5 / AC-14 — No-op update — Unit.
 *
 * Purpose:
 *   When all provided values equal the stored ones, the use case must treat the update
 *   as a no-op and the validator must throw DomainValidationException with NO_CHANGES_APPLIED.
 *   The repository must remain untouched (no save attempts).
 *
 * Mechanics:
 *   - Build deterministic single-row dataset via UpdateEntryScenario::ac14NoOp();
 *   - Seed a Fake repository via EntriesSeeding::intoFakeRepo();
 *   - Build a no-op request with identical title/body/date;
 *   - Expect NO_CHANGES_APPLIED and verify repository had no writes.
 *
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry::execute
 * @group UC-UpdateEntry
 */
final class AC14_NoOpTest extends BaseUpdateEntryUnitTest
{
    use EntryValidationAssertions;

    /**
     * Verify that identical values cause validator to throw NO_CHANGES_APPLIED and repo remains untouched.
     *
     * @return void
     */
    public function testNoOpUpdateThrowsAndRepoUntouched(): void
    {
        // Arrange
        $repo    = $this->makeRepo();
        $dataset = UpdateEntryDataset::ac14NoOp();
        $this->seedFromDataset($repo, $dataset);

        $errorCode = 'NO_CHANGES_APPLIED';
        $validator = $this->makeValidatorThrows($errorCode);
        $useCase   = $this->makeUseCase($repo, $validator);

        // Expect
        $this->expectNoChangesApplied();

        // Act
        $request = $dataset['request'];
        $useCase->execute($request);

        // Assert
        $this->assertRepoUntouched($repo);
    }
}
