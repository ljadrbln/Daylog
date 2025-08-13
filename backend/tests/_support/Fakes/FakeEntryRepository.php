<?php
declare(strict_types=1);

namespace Daylog\Tests\Support\Fakes;

use Daylog\Domain\Interfaces\EntryRepositoryInterface;
use Daylog\Domain\Models\Entry;

/**
 * Class FakeEntryRepository
 *
 * Simple in-memory test double that records calls to save()
 * and returns a preconfigured UUID for assertions.
 */
final class FakeEntryRepository implements EntryRepositoryInterface
{
    /** @var Entry|null */
    public ?Entry $savedEntry = null;

    /** @var int */
    public int $saveCalls = 0;

    /** @var string */
    public string $returnUuid = '00000000-0000-0000-0000-000000000000';

    /**
     * Persist the entry and return a UUID (preconfigured for tests).
     *
     * @param Entry $entry
     * @return string
     */
    public function save(Entry $entry): string
    {
        $this->saveCalls++;
        $this->savedEntry = $entry;

        $uuid = $this->returnUuid;
        return $uuid;
    }
}

