<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Presentation\Requests\Entries;

use Daylog\Presentation\Requests\Entries\AddEntrySanitizer;
use PHPUnit\Framework\TestCase;

/**
 * Unit test for AddEntrySanitizer.
 *
 * Purpose:
 *   Verify BR-3 (Trimming): leading and trailing whitespace is removed
 *   from title, body, and date fields before validation.
 *
 * Mechanics:
 *   - Provide raw params with surrounding whitespace.
 *   - Call sanitize().
 *   - Assert: all fields are trimmed correctly.
 *
 * @covers \Daylog\Presentation\Requests\Entries\AddEntrySanitizer
 */
final class AddEntrySanitizerTest extends TestCase
{
    /**
     * Ensures sanitize() trims all string fields.
     *
     * @return void
     */
    public function testSanitizeTrimsAllFields(): void
    {
        // Arrange
        $raw = [
            'title' => "  My Title  ",
            'body'  => "\n Body with spaces \t ",
            'date'  => " 2025-08-30 ",
        ];

        // Act
        $clean = AddEntrySanitizer::sanitize($raw);

        // Assert
        $this->assertSame('My Title', $clean['title']);
        $this->assertSame('Body with spaces', $clean['body']);
        $this->assertSame('2025-08-30', $clean['date']);
    }

    /**
     * Ensures sanitize() leaves already clean fields unchanged.
     *
     * @return void
     */
    public function testSanitizeLeavesCleanValuesUntouched(): void
    {
        // Arrange
        $raw = [
            'title' => 'Title',
            'body'  => 'Body',
            'date'  => '2025-08-30',
        ];

        // Act
        $clean = AddEntrySanitizer::sanitize($raw);

        // Assert
        $this->assertSame($raw, $clean);
    }
}
