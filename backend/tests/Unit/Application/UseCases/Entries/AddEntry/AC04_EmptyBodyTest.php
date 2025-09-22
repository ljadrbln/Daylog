<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\AddEntry;

use Daylog\Tests\Support\Assertion\EntryValidationAssertions;
use Daylog\Tests\Support\Datasets\Entries\AddEntryDataset;

/**
 * UC-1 / AC-04 — Empty body — Unit.
 *
 * Purpose:
 * Ensure BODY_REQUIRED is thrown and repository remains untouched.
 *
 * @covers \Daylog\Application\UseCases\Entries\AddEntry\AddEntry::execute
 * @group UC-AddEntry
 */
final class AC04_EmptyBodyTest extends BaseAddEntryUnitTest
{
    use EntryValidationAssertions;

    /**
     * BODY_REQUIRED must stop execution before persistence.
     *
     * @return void
     */
    public function testEmptyBodyFailsWithBodyRequired(): void
    {
        // Arrange        
        $dataset   = AddEntryDataset::ac04EmptyBodySanitized();
        
        $errorCode = 'BODY_REQUIRED';
        $validator = $this->makeValidatorThrows($errorCode);
        $repo      = $this->makeRepo();

        // Expect
        $this->expectBodyRequired();

        // Act
        $request = $dataset['request'];
        $useCase = $this->makeUseCase($repo, $validator);
        $useCase->execute($request);

        // Assert
        $this->assertRepoUntouched($repo);
    }
}
