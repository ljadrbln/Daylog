<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\ListEntries;

use Daylog\Tests\Support\Assertion\EntryValidationAssertions;
use Daylog\Tests\Support\DataProviders\ListEntriesDateRangeDataProvider;
use Daylog\Tests\Support\Datasets\Entries\ListEntriesDataset;

/**
 * UC-2 / AC-10 — Reversed date range (dateFrom > dateTo) — Unit.
 *
 * Purpose:
 * Ensure that when 'dateFrom' is later than 'dateTo', validation rejects the input
 * with DATE_RANGE_INVALID and execution stops before any repository access.
 *
 * Mechanics:
 * - Build a request via ListEntriesDataset::ac10DateRangeOrderInvalid($from, $to);
 * - Configure validator mock to throw DomainValidationException('DATE_RANGE_INVALID');
 * - Execute the use case and assert the exception via shared validation assertions.
 *
 * @covers \Daylog\Application\UseCases\Entries\ListEntries\ListEntries::execute
 * @group UC-ListEntries
 */
final class AC10_DateRangeOrderInvalidTest extends BaseListEntriesUnitTest
{
    use EntryValidationAssertions;
    use ListEntriesDateRangeDataProvider;

    /**
     * Reversed date range must trigger DATE_RANGE_INVALID and avoid repository access.
     *
     * @dataProvider provideInvalidDateRanges
     *
     * @param string $from Reversed 'dateFrom' value (later date).
     * @param string $to   Reversed 'dateTo' value (earlier date).
     * @return void
     */
    public function testReversedRangeTriggersValidationError(string $from, string $to): void
    {
        // Arrange
        $dataset = ListEntriesDataset::ac10DateRangeOrderInvalid($from, $to);
        
        $errorCode = 'DATE_RANGE_INVALID';
        $validator = $this->makeValidatorThrows($errorCode);

        $repo    = $this->makeRepo();
        $useCase = $this->makeUseCase($repo, $validator);

        // Expect
        $this->expectDateRangeInvalid();

        // Act
        $request = $dataset['request'];
        $useCase->execute($request);
    }
}
