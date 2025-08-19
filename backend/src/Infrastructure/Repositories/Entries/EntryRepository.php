<?php
declare(strict_types=1);

namespace Daylog\Infrastructure\Repositories\Entries;

use Daylog\Domain\Interfaces\Entries\EntryRepositoryInterface;
use Daylog\Domain\Interfaces\Entries\EntryStorageInterface;
use Daylog\Domain\Models\Entries\Entry;
use Daylog\Infrastructure\Utils\Clock;
/**
 * Class EntryRepository
 *
 * Repository orchestrates persistence via storage adapter.
 */
final class EntryRepository implements EntryRepositoryInterface
{
    /** @var EntryStorageInterface */
    private EntryStorageInterface $storage;

    /**
     * EntryRepository constructor.
     *
     * @param EntryStorageInterface $storage Concrete storage implementation.
     */
    public function __construct(EntryStorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @inheritDoc
     */
    public function save(Entry $entry): array
    {
        $now  = Clock::now();
        $uuid = $this->storage->insert($entry);

        return [
            'id'        => $uuid,
            'date'      => $entry->getDate(),
            'title'     => $entry->getTitle(),
            'body'      => $entry->getBody(),
            'createdAt' => $now,
            'updatedAt' => $now
        ];
    }
    
    /**
     * Fetches all entries from storage.
     *
     * @return Entry[]
     */
    public function fetchAll(): array
    {
        $result = $this->storage->fetchAll();
        return $result;
    }    
}