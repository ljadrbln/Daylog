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
    /**
     * Unit test: EntryRepository::save()
     *
     * Scenario:
     * - We use FakeEntryStorage to simulate persistence layer.
     * - Storage::insert() returns a predefined UUID and records the inserted entry.
     * - Repository::save() must:
     *   1) delegate to storage,
     *   2) return a payload array containing id, title, body, date, createdAt, updatedAt,
     *   3) increment storage->insertCalls and set storage->lastInserted.
     *
     * Assertions:
     * - Payload is an array with expected keys.
     * - Returned id equals FakeEntryStorage::returnUuid.
     * - insertCalls is incremented exactly once.
     * - lastInserted entry equals the original Entry.
     *
     * @return void
     * @covers \Daylog\Infrastructure\Repositories\Entries\EntryRepository::save
     */
    public function testSaveDelegatesToStorageAndReturnsPayload(): void
    {
        $storage = new FakeEntryStorage();
        $repo    = new EntryRepository($storage);

        $data  = EntryHelper::getData();
        $entry = Entry::fromArray($data);

        $result = $repo->save($entry);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('id',        $result);
        $this->assertArrayHasKey('title',     $result);
        $this->assertArrayHasKey('body',      $result);
        $this->assertArrayHasKey('date',      $result);
        $this->assertArrayHasKey('createdAt', $result);
        $this->assertArrayHasKey('updatedAt', $result);

        $this->assertSame($storage->returnUuid, $result['id']);
        $this->assertSame($data['title'],       $result['title']);
        $this->assertSame($data['body'],        $result['body']);
        $this->assertSame($data['date'],        $result['date']);

        $this->assertSame(1, $storage->insertCalls);
        $this->assertSame($entry, $storage->lastInserted);
    }
}