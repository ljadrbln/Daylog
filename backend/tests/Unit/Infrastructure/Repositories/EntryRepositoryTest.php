<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Infrastructure\Repositories;

use Codeception\Test\Unit;
use Daylog\Domain\Models\Entry;
use Daylog\Infrastructure\Repositories\EntryRepository;
use Daylog\Tests\Support\Fakes\FakeEntryStorage;

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

        $data = [
            'title' => 'Valid title',
            'body'  => 'Valid body',
            'date'  => '2025-08-13',
        ];

        $entry = Entry::fromArray($data);

        $uuid = $repo->save($entry);

        $this->assertSame($storage->returnUuid, $uuid);
        $this->assertSame(1, $storage->insertCalls);
        $this->assertSame($entry, $storage->lastInserted);
    }
}