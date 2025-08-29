<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Infrastructure\Storage\Entries;

use Codeception\Test\Unit;
use Daylog\Domain\Models\Entries\Entry;
use Daylog\Infrastructure\Storage\Entries\EntryModel;
use Daylog\Infrastructure\Storage\Entries\EntryStorage;
use Daylog\Domain\Services\UuidGenerator;
use Daylog\Tests\Support\Helper\EntryTestData;

/**
 * Class EntryStorageTest
 *
 * Verifies that EntryStorage::insert() generates a UUID v4
 * and delegates persistence to EntryModel::create().
 *
 * Scenario:
 * - Arrange: mock EntryModel, build Entry from helper, define $now.
 * - Act: call insert($entry, $now).
 * - Assert: returned UUID is valid v4; model->create() called once with array payload.
 *
 * @covers \Daylog\Infrastructure\Storage\Entries\EntryStorage::insert
 */
final class EntryStorageTest extends Unit
{
    /**
     * Ensure insert() generates UUID v4 and delegates to model->create().
     *
     * @return void
     */
    public function testInsertReturnsValidUuidV4(): void
    {
        // Arrange
        $modelClass = EntryModel::class;
        $model      = $this->createMock($modelClass);

        $model
            ->expects($this->once())
            ->method('create')
            ->with($this->callback(function (array $payload): bool {
                // ensure payload has a UUID v4
                return isset($payload['id']) && UuidGenerator::isValid($payload['id']);
            }));

        $storage = new EntryStorage($model);

        $data  = EntryTestData::getOne();
        $entry = Entry::fromArray($data);

        // Act
        $storage->insert($entry);

        // Assert
        $isValid = UuidGenerator::isValid($entry->getId());
        $this->assertTrue($isValid);
    }
}

