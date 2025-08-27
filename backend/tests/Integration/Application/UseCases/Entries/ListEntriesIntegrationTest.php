<?php

declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries;

use Codeception\Test\Unit;
use Daylog\Configuration\Bootstrap\SqlFactory;
use Daylog\Configuration\Providers\Entries\ListEntriesProvider;
use Daylog\Application\UseCases\Entries\ListEntries;
use Daylog\Tests\Support\Helper\ListEntriesHelper;
use Daylog\Tests\Support\Fixture\EntryFixture;
use Daylog\Application\Exceptions\DomainValidationException;

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
     *  - Request query="alpha" => expect two hits ordered by date DESC.
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
     * AC-4 (pagination bounds): If perPage is outside limits, it is clamped to allowed values.
     * Empty pages are valid. See UC-2 AC-4. 
     *
     * Purpose:
     *   Verify clamping behavior for perPage below/above bounds and ensure that requesting
     *   a page beyond the available range yields an empty items list with consistent metadata.
     *
     * Mechanics:
     *   - Seed 5 entries (distinct dates).
     *   - Run scenarios via data provider:
     *     - perPage below minimum => clamped to 1.
     *     - perPage above maximum => clamped to 100.
     *     - Page beyond boundary  => empty items with valid pagination metadata.
     *
     * @dataProvider providePaginationBoundsCases
     *
     * @param int                $perPageInput       Raw perPage from request (may be out of bounds)
     * @param int                $expectedPerPage    Effective perPage after clamping
     * @param int                $page               Requested page number
     * @param int                $expectedItemsCount Expected number of items returned for the page
     * @param int                $expectedPagesCount Expected total pages count
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
     * Provide cases for pagination bounds (UC-2 AC-4).
     *
     * Case names are intentionally descriptive to read well in PHPUnit reports.
     *
     * @return array<string, array{int,int,int,int,int}>
     */
    public function providePaginationBoundsCases(): array
    {
        $cases = [
            // perPage below minimum => clamped to 1; page 1 returns exactly 1 item; total 5 => 5 pages
            'perPage below minimum is clamped to 1' => [0, 1, 1, 1, 5],

            // perPage above maximum => clamped to 100; all 5 items fit on page 1; total pages = 1
            'perPage above maximum is clamped to 100' => [200, 100, 1, 5, 1],

            // page beyond boundary => empty items; with perPage=2 and total=5 => pagesCount = ceil(5/2) = 3
            'requesting page beyond range yields empty items with valid meta' => [2, 2, 10, 0, 3],
        ];

        return $cases;
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

            'sort by date ASC returns oldest date first'    => ['date', 'ASC',  ['id1','id2','id3']],
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
     *  - 'id1' => $id1
     *  - 'id2' => $id2
     *  - 'id3' => $id3
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
     * AC-6: A non-YYYY-MM-DD date (or non-real calendar date) causes DATE_INVALID.
     *
     * Purpose:
     *   Ensure any invalid date in fields (date, dateFrom, dateTo) triggers
     *   a DomainValidationException for UC-2 AC-6.
     *
     * Mechanics:
     *   - Baseline request via helper.
     *   - Override a single date field with an invalid value.
     *   - Expect DomainValidationException; optionally assert message mentions DATE_INVALID.
     *
     * @dataProvider provideInvalidDateInputs
     * @param string $field One of: date|dateFrom|dateTo
     * @param string $value Raw invalid value supplied by the client
     * @return void
     */
    public function testInvalidDateInputThrowsValidationException(string $field, string $value): void
    {
        // Arrange
        $data   = ListEntriesHelper::getData();
        $name   = $field;
        $val    = $value;
        $data[$name] = $val;

        $request = ListEntriesHelper::buildRequest($data);

        // Expectation
        $exception = DomainValidationException::class;
        $this->expectException($exception);

        // Act
        $this->useCase->execute($request);

        $message = 'DomainValidationException was expected for invalid date input';
        $this->fail($message);
    }

    /**
     * Provide invalid date inputs for UC-2 AC-6.
     *
     * Cases:
     *  - Wrong format: 2025/01/01, 2025-1-01, 2025-01-1, 01-01-2025, abc
     *  - Non-real:     2025-02-30, 2025-13-01, 2025-00-10, 2025-04-31, 2025-12-00
     *
     * @return array<string, array{string,string}>
     */
    public function provideInvalidDateInputs(): array
    {
        $cases = [
            // date: wrong format
            'date wrong format slashes'            => ['date',     '2025/01/01'],
            'date wrong format single-digit month' => ['date',     '2025-1-01'],
            'date wrong format single-digit day'   => ['date',     '2025-01-1'],
            'date wrong format reversed'           => ['date',     '01-01-2025'],
            'date alphabetic'                      => ['date',     'abc'],

            // date: non-real
            'date non-real Feb 30'                 => ['date',     '2025-02-30'],
            'date non-real month 13'               => ['date',     '2025-13-01'],
            'date non-real month 00'               => ['date',     '2025-00-10'],
            'date non-real Apr 31'                 => ['date',     '2025-04-31'],
            'date non-real day 00'                 => ['date',     '2025-12-00'],

            // dateFrom: wrong format
            'dateFrom wrong format slashes'        => ['dateFrom', '2025/01/01'],
            'dateFrom wrong format single-digit m' => ['dateFrom', '2025-1-01'],
            'dateFrom wrong format single-digit d' => ['dateFrom', '2025-01-1'],
            'dateFrom wrong format reversed'       => ['dateFrom', '01-01-2025'],
            'dateFrom alphabetic'                  => ['dateFrom', 'abc'],

            // dateFrom: non-real
            'dateFrom non-real Feb 30'             => ['dateFrom', '2025-02-30'],
            'dateFrom non-real month 13'           => ['dateFrom', '2025-13-01'],
            'dateFrom non-real month 00'           => ['dateFrom', '2025-00-10'],
            'dateFrom non-real Apr 31'             => ['dateFrom', '2025-04-31'],
            'dateFrom non-real day 00'             => ['dateFrom', '2025-12-00'],

            // dateTo: wrong format
            'dateTo wrong format slashes'          => ['dateTo',   '2025/01/01'],
            'dateTo wrong format single-digit m'   => ['dateTo',   '2025-1-01'],
            'dateTo wrong format single-digit d'   => ['dateTo',   '2025-01-1'],
            'dateTo wrong format reversed'         => ['dateTo',   '01-01-2025'],
            'dateTo alphabetic'                    => ['dateTo',   'abc'],

            // dateTo: non-real
            'dateTo non-real Feb 30'               => ['dateTo',   '2025-02-30'],
            'dateTo non-real month 13'             => ['dateTo',   '2025-13-01'],
            'dateTo non-real month 00'             => ['dateTo',   '2025-00-10'],
            'dateTo non-real Apr 31'               => ['dateTo',   '2025-04-31'],
            'dateTo non-real day 00'               => ['dateTo',   '2025-12-00'],
        ];

        return $cases;
    }

    /**
     * @covers \Daylog\Configuration\Providers\Entries\ListEntriesProvider
     * @covers \Daylog\Application\UseCases\Entries\ListEntries
     *
     * AC-7: With date=YYYY-MM-DD, only entries with an exact logical date match are returned.
     *
     * Purpose:
     *   Verify that the single-date filter returns exclusively entries whose logical 'date'
     *   equals the requested date. No other dates must leak into the result (UC-2 AC-7).
     *
     * Mechanics:
     *   - Seed five entries.
     *   - Force two entries to target logical date (e.g., 2025-08-12), three — to other dates.
     *   - Call use case with date filter; assert that only the two target IDs are returned
     *     and metadata reflects total=2 (pagesCount=1 with default perPage).
     *
     * @return void
     */
    public function testSingleDateFilterReturnsOnlyExactMatches(): void
    {
        // Arrange
        $rows = EntryFixture::insertRows(5, 0);

        $targetDate = $rows[0]['date'];
        $otherDate1 = '2025-08-10';
        $otherDate2 = '2025-08-11';
        $otherDate3 = '2025-08-13';

        $id1 = $rows[0]['id'];
        $id2 = $rows[1]['id'];
        $id3 = $rows[2]['id'];
        $id4 = $rows[3]['id'];
        $id5 = $rows[4]['id'];

        EntryFixture::updateById($id3, ['date' => $otherDate1]);
        EntryFixture::updateById($id4, ['date' => $otherDate2]);
        EntryFixture::updateById($id5, ['date' => $otherDate3]);

        // Act
        $data = ListEntriesHelper::getData();
        $data['date'] = $targetDate;

        $request  = ListEntriesHelper::buildRequest($data);
        $response = $this->useCase->execute($request);

        $items = $response->getItems();

        // Collect returned IDs (order-agnostic check)
        $actualIds = [];
        foreach ($items as $item) {
            $actualIds[] = $item->getId();
        }

        $expectedIds = [$id1, $id2];
        sort($expectedIds);
        sort($actualIds);

        // Assert: only exact logical-date matches are returned
        $this->assertSame(2, count($items));
        $this->assertSame($expectedIds, $actualIds);

        // Assert: pagination metadata is consistent
        $this->assertSame(2, $response->getTotal());
        $this->assertSame(1, $response->getPage());
        $this->assertSame(1, $response->getPagesCount());
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
     *   - Seed three rows with the same logical 'date' (primary sort key: 'date' => equal).
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

        // Act: primary sort key is 'date' (all equal) => must fall back to createdAt DESC
        $data = ListEntriesHelper::getData();
        $field = 'date';
        $dir   = 'ASC';
        $data['sortField'] = $field;
        $data['sortDir']   = $dir;

        $req   = ListEntriesHelper::buildRequest($data);
        $res   = $this->useCase->execute($req);
        $items = $res->getItems();

        // Assert: stable secondary order by createdAt DESC => id3, id2, id1
        $this->assertSame($id3, $items[0]->getId());
        $this->assertSame($id2, $items[1]->getId());
        $this->assertSame($id1, $items[2]->getId());
    }

    /**
     * @covers \Daylog\Configuration\Providers\Entries\ListEntriesProvider
     * @covers \Daylog\Application\UseCases\Entries\ListEntries
     *
     * AC-9: Given query longer than 30 chars (after trimming), validation fails with QUERY_TOO_LONG.
     *
     * Purpose:
     *   Ensure that the 'query' filter enforces the 30-char limit after trimming.
     *   Any input whose trimmed length > 30 must cause a DomainValidationException with QUERY_TOO_LONG.
     *
     * Mechanics:
     *   - Build a baseline request via helper.
     *   - Override 'query' with values that remain > 30 after trimming (spaces/newlines around).
     *   - Expect DomainValidationException and assert message mentions QUERY_TOO_LONG.
     *
     * @dataProvider provideTooLongQueryValues
     * @param string $rawQuery Raw 'query' value as provided by client (may include outer whitespace)
     * @return void
     */
    public function testQueryLongerThan30FailsWithQueryTooLong(string $rawQuery): void
    {
        // Arrange
        $data = ListEntriesHelper::getData();
        $data['query'] = $rawQuery;

        $request = ListEntriesHelper::buildRequest($data);

        // Expect
        $exception = DomainValidationException::class;
        $this->expectException($exception);

        // Act
        $this->useCase->execute($request);

        // Safety
        $message = 'DomainValidationException was expected for an overlong query';
        $this->fail($message);
    }

    /**
     * Provide overlong 'query' inputs that still exceed 30 chars after trimming.
     *
     * Scenarios:
     *  - Exactly 31 ASCII chars.
     *  - 31 chars with spaces around (trim does not reduce length below 31).
     *  - 31 chars with newlines/tabs around (trim still leaves 31).
     *
     * @return array<string, array{string}>
     */
    public function provideTooLongQueryValues(): array
    {
        $thirtyOneA = str_repeat('a', 31);
        $withSpaces = '   ' . str_repeat('b', 31) . '   ';
        $withNlTab  = "\n\t" . str_repeat('c', 31) . "\t\n";

        $cases = [
            '31 ascii chars'                         => [$thirtyOneA],
            '31 chars with surrounding spaces'       => [$withSpaces],
            '31 chars with surrounding nl/tab chars' => [$withNlTab],
        ];

        return $cases;
    }

    /**
     * @covers \Daylog\Configuration\Providers\Entries\ListEntriesProvider
     * @covers \Daylog\Application\UseCases\Entries\ListEntries
     *
     * AC-10: Given dateFrom > dateTo, validation fails with DATE_RANGE_INVALID.
     *
     * Purpose:
     *   Ensure that the date range order is validated: the lower bound (dateFrom)
     *   must not exceed the upper bound (dateTo). If it does, the use case must
     *   raise a DomainValidationException with DATE_RANGE_INVALID.
     *
     * Mechanics:
     *   - Build a baseline request via helper.
     *   - Override dateFrom and dateTo with reversed ranges.
     *   - Expect DomainValidationException and a message containing DATE_RANGE_INVALID.
     *
     * @dataProvider provideInvalidDateRanges
     *
     * @param string $from Reversed lower bound (greater date)
     * @param string $to   Upper bound (lesser date)
     * @return void
     */
    public function testDateRangeOrderFromGreaterThanToFailsWithDateRangeInvalid(
        string $from,
        string $to
    ): void {
        // Arrange
        $data = ListEntriesHelper::getData();
        $data['dateFrom'] = $from;
        $data['dateTo']   = $to;

        $request = ListEntriesHelper::buildRequest($data);

        // Expectation
        $exceptionClass = DomainValidationException::class;
        $this->expectException($exceptionClass);

        // Act
        $this->useCase->execute($request);

        // Safety (should not reach)
        $message = 'DomainValidationException was expected for reversed date range';
        $this->fail($message);
    }

    /**
     * Provide reversed date ranges (dateFrom > dateTo).
     *
     * Scenarios:
     *  - Adjacent days reversed (same month).
     *  - Cross-month reversed.
     *  - Cross-year reversed.
     *
     * @return array<string, array{string,string}>
     */
    public function provideInvalidDateRanges(): array
    {
        $cases = [
            'adjacent days reversed' => ['2025-08-12', '2025-08-11'],
            'cross month reversed'   => ['2025-09-01', '2025-08-31'],
            'cross year reversed'    => ['2026-01-01', '2025-12-31'],
        ];

        return $cases;
    }
}
