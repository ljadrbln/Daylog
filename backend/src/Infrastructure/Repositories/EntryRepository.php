<?php
declare(strict_types=1);

namespace Daylog\Infrastructure\Repositories;

use Daylog\Domain\Interfaces\EntryRepositoryInterface;
use Daylog\Domain\Interfaces\EntryStorageInterface;
use Daylog\Domain\Models\Entry;

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
    public function save(Entry $entry): string
    {
        $uuid = $this->storage->insert($entry);
        return $uuid;
    }
}
