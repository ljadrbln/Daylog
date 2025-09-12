<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\AddEntry;

use Daylog\Application\DTO\Entries\AddEntry\AddEntryRequest;
use Daylog\Tests\Support\Helper\EntryTestData;
use Daylog\Tests\Support\Assertion\EntryValidationAssertions;

/**
 * UC-1 / AC-08 — Invalid calendar date — Unit.
 *
 * Purpose:
 * Ensure non-existing calendar dates (e.g., 2025-02-30) are rejected by domain validation
 * and do not touch persistence.
 *
 * Mechanics:
 * - Domain-level code uses DATE_INVALID for calendar violations.
 *
 * @covers \Daylog\Application\UseCases\Entries\AddEntry\AddEntry::execute
 * @group UC-AddEntry
 */
final class AC08_InvalidCalendarDateTest extends BaseAddEntryUnitTest
{
    use EntryValidationAssertions;

    /**
     * DATE_INVALID must stop execution before persistence for calendar violations.
     *
     * @return void
     */
    public function testInvalidCalendarDateStopsBeforePersistence(): void
    {
        // Arrange
        $data      = EntryTestData::getOne();
        $request   = AddEntryRequest::fromArray($data);
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
