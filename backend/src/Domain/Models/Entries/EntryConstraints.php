<?php

namespace Daylog\Domain\Models\Entries;

/**
 * Class EntryConstraints
 *
 * Defines global length limits for Entry entities.
 */
final class EntryConstraints
{
    /** @var int */
    public const TITLE_MIN = 1;
    
    /** @var int */
    public const TITLE_MAX = 200;

    /** @var int */
    public const BODY_MIN = 1;
    
    /** @var int */
    public const BODY_MAX = 50000;
}
