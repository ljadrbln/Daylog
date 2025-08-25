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

    /** @inheritDoc */
    public function findByCriteria(ListEntriesCriteria $criteria): array {
        return [];
    }    
}