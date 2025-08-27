<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Infrastructure\Storage\Entries;

use Codeception\Test\Unit;
use Daylog\Infrastructure\Storage\Entries\EntryFieldMapper;

/**
 * Class EntryFieldMapperTest
 *
 * Verifies the bidirectional field/row mapping between domain/application camelCase
 * and database snake_case. The mapper is a deterministic, pure transformation layer:
 * no IO, no side-effects. Tests cover:
 *  - single-field mapping to DB and back (unknown keys pass through);
 *  - full-row mapping to DTO (fromDbRow) and to DB (toDbRow);
 *  - symmetry/involution property on rows (toDbRow(fromDbRow($dbRow)) === $dbRow).
 *
 * @covers \Daylog\Infrastructure\Storage\Entries\EntryFieldMapper
 */
final class EntryFieldMapperTest extends Unit
{
    /**
     * @return array<string, array{camel:string, snake:string}>
     */
    public static function fieldPairsProvider(): array
    {
        $cases = [
            'id'        => ['camel' => 'id',        'snake' => 'id'],
            'date'      => ['camel' => 'date',      'snake' => 'date'],
            'title'     => ['camel' => 'title',     'snake' => 'title'],
            'body'      => ['camel' => 'body',      'snake' => 'body'],
            'createdAt' => ['camel' => 'createdAt', 'snake' => 'created_at'],
            'updatedAt' => ['camel' => 'updatedAt', 'snake' => 'updated_at'],
        ];

        return $cases;
    }

    /**
     * Ensure known camelCase fields are mapped to snake_case DB fields.
     *
     * @dataProvider fieldPairsProvider
     * @param string $camel
     * @param string $snake
     * @return void
     */
    public function testToDbFieldMapsKnownKeys(string $camel, string $snake): void
    {
        $result = EntryFieldMapper::toDbField($camel);

        $this->assertSame($snake, $result);
    }

    /**
     * Ensure unknown camelCase fields pass through unchanged in toDbField().
     *
     * @return void
     */
    public function testToDbFieldPassesUnknownKeysThrough(): void
    {
        $unknown = 'someCustomField';
        $result  = EntryFieldMapper::toDbField($unknown);

        $this->assertSame($unknown, $result);
    }

    /**
     * Ensure known snake_case DB fields are mapped back to camelCase.
     *
     * @dataProvider fieldPairsProvider
     * @param string $camel
     * @param string $snake
     * @return void
     */
    public function testFromDbFieldMapsKnownKeys(string $camel, string $snake): void
    {
        $result = EntryFieldMapper::fromDbField($snake);

        $this->assertSame($camel, $result);
    }

    /**
     * Ensure unknown snake_case fields pass through unchanged in fromDbField().
     *
     * @return void
     */
    public function testFromDbFieldPassesUnknownKeysThrough(): void
    {
        $unknown = 'extra_db_column';
        $result  = EntryFieldMapper::fromDbField($unknown);

        $this->assertSame($unknown, $result);
    }

    /**
     * Map a full DB row (snake_case) to DTO shape (camelCase).
     *
     * Scenarios:
     * - All required keys present => each converted as expected.
     * - Extra unknown keys preserved and re-keyed via fromDbField (pass-through).
     *
     * @return void
     */
    public function testFromDbRowMapsAllKnownKeys(): void
    {
        /** @var array{id:string,date:string,title:string,body:string,created_at:string,updated_at:string,extra_db_column?:string} $dbRow */
        $dbRow = [
            'id'         => 'aaaaaaaa-bbbb-4ccc-8ddd-eeeeeeeeeeee',
            'date'       => '2025-08-26',
            'title'      => 'Valid title',
            'body'       => 'Valid body',
            'created_at' => '2025-08-26T10:00:00Z',
            'updated_at' => '2025-08-26T10:00:00Z',
            'extra_db_column' => 'kept',
        ];

        $mapped = EntryFieldMapper::fromDbRow($dbRow);

        $this->assertSame($dbRow['id'],         $mapped['id']);
        $this->assertSame($dbRow['date'],       $mapped['date']);
        $this->assertSame($dbRow['title'],      $mapped['title']);
        $this->assertSame($dbRow['body'],       $mapped['body']);
        $this->assertSame($dbRow['created_at'], $mapped['createdAt']);
        $this->assertSame($dbRow['updated_at'], $mapped['updatedAt']);

        // Unknown key passes through with same name (fromDbField fallback)
        $this->assertArrayHasKey('extra_db_column', $mapped);
        $this->assertSame('kept', $mapped['extra_db_column']);
    }

    /**
     * Map a full DTO row (camelCase) to DB shape (snake_case).
     *
     * Scenarios:
     * - All known keys converted to snake_case.
     * - Unknown keys preserved via toDbField pass-through.
     *
     * @return void
     */
    public function testToDbRowMapsAllKnownKeys(): void
    {
        /** @var array{id:string,date:string,title:string,body:string,createdAt:string,updatedAt:string,someCustomField?:string} $dtoRow */
        $dtoRow = [
            'id'            => 'aaaaaaaa-bbbb-4ccc-8ddd-eeeeeeeeeeee',
            'date'          => '2025-08-26',
            'title'         => 'Valid title',
            'body'          => 'Valid body',
            'createdAt'     => '2025-08-26T10:00:00Z',
            'updatedAt'     => '2025-08-26T10:00:00Z',
            'someCustomField' => 'keep',
        ];

        $mapped = EntryFieldMapper::toDbRow($dtoRow);

        $this->assertSame($dtoRow['id'],        $mapped['id']);
        $this->assertSame($dtoRow['date'],      $mapped['date']);
        $this->assertSame($dtoRow['title'],     $mapped['title']);
        $this->assertSame($dtoRow['body'],      $mapped['body']);
        $this->assertSame($dtoRow['createdAt'], $mapped['created_at']);
        $this->assertSame($dtoRow['updatedAt'], $mapped['updated_at']);

        // Unknown field passes through unchanged in name
        $this->assertArrayHasKey('someCustomField', $mapped);
        $this->assertSame('keep', $mapped['someCustomField']);
    }

    /**
     * Symmetry property: toDbRow(fromDbRow($dbRow)) === $dbRow for known keys.
     *
     * Mechanics:
     * - Start from snake_case row;
     * - Map to camelCase via fromDbRow();
     * - Map back via toDbRow();
     * - Expect exactly the original shape for all known keys.
     *
     * @return void
     */
    public function testRowMappingIsSymmetricForKnownKeys(): void
    {
        $dbRow = [
            'id'         => 'aaaaaaaa-bbbb-4ccc-8ddd-eeeeeeeeeeee',
            'date'       => '2025-08-26',
            'title'      => 'Valid title',
            'body'       => 'Valid body',
            'created_at' => '2025-08-26T10:00:00Z',
            'updated_at' => '2025-08-26T10:00:00Z',
        ];

        $camel = EntryFieldMapper::fromDbRow($dbRow);
        $back  = EntryFieldMapper::toDbRow($camel);

        $this->assertSame($dbRow, $back);
    }
}
