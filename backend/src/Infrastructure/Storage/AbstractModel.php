<?php
declare(strict_types=1);

namespace Daylog\Infrastructure\Storage;

use DB\SQL;
use DB\SQL\Mapper;

/**
 * Class AbstractModel
 *
 * Base class for F3 Mapper-based models with explicit constructor dependency on DB\SQL.
 * Subclasses must define the target table name via $DB_TABLE_NAME. Optional $CACHE_TTL
 * may be provided to enable F3 Mapper caching for read-heavy scenarios.
 *
 * @template T
 */
abstract class AbstractModel extends Mapper
{
    /**
     * @var string Physical table name for the mapper.
     */
    protected string $DB_TABLE_NAME = '';

    /**
     * @var int|null Cache TTL in seconds for Mapper, null to disable.
     */
    protected ?int $CACHE_TTL = null;

    /**
     * Construct the Mapper; the DB connection is injected.
     * Table name is taken from $DB_TABLE_NAME and must be defined in a subclass.
     *
     * @param SQL $db Injected DB connection (already configured by the caller).
     */
    public function __construct(SQL $db)
    {
        $table = $this->DB_TABLE_NAME;
        parent::__construct($db, $table);

        $ttl = $this->CACHE_TTL;
        if ($ttl !== null) {
            $this->ttl($ttl);
        }
    }

    /**
     * Cast a list of Mapper results into a plain array
     *
     * Intended usage: pass the array returned by Mapper::find(...) as $records, then transform each row via Mapper::cast().
     * Typical cases include mapping rows into domain entities or DTOs.
     *
     * @param array<int, Mapper> $records List returned by Mapper::find().
     * @return array<int,array<string,mixed>>
     */
    protected function castList(array $records): array
    {
        $result = [];

        foreach ($records as $row) {
            $result[] = $row->cast();
        }

        return $result;
    }
}
