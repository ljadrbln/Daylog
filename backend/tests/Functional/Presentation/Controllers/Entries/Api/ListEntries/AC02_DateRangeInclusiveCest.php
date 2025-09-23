<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\ListEntries;

use Daylog\Tests\FunctionalTester;
use Daylog\Tests\Support\Datasets\Entries\ListEntriesDataset;

/**
 * UC-2 / AC-02 — Date range inclusive — Functional.
 *
 * Purpose:
 * Prove that entries equal to the range bounds are included and ordering remains date DESC.
 *
 * Mechanics:
 * - Seed three consecutive entries with increasing dates via dataset;
 * - Issue GET /api/entries with dateFrom/dateTo params via base helper;
 * - Expect exactly two results (the boundary items) in date DESC order.
 *
 * @covers \Daylog\Presentation\Controllers\Entries\Api\ListEntriesController::list
 * @group UC-ListEntries
 */
final class AC02_DateRangeInclusiveCest extends BaseListEntriesFunctionalCest
{
    /**
     * Inclusive [dateFrom..dateTo] returns boundary items ordered by date DESC.
     *
     * @param FunctionalTester $I
     * @return void
     */
    public function testDateRangeInclusiveReturnsMatchingItems(FunctionalTester $I): void
    {
        // Arrange
        $dataset = ListEntriesDataset::ac02DateRangeInclusive();
        $this->seedFromDataset($I, $dataset);

        // Act
        $this->listEntriesFromDataset($I, $dataset);

        // Assert (HTTP + contract)
        $this->assertOkContract($I);

        // Assert (items and order)
        $envelope = $this->grabTypedDataEnvelope($I);
        $items    = $envelope['items'] ?? [];

        $I->assertSame(2, count($items));

        $expectedIds = $dataset['expectedIds'];
        $actualIds   = array_column($items, 'id');
        $I->assertSame($expectedIds, $actualIds);
    }
}
