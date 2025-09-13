<?php
declare(strict_types=1);

namespace Daylog\Tests\Support\Factory;

use Daylog\Application\DTO\Entries\ListEntries\ListEntriesRequest;
use Daylog\Application\DTO\Entries\ListEntries\ListEntriesRequestInterface;
use Daylog\Tests\Support\Helper\ListEntriesHelper;

/**
 * Factory for building ListEntries test requests.
 *
 * Purpose:
 * Provide compact builders for UC-2 scenarios (happy path, invalid date, etc.).
 *
 * Mechanics:
 * - Baseline from ListEntriesHelper::getData().
 * - Overrides fields for negative cases.
 */
final class ListEntriesTestRequestFactory
{
    /**
     * @return ListEntriesRequestInterface
     */
    public static function happy(): ListEntriesRequestInterface
    {
        $data = ListEntriesHelper::getData();
        $request = ListEntriesRequest::fromArray($data);
        return $request;
    }

    /**
     * @param string $field
     * @param string $value
     * @return ListEntriesRequestInterface
     */
    public static function withInvalidDate(string $field, string $value): ListEntriesRequestInterface
    {
        $data = ListEntriesHelper::getData();
        $data[$field] = $value;

        $request = ListEntriesRequest::fromArray($data);
        return $request;
    }
}
