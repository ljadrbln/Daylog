<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\AddEntry;

use Daylog\Tests\Support\Assertion\EntryValidationAssertions;
use Daylog\Tests\Support\Datasets\Entries\AddEntryDataset;

/**
 * UC-1 / AC-07 — Invalid date format — Unit.
 *
 * Purpose:
 * Ensure invalid date format triggers domain error and no persistence occurs.
 *
 * Mechanics:
 * - Domain-level code for dates uses DATE_INVALID for both format and calendar errors.
 *
 * @covers \Daylog\Application\UseCases\Entries\AddEntry\AddEntry::execute
 * @group UC-AddEntry
 */
final class AC07_InvalidDateFormatTest extends BaseAddEntryUnitTest
{
    use EntryValidationAssertions;

    /**
     * DATE_INVALID must stop execution before persistence.
     *
     * @return void
     */
    public function testInvalidDateFormatStopsBeforePersistence(): void
    {
        // Arrange        
        $dataset   = AddEntryDataset::ac07InvalidDateFormat();

        $errorCode = 'DATE_INVALID';
        $validator = $this->makeValidatorThrows($errorCode);
        $repo      = $this->makeRepo();

        // Expect
        $this->expectDateInvalid();

        // Act
        $request = $dataset['request'];
        $useCase = $this->makeUseCase($repo, $validator);
        $useCase->execute($request);

        // Assert
        $this->assertRepoUntouched($repo);
    }
}
