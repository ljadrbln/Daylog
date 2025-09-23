<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\ListEntries;

use Codeception\Attribute\DataProvider;
use Codeception\Example;
use Daylog\Tests\FunctionalTester;
use Daylog\Tests\Support\DataProviders\ListEntriesQueryTooLongDataProvider;
use Daylog\Tests\Support\Datasets\Entries\ListEntriesDataset;

/**
 * UC-2 / AC-09 — Query > 30 chars (after trim) → QUERY_TOO_LONG — Functional.
 *
 * Purpose:
 * Ensure that an overlong `query` parameter (length > 30 after trimming) sent via HTTP
 * yields 422 Unprocessable Entity with the error code QUERY_TOO_LONG and no data payload.
 *
 * Mechanics:
 * - Build the request via ListEntriesDataset::ac09QueryTooLong($rawQuery);
 * - Issue GET /api/entries with the overlong query;
 * - Assert HTTP contract (422 + success=false) and that errors contain QUERY_TOO_LONG.
 *
 * @covers \Daylog\Presentation\Controllers\Entries\Api\ListEntriesController::list
 * @group UC-ListEntries
 */
final class AC09_QueryTooLongCest extends BaseListEntriesFunctionalCest
{
    use ListEntriesQueryTooLongDataProvider;

    /**
     * AC-09: Overlong query (>30 chars after trim) must raise QUERY_TOO_LONG (HTTP).
     *
     * @param FunctionalTester $I Codeception functional tester.
     * @param Example $example Positional case:
     *   [0] rawQuery — an overlong string (will be trimmed in the application layer)
     * @return void
     */
    #[DataProvider('provideTooLongQueries')]
    public function testOverlongQueryReturns422WithQueryTooLong(FunctionalTester $I, Example $example): void
    {
        // Arrange
        $rawQuery = $example[0];
        $dataset  = ListEntriesDataset::ac09QueryTooLong($rawQuery);

        // Act
        $this->listEntriesFromDataset($I, $dataset);

        // Assert (HTTP contract — 422 + success=false)
        $this->assertUnprocessableContract($I);

        // Assert (error code presence and no data)        
        $this->assertErrorCode($I, 'QUERY_TOO_LONG');
    }
}
