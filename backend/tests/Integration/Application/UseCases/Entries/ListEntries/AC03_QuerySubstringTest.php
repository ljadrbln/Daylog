<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\ListEntries;

use Daylog\Tests\Support\Datasets\Entries\ListEntriesDataset;

/**
 * UC-2 / AC-03 — Query substring (case-insensitive) — Integration.
 *
 * Purpose:
 * Verify that a case-insensitive substring query matches either title or body,
 * excludes unrelated entries, and the results remain ordered by date DESC.
 *
 * Mechanics:
 * - Seed 3 entries.
 * - Set title match "Alpha" for row[0], body match "aLpHa" for row[1], keep row[2] unrelated.
 * - Query "alpha" ⇒ expect two hits ordered by date DESC.
 *
 * @covers \Daylog\Configuration\Providers\Entries\ListEntriesProvider
 * @covers \Daylog\Application\UseCases\Entries\ListEntries
 *
 * @group UC-ListEntries
 */
final class AC03_QuerySubstringTest extends BaseListEntriesIntegrationTest
{
    /**
     * Case-insensitive substring query matches title OR body; unrelated entries excluded.
     *
     * @return void
     */
    public function testQueryFiltersTitleOrBodyCaseInsensitive(): void
    {
        // Arrange
        $dataset = ListEntriesDataset::ac03QueryTitleOrBodyCaseInsensitive();
        $this->seedFromDataset($dataset);

        // Act
        $request  = $dataset['request'];
        $response = $this->useCase->execute($request);
        $items    = $response->getItems();
        
        // Assert
        $this->assertSame(2, count($items));
        
        $expectedIds = $dataset['expectedIds'];
        $actualIds   = array_column($items, 'id');
        $this->assertSame($expectedIds, $actualIds);
    }
}