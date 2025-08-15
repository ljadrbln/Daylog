<?php
declare(strict_types=1);

namespace Daylog\Application\DTO\Entries;

/**
 * DTO for adding an entry.
 */
interface AddEntryRequestInterface
{
    public function getTitle(): string;
    public function getBody(): string;
    public function getDate(): string;
}
