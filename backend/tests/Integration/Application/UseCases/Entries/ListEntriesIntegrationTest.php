<?php

declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries;

use Codeception\Test\Unit;
use Daylog\Configuration\Bootstrap\SqlFactory;
use Daylog\Configuration\Providers\Entries\ListEntriesProvider;
use Daylog\Application\UseCases\Entries\ListEntries;
use Daylog\Tests\Support\Helper\ListEntriesHelper;
use Daylog\Tests\Support\Fixture\EntryFixture;

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
     * AC-1 Happy path: returns first page sorted by date DESC with correct meta.
     *
     * Purpose:
     *  Verify default ordering by logical date (DESC) and consistent pagination metadata
     *  using a real DB wiring and minimal fixtures.
     *
     * Mechanics:
     *  - Prepare DB state via EntryFixture::insertRows($rowsCount, $datesStep) for three consecutive dates.
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
     *    using EntryFixture::insertRows($rowsCount, $datesStep).
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
     * @covers \Daylog\Configuration\Providers\Entries\ListEntriesProvider
     * @covers \Daylog\Application\UseCases\Entries\ListEntries
     *
     * AC-5: sorting by createdAt and updatedAt (ASC/DESC) is supported.
     *
     * Purpose:
     *   Verify that the 'sort' parameter changes ordering according to UC-2 rules
     *   for both timestamp fields and both directions using real DB wiring.
     *
     * Mechanics:
     *   - Seed three rows with the same logical 'date'.
     *   - Adjust created_at/updated_at per row to form clear ASC/DESC orders.
     *   - Run with (field, dir, expected order markers) from dataProvider.
     *   - Replace markers with real UUIDs inside the test before asserting.
     *
     * @dataProvider provideSortingScenarios
     * @param string                 $sortField       One of: createdAt|updatedAt
     * @param 'ASC'|'DESC'           $sortDir         Sort direction
     * @param array<int,string>      $expectedMarkers Expected order using markers: id1|id2|id3
     * @return void
     */
    public function testSortingByCreatedAtAndUpdatedAtAscDesc(
        string $sortField,
        string $sortDir,
        array $expectedMarkers
    ): void {
        // Arrange: seed 3 rows with the same logical date
        $rows = EntryFixture::insertRows(3, 0);

        $id1 = $rows[0]['id'];
        $c1  = '2025-08-12 10:00:00';
        $u1  = '2025-08-12 10:05:00';

        $id2 = $rows[1]['id'];
        $c2  = '2025-08-12 11:00:00';
        $u2  = '2025-08-12 11:05:00';

        $id3 = $rows[2]['id'];
        $c3  = '2025-08-12 12:00:00';
        $u3  = '2025-08-12 12:05:00';

        EntryFixture::updateById($id1, ['created_at' => $c1, 'updated_at' => $u1]);
        EntryFixture::updateById($id2, ['created_at' => $c2, 'updated_at' => $u2]);
        EntryFixture::updateById($id3, ['created_at' => $c3, 'updated_at' => $u3]);

        // Replace expected order markers with real UUIDs
        $expected = $this->resolveExpectedOrder($expectedMarkers, $id1, $id2, $id3);

        // Act
        $data          = ListEntriesHelper::getData();
        $field         = $sortField;
        $dir           = $sortDir;
        $data['sortField'] = $field;
        $data['sortDir']   = $dir;

        $req   = ListEntriesHelper::buildRequest($data);
        $res   = $this->useCase->execute($req);
        $items = $res->getItems();

        // Assert
        $this->assertSame($expected[0], $items[0]->getId());
        $this->assertSame($expected[1], $items[1]->getId());
        $this->assertSame($expected[2], $items[2]->getId());
    }

    /**
     * Provide scenarios for sorting by createdAt/updatedAt ASC/DESC.
     *
     * Each case returns [field, direction, expected order markers].
     * Markers are symbolic ('id1','id2','id3') and are resolved to real UUIDs
     * inside the test to avoid coupling the provider to runtime-generated values.
     *
     * @return array<string, array{0:string,1:'ASC'|'DESC',2:array<int,string>}>
     */
    public function provideSortingScenarios(): array
    {
        $cases = [
            'createdAt ASC returns oldest first'            => ['createdAt', 'ASC',  ['id1', 'id2', 'id3']],
            'createdAt DESC returns newest first'           => ['createdAt', 'DESC', ['id3', 'id2', 'id1']],
            'updatedAt ASC returns earliest updates first'  => ['updatedAt', 'ASC',  ['id1', 'id2', 'id3']],
            'updatedAt DESC returns latest updates first'   => ['updatedAt', 'DESC', ['id3', 'id2', 'id1']],
        ];

        return $cases;
    }

    /**
     * Resolve expected order markers to real UUIDs generated at runtime.
     *
     * Scenario:
     *  - Input markers come from dataProvider ('id1','id2','id3').
     *  - This method maps them to actual UUIDs produced by EntryFixture::insertRows().
     *
     * Cases:
     *  - 'id1' → $id1
     *  - 'id2' → $id2
     *  - 'id3' → $id3
     *
     * @param array<int,string> $markers List of 'id1'|'id2'|'id3'.
     * @param string            $id1     UUID of the first seeded row.
     * @param string            $id2     UUID of the second seeded row.
     * @param string            $id3     UUID of the third seeded row.
     * @return array<int,string>         Expected UUID order for assertions.
     */
    private function resolveExpectedOrder(array $markers, string $id1, string $id2, string $id3): array
    {
        $map = [
            'id1' => $id1,
            'id2' => $id2,
            'id3' => $id3,
        ];

        $expected = [];
        for ($i = 0; $i < count($markers); $i++) {
            $marker   = $markers[$i];
            $expected[] = $map[$marker];
        }

        return $expected;
    }

    /**
     * @covers \Daylog\Configuration\Providers\Entries\ListEntriesProvider
     * @covers \Daylog\Application\UseCases\Entries\ListEntries
     *
     * AC-8: When sort keys are equal, a stable secondary order by createdAt DESC is applied.
     *
     * Purpose:
     *   Verify that when the primary sort field yields equal values for all items,
     *   the list is deterministically ordered by the stable secondary key: createdAt DESC.
     *
     * Mechanics:
     *   - Seed three rows with the same logical 'date' (primary sort key: 'date' → equal).
     *   - Assign strictly increasing created_at timestamps (10:00:00, 11:00:00, 12:00:00).
     *   - Equalize updated_at to remove incidental effects.
     *   - Sort by 'date' (ASC). Expect order by createdAt DESC: id3, id2, id1.
     *
     * @return void
     */
    public function testStableSecondaryOrderWhenPrimarySortKeysAreEqual(): void
    {
        // Arrange: three entries share the same 'date' (primary key equal by design)
        $rows = EntryFixture::insertRows(3, 0);

        $id1 = $rows[0]['id'];
        $id2 = $rows[1]['id'];
        $id3 = $rows[2]['id'];

        // created_at strictly increasing to make DESC order unambiguous
        $c1 = '2025-08-12 10:00:00';
        $c2 = '2025-08-12 11:00:00';
        $c3 = '2025-08-12 12:00:00';

        // updated_at equalized to avoid affecting the outcome
        $u  = '2025-08-12 13:00:00';

        EntryFixture::updateById($id1, ['created_at' => $c1, 'updated_at' => $u]);
        EntryFixture::updateById($id2, ['created_at' => $c2, 'updated_at' => $u]);
        EntryFixture::updateById($id3, ['created_at' => $c3, 'updated_at' => $u]);

        // Act: primary sort key is 'date' (all equal) → must fall back to createdAt DESC
        $data = ListEntriesHelper::getData();
        $field = 'date';
        $dir   = 'ASC';
        $data['sortField'] = $field;
        $data['sortDir']   = $dir;

        $req   = ListEntriesHelper::buildRequest($data);
        $res   = $this->useCase->execute($req);
        $items = $res->getItems();

        // Assert: stable secondary order by createdAt DESC → id3, id2, id1
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
