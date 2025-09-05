<?php
declare(strict_types=1);

namespace Daylog\Infrastructure\Storage\Entries;

use Daylog\Infrastructure\Storage\Entries\EntryModel;
use Daylog\Domain\Interfaces\Entries\EntryStorageInterface;
use Daylog\Domain\Models\Entries\Entry;
use Daylog\Domain\Models\Entries\ListEntriesCriteria;
use Daylog\Infrastructure\Storage\Entries\EntryFieldMapper;

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
     */
    public function insert(Entry $entry): void
    {
        $data = EntryFieldMapper::toDbRowFromEntry($entry);
        $this->model->create($data);
    }

    /**
     * {@inheritDoc}
     *
     * Retrieves entry by the given id.
     * Returns that entry if it exists, otherwise null.
     *
     * @param string $id UUIDv4 identifier.
     * @return Entry|null
     */
    public function findById(string $id): ?Entry
    {
        $row = $this->model->findById($id);
        if(is_null($row)) {
            return null;
        }

        $data  = EntryFieldMapper::fromDbRow($row);
        $entry = Entry::fromArray($data);

        return $entry;
    }

    /**
     * {@inheritDoc}
     */
    public function deleteById(string $id): void {
        $this->model->deleteById($id);
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
     *     items: array<int, array{id:string,title:string,body:string,date:string,createdAt:string,updatedAt:string}>,
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

        $rows  = $this->model->findRows($filter, $options);
        $total = $this->model->countRows($filter);

        $pagesCount = (int) ceil($total / $perPage);

        $items = [];
        foreach ($rows as $row) {
            $mapped = EntryFieldMapper::fromDbRow($row);
            $items[] = $mapped;
        }

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
        $order = EntryFieldMapper::buildOrderSafe($descriptors);

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