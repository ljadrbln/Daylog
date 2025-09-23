<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\ListEntries;

use Daylog\Tests\FunctionalTester;
use Daylog\Tests\Support\Datasets\Entries\ListEntriesDataset;

/**
 * UC-2 / AC-07 — Single-date exact match — Functional.
 *
 * Purpose:
 * Ensure that when a single logical date is provided (`date=YYYY-MM-DD`), the API
 * returns only entries whose logical `date` equals the requested value; no other
 * dates are present in the result.
 *
 * Mechanics:
 * - Seed 5 deterministic entries where exactly two share the target logical `date`;
 * - Perform GET /api/entries with the single-date filter from the dataset;
 * - Assert HTTP contract (200 + success=true) and envelope (items, meta);
 * - Verify that only the two expected entries are returned, in the expected order.
 *
 * @covers \Daylog\Presentation\Controllers\Entries\Api\ListEntriesController::list
 * @group UC-ListEntries
 */
final class AC07_SingleDateExactMatchCest extends BaseListEntriesFunctionalCest
{
    /**
     * AC-07: API returns only exact logical-date matches (ordered by date DESC per defaults).
     *
     * @param FunctionalTester $I Codeception functional tester.
     * @return void
     */
    public function testSingleDateFilterReturnsOnlyExactMatches(FunctionalTester $I): void
    {
        // Arrange
        $dataset = ListEntriesDataset::ac07SingleDateExact();
        $this->seedFromDataset($I, $dataset);

        // Act
        $this->listEntriesFromDataset($I, $dataset);

        // Assert (HTTP contract)
        $this->assertOkContract($I);

        // Assert (payload)
        $envelope   = $this->grabTypedDataEnvelope($I);
        $items      = $envelope['items'] ?? [];

        $itemsCount  = count($items);
        $total       = $envelope['total'] ?? null;
        $page        = $envelope['page'] ?? null;
        $pagesCount  = $envelope['pagesCount'] ?? null;

        $I->assertSame(2, $itemsCount);
        
        $expectedIds = $dataset['expectedIds'];
        $actualIds   = array_column($items, 'id');        
        $I->assertSame($expectedIds, $actualIds);

        $I->assertSame(2, $total);
        $I->assertSame(1, $page);
        $I->assertSame(1, $pagesCount);
    }
}
