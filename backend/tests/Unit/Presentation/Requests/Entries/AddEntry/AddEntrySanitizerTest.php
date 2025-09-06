<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Presentation\Requests\Entries\AddEntry;

use Codeception\Test\Unit;
use Daylog\Presentation\Requests\Entries\AddEntry\AddEntrySanitizer;
use Daylog\Tests\Support\Helper\EntryTestData;

/**
 * Unit test for AddEntrySanitizer (BR-1 trimming).
 *
 * Purpose:
 *   Ensure that AddEntrySanitizer::sanitize() removes leading/trailing
 *   whitespace from title, body, and date fields before validation.
 *
 * Mechanics:
 *   - Build baseline payload via EntryTestData::getOne().
 *   - Override individual fields with whitespace variations.
 *   - Expect sanitized fields to be trimmed.
 *
 * @covers \Daylog\Presentation\Requests\Entries\AddEntry\AddEntrySanitizer
 * @group UC-AddEntry
 */
final class AddEntrySanitizerTest extends Unit
{
    /**
     * Provides whitespace variations for title, body, and date.
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
     * Ensures sanitize() trims title correctly.
     *
     * @dataProvider whitespaceProvider
     * @param string $input
     * @param string $expected
     * @return void
     */
    public function testSanitizeTrimsTitle(string $input, string $expected): void
    {
        // Arrange
        $row = EntryTestData::getOne(title: $input);

        // Act
        $clean = AddEntrySanitizer::sanitize($row);

        // Assert
        $this->assertSame($expected, $clean['title']);
    }

    /**
     * Ensures sanitize() trims body correctly.
     *
     * @dataProvider whitespaceProvider
     */
    public function testSanitizeTrimsBody(string $input, string $expected): void
    {
        // Arrange
        $row = EntryTestData::getOne(body: $input);

        // Act
        $clean = AddEntrySanitizer::sanitize($row);

        // Assert
        $this->assertSame($expected, $clean['body']);
    }

    /**
     * Ensures sanitize() trims date correctly.
     *
     * @dataProvider whitespaceProvider
     */
    public function testSanitizeTrimsDate(string $input, string $expected): void
    {
        // Arrange
        $row = EntryTestData::getOne(date: $input);

        // Act
        $clean = AddEntrySanitizer::sanitize($row);

        // Assert
        $this->assertSame($expected, $clean['date']);
    }
}