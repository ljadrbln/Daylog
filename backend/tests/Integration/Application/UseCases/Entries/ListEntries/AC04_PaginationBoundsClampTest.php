<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\ListEntries;

use Daylog\Domain\Models\Entries\ListEntriesConstraints;

use Daylog\Tests\Support\Factory\ListEntriesTestRequestFactory;
use Daylog\Tests\Support\Scenarios\Entries\ListEntriesScenario;
use Daylog\Tests\Support\Helper\EntriesSeeding;

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
        $dataset = ListEntriesScenario::ac04PaginationBoundsClamp();
        $rows    = $dataset['rows'];

        $overrides = [
            'perPage' => $perPageInput,
            'page'    => $page,
        ];

        $request = ListEntriesTestRequestFactory::fromOverrides($overrides);
        EntriesSeeding::intoDb($rows);

        // Act
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

        $expectedTotal = count($rows);
        $this->assertSame($expectedTotal, $total);
    }

    /**
     * Provide cases for pagination bounds.
     *
     * @return array<string, array{int,int,int,int,int}>
     */
    public function providePaginationBoundsCases(): array
    {
        $minPerPage = ListEntriesConstraints::PER_PAGE_MIN;
        $maxPerPage = ListEntriesConstraints::PER_PAGE_MAX;
        $minPage    = ListEntriesConstraints::PAGE_MIN;

        $cases = [
            // perPage below minimum => clamped to 1; page 1 returns exactly 1 item; total 5 => 5 pages
            'perPage below minimum is clamped to 1'
                => [0, $minPerPage, $minPage, 1, 5],

            // perPage above maximum => clamped to 100; all 5 items fit on page 1; total pages = 1
            'perPage above maximum is clamped to 100'
                => [200, $maxPerPage, $minPage, 5, 1],

            // page beyond boundary => empty items; with perPage=2 and total=5 => pagesCount = ceil(5/2) = 3
            'requesting page beyond range yields empty items with valid meta'
                => [2, 2, 10, 0, 3],
        ];

        return $cases;
    }
}
