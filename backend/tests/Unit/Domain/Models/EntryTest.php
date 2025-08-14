<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Domain\Models;

use Codeception\Test\Unit;

use Daylog\Domain\Models\Entry;
use Daylog\Domain\Errors\ValidationException;
use Daylog\Domain\Models\EntryConstraints;

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
        return [
            'simple valid entry' => [[
                'title' => 'My first entry',
                'body'  => 'Meaningful body text.',
                'date'  => '2025-08-12',
            ]],
            'trimmed inputs' => [[
                'title' => '  Trimmed  ',
                'body'  => '  Trimmed body  ',
                'date'  => '2025-08-12',
            ]],
        ];
    }

    /**
     * @dataProvider provideInvalidEntries
     */
    public function testInvalidEntriesThrow(array $data): void
    {
        $this->expectException(ValidationException::class);
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
                'body'  => 'Body is present',
                'date'  => '2025-08-12',
            ]],
            'empty body' => [[
                'title' => 'Valid title',
                'body'  => '',
                'date'  => '2025-08-12',
            ]],
            'empty date' => [[
                'title' => 'Valid title',
                'body'  => 'Valid body',
                'date'  => '',
            ]],
            'title too long' => [[
                'title' => str_repeat('a', EntryConstraints::TITLE_MAX + 1),
                'body'  => 'Valid body',
                'date'  => '2025-08-12',
            ]],
            'body too long' => [[
                'title' => 'Valid title',
                'body'  => str_repeat('a', EntryConstraints::BODY_MAX + 1),
                'date'  => '2025-08-12',
            ]],
        ];
    }

    /**
     * @covers \Daylog\Domain\Models\Entry::fromArray
     */
    public function testCreateEntryFromArray(): void
    {
        $data = [
            'title' => 'Array title',
            'body'  => 'Array body',
            'date'  => '2025-08-14',
        ];

        $entry = Entry::fromArray($data);

        $this->assertSame($data['title'], $entry->getTitle());
        $this->assertSame($data['body'], $entry->getBody());
        $this->assertSame($data['date'], $entry->getDate());
    }
}

