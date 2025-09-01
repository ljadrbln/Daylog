<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Presentation\Requests\Entries;

use Codeception\Test\Unit;
use Daylog\Presentation\Requests\Entries\ListEntriesSanitizer;

/**
 * Unit test for ListEntriesSanitizer (BR-3 trimming).
 *
 * Purpose:
 *   Ensure that ListEntriesSanitizer::sanitize() removes leading/trailing
 *   whitespace from all string filter/sort fields (UC-2).
 *
 * Mechanics:
 *   - Supply minimal arrays with only the field under test.
 *   - Override the field with whitespace variations.
 *   - Expect sanitized fields to be trimmed.
 *
 * Covered fields (if present in input):
 *   - sortField, sortDir, dateFrom, dateTo, date, query
 *
 * @covers \Daylog\Presentation\Requests\Entries\ListEntriesSanitizer
 */
final class ListEntriesSanitizerTest extends Unit
{
    /**
     * Provides whitespace variations for UC-2 string params.
     *
     * @return array<string,array{string,string}>
     */
    public static function whitespaceProvider(): array
    {
        return [
            'spaces'   => ['  foo  ', 'foo'],
            'tabs'     => ["\tbar\t", 'bar'],
            'newlines' => ["\n\nbaz\n", 'baz'],
            'mixed'    => [" \tqux\n ", 'qux'],
        ];
    }

    /**
     * Ensures sanitize() trims sortField.
     *
     * @dataProvider whitespaceProvider
     * @param string $input
     * @param string $expected
     * @return void
     */
    public function testSanitizeTrimsSortField(string $input, string $expected): void
    {
        // Arrange
        $row = ['sortField' => $input];

        // Act
        $clean = ListEntriesSanitizer::sanitize($row);

        // Assert
        $this->assertSame($expected, $clean['sortField']);
    }

    /**
     * Ensures sanitize() trims sortDir.
     *
     * @dataProvider whitespaceProvider
     */
    public function testSanitizeTrimsSortDir(string $input, string $expected): void
    {
        $row   = ['sortDir' => $input];
        $clean = ListEntriesSanitizer::sanitize($row);
        $this->assertSame($expected, $clean['sortDir']);
    }

    /**
     * Ensures sanitize() trims dateFrom.
     *
     * @dataProvider whitespaceProvider
     */
    public function testSanitizeTrimsDateFrom(string $input, string $expected): void
    {
        $row   = ['dateFrom' => $input];
        $clean = ListEntriesSanitizer::sanitize($row);
        $this->assertSame($expected, $clean['dateFrom']);
    }

    /**
     * Ensures sanitize() trims dateTo.
     *
     * @dataProvider whitespaceProvider
     */
    public function testSanitizeTrimsDateTo(string $input, string $expected): void
    {
        $row   = ['dateTo' => $input];
        $clean = ListEntriesSanitizer::sanitize($row);
        $this->assertSame($expected, $clean['dateTo']);
    }

    /**
     * Ensures sanitize() trims date.
     *
     * @dataProvider whitespaceProvider
     */
    public function testSanitizeTrimsDate(string $input, string $expected): void
    {
        $row   = ['date' => $input];
        $clean = ListEntriesSanitizer::sanitize($row);
        $this->assertSame($expected, $clean['date']);
    }

    /**
     * Ensures sanitize() trims query.
     *
     * @dataProvider whitespaceProvider
     */
    public function testSanitizeTrimsQuery(string $input, string $expected): void
    {
        $row   = ['query' => $input];
        $clean = ListEntriesSanitizer::sanitize($row);
        $this->assertSame($expected, $clean['query']);
    }
}
