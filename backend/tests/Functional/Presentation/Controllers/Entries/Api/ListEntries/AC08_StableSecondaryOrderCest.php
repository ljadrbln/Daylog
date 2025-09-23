<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\ListEntries;

use Daylog\Tests\FunctionalTester;
use Daylog\Tests\Support\Datasets\Entries\ListEntriesDataset;

/**
 * UC-2 / AC-08 — Stable secondary order — Functional.
 *
 * Purpose:
 * Ensure deterministic ordering when the primary sort key yields equal values:
 * when sorting by 'date' and all items share the same date, results must be
 * ordered by 'createdAt' in DESC order (stable tie-breaker).
 *
 * Mechanics:
 * - Seed 3 entries with identical logical 'date';
 * - strictly increasing createdAt values ensure DESC order is unambiguous;
 * - request sorting by 'date' (primary keys equal);
 * - expect order by createdAt DESC (id3, id2, id1).
 *
 * @covers \Daylog\Presentation\Controllers\Entries\Api\ListEntriesController::list
 * @group UC-ListEntries
 */
final class AC08_StableSecondaryOrderCest extends BaseListEntriesFunctionalCest
{
    /**
     * AC-08: Equal primary keys fall back to createdAt DESC (stable).
     *
     * @param FunctionalTester $I
     * @return void
     */
    public function testStableSecondaryOrderWhenPrimaryKeysEqual(FunctionalTester $I): void
    {
        // Arrange
        $dataset = ListEntriesDataset::ac08StableSecondaryOrder();
        $this->seedFromDataset($I, $dataset);

        // Act
        $this->listEntriesFromDataset($I, $dataset);

        // Assert
        $this->assertOkContract($I);

        $envelope   = $this->grabTypedDataEnvelope($I);
        $items      = $envelope['items'];
        
        $expectedIds = $dataset['expectedIds'];
        $actualIds   = array_column($items, 'id');

        $I->assertCount(3, $items);
        $I->assertSame($expectedIds, $actualIds);
    }
}
