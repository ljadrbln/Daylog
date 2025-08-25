<?php

declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases;

use Codeception\Test\Unit;
use Daylog\Configuration\Bootstrap\SqlFactory;
use Daylog\Configuration\Providers\Entries\ListEntriesProvider;
use Daylog\Application\UseCases\Entries\ListEntries;
use Daylog\Tests\Support\Helper\ListEntriesHelper;
use DB\SQL;

/**
 * @covers \Daylog\Configuration\Providers\Entries\ListEntriesProvider
 * @covers \Daylog\Application\UseCases\Entries\ListEntries
 *
 * Integration test for UC-2 ListEntries with real wiring and DB.
 *
 * Purpose:
 *  Verify end-to-end behavior using production wiring (Provider + SqlFactory)
 *  against a real database: default sorting (date DESC) and pagination meta.
 *
 * Mechanics:
 *  - Prepare DB state directly via DB\SQL (truncate + inserts).
 *  - Build the use case through ListEntriesProvider::useCase().
 *  - Execute with default request (no filters).
 *  - Assert ordering and pagination metadata.
 */
final class ListEntriesIntegrationTest extends Unit
{
    /** @var SQL */
    private SQL $db;

    /** @var ListEntries */
    private ListEntries $useCase;

    /**
     * Prepare database and provider-wired use case before each test.
     *
     * @return void
     */
    protected function _before(): void
    {
        $db = SqlFactory::get();
        $this->db = $db;

        // Clean slate
        $truncate = 'DELETE FROM entries';
        $this->db->exec($truncate);

        $useCase = ListEntriesProvider::useCase();
        $this->useCase = $useCase;
    }

    /**
     * Happy path: returns first page sorted by date DESC with correct meta.
     *
     * Data setup:
     *  - Three entries with dates 2025-08-10, 2025-08-11, 2025-08-12.
     *  - Inserted directly via DB\SQL to exercise the real stack.
     *
     * Checks:
     *  - Items are ordered: 2025-08-12, 2025-08-11, 2025-08-10.
     *  - total/page/perPage/pagesCount are consistent with data.
     *
     * @return void
     */
    public function testHappyPathReturnsFirstPageSortedByDateDesc(): void
    {
        // Arrange: insert three rows
        $id1       = '00000000-0000-0000-0000-000000000001';
        $title1    = 'Oldest';
        $body1     = 'Valid body';
        $date1     = '2025-08-10';
        $created1  = '2025-08-10 10:00:00';
        $updated1  = '2025-08-10 10:00:00';
        $status1   = 'published';

        $id2       = '00000000-0000-0000-0000-000000000002';
        $title2    = 'Middle';
        $body2     = 'Valid body';
        $date2     = '2025-08-11';
        $created2  = '2025-08-11 10:00:00';
        $updated2  = '2025-08-11 10:00:00';
        $status2   = 'published';

        $id3       = '00000000-0000-0000-0000-000000000003';
        $title3    = 'Newest';
        $body3     = 'Valid body';
        $date3     = '2025-08-12';
        $created3  = '2025-08-12 10:00:00';
        $updated3  = '2025-08-12 10:00:00';
        $status3   = 'published';

        $sql = '
            INSERT INTO entries (id, title, body, date, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?)
        ';

        $this->db->exec($sql, [$id1, $title1, $body1, $date1, $created1, $updated1]);
        $this->db->exec($sql, [$id2, $title2, $body2, $date2, $created2, $updated2]);
        $this->db->exec($sql, [$id3, $title3, $body3, $date3, $created3, $updated3]);

        // Request with defaults (no filters)
        $data    = ListEntriesHelper::getData();
        $request = ListEntriesHelper::buildRequest($data);

        // Act
        $response = $this->useCase->execute($request);

        // Assert: order and meta
        $items = $response->getItems();

        $this->assertCount(3, $items);
        $this->assertSame($date3, $items[0]->getDate());
        $this->assertSame($date2, $items[1]->getDate());
        $this->assertSame($date1, $items[2]->getDate());

        $this->assertSame(3,                      $response->getTotal());
        $this->assertSame($request->getPage(),    $response->getPage());
        $this->assertSame($request->getPerPage(), $response->getPerPage());

        $expectedPages = 1;
        $this->assertSame($expectedPages, $response->getPagesCount());
    }
}
