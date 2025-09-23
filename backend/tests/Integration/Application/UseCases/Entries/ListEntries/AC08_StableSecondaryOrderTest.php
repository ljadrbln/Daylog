<?php

declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\ListEntries;

use Daylog\Tests\Support\Datasets\Entries\ListEntriesDataset;

/**
 * AC-08: When primary sort keys are equal, a stable secondary order by createdAt DESC is applied.
 *
 * Purpose:
 *   Ensure deterministic ordering when the primary sort key yields equal values:
 *   when sorting by 'date' and all items share the same date, results must be ordered
 *   by 'createdAt' in DESC order (stable tie-breaker).
 *
 * Mechanics:
 *   - Seed 3 entries that share the same logical 'date';
 *   - set strictly increasing createdAt values to make DESC unambiguous;
 *   - request sorting by 'date' (primary keys equal);
 *   - expect order by createdAt DESC (id3, id2, id1).
 *
 * @covers \Daylog\Configuration\Providers\Entries\ListEntriesProvider
 * @covers \Daylog\Application\UseCases\Entries\ListEntries
 *
 * @group UC-ListEntries
 */
final class AC08_StableSecondaryOrderTest extends BaseListEntriesIntegrationTest
{
    /**
     * AC-08: Equal primary keys fall back to createdAt DESC (stable).
     *
     * @return void
     */
    public function testStableSecondaryOrderWhenPrimarySortKeysAreEqual(): void
    {
        // Arrange
        $dataset = ListEntriesDataset::ac08StableSecondaryOrder();
        $this->seedFromDataset($dataset);

        // Act
        $request  = $dataset['request'];
        $response = $this->useCase->execute($request);
        $items    = $response->getItems();

        $expectedIds = $dataset['expectedIds'];
        $actualIds   = array_column($items, 'id');

        // Assert
        $this->assertSame(3, count($items));
        $this->assertSame($expectedIds, $actualIds);
    }
}
