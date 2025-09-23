<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\ListEntries;

use Daylog\Tests\Support\DataProviders\ListEntriesSortingDataProvider;
use Daylog\Tests\Support\Datasets\Entries\ListEntriesDataset;
use Daylog\Tests\Support\Helper\ListEntriesExpectationHelper;

/**
 * UC-2 / AC-05 — Sorting by createdAt/updatedAt supports ASC/DESC (Unit).
 *
 * Purpose:
 * Validate that sorting by timestamp fields changes the order of results according
 * to the requested field ('createdAt' or 'updatedAt') and direction ('ASC' or 'DESC').
 * The expected order is derived dynamically from the seeded rows to avoid coupling
 * to hardcoded IDs or positions.
 *
 * Mechanics:
 * - Build rows with the same logical date and distinct timestamps via ListEntriesDataset;
 * - Pass {sortField, sortDir} into the dataset so the request DTO is constructed inside it;
 * - Seed rows into a fake repository, execute the use case, compare the order of returned IDs
 *   with the runtime-computed expected order (helper).
 *
 * @covers \Daylog\Application\UseCases\Entries\ListEntries\ListEntries::execute
 * @group UC-ListEntries
 */
final class AC05_SortingByFieldsTest extends BaseListEntriesUnitTest
{
    use ListEntriesExpectationHelper;
    use ListEntriesSortingDataProvider;

    /**
     * AC-05: runtime-derived expectations without symbolic markers.
     *
     * @dataProvider provideTimestampSortingCases
     *
     * @param string       $sortField One of: 'createdAt'|'updatedAt'
     * @param 'ASC'|'DESC' $sortDir   Sorting direction
     *
     * @return void
     */
    public function testSortingByTimestampsIsApplied(string $sortField, string $sortDir): void
    {
        // Arrange
        $repo = $this->makeRepo();

        $dataset = ListEntriesDataset::ac05SortingByTimestamps($sortField, $sortDir);
        $this->seedFromDataset($repo, $dataset);

        $validator = $this->makeValidatorOk();
        $useCase   = $this->makeUseCase($repo, $validator);

        $request = $dataset['request'];

        // Act
        $response = $useCase->execute($request);
        $items    = $response->getItems();

        // Assert
        $rows        = $dataset['rows'];
        $actualIds   = array_column($items, 'id');
        $expectedIds = $this->buildExpectedIds($rows, $sortField, $sortDir);

        $this->assertSame($expectedIds, $actualIds);
    }
}
