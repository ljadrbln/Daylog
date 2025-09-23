<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\ListEntries;

use Codeception\Attribute\DataProvider;
use Codeception\Example;
use Daylog\Tests\FunctionalTester;
use Daylog\Tests\Support\DataProviders\ListEntriesDateDataProvider;
use Daylog\Tests\Support\Datasets\Entries\ListEntriesDataset;

/**
 * UC-2 / AC-06 — Invalid date input — Functional.
 *
 * Purpose:
 * Ensure that invalid values in date/dateFrom/dateTo sent over HTTP yield
 * 422 Unprocessable Entity with error code DATE_INVALID and no data payload.
 *
 * Mechanics:
 * - Build request via ListEntriesDataset::ac06InvalidDateInput(field, value);
 * - Issue GET /api/entries with the invalid date parameter from the dataset;
 * - Assert HTTP contract (422 + success=false) and presence of DATE_INVALID in errors.
 *
 * @covers \Daylog\Presentation\Controllers\Entries\Api\ListEntriesController::list
 * @group UC-ListEntries
 */
final class AC06_InvalidDateInputCest extends BaseListEntriesFunctionalCest
{
    use ListEntriesDateDataProvider;

    /**
     * AC-06: Invalid date input yields DATE_INVALID (HTTP).
     *
     * @param FunctionalTester $I Codeception functional tester.
     * @param Example $example Positional case:
     *   [0] field ∈ {date, dateFrom, dateTo}
     *   [1] value — raw invalid date string
     * @return void
     */
    #[DataProvider('provideInvalidDateInputs')]
    public function testInvalidDateInputReturns422WithDateInvalid(FunctionalTester $I, Example $example): void
    {
        // Arrange
        $field   = $example[0];
        $value   = $example[1];
        $dataset = ListEntriesDataset::ac06InvalidDateInput($field, $value);

        // Act
        $this->listEntriesFromDataset($I, $dataset);

        // Assert (HTTP contract — 422 + success=false)
        $this->assertUnprocessableContract($I);

        // Assert (error code presence and no data)
        $this->assertErrorCode($I, 'DATE_INVALID');
    }
}
