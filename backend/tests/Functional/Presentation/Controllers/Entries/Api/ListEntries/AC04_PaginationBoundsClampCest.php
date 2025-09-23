<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\ListEntries;

use Codeception\Attribute\DataProvider;
use Codeception\Example;
use Daylog\Tests\FunctionalTester;
use Daylog\Tests\Support\DataProviders\ListEntriesPaginationDataProvider;
use Daylog\Tests\Support\Datasets\Entries\ListEntriesDataset;

/**
 * UC-2 / AC-04 — Pagination bounds clamp — Functional.
 *
 * Purpose:
 * Keep the shared trait provider positional for backward compatibility,
 * but expose a class-local provider that maps each positional case into
 * a named associative structure for readability in this Cest.
 *
 * Mechanics:
 * - The local provider `provideCases()` calls trait's `providePaginationBoundsCases()`;
 * - Each positional array [0..4] is mapped into an associative array with keys:
 *   perPageInput, expectedPerPage, page, expectedItemsCount, expectedPagesCount.
 *
 * @covers \Daylog\Presentation\Controllers\Entries\Api\ListEntriesController::list
 * @group UC-ListEntries
 */
final class AC04_PaginationBoundsClampCest extends BaseListEntriesFunctionalCest
{
    use ListEntriesPaginationDataProvider;

    /**
     * AC-04: perPage clamping and valid empty pages via attribute-based data provider.
     *
     * @param FunctionalTester $I
     * @param Example $example Positional case:
     *   [0] perPageInput
     *   [1] expectedPerPage
     *   [2] page
     *   [3] expectedItemsCount
     *   [4] expectedPagesCount
     * @return void
     */
    #[DataProvider('providePaginationBoundsCases')]
    public function testPaginationBoundsAreClampedAndEmptyPagesAreValid(FunctionalTester $I, Example $example): void
    {
        $perPageInput       = $example[0];
        $expectedPerPage    = $example[1];
        $page               = $example[2];
        $expectedItemsCount = $example[3];
        $expectedPagesCount = $example[4];

        $dataset = ListEntriesDataset::ac04PaginationBoundsClamp($perPageInput, $page);
        $this->seedFromDataset($I, $dataset);

        $this->listEntriesFromDataset($I, $dataset);
        $this->assertOkContract($I);

        $envelope       = $this->grabTypedDataEnvelope($I);
        $items          = $envelope['items'];
        $itemsCount     = count($items);
        $actualPerPage  = $envelope['perPage'];
        $actualPages    = $envelope['pagesCount'];
        $actualPage     = $envelope['page'];
        $total          = $envelope['total'];

        $I->assertSame($expectedPerPage, $actualPerPage);
        $I->assertSame($expectedItemsCount, $itemsCount);
        $I->assertSame($expectedPagesCount, $actualPages);
        $I->assertSame($page, $actualPage);

        $rows          = $dataset['rows'];
        $expectedTotal = count($rows);
        $I->assertSame($expectedTotal, $total);
    }
}
