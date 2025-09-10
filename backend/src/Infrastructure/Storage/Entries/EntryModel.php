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
     * Create a new entry.
     *
     * @param array<string,mixed> $data
     * @return void
     */
    public function createEntry(array $data): void
    {
        $this->reset();
        $this->copyfrom($data);

        $this->save();
    }

    /**
     * Update an existing entry by id.
     *
     * @param string              $id
     * @param array<string,mixed> $data
     * @return void
     */
    public function updateEntry(string $id, array $data): void
    {
        $this->reset();
        $this->load(['id = ?', $id]);

        $this->copyfrom($data);

        $this->save();
    }

    /**
     * Return plain arrays (snake_case DB fields) for the given filter/options.
     *
     * @param array<int,string>|null $filter
     * @param array{order:string,limit:int,offset:int} $options
     * @return array<int,array{id:string,date:string,title:string,body:string,created_at:string,updated_at:string}>
     */
    public function findRows(?array $filter, array $options): array
    {
        $rows = $this->find($filter, $options);

        $result = [];
        foreach ($rows as $row) {
            $result[] = $row->cast();
        }

        return $result;
    }

    /**
     * Count rows for the given filter.
     *
     * @param array<int,string>|null $filter
     * @return int
     */
    public function countRows(?array $filter): int
    {
        $total = $this->count($filter);
        return $total;
    }

    /**
     * Get one row by UUID as a plain array.
     *
     * Mechanics:
     * - Uses Mapper::load() with a parameterized condition.
     * - Returns null when dry().
     *
     * @param string $uuid Entry UUID (v4).
     * @return array{id:string,date:string,title:string,body:string,created_at:string,updated_at:string}|null
     *         Plain row as associative array or null if not found.
     */
    public function findById(string $uuid): ?array
    {
        $condition = ['id = ?', $uuid];
        $this->load($condition);

        $row = $this->dry() 
            ? null 
            : $this->cast();

        return $row;
    }

    /**
     * Delete one row by UUID.
     *
     * Purpose:
     * Remove a single entry identified by its UUID. If the row does not exist,
     * the method performs no action.
     *
     * Mechanics:
     * - Loads the record via Mapper::load() with a parameterized condition.
     * - If the mapper is dry(), returns immediately.
     * - Otherwise calls erase() to delete the record.
     *
     * @param string $uuid Entry UUID (v4).
     * @return void
     */
    public function deleteById(string $uuid): void
    {
        $condition = ['id = ?', $uuid];
        $this->load($condition);

        $isDry = $this->dry();
        if ($isDry) {
            return;
        }

        $this->erase();
    }    
}        
