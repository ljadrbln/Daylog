<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\UpdateEntry;

use Daylog\Tests\Support\Datasets\Entries\UpdateEntryDataset;
use Daylog\Tests\Support\Assertion\EntryValidationAssertions;

/**
 * UC-5 / AC-05 — Missing id.
 *
 * Purpose:
 * When the id is missing (empty after trimming), domain validation must fail
 * with ID_REQUIRED and the repository must not be touched.
 *
 * Mechanics:
 * - Build UpdateEntryRequest with an empty 'id' to simulate missing value.
 * - Use validator stub that throws DomainValidationException('ID_REQUIRED').
 * - Assert that Fake repository's saveCalls() remains 0 to prove no persistence.
 *
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry::execute
 * @group UC-UpdateEntry
 */
final class AC05_MissingIdTest extends BaseUpdateEntryUnitTest
{
    use EntryValidationAssertions;

    /**
     * Validate that missing id triggers ID_REQUIRED and repo is not used.
     *
     * @return void
     */
    public function testMissingIdFailsValidationAndRepoUntouched(): void
    {
        // Arrange        
        $dataset   = UpdateEntryDataset::ac05MissingId();
        $request   = $dataset['request'];

        $errorCode = 'ID_REQUIRED';
        $validator = $this->makeValidatorThrows($errorCode);
        $repo      = $this->makeRepo();
        
        // Expect
        $this->expectIdRequired();

        // Act
        $useCase = $this->makeUseCase($repo, $validator);
        $useCase->execute($request);

        // Assert
        $this->assertRepoUntouched($repo);
    }
}
