<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\ListEntries;

use Daylog\Tests\FunctionalTester;
use Daylog\Tests\Support\Datasets\Entries\ListEntriesDataset;
use Daylog\Tests\Support\Fixture\EntryFixture;


/**
 * UC-2 / AC-01 — Happy path — Functional.
 *
 * Purpose:
 * Ensure that with no filters GET /api/entries returns the first page sorted by
 * logical date in DESC order and follows the response contract.
 *
 * Mechanics:
 * - Seed DB with three deterministic rows from the dataset using EntryFixture::insertMany();
 * - Issue GET /api/entries via the base helper using dataset payload;
 * - Assert contract (200, success=true, code=OK), items order, and DB contents.
 *
 * @covers \Daylog\Presentation\Controllers\Entries\Api\ListEntriesController::list
 * @group UC-ListEntries
 */
final class AC01_HappyPathCest extends BaseListEntriesFunctionalCest
{
    /**
     * AC-01: returns first page sorted by date DESC with correct meta.
     *
     * @param FunctionalTester $I
     * @return void
     */
    public function testHappyPathReturnsFirstPageSortedByDateDesc(FunctionalTester $I): void
    {
        // Arrange (seed exactly what dataset provides; no factories/getMany here)
        $dataset = ListEntriesDataset::ac01HappyPath();
        $this->seedFromDataset($I, $dataset);

        // Act
        $this->listEntriesFromDataset($I, $dataset);

        // Assert (HTTP + contract)
        $this->assertOkContract($I);

        // Assert (items and order)
        $envelope = $this->grabTypedDataEnvelope($I);
        $items    = $envelope['items'] ?? [];

        $I->assertSame(3, count($items));

        $expectedIds = $dataset['expectedIds'];
        $actualIds   = array_column($items, 'id');
        $I->assertSame($expectedIds, $actualIds);
    }
}
