<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\ListEntries;

use Codeception\Attribute\DataProvider;
use Codeception\Example;
use Daylog\Tests\FunctionalTester;
use Daylog\Tests\Support\DataProviders\ListEntriesSortingDataProvider;
use Daylog\Tests\Support\Datasets\Entries\ListEntriesDataset;
use Daylog\Tests\Support\Helper\ListEntriesExpectationHelper;

/**
 * UC-2 / AC-05 — Sorting by createdAt/updatedAt supports ASC/DESC — Functional.
 *
 * Purpose:
 * Verify that HTTP layer applies sorting by timestamp fields (createdAt|updatedAt) in both directions.
 * Expected order is derived at runtime from seeded rows to avoid coupling to fixed markers.
 *
 * Mechanics:
 * - Seed 3 deterministic rows with same logical 'date' and distinct timestamps;
 * - Drive four cases via data provider (createdAt ASC/DESC, updatedAt ASC/DESC);
 * - Call GET /api/entries with sort params from dataset; assert items order equals runtime-derived expectation.
 *
 * @covers \Daylog\Presentation\Controllers\Entries\Api\ListEntriesController::list
 * @group UC-ListEntries
 */
final class AC05_SortingByFieldsCest extends BaseListEntriesFunctionalCest
{
    use ListEntriesExpectationHelper;
    use ListEntriesSortingDataProvider;

    /**
     * AC-05: runtime-derived expectations without symbolic markers (functional HTTP).
     *
     * @param FunctionalTester $I Codeception functional tester.
     * @param Example $example Positional case:
     *   [0] sortField ('createdAt'|'updatedAt')
     *   [1] sortDir   ('ASC'|'DESC')
     * @return void
     */
    #[DataProvider('provideTimestampSortingCases')]
    public function testSortingByTimestampsIsAppliedOverHttp(FunctionalTester $I, Example $example): void
    {
        // Arrange
        $sortField = (string) $example[0];
        $sortDir   = (string) $example[1];

        $dataset = ListEntriesDataset::ac05SortingByTimestamps($sortField, $sortDir);
        $this->seedFromDataset($I, $dataset);

        // Act
        $this->listEntriesFromDataset($I, $dataset);

        // Assert (HTTP contract)
        $this->assertOkContract($I);

        // Assert (order)
        $envelope   = $this->grabTypedDataEnvelope($I);
        $items      = $envelope['items'] ?? [];
        
        $rows        = $dataset['rows'];
        $expectedIds = $this->buildExpectedIds($rows, $sortField, $sortDir);
        $actualIds   = array_column($items, 'id');

        $I->assertSame($expectedIds, $actualIds);
    }
}
