<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Infrastructure\Repositories;

use Codeception\Test\Unit;
use Daylog\Domain\Models\Entries\Entry;
use Daylog\Infrastructure\Repositories\Entries\EntryRepository;
use Daylog\Tests\Support\Fakes\FakeEntryStorage;
use Daylog\Tests\Support\Helper\EntryTestData;

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
     * Purpose:
     * Verify that repository delegates persistence to storage and returns a domain Entry,
     * not an array payload.
     *
     * Mechanics:
     * - Use FakeEntryStorage to simulate persistence.
     * - storage->insert() returns predefined UUID and records the inserted Entry instance.
     * - Repository returns an Entry with id/title/body/date/createdAt/updatedAt filled.
     *
     * Assertions:
     * - Result is an Entry instance.
     * - Result id equals FakeEntryStorage::returnUuid.
     * - Result title/body/date equal to the original data.
     * - Storage insert is called exactly once.
     * - Storage lastInserted is the same Entry instance that we passed in.
     *
     * @return void
     * @covers \Daylog\Infrastructure\Repositories\Entries\EntryRepository::save
     */
    public function testSaveDelegatesToStorageAndReturnsPayload(): void
    {
        // Arrange 
        $storage = new FakeEntryStorage();
        $repo    = new EntryRepository($storage);

        $data  = EntryTestData::getOne();
        $entry = Entry::fromArray($data);

        // Act
        $result = $repo->save($entry);

        // Assert
        $id        = $result->getId();
        $title     = $result->getTitle();
        $body      = $result->getBody();
        $date      = $result->getDate();
        $createdAt = $result->getCreatedAt();
        $updatedAt = $result->getUpdatedAt();

        $this->assertSame($storage->returnUuid, $id);
        $this->assertSame($data['title'],       $title);
        $this->assertSame($data['body'],        $body);
        $this->assertSame($data['date'],        $date);

        $this->assertNotEmpty($createdAt);
        $this->assertNotEmpty($updatedAt);

        $this->assertSame(1, $storage->insertCalls);
        $this->assertSame($entry, $storage->lastInserted);
    }
}