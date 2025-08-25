<?php
declare(strict_types=1);

namespace Daylog\Infrastructure\Storage\Entries;

use Daylog\Infrastructure\Storage\Entries\EntryModel;
use Daylog\Domain\Interfaces\Entries\EntryStorageInterface;
use Daylog\Domain\Models\Entries\Entry;
use Daylog\Domain\Models\Entries\ListEntriesCriteria;
use Daylog\Domain\Services\UuidGenerator;

/**
 * Class EntryStorage
 *
 * Minimal storage implementation for unit tests.
 * Generates a UUID and returns it; real persistence will be added later.
 */
final class EntryStorage implements EntryStorageInterface
{
    /** @var EntryModel */
    private EntryModel $model;

    /**
     * Inject persistence model.
     *
     * Mechanics:
     * - The model encapsulates the low-level DB\SQL connection and table mapping.
     * - This storage delegates actual insert operations to the model.
     *
     * @param EntryModel $model Prepared model bound to a shared DB\SQL instance.
     * @return void
     */
    public function __construct(EntryModel $model)
    {
        $this->model = $model;
    }

    /**
     * Insert the given Entry and return generated UUID.
     *
     * @param Entry  $entry Domain entry to persist.
     * @param string $now   Current timestamp (UTC, ISO 8601).
     * @return string Generated UUID (v4).
     */
    public function insert(Entry $entry, string $now): string
    {
        $uuid = UuidGenerator::generate();

        $data = [
            'id'        => $uuid,
            'date'      => $entry->getDate(),
            'title'     => $entry->getTitle(),
            'body'      => $entry->getBody(),
            'createdAt' => $now,
            'updatedAt' => $now
        ];

        $this->model->create($data);

        return $uuid;
    }

    /**
     * Retrieve entries by UC-2 criteria with pagination.
     *
     * Mechanics:
     * - Build F3 $filter and $options via dedicated helpers.
     * - Delegate to EntryModel::find()/count() and map rows to array shape.
     *
     * @param ListEntriesCriteria $criteria Normalized criteria from validator.
     * @return array{
     *     items: array<int, array{id:string,date:string,title:string,body:string,createdAt:string,updatedAt:string}>,
     *     total: int,
     *     page: int,
     *     perPage: int,
     *     pagesCount: int
     * }
     */
    public function findByCriteria(ListEntriesCriteria $criteria): array
    {
        $page    = $criteria->getPage();
        $perPage = $criteria->getPerPage();

        $filter  = $this->buildF3Filter($criteria);
        $options = $this->buildF3Options($criteria);

        $rows  = $this->model->find($filter, $options);
        $total = $this->model->count($filter);

        $items = [];
        foreach ($rows as $row) {
            $items[] = [
                'id'        => $row->id,
                'date'      => $row->date,
                'title'     => $row->title,
                'body'      => $row->body,
                'createdAt' => $row->created_at,
                'updatedAt' => $row->updated_at,
            ];
        }

        $pagesCount = (int) ceil($total / $perPage);

        $result = [
            'items'      => $items,
            'total'      => $total,
            'page'       => $page,
            'perPage'    => $perPage,
            'pagesCount' => $pagesCount,
        ];

        return $result;
    }

    /**
     * Build Fat‑Free filter array: ['cond with ? placeholders', ...$params] or null.
     *
     * Design:
     * - Encapsulates all UC‑2 filters (date, dateFrom/dateTo, query) in one place.
     * - Keeps Criteria framework‑agnostic; no F3 leaks into Domain.
     *
     * @param ListEntriesCriteria $criteria
     * @return array<int, string>|null
     */
    private function buildF3Filter(ListEntriesCriteria $criteria): ?array
    {
        $clauses = [];
        $params  = [];

        $date = $criteria->getDate();
        if ($date !== null) {
            $clauses[] = 'date = ?';
            $params[]  = $date;
        }

        $dateFrom = $criteria->getDateFrom();
        if ($dateFrom !== null) {
            $clauses[] = 'date >= ?';
            $params[]  = $dateFrom;
        }

        $dateTo = $criteria->getDateTo();
        if ($dateTo !== null) {
            $clauses[] = 'date <= ?';
            $params[]  = $dateTo;
        }

        $query = $criteria->getQuery();
        if ($query !== null && $query !== '') {
            $clauses[] = '(title LIKE ? OR body LIKE ?)';

            $like   = '%' . $query . '%';
            $param1 = $like;
            $param2 = $like;

            $params[] = $param1;
            $params[] = $param2;
        }

        $result = null;

        if ($clauses !== []) {
            $glue      = ' AND ';
            $condition = implode($glue, $clauses);

            $filter = array_merge([$condition], $params);
            $result = $filter;
        }

        return $result;
    }

    /**
     * Build Fat‑Free options array: ['order' => ..., 'limit' => ..., 'offset' => ...].
     *
     * Includes stable secondary order by createdAt DESC (AC‑8 of UC‑2).
     *
     * @param ListEntriesCriteria $criteria
     * @return array{order:string,limit:int,offset:int}
     */
    private function buildF3Options(ListEntriesCriteria $criteria): array
    {
        $descriptors = $criteria->getSortDescriptor(); // [['field'=>'date|createdAt|updatedAt','direction'=>'ASC|DESC'], ...]

        $fieldMap = [
            'date'      => 'date',
            'createdAt' => 'created_at',
            'updatedAt' => 'updated_at',
        ];

        $parts = [];
        foreach ($descriptors as $desc) {
            $fieldDomain = $desc['field'];
            $dirVar      = $desc['direction'];

            $fieldDb = $fieldMap[$fieldDomain] ?? $fieldDomain;

            $part = sprintf('%s %s', $fieldDb, $dirVar);
            $parts[] = $part;
        }

        $order = implode(', ', $parts);

        $page    = $criteria->getPage();
        $perPage = $criteria->getPerPage();
        $offset  = ($page - 1) * $perPage;

        $options = [
            'order'  => $order,
            'limit'  => $perPage,
            'offset' => $offset,
        ];

        return $options;
    }
}