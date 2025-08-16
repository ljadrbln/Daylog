<?php
declare(strict_types=1);

namespace Daylog\Infrastructure\Storage\Entries;

use Daylog\Infrastructure\Storage\AbstractModel;

class EntryModel extends AbstractModel {
    /**
     * @var string Physical table name for F3 Mapper.
     */
    protected string $DB_TABLE_NAME = 'entries';
    
    /**
     * Create a new entry with given data.
     *
     * @param array $data
     * @return void
     */
    public function create(array $data): void
    {
        $this->reset();
        $this->copyfrom($data);
        
        $this->save();
    }

    /**
     * Get one row by UUID as a plain array.
     *
     * Mechanics:
     * - Uses Mapper::load() with a parameterized condition.
     * - Returns null when dry().
     *
     * @param string $uuid Entry UUID (v4).
     * @return array<string,mixed>|null Plain row or null if not found.
     */
    public function getRowByUuid(string $uuid): ?array
    {
        $this->load(['id = ?', $uuid]);

        $result = $this->dry() 
            ? null 
            : $this->cast();

        return $result;
    }
}        
