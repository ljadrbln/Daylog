<?php

namespace Daylog\Domain\Models\Entries;
use Daylog\Domain\Models\Entries\ListEntriesConstraints;

/**
 * Class ListEntriesConstraints
 *
 * Defines global length limits for Entry entities.
 */
final class ListEntriesConstraints
{
    public const PAGE_MIN      = 1;
    public const PER_PAGE_MIN  = 1;
    public const PER_PAGE_MAX  = 100;
    public const PER_PAGE_DEFAULT = 10;

    public const SORT_FIELD_DEFAULT = 'date';
    public const SORT_DIR_DEFAULT   = 'DESC';

    public const SORT_DESCRIPTOR = [
        ['field' => 'date',      'direction' => 'DESC'],
        ['field' => 'createdAt', 'direction' => 'DESC'],
    ];
}
