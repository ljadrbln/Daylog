<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\GetEntry;

use Daylog\Tests\Support\Assertion\EntryValidationAssertions;
use Daylog\Tests\Support\Factory\GetEntryTestRequestFactory;
use Daylog\Tests\Support\Helper\EntriesSeeding;
use Daylog\Tests\Support\Scenarios\Entries\GetEntryScenario;
use Daylog\Tests\Support\Datasets\Entries\GetEntryDataset;


/**
 * UC-3 / AC-03 — Not found — Unit.
 *
 * Purpose:
 *   Ensure that when the requested id does not exist in the repository,
 *   the use case fails with ENTRY_NOT_FOUND and performs no persistence.
 *
 * Mechanics:
 *   - Build a deterministic dataset via GetEntryScenario::ac01HappyPath() and seed the Fake repo;
 *   - Create a request with a fresh UUID v4 that is absent in the repo (factory::notFound());
 *   - Run the use case with a validator that succeeds exactly once;
 *   - Expect DomainValidationException('ENTRY_NOT_FOUND') and verify the repo is untouched.
 *
 * @covers \Daylog\Application\UseCases\Entries\GetEntry\GetEntry::execute
 * @group UC-GetEntry
 */
final class AC03_NotFoundTest extends BaseGetEntryUnitTest
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
        $dataset   = GetEntryDataset::ac03NotFound();
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
