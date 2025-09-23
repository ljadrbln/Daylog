<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\ListEntries;

use Daylog\Tests\Support\DataProviders\ListEntriesPaginationDataProvider;
use Daylog\Tests\Support\Datasets\Entries\ListEntriesDataset;

/**
 * AC-04: perPage is clamped to allowed bounds; empty pages are valid (Unit).
 *
 * Purpose:
 * Verify clamping for perPage below/above limits and that out-of-range pages
 * return an empty list with consistent pagination metadata.
 *
 * Mechanics:
 * - Seed 5 entries into a fake repo;
 * - Run three scenarios via data provider (below min → 1, above max → 100, page beyond range → empty list).
 *
 * @covers \Daylog\Application\UseCases\Entries\ListEntries\ListEntries::execute
 * @group UC-ListEntries
 */
final class AC04_PaginationBoundsClampUnitTest extends BaseListEntriesUnitTest
{
    use ListEntriesPaginationDataProvider;

    /**
     * AC-04: perPage clamping and valid empty pages via data provider (Unit).
     *
     * @dataProvider providePaginationBoundsCases
     *
     * @param int $perPageInput
     * @param int $expectedPerPage
     * @param int $page
     * @param int $expectedItemsCount
     * @param int $expectedPagesCount
     *
     * @return void
     */
    public function testPaginationBoundsAreClampedAndEmptyPagesAreValid(
        int $perPageInput,
        int $expectedPerPage,
        int $page,
        int $expectedItemsCount,
        int $expectedPagesCount
    ): void {
        // Arrange
        $repo    = $this->makeRepo();

        $dataset = ListEntriesDataset::ac04PaginationBoundsClamp($perPageInput, $page);
        $this->seedFromDataset($repo, $dataset);

        $validator = $this->makeValidatorOk();
        $useCase   = $this->makeUseCase($repo, $validator);

        // Act
        $request  = $dataset['request'];
        $response = $useCase->execute($request);

        $items         = $response->getItems();
        $itemsCount    = count($items);
        $actualPerPage = $response->getPerPage();
        $actualPages   = $response->getPagesCount();
        $actualPage    = $response->getPage();
        $actualTotal   = $response->getTotal();

        // Assert
        $this->assertSame($expectedPerPage, $actualPerPage);
        $this->assertSame($expectedItemsCount, $itemsCount);
        $this->assertSame($expectedPagesCount, $actualPages);
        $this->assertSame($page, $actualPage);

        $rows          = $dataset['rows'];
        $expectedTotal = count($rows);
        $this->assertSame($expectedTotal, $actualTotal);
    }
}
