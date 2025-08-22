<?php

namespace Daylog\Domain\Models\Entries;

/**
 * Class ListEntriesConstraints
 *
 * Defines global length limits for ListEntries entities.
 */
final class ListEntriesConstraints
{
    /** @var int */
    public const PAGE_MIN = 1;
    
    /** @var int */
    public const PER_PAGE_MIN = 1;
    
    /** @var int */
    public const PER_PAGE_MAX = 100;

    /** @var int */
    public const PER_PAGE_DEFAULT = 10;

    /** @var int */
    public const QUERY_MAX = 30;

    /** @var string */
    public const SORT_FIELD_DEFAULT = 'date';
    
    /** @var string */
    public const SORT_DIR_DEFAULT = 'DESC';

    /** @var string[] */
    public const ALLOWED_SORT_FIELDS = ['date', 'createdAt', 'updatedAt'];

    /** @var string[] */
    public const ALLOWED_SORT_DIRS = ['ASC', 'DESC'];

    /** @var array<int, array{field: string, direction: string}> */
    public const SORT_DESCRIPTOR = [
        ['field' => 'date',      'direction' => 'DESC'],
        ['field' => 'createdAt', 'direction' => 'DESC'],
    ];
}
