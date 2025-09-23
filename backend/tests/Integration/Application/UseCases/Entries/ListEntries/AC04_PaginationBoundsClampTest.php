<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\ListEntries;

use Daylog\Tests\Support\Factory\ListEntriesTestRequestFactory;
use Daylog\Tests\Support\Scenarios\Entries\ListEntriesScenario;
use Daylog\Tests\Support\Helper\EntriesSeeding;
use Daylog\Tests\Support\DataProviders\ListEntriesPaginationDataProvider;
use Daylog\Tests\Support\Datasets\Entries\ListEntriesDataset;

/**
 * AC-04: perPage is clamped to allowed bounds; empty pages are valid.
 *
 * Purpose:
 * Verify clamping for perPage below/above limits and that out-of-range pages
 * return an empty list with consistent pagination metadata.
 *
 * Mechanics:
 * - Seed 5 entries;
 * - run three scenarios via data provider (below min → 1, above max → 100, page beyond range → empty list).
 *
 * @covers \Daylog\Configuration\Providers\Entries\ListEntriesProvider
 * @covers \Daylog\Application\UseCases\Entries\ListEntries
 *
 * @group UC-ListEntries
 */
final class AC04_PaginationBoundsClampTest extends BaseListEntriesIntegrationTest
{
    use ListEntriesPaginationDataProvider;
    
    /**
     * AC-04: perPage clamping and valid empty pages via data provider.
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
        $dataset = ListEntriesDataset::ac04PaginationBoundsClamp($perPageInput, $page);
        $this->seedFromDataset($dataset);
        
        // Act
        $request  = $dataset['request'];
        $response = $this->useCase->execute($request);

        $items          = $response->getItems();
        $itemsCount     = count($items);
        $actualPerPage  = $response->getPerPage();
        $actualPages    = $response->getPagesCount();
        $actualPage     = $response->getPage();
        $total          = $response->getTotal();

        // Assert
        $this->assertSame($expectedPerPage, $actualPerPage);
        $this->assertSame($expectedItemsCount, $itemsCount);
        $this->assertSame($expectedPagesCount, $actualPages);
        $this->assertSame($page, $actualPage);

        $expectedTotal = count($dataset['rows']);
        $this->assertSame($expectedTotal, $total);
    }
}