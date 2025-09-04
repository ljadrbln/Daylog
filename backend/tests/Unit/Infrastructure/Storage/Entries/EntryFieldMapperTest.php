<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Infrastructure\Storage\Entries;

use Codeception\Test\Unit;
use Daylog\Domain\Models\Entries\Entry;
use Daylog\Domain\Services\DateService;
use Daylog\Infrastructure\Storage\Entries\EntryFieldMapper;
use Daylog\Tests\Support\Helper\EntryTestData;

/**
 * Unit tests for EntryFieldMapper (minimal API).
 *
 * Purpose:
 * Validate two transformations only:
 *  1) DB row (snake_case, SQL datetime 'Y-m-d H:i:s') → camelCase with ISO-8601 UTC.
 *  2) Domain Entry → DB row (snake_case) without implicit mutation.
 *
 * Mechanics:
 * - fromDbRow(): created_at/updated_at MUST be converted to 'Y-m-d\TH:i:sP' (+00:00).
 * - toDbRowFromEntry(): field names mapped 1:1; values passed as-is.
 *
 * @covers \Daylog\Infrastructure\Storage\Entries\EntryFieldMapper
 */
final class EntryFieldMapperTest extends Unit
{
    /**
     * Ensure fromDbRow() converts SQL DATETIME to ISO-8601 UTC and maps keys to camelCase.
     *
     * Cases:
     * - created_at/updated_at given as 'Y-m-d H:i:s' (UTC) → 'Y-m-d\TH:i:sP' (+00:00).
     * - id/date/title/body passed through unchanged.
     *
     * @return void
     */
    public function testFromDbRowConvertsTimestampsAndMapsKeys(): void
    {
        // Arrange
        $data  = EntryTestData::getOne();
        $entry = Entry::fromArray($data);
        $dbRow = EntryFieldMapper::toDbRowFromEntry($entry);

        // Act
        $mapped = EntryFieldMapper::fromDbRow($dbRow);

        // Assert (values unchanged for plain fields)
        $this->assertSame($dbRow['id'],    $mapped['id']);
        $this->assertSame($dbRow['title'], $mapped['title']);
        $this->assertSame($dbRow['body'],  $mapped['body']);
        $this->assertSame($dbRow['date'],  $mapped['date']);

        // Assert (timestamps converted to ISO-8601 UTC)
        $createdAt = $mapped['createdAt'];
        $isCreatedAtValid = DateService::isValidIsoUtcDateTime($createdAt);
        $this->assertTrue($isCreatedAtValid);

        $updatedAt = $mapped['updatedAt'];
        $isUpdatedAtValid = DateService::isValidIsoUtcDateTime($updatedAt);
        $this->assertTrue($isUpdatedAtValid);
    }

    /**
     * Ensure toDbRowFromEntry() maps a domain Entry to snake_case DB row as-is.
     *
     * Mechanics:
     * - Prepare a simple stub Entry with getters.
     * - Mapper must copy values verbatim and rename keys to snake_case.
     *
     * @return void
     */
    public function testToDbRowFromEntryMapsFieldsVerbatim(): void
    {
        // Arrange
        $data  = EntryTestData::getOne();
        $entry = Entry::fromArray($data);

        // Act
        $row = EntryFieldMapper::toDbRowFromEntry($entry);

        // Assert
        $this->assertSame($data['id'],        $row['id']);
        $this->assertSame($data['title'],     $row['title']);
        $this->assertSame($data['body'],      $row['body']);
        $this->assertSame($data['date'],      $row['date']);
        $this->assertSame($data['createdAt'], $row['created_at']);
        $this->assertSame($data['updatedAt'], $row['updated_at']);
    }
}
