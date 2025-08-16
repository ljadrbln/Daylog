<?php

namespace Daylog\Domain\Models\Entries;

/**
 * Class EntryConstraints
 *
 * Defines global length limits for Entry entities.
 */
final class EntryConstraints
{
    public const TITLE_MIN = 1;
    public const TITLE_MAX = 200;

    public const BODY_MIN = 1;
    public const BODY_MAX = 50000;
}
