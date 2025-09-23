<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\ListEntries;

use Codeception\Attribute\DataProvider;
use Codeception\Example;
use Daylog\Tests\FunctionalTester;
use Daylog\Tests\Support\DataProviders\ListEntriesDateRangeDataProvider;
use Daylog\Tests\Support\Datasets\Entries\ListEntriesDataset;

/**
 * UC-2 / AC-10 — dateFrom > dateTo → DATE_RANGE_INVALID — Functional.
 *
 * Purpose:
 * Ensure that a reversed date range sent over HTTP (dateFrom greater than dateTo)
 * yields 422 Unprocessable Entity with error code DATE_RANGE_INVALID and no data payload.
 *
 * Mechanics:
 * - Build a dataset via ListEntriesDataset::ac10DateRangeOrderInvalid(from, to) with reversed range;
 * - Call GET /api/entries using query params from the dataset;
 * - Assert HTTP contract (422 + success=false) and presence of DATE_RANGE_INVALID in errors.
 *
 * @covers \Daylog\Presentation\Controllers\Entries\Api\ListEntriesController::list
 * @group UC-ListEntries
 */
final class AC10_DateRangeOrderInvalidCest extends BaseListEntriesFunctionalCest
{
    use ListEntriesDateRangeDataProvider;

    /**
     * AC-10: Reversed date range (dateFrom > dateTo) raises DATE_RANGE_INVALID (HTTP).
     *
     * @param FunctionalTester $I Codeception functional tester.
     * @param Example $example Positional case:
     *   [0] from — later date (dateFrom)
     *   [1] to   — earlier date (dateTo)
     * @return void
     */
    #[DataProvider('provideInvalidDateRanges')]
    public function testReversedRangeReturns422WithDateRangeInvalid(FunctionalTester $I, Example $example): void
    {
        // Arrange
        $from    = $example[0];
        $to      = $example[1];
        $dataset = ListEntriesDataset::ac10DateRangeOrderInvalid($from, $to);

        // Act
        $this->listEntriesFromDataset($I, $dataset);

        // Assert (HTTP contract — 422 + success=false)
        $this->assertUnprocessableContract($I);

        // Assert (error code presence and no data)
        $this->assertErrorCode($I, 'DATE_RANGE_INVALID');
    }
}
