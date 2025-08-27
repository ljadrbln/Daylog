<?php

declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries;

use Codeception\Test\Unit;
use Daylog\Configuration\Bootstrap\SqlFactory;
use Daylog\Configuration\Providers\Entries\ListEntriesProvider;
use Daylog\Application\UseCases\Entries\ListEntries;
use Daylog\Tests\Support\Helper\ListEntriesHelper;
use Daylog\Tests\Support\Fixture\EntryFixture;
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
     * Prepare shared DB and wire the use case.
     *
     * Mechanics:
     * - Obtain SQL via SqlFactory (single source of truth).
     * - Store locally for direct SQL asserts if needed.
     * - Register DB in EntryFixture to avoid passing it every call.
     *
     * @return void
     */
    protected function _before(): void
    {
        $db = SqlFactory::get();
        EntryFixture::setDb($db);

        $sql = 'DELETE FROM entries';
        $db->exec($sql);

        $useCase = ListEntriesProvider::useCase();
        $this->useCase = $useCase;
    }

    /**
     * Happy path: returns first page sorted by date DESC with correct meta.
     *
     * Purpose:
     *  Verify default ordering by logical date (DESC) and consistent pagination metadata
     *  using a real DB wiring and minimal fixtures.
     *
     * Mechanics:
     *  - Prepare DB state via EntryFixture::insertByDates() for three consecutive dates.
     *  - Build a default request (no filters).
     *  - Execute UC-2 and assert item order and pagination meta.
     *
     * Checks:
     *  - Items are ordered: date3, date2, date1.
     *  - total/page/perPage/pagesCount are consistent.
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

    /**
     * AC-2: dateFrom/dateTo are inclusive by logical 'date' (YYYY-MM-DD).
     *
     * Purpose:
     *  Verify that entries on the range boundaries are included (inclusive filter)
     *  and ordering remains the default `date DESC`.
     *
     * Mechanics:
     *  - Prepare three entries with dates 2025-08-10, 2025-08-11, 2025-08-12
     *    using EntryFixture::insertByDates().
     *  - Request with dateFrom=2025-08-10 and dateTo=2025-08-11.
     *  - Expect exactly two items: 2025-08-11, 2025-08-10.
     *
     * @return void
     */
    public function testDateRangeInclusiveReturnsMatchingItems(): void
    {
        // Arrange: insert three rows via fixture
        $rows = EntryFixture::insertRows(3, 1);

        // Request with inclusive date range [[0]..[1]]
        $data             = ListEntriesHelper::getData();
        $data['dateFrom'] = $rows[0]['date'];
        $data['dateTo']   = $rows[1]['date'];

        $request  = ListEntriesHelper::buildRequest($data);
        $response = $this->useCase->execute($request);

        // Assert
        $items = $response->getItems();

        $this->assertCount(2, $items);
        $this->assertSame($data['dateTo'],   $items[0]->getDate());
        $this->assertSame($data['dateFrom'], $items[1]->getDate());
    }
    
    /**
     * @covers \Daylog\Configuration\Providers\Entries\ListEntriesProvider
     * @covers \Daylog\Application\UseCases\Entries\ListEntries
     *
     * AC-3: query filters by substring in title OR body (case-insensitive).
     *
     * Purpose:
     *  Ensure that a case-insensitive substring matches either field and that
     *  unrelated entries are excluded.
     *
     * Mechanics:
     *  - Seed three rows via EntryFixture: dates 2025-08-10/11/12 with default title/body.
     *  - Update row for 2025-08-10 to have title match "Alpha".
     *  - Update row for 2025-08-11 to have body match "aLpHa".
     *  - Keep row for 2025-08-12 unrelated.
     *  - Request query="alpha" → expect two hits ordered by date DESC.
     *
     * @return void
     */
    public function testQueryFiltersTitleOrBodyCaseInsensitive(): void
    {
        // Arrange: seed three dates via fixture
        $rows = EntryFixture::insertRows(3, 1);

        $id1 = $rows[0]['id'];
        $id2 = $rows[1]['id'];

        // Adjust specific rows to craft title/body matches for query
        EntryFixture::updateById($id1, ['title' => 'Alpha note']);
        EntryFixture::updateById($id2, ['body'  => 'This body has aLpHa inside']);

        // Request with query
        $data  = ListEntriesHelper::getData();
        $query = 'alpha';
        $data['query'] = $query;

        $request  = ListEntriesHelper::buildRequest($data);
        $response = $this->useCase->execute($request);

        // Assert: two hits (body match; title match), ordered by date DESC
        $items = $response->getItems();

        $this->assertCount(2, $items);
        $this->assertSame($rows[1]['date'], $items[0]->getDate());
        $this->assertSame($rows[0]['date'], $items[1]->getDate());
    }

    /**
     * @skip
     * @covers \Daylog\Configuration\Providers\Entries\ListEntriesProvider
     * @covers \Daylog\Application\UseCases\Entries\ListEntries
     *
     * AC-5: sorting by createdAt and updatedAt (ASC/DESC) is supported.
     *
     * Purpose:
     *  Verify that the 'sort' parameter changes ordering according to UC-2 rules
     *  for both timestamp fields and both directions using real DB wiring.
     *
     * Mechanics:
     *  - Seed three rows with the same logical 'date'.
     *  - Adjust created_at/updated_at per row to form clear ASC/DESC orders.
     *  - Sort by createdAt ASC/DESC; then by updatedAt ASC/DESC; assert deterministic order.
     *
     * @return void
     */
    public function testSortingByCreatedAtAndUpdatedAtAscDesc(): void
    {
        // Arrange: seed 3 rows with the same logical date
        $date         = '2025-08-12';
        $dates        = [$date, $date, $date];

        $defaultTitle = 'Valid title';
        $defaultBody  = 'Valid body';

        $rows = EntryFixture::insertByDates(
            $this->db,
            $dates,
            $defaultTitle,
            $defaultBody
        );

        $id1 = $rows[0]['id'];
        $c1 = '2025-08-12 10:00:00';
        $u1 = '2025-08-12 10:05:00';

        $id2 = $rows[1]['id'];
        $c2 = '2025-08-12 11:00:00';
        $u2 = '2025-08-12 11:05:00';

        $id3 = $rows[2]['id'];
        $c3 = '2025-08-12 12:00:00';
        $u3 = '2025-08-12 12:05:00';

        // Set distinct created_at/updated_at to exercise sorting
        EntryFixture::updateById($this->db, $id1, ['created_at' => $c1, 'updated_at' => $u1]);
        EntryFixture::updateById($this->db, $id2, ['created_at' => $c2, 'updated_at' => $u2]);
        EntryFixture::updateById($this->db, $id3, ['created_at' => $c3, 'updated_at' => $u3]);

        // Sort by createdAt ASC
        $sort1 = 'createdAt,ASC';
        $data1 = ListEntriesHelper::getData();
        $data1['sort'] = $sort1;

        $req1   = ListEntriesHelper::buildRequest($data1);
        $res1   = $this->useCase->execute($req1);
        $items1 = $res1->getItems();

        //$this->assertSame($id1, $items1[0]->getId());
        //$this->assertSame($id2, $items1[1]->getId());
        //$this->assertSame($id3, $items1[2]->getId());

        // Sort by createdAt DESC
        $sort2 = 'createdAt, DESC';
        $data2 = ListEntriesHelper::getData();
        $data2['sort'] = $sort2;

        $req2   = ListEntriesHelper::buildRequest($data2);
        $res2   = $this->useCase->execute($req2);
        $items2 = $res2->getItems();

        $this->assertSame($id3, $items2[0]->getId());
        $this->assertSame($id2, $items2[1]->getId());
        $this->assertSame($id1, $items2[2]->getId());

        // Sort by updatedAt ASC
        $sort3 = 'updatedAt, ASC';
        $data3 = ListEntriesHelper::getData();
        $data3['sort'] = $sort3;

        $req3   = ListEntriesHelper::buildRequest($data3);
        $res3   = $this->useCase->execute($req3);
        $items3 = $res3->getItems();

        //$this->assertSame($id1, $items3[0]->getId());
        //$this->assertSame($id2, $items3[1]->getId());
        //$this->assertSame($id3, $items3[2]->getId());

        // Sort by updatedAt DESC
        $sort4 = 'updatedAt, DESC';
        $data4 = ListEntriesHelper::getData();
        $data4['sort'] = $sort4;

        $req4   = ListEntriesHelper::buildRequest($data4);
        $res4   = $this->useCase->execute($req4);
        $items4 = $res4->getItems();

        $this->assertSame($id3, $items4[0]->getId());
        $this->assertSame($id2, $items4[1]->getId());
        $this->assertSame($id1, $items4[2]->getId());
    }

    /**
     * @covers \Daylog\Configuration\Providers\Entries\ListEntriesProvider
     * @covers \Daylog\Application\UseCases\Entries\ListEntries
     *
     * AC-8: stable secondary order when primary sort keys are equal.
     *
     * Purpose:
     *  Guarantee deterministic order via stable secondary sort by createdAt DESC
     *  when primary keys are equal (all items share the same 'date').
     *
     * Mechanics:
     *  - Seed three rows with the same logical date via EntryFixture::insertByDates().
     *  - Adjust created_at/updated_at to distinct values (10:00, 11:00, 12:00).
     *  - Sort by date DESC (primary equal) → expect createdAt DESC as tiebreaker.
     *
     * @return void
     */
    public function testStableSecondaryOrderByCreatedAtDescWhenPrimaryEqual(): void
    {
        // Arrange: same date for all rows
        $rows = EntryFixture::insertRows(3, 0);

        $id1 = $rows[0]['id'];
        $c1 = '2025-08-12 10:00:00'; 
        $u1 = '2025-08-12 10:01:00';
        
        $id2 = $rows[1]['id'];
        $c2 = '2025-08-12 11:00:00'; 
        $u2 = '2025-08-12 11:01:00';
        
        $id3 = $rows[2]['id'];
        $c3 = '2025-08-12 12:00:00'; 
        $u3 = '2025-08-12 12:01:00';

        // Distinct timestamps to define the stable secondary order
        EntryFixture::updateById($id1, ['created_at' => $c1, 'updated_at' => $u1]);
        EntryFixture::updateById($id2, ['created_at' => $c2, 'updated_at' => $u2]);
        EntryFixture::updateById($id3, ['created_at' => $c3, 'updated_at' => $u3]);

        // Primary sort: date DESC (all equal) → tiebreaker should be createdAt DESC
        $sort = 'date,DESC';
        $data = ListEntriesHelper::getData();
        $data['sort'] = $sort;

        $request  = ListEntriesHelper::buildRequest($data);
        $response = $this->useCase->execute($request);

        // Assert stable secondary order by createdAt DESC
        $items = $response->getItems();

        $this->assertSame($id3, $items[0]->getId());
        $this->assertSame($id2, $items[1]->getId());
        $this->assertSame($id1, $items[2]->getId());
    }

    /**
     * @covers \Daylog\Configuration\Providers\Entries\ListEntriesProvider
     * @covers \Daylog\Application\UseCases\Entries\ListEntries
     *
     * Pagination edge: requesting a page beyond pagesCount returns an empty list.
     *
     * Purpose:
     *  Validate AF-3/AC-4 behavior for pagination: empty pages are valid and meta
     *  remains consistent (total/perPage/page/pagesCount) when page exceeds boundary.
     *
     * Mechanics:
     *  - Seed three entries via EntryFixture with consecutive dates.
     *  - perPage = 2 → pagesCount = 2.
     *  - Request page = 3 → expect items = [] and correct meta.
     *
     * @return void
     */
    public function testEmptyPageBeyondBoundaryReturnsNoItemsWithValidMeta(): void
    {
        // Arrange: three rows via fixture
        $rowsCount = 3;
        $datesStep = 1;
        EntryFixture::insertRows($rowsCount, $datesStep);

        // Request page beyond boundary: perPage=2 → pagesCount=2; request page=3
        $perPage = 2;
        $page    = 3;

        $data            = ListEntriesHelper::getData();
        $data['page']    = $page;
        $data['perPage'] = $perPage;

        $request  = ListEntriesHelper::buildRequest($data);
        $response = $this->useCase->execute($request);

        // Assert: empty items and valid meta
        $items = $response->getItems();

        $this->assertCount(0, $items);
        $this->assertSame($rowsCount, $response->getTotal());
        $this->assertSame($perPage,   $response->getPerPage());
        $this->assertSame($page,      $response->getPage());

        $expectedPages = (int) ceil($rowsCount / $perPage);
        $this->assertSame($expectedPages, $response->getPagesCount());
    }
}
