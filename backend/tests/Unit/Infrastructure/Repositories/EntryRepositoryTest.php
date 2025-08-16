<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Infrastructure\Repositories;

use Codeception\Test\Unit;
use Daylog\Domain\Models\Entries\Entry;
use Daylog\Infrastructure\Repositories\Entries\EntryRepository;
use Daylog\Tests\Support\Fakes\FakeEntryStorage;
use Daylog\Tests\Support\Helper\EntryHelper;

/**
 * Class EntryRepositoryTest
 *
 * Verifies that repository delegates to storage and returns UUID.
 */
final class EntryRepositoryTest extends Unit
{
    public function testSaveDelegatesToStorageAndReturnsUuid(): void
    {
        $storage = new FakeEntryStorage();

        $repoClass = EntryRepository::class;
        $repo      = new $repoClass($storage);

        $data  = EntryHelper::getData();
        $entry = Entry::fromArray($data);

        $uuid = $repo->save($entry);

        $this->assertSame($storage->returnUuid, $uuid);
        $this->assertSame(1, $storage->insertCalls);
        $this->assertSame($entry, $storage->lastInserted);
    }
}