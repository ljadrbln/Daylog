<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Presentation\Requests\Entries\DeleteEntry;

use Codeception\Test\Unit;
use Daylog\Presentation\Requests\Entries\DeleteEntry\DeleteEntrySanitizer;
use Daylog\Tests\Support\Helper\EntryTestData;

/**
 * Unit test for DeleteEntrySanitizer (BR-1 trimming).
 *
 * Purpose:
 *   Ensure that DeleteEntrySanitizer::sanitize() removes leading/trailing
 *   whitespace from id field before validation.
 *
 * Mechanics:
 *   - Build baseline payload via EntryTestData::getOne().
 *   - Override id field with whitespace variations.
 *   - Expect sanitized id to be trimmed.
 *
 * @covers \Daylog\Presentation\Requests\Entries\DeleteEntry\DeleteEntrySanitizer
 * @group UC-DeleteEntry
 */
final class DeleteEntrySanitizerTest extends Unit
{
    /**
     * Provides whitespace variations for id.
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
     * Ensures sanitize() trims id correctly.
     *
     * @dataProvider whitespaceProvider
     * @param string $input
     * @param string $expected
     * @return void
     */
    public function testSanitizeTrimsId(string $input, string $expected): void
    {
        // Arrange
        $row = EntryTestData::getOne();
        $row['id'] = $input;

        // Act
        $clean = DeleteEntrySanitizer::sanitize($row);

        // Assert
        $this->assertSame($expected, $clean['id']);
    }
}
