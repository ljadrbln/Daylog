<?php
declare(strict_types=1);

namespace Daylog\Tests\Support\Fakes;

use Daylog\Domain\Interfaces\Entries\EntryStorageInterface;
use Daylog\Domain\Models\Entries\Entry;
use Daylog\Domain\Models\Entries\ListEntriesCriteria;

/**
 * FakeEntryStorage
 *
 * In-memory storage double for repository unit tests.
 * Records last inserted entry and returns a preconfigured UUID.
 */
final class FakeEntryStorage implements EntryStorageInterface
{
    /** @var Entry|null */
    public ?Entry $lastInserted = null;

    /** @var int */
    public int $insertCalls = 0;

    /** @var string */
    public string $returnUuid = '11111111-1111-1111-1111-111111111111';

    /** @inheritDoc */
    public function insert(Entry $entry, string $now): string
    {
        $this->insertCalls++;
        $this->lastInserted = $entry;

        $uuid = $this->returnUuid;
        return $uuid;
    }

    /** 
     * {@inheritDoc} 
     * 
     * This fake ignores all criteria and always returns an empty page
     * with correct pagination metadata (items=[], total=0, page=1, perPage=20, pagesCount=0).
     * Useful for unit tests that only assert interaction with storage,
     * not actual filtering or pagination logic.
     */
    public function findByCriteria(ListEntriesCriteria $criteria): array
    {
        /** @var array<int, array{
         *   id: string,
         *   date: string,
         *   title: string,
         *   body: string,
         *   createdAt: string,
         *   updatedAt: string
         * }> $items
         */
        $items = [];

        // UC-2 defaults: page=1, perPage=20; empty result ⇒ total=0, pagesCount=0.
        $total      = 0;
        $page       = 1;
        $perPage    = 20;
        $pagesCount = 0;

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