<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\ListEntries;

use Daylog\Tests\FunctionalTester;
use Daylog\Tests\Support\Datasets\Entries\ListEntriesDataset;

/**
 * UC-2 / AC-03 — Query substring (case-insensitive) — Functional.
 *
 * Purpose:
 * Verify that a case-insensitive substring query matches either title or body,
 * excludes unrelated entries, and the results remain ordered by date DESC.
 *
 * Mechanics:
 * - Seed 3 entries from dataset;
 * - Set title match "Alpha" for row[0], body match "aLpHa" for row[1], keep row[2] unrelated;
 * - Issue GET /api/entries with query="alpha";
 * - Expect two hits ordered by date DESC.
 *
 * @covers \Daylog\Presentation\Controllers\Entries\Api\ListEntriesController::list
 * @group UC-ListEntries
 */
final class AC03_QuerySubstringCest extends BaseListEntriesFunctionalCest
{
    /**
     * Case-insensitive substring query matches title OR body; unrelated entries excluded.
     *
     * @param FunctionalTester $I
     * @return void
     */
    public function testQueryFiltersTitleOrBodyCaseInsensitive(FunctionalTester $I): void
    {
        // Arrange
        $dataset = ListEntriesDataset::ac03QueryTitleOrBodyCaseInsensitive();
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
