<?php
declare(strict_types=1);

namespace Daylog\Infrastructure\Repositories\Entries;

use Daylog\Domain\Models\Entries\ListEntriesCriteria;
use Daylog\Domain\Interfaces\Entries\EntryRepositoryInterface;
use Daylog\Domain\Interfaces\Entries\EntryStorageInterface;
use Daylog\Domain\Models\Entries\Entry;
use Daylog\Domain\Services\Clock;


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

    /** @inheritDoc */
    public function save(Entry $entry): array
    {
        $now  = Clock::now();
        $uuid = $this->storage->insert($entry, $now);

        $entry = [
            'id'        => $uuid,
            'date'      => $entry->getDate(),
            'title'     => $entry->getTitle(),
            'body'      => $entry->getBody(),
            'createdAt' => $now,
            'updatedAt' => $now
        ];

        return $entry;
    }

    /**
     * Fetch a page of entries by criteria (UC-2).
     *
     * Mechanics:
     * - Delegates to storage which applies filters (date/dateFrom/dateTo/query),
     *   sorting (field + direction with stable secondary order), and pagination
     *   (LIMIT/OFFSET) directly in SQL for performance and determinism.
     * - Returns result as an associative array with page metadata.
     *
     * Expected result shape:
     * - items: Entry[] mapped to arrays for presentation boundary
     * - total: int total number of matching items (without pagination)
     * - page: int current page (echo from criteria)
     * - perPage: int current page size (echo from criteria)
     * - pagesCount: int ceil(total / perPage)
     *
     * @param ListEntriesCriteria $criteria Normalized criteria (validated upstream).
     * @return array{
     *     items: list<Entry>,
     *     total: int,
     *     page: int,
     *     perPage: int,
     *     pagesCount: int
     * }
     */
    public function findByCriteria(ListEntriesCriteria $criteria): array
    {
        $result = $this->storage->findByCriteria($criteria);

        $rawItems = array_values($result['items']);

        /** @var list<Entry> $entries */
        $entries = [];

        for($i=0; $i<count($rawItems); $i++) {
            $item = $rawItems[$i];
            $item = Entry::fromArray($item);

            $entries[] = $item;
        }

        $result['items'] = $entries;

        return $result;
    }
}