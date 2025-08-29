<?php

declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\Normalization\Entries;

use Codeception\Test\Unit;
use Daylog\Application\Normalization\Entries\ListEntriesInputNormalizer;
use Daylog\Application\DTO\Entries\ListEntries\ListEntriesRequest;
use Daylog\Domain\Models\Entries\ListEntriesConstraints;
use Daylog\Tests\Support\Helper\ListEntriesHelper;

/**
 * Unit test for ListEntriesInputNormalizer.
 *
 * Purpose:
 * Verifies UC-2 input normalization rules (defaults, clamping, trimming,
 * empty-to-null, and sort fallbacks) in isolation from the rest of the system.
 *
 * Scope:
 * - No business validation here (date format, query length).
 * - Uses ListEntriesConstraints for all expectations (no magic numbers/strings).
 *
 * @covers \Daylog\Application\Normalization\Entries\ListEntriesInputNormalizer
 */
final class ListEntriesInputNormalizerTest extends Unit
{
    /**
     * Ensures defaults are applied when input is empty.
     *
     * Mechanics:
     * - Given an empty raw input, the normalizer must provide PAGE_MIN, PER_PAGE_DEFAULT,
     *   SORT_FIELD_DEFAULT, and SORT_DIR_DEFAULT; all date fields and query become null.
     *
     * @return void
     * @covers \Daylog\Application\Normalization\Entries\ListEntriesInputNormalizer::normalize
     */
    public function testDefaultsAppliedWhenMissing(): void
    {
        $data    = ListEntriesHelper::getData();
        $request = ListEntriesRequest::fromArray($data);
        $params  = ListEntriesInputNormalizer::normalize($request);

        $expectedPage     = ListEntriesConstraints::PAGE_MIN;
        $expectedPerPage  = ListEntriesConstraints::PER_PAGE_DEFAULT;
        $expectedSort     = ListEntriesConstraints::SORT_FIELD_DEFAULT;
        $expectedDir      = ListEntriesConstraints::SORT_DIR_DEFAULT;

        $this->assertSame($expectedPage, $params['page']);
        $this->assertSame($expectedPerPage, $params['perPage']);
        $this->assertSame($expectedSort, $params['sortField']);
        $this->assertSame($expectedDir, $params['sortDir']);
        $this->assertNull($params['date']);
        $this->assertNull($params['dateFrom']);
        $this->assertNull($params['dateTo']);
        $this->assertNull($params['query']);
    }

    /**
     * Data provider for pagination bounds (page & perPage in one go).
     *
     * Purpose:
     * Supplies (pageIn, perPageIn, pageExpected, perPageExpected) tuples to verify:
     * - page is min-bounded by PAGE_MIN;
     * - perPage is clamped to [PER_PAGE_MIN..PER_PAGE_MAX] and keeps in-range values.
     *
     * Titles document intent for each edge case.
     *
     * @return array<string, array{0:int,1:int,2:int,3:int}>
     */
    public function paginationProvider(): array
    {
        $pageMin = ListEntriesConstraints::PAGE_MIN;
        $ppMin   = ListEntriesConstraints::PER_PAGE_MIN;
        $ppMax   = ListEntriesConstraints::PER_PAGE_MAX;

        $cases = [
            // page: below min -> min, perPage: below min -> min
            'page below min; perPage below min' => [$pageMin - 10, $ppMin - 10, $pageMin, $ppMin],

            // page: at min -> min, perPage: at min -> min
            'page at min; perPage at min'       => [$pageMin,      $ppMin,      $pageMin, $ppMin],

            // page: above min -> unchanged, perPage: in range -> unchanged
            'page above min; perPage in range'  => [$pageMin + 3,  $ppMin + 7,  $pageMin + 3, $ppMin + 7],

            // page: above min -> unchanged, perPage: at max -> max
            'page above min; perPage at max'    => [$pageMin + 5,  $ppMax,      $pageMin + 5, $ppMax],

            // page: below min -> min, perPage: above max -> max
            'page below min; perPage above max' => [$pageMin - 1,  $ppMax + 10, $pageMin, $ppMax],
        ];

        return $cases;
    }

    /**
     * Ensures both page and perPage bounds are respected (single provider).
     *
     * Mechanics:
     * - Given raw (page, perPage) inputs from the provider,
     *   when normalized, then page == pageExpected and perPage == perPageExpected.
     *
     * @param int $pageIn            Raw page from provider.
     * @param int $perPageIn         Raw perPage from provider.
     * @param int $pageExpected      Expected normalized page.
     * @param int $perPageExpected   Expected normalized perPage.
     * @return void
     * @covers \Daylog\Application\Normalization\Entries\ListEntriesInputNormalizer::normalize
     * @dataProvider paginationProvider
     */
    public function testPaginationBoundsWithProvider(
        int $pageIn,
        int $perPageIn,
        int $pageExpected,
        int $perPageExpected
    ): void {

        $data    = ListEntriesHelper::getData(page: $pageIn, perPage: $perPageIn);
        $request = ListEntriesRequest::fromArray($data);
        $params  = ListEntriesInputNormalizer::normalize($request);

        $this->assertSame($pageExpected, $params['page']);
        $this->assertSame($perPageExpected, $params['perPage']);
    }

    /**
     * Data provider for query normalization.
     *
     * Purpose:
     * Supplies (raw, expected) pairs with titled cases to verify trimming and
     * empty-to-null behavior for the free-text query.
     *
     * @return array<string, array{0:string,1:?string}>
     */
    public function queryProvider(): array
    {
        $cases = [
            'trim both sides'      => ['   hello   ', 'hello'],
            'whitespace only'      => ['     ',       null],
            'empty string'         => ['',            null],
            'tabs and newline'     => ["\tfoo\n",     'foo'],
            'leading space only'   => [' bar',        'bar'],
            'trailing space only'  => ['baz ',        'baz'],
        ];

        return $cases;
    }

    /**
     * Ensures query is trimmed and empty-after-trim becomes null (titled provider).
     *
     * Mechanics:
     * - Given a raw 'query' string, normalization must either trim it
     *   or convert it to null when the trimmed value is empty.
     *
     * @param string      $in       Raw query string.
     * @param string|null $expected Expected normalized value.
     * @return void
     * @covers \Daylog\Application\Normalization\Entries\ListEntriesInputNormalizer::normalize
     * @dataProvider queryProvider
     */
    public function testQueryNormalizationWithProvider(string $in, ?string $expected): void
    {
        $data = ListEntriesHelper::getData();
        $data['query'] = $in;

        $request = ListEntriesRequest::fromArray($data);
        $params  = ListEntriesInputNormalizer::normalize($request);

        $this->assertSame($expected, $params['query']);
    }

    /**
     * Data provider for empty date fields.
     *
     * Purpose:
     * Supplies date keys whose empty string input must become null.
     *
     * @return array<string, array{0:string}>
     */
    public function emptyDateKeyProvider(): array
    {
        $cases = [
            'date empty -> null'     => ['date'],
            'dateFrom empty -> null' => ['dateFrom'],
            'dateTo empty -> null'   => ['dateTo'],
        ];

        return $cases;
    }

    /**
     * Ensures empty strings in date fields become null (titled provider).
     *
     * @param string $key Date field name from provider.
     * @return void
     * @covers \Daylog\Application\Normalization\Entries\ListEntriesInputNormalizer::normalize
     * @dataProvider emptyDateKeyProvider
     */
    public function testEmptyDateFieldsBecomeNull(string $key): void
    {
        $data = ListEntriesHelper::getData();
        $data[$key] = '';

        $request = ListEntriesRequest::fromArray($data);
        $params  = ListEntriesInputNormalizer::normalize($request);

        $this->assertNull($params[$key]);
    }

    /**
     * Ensures non-empty date is passed through unchanged (no trimming/format checks here).
     *
     * @return void
     * @covers \Daylog\Application\Normalization\Entries\ListEntriesInputNormalizer::normalize
     */
    public function testNonEmptyDatePassThrough(): void
    {
        $expectedDate = '2025-08-15';
        $data = ListEntriesHelper::getData();
        $data['date'] = $expectedDate;

        $request = ListEntriesRequest::fromArray($data);
        $params  = ListEntriesInputNormalizer::normalize($request);

        $this->assertSame($expectedDate, $params['date']);
    }

    /**
     * Data provider for sort field normalization.
     *
     * Purpose:
     * Verifies allow-list for sort field and default fallback for invalid/empty values.
     *
     * @return array<string, array{0:string,1:string}>
     */
    public function sortFieldProvider(): array
    {
        $default = ListEntriesConstraints::SORT_FIELD_DEFAULT;
        $allowed = ListEntriesConstraints::ALLOWED_SORT_FIELDS;
        $first   = $allowed[0];

        $cases = [
            'allowed field'   => [$first,         $first],
            'invalid field'   => ['not_a_field',  $default],
            'empty -> default'=> ['',             $default],
        ];

        return $cases;
    }

    /**
     * Ensures sort field is validated against allow-list with default fallback.
     *
     * @param string $in       Raw sort field.
     * @param string $expected Expected normalized sort field.
     * @return void
     * @covers \Daylog\Application\Normalization\Entries\ListEntriesInputNormalizer::normalize
     * @dataProvider sortFieldProvider
     */
    public function testSortFieldNormalization(string $in, string $expected): void
    {
        $data    = ListEntriesHelper::getData(sortField: $in);
        $request = ListEntriesRequest::fromArray($data);
        $params  = ListEntriesInputNormalizer::normalize($request);

        $this->assertSame($expected, $params['sortField']);
    }

    /**
     * Data provider for sort direction normalization.
     *
     * Purpose:
     * Verifies case-insensitive acceptance of valid directions and default fallback for invalid/empty values.
     *
     * @return array<string, array{0:string,1:string}>
     */
    public function sortDirProvider(): array
    {
        $default = ListEntriesConstraints::SORT_DIR_DEFAULT;

        $cases = [
            'valid asc lower'    => ['asc',  'ASC'],
            'valid desc lower'   => ['desc', 'DESC'],
            'valid ASC'          => ['ASC',  'ASC'],
            'valid DESC'         => ['DESC', 'DESC'],
            'invalid -> default' => ['sideways', $default],
            'empty -> default'   => ['',          $default],
        ];

        return $cases;
    }

    /**
     * Ensures sort direction is uppercased and validated with default fallback.
     *
     * @param string $in       Raw sort direction.
     * @param string $expected Expected normalized sort direction ('ASC'|'DESC').
     * @return void
     * @covers \Daylog\Application\Normalization\Entries\ListEntriesInputNormalizer::normalize
     * @dataProvider sortDirProvider
     */
    public function testSortDirNormalization(string $in, string $expected): void
    {
        $data    = ListEntriesHelper::getData(sortDir: $in);
        $request = ListEntriesRequest::fromArray($data);        
        $params  = ListEntriesInputNormalizer::normalize($request);

        $this->assertSame($expected, $params['sortDir']);
    }
}
