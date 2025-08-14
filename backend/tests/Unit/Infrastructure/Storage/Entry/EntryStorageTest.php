<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Infrastructure\Storage\Entry;

use Codeception\Test\Unit;
use Daylog\Domain\Models\Entry;
use Daylog\Domain\Services\UuidGenerator;
use Daylog\Infrastructure\Storage\Entry\EntryStorage;
use Daylog\Tests\Support\Helper\EntryHelper;

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

        $data  = EntryHelper::getData();
        $entry = Entry::fromArray($data);

        $uuid = $storage->insert($entry);

        $isValid = UuidGenerator::isValid($uuid);
        $this->assertTrue($isValid);
    }
}
