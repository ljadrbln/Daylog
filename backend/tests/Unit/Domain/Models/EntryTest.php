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
     * Ensures a valid Entry can be created with title, body, and date.
     *
     * @return void
     */
    public function testCreateValidEntry(): void
    {
        $title = 'My first entry';
        $body  = 'Meaningful body text.';
        $date  = '2025-08-12';

        $entry = new Entry($title, $body, $date);

        $this->assertSame($title, $entry->getTitle());
        $this->assertSame($body, $entry->getBody());
        $this->assertSame($date, $entry->getDate());
    }

    /**
     * Title must not be empty after trimming.
     *
     * @return void
     */
    public function testEmptyTitleThrows(): void
    {
        $title = '';
        $body  = 'Body is present';
        $date  = '2025-08-12';

        $this->expectException(ValidationException::class);
        new Entry($title, $body, $date);
    }

    /**
     * Title must not exceed TITLE_MAX characters.
     *
     * @return void
     */
    public function testTooLongTitleThrows(): void
    {
        $title = str_repeat('T', EntryConstraints::TITLE_MAX + 1);
        $body  = 'Body is present';
        $date  = '2025-08-12';

        $this->expectException(ValidationException::class);
        new Entry($title, $body, $date);
    }

    /**
     * Body must not be empty after trimming.
     *
     * @return void
     */
    public function testEmptyBodyThrows(): void
    {
        $title = 'Valid title';
        $body  = '';
        $date  = '2025-08-12';

        $this->expectException(ValidationException::class);
        new Entry($title, $body, $date);
    }

    /**
     * Body must not exceed BODY_MAX characters.
     *
     * @return void
     */
    public function testTooLongBodyThrows(): void
    {
        $title = 'Valid title';
        $body  = str_repeat('B', EntryConstraints::BODY_MAX + 1);
        $date  = '2025-08-12';

        $this->expectException(ValidationException::class);
        new Entry($title, $body, $date);
    }

    /**
     * Date must be provided.
     *
     * @return void
     */
    public function testMissingDateThrows(): void
    {
        $title = 'Valid title';
        $body  = 'Valid body';
        $date  = '';

        $this->expectException(ValidationException::class);
        new Entry($title, $body, $date);
    }

    /**
     * Date must match YYYY-MM-DD format.
     *
     * @return void
     */
    public function testInvalidDateFormatThrows(): void
    {
        $title = 'Valid title';
        $body  = 'Valid body';
        $date  = '12-08-2025';

        $this->expectException(ValidationException::class);
        new Entry($title, $body, $date);
    }

    /**
     * Date must be a valid calendar date.
     *
     * @return void
     */
    public function testInvalidCalendarDateThrows(): void
    {
        $title = 'Valid title';
        $body  = 'Valid body';
        $date  = '2025-02-30';

        $this->expectException(ValidationException::class);
        new Entry($title, $body, $date);
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

