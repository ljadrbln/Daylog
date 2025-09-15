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
     * Get saved entry by given id from in-memory storage.
     * 
     * @param String $id Target entry id
     * @return Entry|null Same entry
     */
    public function findById(string $id): ?Entry
    {
        foreach ($this->entries as $entry) {
            if ($entry->getId() === $id) {
                return $entry;
            }
        }

        return null;
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

        // Filtering
        $target = $criteria->getDate();
        $from   = $criteria->getDateFrom();
        $to     = $criteria->getDateTo();
        $query  = $criteria->getQuery();

        $pool = array_filter($this->entries, function (Entry $e) use ($from, $to, $target, $query): bool {
            $date = $e->getDate();
            
            if ($from !== null && $date < $from) {
                return false;
            }

            if ($to !== null && $date > $to) {
                return false;
            }

            if ($target !== null && $date !== $target) {
                return false;
            }

            if ($query !== null) {
                $q = strtolower($query);

                $title = $e->getTitle();
                $body  = $e->getBody();

                $titleLower = strtolower($title);
                $bodyLower  = strtolower($body);

                $posInTitle = strpos($titleLower, $q);
                $posInBody  = strpos($bodyLower, $q);

                $matchInTitle = ($posInTitle !== false);
                $matchInBody  = ($posInBody !== false);

                $matches = $matchInTitle || $matchInBody;
                if (!$matches) {
                    return false;
                }
            }

            return true;
        });        

        // Sorting: date DESC, then createdAt DESC (stable)
        usort($pool, self::makeComparator($criteria));

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

    /**
     * Delete by UUID.
     *
     * @param string $id
     * @return void
     */
    public function deleteById(string $id): void
    {
        $indexToRemove = null;

        foreach ($this->entries as $index => $entry) {
            $entryId = $entry->getId();
            if ($entryId === $id) {
                $indexToRemove = $index;
                break;
            }
        }

        $notFound = $indexToRemove === null;
        if ($notFound) {
            return;
        }

        unset($this->entries[$indexToRemove]);

        // keep sequential indexing for predictable order
        $reindexed = array_values($this->entries);
        $this->entries = $reindexed;
    }

    /**
     * Build comparator for Entries.
     *
     * Purpose:
     * Sorts by $criteria->sortField (ASC|DESC). If values are equal, applies
     * stable secondary order: createdAt DESC.
     *
     * Usage:
     * Pass result directly to usort() for UC-2 ListEntries.
     *
     * @param ListEntriesCriteria $criteria
     * @return callable(Entry,Entry):int
     */
    public static function makeComparator(ListEntriesCriteria $criteria): callable
    {
        $sortDescriptior = $criteria->getSortDescriptor();

        $sortField = $sortDescriptior[0]['field'];
        $sortDir   = $sortDescriptior[0]['direction'];

        return static function (Entry $a, Entry $b) use ($sortField, $sortDir): int {

            // Primary key as ISO-like strings to keep lexicographic order time-safe
            $aPrimary = $sortField === 'date'
                ? $a->getDate()
                : ($sortField === 'createdAt' 
                    ? $a->getCreatedAt() 
                    : $a->getUpdatedAt()
                );

            $bPrimary = $sortField === 'date'
                ? $b->getDate()
                : ($sortField === 'createdAt' 
                    ? $b->getCreatedAt() 
                    : $b->getUpdatedAt());

            $cmp = strcmp($aPrimary, $bPrimary);

            if ($sortDir === 'DESC') {
                $cmp = -$cmp;
            }

            if ($cmp === 0) {
                // Stable secondary: createdAt DESC (independent of $dir)
                $aCreated = $a->getCreatedAt();
                $bCreated = $b->getCreatedAt();

                $cmp = strcmp($bCreated, $aCreated); // DESC
            }

            return $cmp;
        };
    }
}