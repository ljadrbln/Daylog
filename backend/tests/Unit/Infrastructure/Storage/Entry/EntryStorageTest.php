<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Infrastructure\Storage\Entry;

use Codeception\Test\Unit;
use Daylog\Domain\Models\Entry;
use Daylog\Infrastructure\Storage\Entry\EntryStorage;

/**
 * Class EntryStorageTest
 *
 * Verifies that storage returns a valid UUID v4 on insert().
 */
final class EntryStorageTest extends Unit
{
    public function testInsertReturnsValidUuidV4(): void
    {
        /** @var EntryStorage $storage */
        $storageClass = EntryStorage::class;
        $storage      = new $storageClass();

        $data = [
            'title' => 'Valid title',
            'body'  => 'Valid body',
            'date'  => '2025-08-13',
        ];

        $entry = Entry::fromArray($data);

        $uuid = $storage->insert($entry);

        $pattern = '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i';
        $this->assertSame(1, preg_match($pattern, $uuid));
    }
}
