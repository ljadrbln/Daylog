<?php

declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\ListEntries;

use Daylog\Domain\Models\Entries\ListEntriesConstraints;
use Daylog\Tests\Support\Fixture\EntryFixture;
use Daylog\Tests\Support\Helper\ListEntriesHelper;
use Daylog\Tests\Integration\Application\UseCases\Entries\ListEntries\BaseListEntriesIntegrationTest;

/**
 * AC-4: perPage is clamped to allowed bounds; empty pages are valid.
 *
 * Purpose: 
 *   - Verify clamping for perPage below/above limits and that out-of-range pages return an empty list with consistent pagination meta.
 * 
 * Mechanics: 
 *   - Seed 5 entries; 
 *   - run three scenarios via data provider (below min → 1, above max → 100, page beyond range → empty items with valid meta).
 *
 * @covers \Daylog\Configuration\Providers\Entries\ListEntriesProvider
 * @covers \Daylog\Application\UseCases\Entries\ListEntries
 */
final class AC4_PaginationBoundsClampTest extends BaseListEntriesIntegrationTest
{
    /** 
     * AC-4: perPage clamping and valid empty pages via data provider. 
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
        // Arrange: seed 5 entries
        $rows = EntryFixture::insertRows(5, 1);

        // Act
        $data = ListEntriesHelper::getData();
        $data['perPage'] = $perPageInput;
        $data['page']    = $page;

        $request  = ListEntriesHelper::buildRequest($data);
        $response = $this->useCase->execute($request);

        $items        = $response->getItems();
        $itemsCount   = count($items);
        $actualPerPage= $response->getPerPage();
        $actualPages  = $response->getPagesCount();
        $actualPage   = $response->getPage();
        $total        = $response->getTotal();

        // Assert: clamping & metadata consistency
        $this->assertSame($expectedPerPage, $actualPerPage);
        $this->assertSame($expectedItemsCount, $itemsCount);
        $this->assertSame($expectedPagesCount, $actualPages);
        $this->assertSame($page, $actualPage);

        // Extra sanity for this fixture
        $this->assertSame(count($rows), $total);
    }

    /**
     * Provide cases for pagination bounds.
     *
     * Case names are intentionally descriptive to read well in PHPUnit reports.
     *
     * @return array<string, array{int,int,int,int,int}>
     */
    public function providePaginationBoundsCases(): array
    {
        $cases = [
            // perPage below minimum => clamped to 1; page 1 returns exactly 1 item; total 5 => 5 pages
            'perPage below minimum is clamped to 1' => [0, ListEntriesConstraints::PER_PAGE_MIN, ListEntriesConstraints::PAGE_MIN, 1, 5],

            // perPage above maximum => clamped to 100; all 5 items fit on page 1; total pages = 1
            'perPage above maximum is clamped to 100' => [200, ListEntriesConstraints::PER_PAGE_MAX, ListEntriesConstraints::PAGE_MIN, 5, 1],

            // page beyond boundary => empty items; with perPage=2 and total=5 => pagesCount = ceil(5/2) = 3
            'requesting page beyond range yields empty items with valid meta' => [2, 2, 10, 0, 3],
        ];

        return $cases;
    }
}
