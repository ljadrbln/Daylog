<?php
declare(strict_types=1);

namespace Daylog\Tests\Support\Fakes;

use Daylog\Domain\Models\Entries\ListEntriesCriteria;
use Daylog\Domain\Interfaces\Entries\EntryRepositoryInterface;
use Daylog\Domain\Models\Entries\Entry;
use Daylog\Domain\Services\UuidGenerator;
use Daylog\Infrastructure\Utils\Clock;

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
     * Saves the given entry to the in-memory storage.
     *
     * Increments the save counter, stores the last saved entry,
     * and returns an array with:
     *  - id (string)       — generated UUID v4
     *  - title (string)    — entry title
     *  - body (string)     — entry body
     *  - date (string)     — entry date
     *  - createdAt (string)— ISO-8601 timestamp
     *  - updatedAt (string)— ISO-8601 timestamp
     *
     * @param Entry $entry Entry to save
     * @return array<string,string> Saved entry data
     */
    public function save(Entry $entry): array
    {
        $this->entries[]  = $entry;
        $this->saveCalls++;

        $uuid = UuidGenerator::generate();
        $now  = Clock::now();

        $result = [
            'id'        => $uuid,
            'title'     => $entry->getTitle(),
            'body'      => $entry->getBody(),
            'date'      => $entry->getDate(),
            'createdAt' => $now,
            'updatedAt' => $now
        ];
        
        return $result;
    }

    /**
     * Returns the number of times save() has been called.
     *
     * @return int
     */
    public function getSaveCalls(): int
    {
        return $this->saveCalls;
    }

    /**
     * Returns the last Entry instance passed to save(), or null if none.
     *
     * @return Entry|null
     */
    public function getLastSaved(): ?Entry
    {
        if (empty($this->entries)) {
            return null;
        }

        return end($this->entries);
    }

    /**
     * Find entries by Domain Criteria.
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

        // 1) (Optional) Filters — включи при необходимости:
        $pool = $this->items;

        // 2) Sorting: date DESC, then createdAt DESC (stable)
        $cmp = function (Entry $a, Entry $b): int {
            $dateA = $a->getDate();
            $dateB = $b->getDate();

            $primary = strcmp($dateB, $dateA);
            if ($primary !== 0) {
                $result = $primary;
                return $result;
            }

            $createdA = $a->getCreatedAt();
            $createdB = $b->getCreatedAt();

            $secondary = strcmp($createdB, $createdA);
            $result    = $secondary;
            return $result;
        };

        usort($pool, $cmp);

        // 3) Pagination
        $total   = count($pool);
        $offset  = ($page - 1) * $perPage;
        if ($offset < 0) {
            $offset = 0;
        }

        $slice       = array_slice($pool, $offset, $perPage);
        $pagesCount  = $perPage > 0 ? (int)ceil($total / $perPage) : 0;

        $items = array_values($slice);

        $result = [
            'items'      => $items,
            'total'      => $total,
            'page'       => $page,
            'perPage'    => $perPage,
            'pagesCount' => $pagesCount,
        ];

        return $result;
    }

}