<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\ListEntries;

use Daylog\Tests\Support\Datasets\Entries\ListEntriesDataset;

/**
 * UC-2 / AC-08 — Stable secondary order (Unit).
 *
 * Purpose:
 * Ensure deterministic ordering when the primary sort key yields equal values.
 * When sorting by 'date' and all items share the same logical date, results must
 * fall back to a stable tie-breaker: 'createdAt' DESC.
 *
 * Mechanics:
 * - Build dataset via ListEntriesDataset::ac08StableSecondaryOrder()
 *   (3 rows with same date, strictly increasing createdAt);
 * - Seed rows into fake repository;
 * - Use the request prepared by the dataset;
 * - Execute the use case and assert the returned ids match expectedIds.
 *
 * @covers \Daylog\Application\UseCases\Entries\ListEntries\ListEntries::execute
 * @group UC-ListEntries
 */
final class AC08_StableSecondaryOrderTest extends BaseListEntriesUnitTest
{
    /**
     * AC-08: Equal primary keys fall back to createdAt DESC (stable).
     *
     * @return void
     */
    public function testStableSecondaryOrderWhenPrimarySortKeysAreEqual(): void
    {
        // Arrange
        $repo    = $this->makeRepo();
        $dataset = ListEntriesDataset::ac08StableSecondaryOrder();
        $this->seedFromDataset($repo, $dataset);

        $validator = $this->makeValidatorOk();
        $useCase   = $this->makeUseCase($repo, $validator);

        // Act
        $request   = $dataset['request'];
        $response  = $useCase->execute($request);
        $items     = $response->getItems();

        $actualIds   = array_column($items, 'id');
        $expectedIds = $dataset['expectedIds'];

        // Assert
        $this->assertCount(3, $items);
        $this->assertSame($expectedIds, $actualIds);
    }
}
