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
     * @dataProvider provideValidEntries
     */
    public function testCreateValidEntryFromData(array $data): void
    {
        $entry = Entry::fromArray($data);

        $this->assertSame(trim($data['title']), $entry->getTitle());
        $this->assertSame(trim($data['body']), $entry->getBody());
        $this->assertSame(trim($data['date']), $entry->getDate());
    }

    /**
     * @return array<string, array{0:array<string,string>}>
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
     * @dataProvider provideInvalidEntries
     */
    public function testInvalidEntriesThrow(array $overrides): void
    {
        $this->expectException(ValidationException::class);

        $data = EntryHelper::getData();
        $data = array_merge($data, $overrides);

        Entry::fromArray($data);
    }

    /**
     * @return array<string, array{0:array<string,string>}>
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
     * @covers \Daylog\Domain\Models\Entry::fromArray
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
     * @covers \Daylog\Domain\Models\Entry::equals
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
     * @dataProvider provideNonEqualOverrides
     * @covers \Daylog\Domain\Models\Entry::equals
     *
     * @param array<string,string> $overrides
     */
    public function testEqualsReturnsFalseWhenAnyFieldDiffers(array $overrides): void
    {
        /** @var array<string,string> $base */
        $data = EntryHelper::getData();

        $left  = Entry::fromArray($data);

        $right = array_merge($data, $overrides);
        $right = Entry::fromArray($right);

        $result = $left->equals($right);
        $this->assertFalse($result);
    }

    /**
     * Provides field overrides that must make equals() return false.
     *
     * @return array<string, array{0: array<string,string>}>
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

