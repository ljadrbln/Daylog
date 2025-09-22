<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\AddEntry;

use Daylog\Tests\Support\Assertion\EntryValidationAssertions;
use Daylog\Tests\Support\Datasets\Entries\AddEntryDataset;

/**
 * UC-1 / AC-06 — Missing date — Unit.
 *
 * Purpose:
 * Ensure DATE_REQUIRED is reported and repository is untouched.
 *
 * @covers \Daylog\Application\UseCases\Entries\AddEntry\AddEntry::execute
 * @group UC-AddEntry
 */
final class AC06_MissingDateTest extends BaseAddEntryUnitTest
{
    use EntryValidationAssertions;

    /**
     * DATE_REQUIRED must stop execution before persistence.
     *
     * @return void
     */
    public function testMissingDateFailsWithDateRequired(): void
    {
        // Arrange        
        $dataset   = AddEntryDataset::ac06EmptyDateSanitized();
        
        $errorCode = 'DATE_REQUIRED';
        $validator = $this->makeValidatorThrows($errorCode);
        $repo      = $this->makeRepo();

        // Expect
        $this->expectDateRequired();

        // Act
        $request = $dataset['request'];
        $useCase = $this->makeUseCase($repo, $validator);
        $useCase->execute($request);

        // Assert
        $this->assertRepoUntouched($repo);
    }
}
