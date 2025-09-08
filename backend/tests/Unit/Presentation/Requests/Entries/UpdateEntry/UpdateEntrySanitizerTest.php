<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Presentation\Requests\Entries\UpdateEntry;

use Codeception\Test\Unit;
use Daylog\Presentation\Requests\Entries\UpdateEntry\UpdateEntrySanitizer;
use Daylog\Tests\Support\DataProviders\WhitespaceDataProvider;

/**
 * Unit test for UpdateEntrySanitizer (BR-1 trimming).
 *
 * Purpose:
 * Validate that UpdateEntrySanitizer::sanitize() removes leading/trailing
 * whitespace from each provided string field before validation in UC-4.
 * The request is partial by design; sanitizer must trim only fields present
 * in the payload without inventing defaults.
 *
 * Mechanics:
 * - Build minimal payloads containing only the field under test.
 * - Feed whitespace variants from a centralized provider.
 * - Assert the sanitized payload equals the expected trimmed value.
 *
 * Cases:
 * - id: spaces, tabs, newlines, mixed whitespace.
 * - title: spaces, tabs, newlines, mixed whitespace.
 * - body: spaces, tabs, newlines, mixed whitespace.
 * - date: spaces, tabs, newlines, mixed whitespace.
 *
 * @covers \Daylog\Presentation\Requests\Entries\UpdateEntry\UpdateEntrySanitizer
 * @group UC-UpdateEntry
 */
final class UpdateEntrySanitizerTest extends Unit
{
    use WhitespaceDataProvider;

    /**
     * Ensures sanitize() trims 'id' correctly when present.
     *
     * @dataProvider provideWhitespaceCases
     *
     * @param string $inputRaw     Raw incoming value with whitespace.
     * @param string $expectedTrim Expected value after trimming.
     * @return void
     */
    public function testSanitizeTrimsId(string $inputRaw, string $expectedTrim): void
    {
        // Arrange
        $payload = ['id' => $inputRaw];

        // Act
        $clean = UpdateEntrySanitizer::sanitize($payload);

        // Assert
        $actual = $clean['id'];
        $this->assertSame($expectedTrim, $actual);
    }

    /**
     * Ensures sanitize() trims 'title' correctly when provided.
     *
     * @dataProvider provideWhitespaceCases
     *
     * @param string $inputRaw
     * @param string $expectedTrim
     * @return void
     */
    public function testSanitizeTrimsTitle(string $inputRaw, string $expectedTrim): void
    {
        // Arrange
        $payload = ['title' => $inputRaw];

        // Act
        $clean = UpdateEntrySanitizer::sanitize($payload);

        // Assert
        $actual = $clean['title'];
        $this->assertSame($expectedTrim, $actual);
    }

    /**
     * Ensures sanitize() trims 'body' correctly when provided.
     *
     * @dataProvider provideWhitespaceCases
     *
     * @param string $inputRaw
     * @param string $expectedTrim
     * @return void
     */
    public function testSanitizeTrimsBody(string $inputRaw, string $expectedTrim): void
    {
        // Arrange
        $payload = ['body' => $inputRaw];

        // Act
        $clean = UpdateEntrySanitizer::sanitize($payload);

        // Assert
        $actual = $clean['body'];
        $this->assertSame($expectedTrim, $actual);
    }

    /**
     * Ensures sanitize() trims 'date' correctly when provided.
     *
     * @dataProvider provideWhitespaceCases
     *
     * @param string $inputRaw
     * @param string $expectedTrim
     * @return void
     */
    public function testSanitizeTrimsDate(string $inputRaw, string $expectedTrim): void
    {
        // Arrange
        $payload = ['date' => $inputRaw];

        // Act
        $clean = UpdateEntrySanitizer::sanitize($payload);

        // Assert
        $actual = $clean['date'];
        $this->assertSame($expectedTrim, $actual);
    }
}
