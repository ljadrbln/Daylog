<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\ListEntries;

use Daylog\Tests\Support\Assertion\EntryValidationAssertions;
use Daylog\Tests\Support\DataProviders\ListEntriesDateRangeDataProvider;
use Daylog\Tests\Integration\Application\UseCases\Entries\ListEntries\BaseListEntriesIntegrationTest;
use Daylog\Tests\Support\Datasets\Entries\ListEntriesDataset;

/**
 * AC-10: dateFrom greater than dateTo must fail with DATE_RANGE_INVALID.
 *
 * Purpose:
 *   Verify that the date range order is enforced: 'dateFrom' must not exceed 'dateTo'.
 *   A reversed range must trigger DomainValidationException with code DATE_RANGE_INVALID.
 *
 * Mechanics:
 *   - Build a ListEntries request via the dedicated factory using a reversed range;
 *   - Set an explicit expectation for DATE_RANGE_INVALID using test assertions trait;
 *   - Execute the use case and ensure the exception is thrown.
 *
 * @covers \Daylog\Configuration\Providers\Entries\ListEntriesProvider
 * @covers \Daylog\Application\UseCases\Entries\ListEntries
 *
 * @group UC-ListEntries
 */
final class AC10_DateRangeOrderInvalidTest extends BaseListEntriesIntegrationTest
{
    use EntryValidationAssertions;
    use ListEntriesDateRangeDataProvider;

    /**
     * AC-10: Reversed date range (dateFrom > dateTo) raises DATE_RANGE_INVALID.
     *
     * @dataProvider provideInvalidDateRanges
     *
     * @param string $from Reversed 'dateFrom' value (later date).
     * @param string $to   Reversed 'dateTo' value (earlier date).
     *
     * @return void
     */
    public function testReversedRangeFailsWithDateRangeInvalid(string $from, string $to): void
    {
        // Arrange
        $dataset = ListEntriesDataset::ac10DateRangeOrderInvalid($from, $to);

        // Expect
        $this->expectDateRangeInvalid();

        // Act
        $request = $dataset['request'];
        $this->useCase->execute($request);

        // Safety
        $message = 'Expected DomainValidationException with DATE_RANGE_INVALID for a reversed date range';
        $this->fail($message);
    }
}
