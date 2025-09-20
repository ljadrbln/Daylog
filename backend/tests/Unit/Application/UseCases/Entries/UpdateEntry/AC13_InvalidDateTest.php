<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\UpdateEntry;

use Daylog\Tests\Support\Datasets\Entries\UpdateEntryDataset;
use Daylog\Tests\Support\Assertion\EntryValidationAssertions;

/**
 * UC-5 / AC-13 — Invalid date.
 *
 * Purpose:
 *   Given a date that does not match YYYY-MM-DD or is not a real date,
 *   validation must fail with DATE_INVALID and repository must not be touched.
 *
 * Mechanics:
 *   - Generate a valid UUID and build a request with an invalid date (e.g., '2025-02-30').
 *   - Configure validator mock to throw DomainValidationException('DATE_INVALID').
 *   - Execute the use case and assert that repository save was not called.
 *
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry::execute
 * @group UC-UpdateEntry
 */
final class AC13_InvalidDateTest extends BaseUpdateEntryUnitTest
{
    use EntryValidationAssertions;

    /**
     * Validate that invalid date triggers DATE_INVALID and repo remains untouched.
     *
     * @return void
     */
    public function testInvalidDateFailsValidationAndRepoUntouched(): void
    {
        // Arrange
        $dataset   = UpdateEntryDataset::ac06InvalidId();
        $request   = $dataset['request'];

        $errorCode = 'DATE_INVALID';
        $validator = $this->makeValidatorThrows($errorCode);
        $repo      = $this->makeRepo();

        // Expect
        $this->expectDateInvalid();

        // Act
        $useCase = $this->makeUseCase($repo, $validator);
        $useCase->execute($request);

        // Assert
        $this->assertRepoUntouched($repo);
    }
}
