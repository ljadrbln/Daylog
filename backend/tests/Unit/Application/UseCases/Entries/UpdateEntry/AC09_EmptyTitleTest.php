<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\UpdateEntry;

use Daylog\Tests\Support\Datasets\Entries\UpdateEntryDataset;
use Daylog\Tests\Support\Assertion\EntryValidationAssertions;

/**
 * UC-5 / AC-09 — Empty title.
 *
 * Purpose:
 *   Given a title that is empty after trimming, validation must fail with
 *   TITLE_REQUIRED and the repository must not be touched.
 *
 * Mechanics:
 *   - Build UpdateEntryRequest with a valid UUID and a whitespace-only title ('   ').
 *   - Configure validator mock to throw DomainValidationException('TITLE_REQUIRED').
 *   - Execute the use case and assert that repository save was not called.
 *
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry::execute
 * @group UC-UpdateEntry
 */
final class AC09_EmptyTitleTest extends BaseUpdateEntryUnitTest
{
    use EntryValidationAssertions;

    /**
     * Validate that empty (after trimming) title triggers TITLE_REQUIRED and repo remains untouched.
     *
     * @return void
     */
    public function testEmptyTitleFailsValidationAndRepoUntouched(): void
    {
        // Arrange
        $dataset   = UpdateEntryDataset::ac06InvalidId();
        $request   = $dataset['request'];

        $errorCode = 'TITLE_REQUIRED';
        $validator = $this->makeValidatorThrows($errorCode);
        $repo      = $this->makeRepo();

        // Expect
        $this->expectTitleRequired();

        // Act
        $useCase = $this->makeUseCase($repo, $validator);
        $useCase->execute($request);

        // Assert
        $this->assertRepoUntouched($repo);
    }
}
