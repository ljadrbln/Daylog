<?php
declare(strict_types=1);

namespace Daylog\Tests\Support\Fakes;

use Daylog\Domain\Interfaces\Entries\EntryRepositoryInterface;
use Daylog\Domain\Models\Entries\Entry;
use Daylog\Domain\Models\Entries\ListEntriesCriteria;

/**
 * Fake implementation of EntryRepositoryInterface for tests.
 *
 * Stores entries in memory and allows inspection of save() calls.
 */
final class FakeEntryRepository implements EntryRepositoryInterface
{
    /** @var Entry[] */
    private array $entries = [];

    /** @var int */
    private int $saveCalls = 0;

    /**
     * Save the given entry into in-memory storage.
     *
     * Increments the save counter, stores the entry, and
     * returns the same Entry instance.
     *
     * @param Entry $entry Entry to save
     * @return Entry Same entry
     */
    public function save(Entry $entry): Entry
    {
        $this->entries[] = $entry;
        $this->saveCalls++;

        return $entry;
    }

    /**
     * Get the number of times save() has been called.
     *
     * @return int
     */
    public function getSaveCalls(): int
    {
        return $this->saveCalls;
    }

    /**
     * Get the last Entry instance saved, or null if none.
     *
     * @return Entry|null
     */
    public function getLastSaved(): ?Entry
    {
        if ($this->entries === []) {
            return null;
        }

        $last = end($this->entries);
        return $last;
    }

    /**
     * Find entries by domain criteria.
     *
     * Simplified: applies stable sort by date DESC, then createdAt DESC,
     * then applies pagination. Filtering by query/date not implemented.
     *
     * @param ListEntriesCriteria $criteria
     * @return array{
     *     items:      list<Entry>,
     *     total:      int,
     *     page:       int,
     *     perPage:    int,
     *     pagesCount: int
     * }
     */
    public function findByCriteria(ListEntriesCriteria $criteria): array
    {
        $page    = $criteria->getPage();
        $perPage = $criteria->getPerPage();

        // Use all entries as pool
        $pool = $this->entries;

        // Sorting: date DESC, then createdAt DESC (stable)
        usort($pool, function (Entry $a, Entry $b): int {
            $primary = strcmp($b->getDate(), $a->getDate());
            if ($primary !== 0) {
                return $primary;
            }

            return strcmp($b->getCreatedAt(), $a->getCreatedAt());
        });

        // Pagination
        $total   = count($pool);
        $offset  = max(0, ($page - 1) * $perPage);
        $slice   = array_slice($pool, $offset, $perPage);
        $pagesCount = $perPage > 0 ? (int)ceil($total / $perPage) : 0;

        return [
            'items'      => array_values($slice),
            'total'      => $total,
            'page'       => $page,
            'perPage'    => $perPage,
            'pagesCount' => $pagesCount,
        ];
    }
}
