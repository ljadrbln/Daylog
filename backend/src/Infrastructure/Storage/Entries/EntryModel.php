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
