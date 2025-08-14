<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Domain\Models;

use Codeception\Test\Unit;

use Daylog\Domain\Models\Entry;
use Daylog\Domain\Errors\ValidationException;
use Daylog\Domain\Models\EntryConstraints;
use Daylog\Tests\Support\Helper\EntryHelper;

/**
 * Domain Model: Entry
 *
 * Red test for the primary diary entry model.
 * Fields come from the user: title, body, date (YYYY-MM-DD).
 * Business rules: BR-1..BR-3, BR-6.
 */
final class EntryTest extends Unit
{
    /**
     * Creates an Entry instance from valid data and verifies that all fields are set correctly.
     *
     * Trims leading/trailing whitespace from title, body, and date before comparison.
     *
     * @dataProvider provideValidEntries
     * @covers \Daylog\Domain\Models\Entry::fromArray
     *
     * @param array{title:string, body:string, date:string} $data Valid entry data.
     */
    public function testCreateValidEntryFromData(array $data): void
    {
        $entry = Entry::fromArray($data);

        $this->assertSame(trim($data['title']), $entry->getTitle());
        $this->assertSame(trim($data['body']), $entry->getBody());
        $this->assertSame(trim($data['date']), $entry->getDate());
    }

    /**
     * Ensures that creating an Entry with invalid data throws a ValidationException.
     *
     * Uses valid base data from EntryHelper::getData() and applies the given overrides
     * from the data provider to trigger specific validation failures.
     *
     * @dataProvider provideInvalidEntries
     * @covers \Daylog\Domain\Models\Entry::fromArray
     *
     * @param array<string,string> $overrides Field values to override in the base valid data.
     */
    public function testInvalidEntriesThrow(array $overrides): void
    {
        $this->expectException(ValidationException::class);

        $data = EntryHelper::getData();
        $data = array_merge($data, $overrides);

        Entry::fromArray($data);
    }

    /**
     * Creates an Entry from valid data and verifies that all fields are assigned without trimming.
     *
     * Uses default valid values from EntryHelper::getData() and ensures that the title,
     * body, and date are stored exactly as provided.
     *
     * @covers \Daylog\Domain\Models\Entry::fromArray
     *
     * @return void
     */
    public function testCreateEntryFromArray(): void
    {
        $data  = EntryHelper::getData();
        $entry = Entry::fromArray($data);

        $this->assertSame($data['title'], $entry->getTitle());
        $this->assertSame($data['body'], $entry->getBody());
        $this->assertSame($data['date'], $entry->getDate());
    }

    /**
     * Verifies that equals() returns true when two Entry instances are created from identical data.
     *
     * Uses default valid values from EntryHelper::getData() for both entries.
     *
     * @covers \Daylog\Domain\Models\Entry::equals
     *
     * @return void
     */
    public function testEqualsReturnsTrueForSameValues(): void
    {
        /** @var array<string,string> $data */
        $data = EntryHelper::getData();

        $left  = Entry::fromArray($data);
        $right = Entry::fromArray($data);

        $result = $left->equals($right);
        $this->assertTrue($result);
    }

    /**
     * Verifies that equals() returns false when at least one field value differs.
     *
     * Starts from valid base data from EntryHelper::getData(), applies the provided overrides
     * to create a second Entry instance with one or more differing fields, and checks that
     * equals() detects the inequality.
     *
     * @dataProvider provideNonEqualOverrides
     * @covers \Daylog\Domain\Models\Entry::equals
     *
     * @param array<string,string> $overrides Field values to override in the base valid data.
     * @return void
     */
    public function testEqualsReturnsFalseWhenAnyFieldDiffers(array $overrides): void
    {
        /** @var array<string,string> $data */
        $data = EntryHelper::getData();

        $left  = Entry::fromArray($data);

        $right = array_merge($data, $overrides);
        $right = Entry::fromArray($right);

        $result = $left->equals($right);
        $this->assertFalse($result);
    }

    /**
     * Provides sets of valid entry data for testing Entry::fromArray().
     *
     * The first set uses base valid values from EntryHelper::getData() as-is.
     * The second set contains the same values surrounded with leading and trailing spaces
     * to verify that trimming logic is applied correctly.
     *
     * @return array<string, array{0: array<string,string>}> Named data sets with valid entry data.
     */
    public function provideValidEntries(): array
    {
        $data = EntryHelper::getData();

        return [
            'simple valid entry' => [$data],
            'trimmed inputs' => [[
                'title' => $this->surroundWithSpaces($data['title']),
                'body'  => $this->surroundWithSpaces($data['body']),
                'date'  => $this->surroundWithSpaces($data['date'])
            ]],
        ];
    }

    /**
     * Provides sets of invalid entry data overrides to trigger specific validation errors.
     *
     * Each set contains only the fields to override in the base valid data from EntryHelper::getData(),
     * leaving other fields unchanged in the test. These cases cover:
     * - Empty title, body, or date.
     * - Title exceeding EntryConstraints::TITLE_MAX.
     * - Body exceeding EntryConstraints::BODY_MAX.
     *
     * @return array<string, array{0: array<string,string>}> Named data sets with invalid entry field overrides.
     */
    public function provideInvalidEntries(): array
    {
        return [
            'empty title' => [[
                'title' => '',
            ]],
            'empty body' => [[
                'body' => '',
            ]],
            'empty date' => [[
                'date' => '',
            ]],
            'title too long' => [[
                'title' => str_repeat('a', EntryConstraints::TITLE_MAX + 1),
            ]],
            'body too long' => [[
                'body' => str_repeat('a', EntryConstraints::BODY_MAX + 1),
            ]],
        ];
    }

    /**
     * Provides field overrides that ensure equals() returns false.
     *
     * Starts from valid base data from EntryHelper::getData() and applies one or more
     * field changes to create a non-equal Entry. Each case changes only the necessary
     * field(s) to isolate the comparison logic being tested:
     * - Different title.
     * - Different body.
     * - Different date.
     *
     * @return array<string, array{0: array<string,string>}> Named data sets with field overrides causing inequality.
     */
    public function provideNonEqualOverrides(): array
    {
        /** @var array<string,string> $base */
        $base = EntryHelper::getData();

        return [
            'different title' => [[
                'title' => $base['title'] . ' (v2)',
            ]],
            'different body' => [[
                'body' => $base['body'] . ' (extended)',
            ]],
            'different date' => [[
                'date' => '2024-08-13',
            ]],
        ];
    }

    /**
     * Surrounds the given string with a single leading and trailing double-space.
     *
     * Useful for simulating trimmed input values in tests.
     *
     * @param string $value Original string value.
     * @return string String surrounded with double spaces on both sides.
     */
    private function surroundWithSpaces(string $value): string
    {
        $value = sprintf('  %s  ', $value);
        return $value;
    }    
}

