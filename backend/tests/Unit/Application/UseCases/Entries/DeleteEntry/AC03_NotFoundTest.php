<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\DeleteEntry;

use Daylog\Tests\Support\Assertion\EntryValidationAssertions;
use Daylog\Tests\Support\Datasets\Entries\DeleteEntryDataset;

/**
 * UC-4 / AC-03 — Not found — Unit.
 *
 * Purpose:
 * Ensure that when the requested id does not exist in the repository,
 * the use case fails with ENTRY_NOT_FOUND and does not perform persistence.
 *
 * Mechanics:
 * - Build request with a fresh UUID v4 (via factory).
 * - Do not seed the repository so findById() returns null.
 * - Validator is expected to run exactly once and succeed (failure is not from validator).
 * - Expect DomainValidationException('ENTRY_NOT_FOUND') from the use case.
 *
 * @covers \Daylog\Application\UseCases\Entries\DeleteEntry\DeleteEntry::execute
 * @group UC-DeleteEntry
 */
final class AC03_NotFoundTest extends BaseDeleteEntryUnitTest
{
    use EntryValidationAssertions;

    /**
     * Missing entry must result in ENTRY_NOT_FOUND and no repository writes.
     *
     * @return void
     */
    public function testAbsentIdYieldsEntryNotFound(): void
    {
        // Arrange
        $dataset   = DeleteEntryDataset::ac03NotFound();
        $request   = $dataset['request'];

        $validator = $this->makeValidatorOk();
        $repo      = $this->makeRepo();

        // Expect
        $this->expectEntryNotFound();

        // Act
        $useCase = $this->makeUseCase($repo, $validator);
        $useCase->execute($request);

        // Assert
        $this->assertRepoUntouched($repo);
    }
}