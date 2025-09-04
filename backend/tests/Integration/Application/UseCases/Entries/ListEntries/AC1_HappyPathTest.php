<?php

declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\ListEntries;

use Daylog\Tests\Support\Fixture\EntryFixture;
use Daylog\Tests\Support\Helper\ListEntriesHelper;
use Daylog\Tests\Integration\Application\UseCases\Entries\ListEntries\BaseListEntriesIntegrationTest;

/**
 * AC-1: With no filters, the first page is returned, sorted by date DESC by default.
 *
 * Purpose:
 *   - Verify default ordering by logical date (DESC) and consistent pagination metadata
 *   - using real wiring (Provider + SqlFactory) and a clean DB prepared in the base class.
 *
 * Mechanics:
 *   - Insert three entries with consecutive logical dates.
 *   - Execute use case with default request (no filters).
 *   - Assert: order is date3, date2, date1; pagination meta is consistent.
 *
 * @covers \Daylog\Configuration\Providers\Entries\ListEntriesProvider
 * @covers \Daylog\Application\UseCases\Entries\ListEntries
 * 
* @group UC-ListEntries
 */
final class AC1_HappyPathTest extends BaseListEntriesIntegrationTest
{
    /**
     * AC-1 Happy path: returns first page sorted by date DESC with correct meta.
     *
     * @return void
     */    
    public function testHappyPathReturnsFirstPageSortedByDateDesc(): void
    {
        // Arrange: insert three rows via fixture
        $rowsCount = 3;
        $datesStep = 1;
        $rows = EntryFixture::insertRows($rowsCount, $datesStep);

        // Request with defaults (no filters)
        $data    = ListEntriesHelper::getData();
        $request = ListEntriesHelper::buildRequest($data);

        // Act
        $response = $this->useCase->execute($request);

        // Assert: order and meta
        $items = $response->getItems();

        $this->assertCount($rowsCount, $items);
        $this->assertSame($rows[2]['date'], $items[0]->getDate());
        $this->assertSame($rows[1]['date'], $items[1]->getDate());
        $this->assertSame($rows[0]['date'], $items[2]->getDate());

        $this->assertSame($rowsCount,               $response->getTotal());
        $this->assertSame($request->getPage(),      $response->getPage());
        $this->assertSame($request->getPerPage(),   $response->getPerPage());

        $expectedPages = 1;
        $this->assertSame($expectedPages, $response->getPagesCount());
    }
}
