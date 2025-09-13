<?php
declare(strict_types=1);

namespace Daylog\Tests\Support\Factory;

use Daylog\Application\DTO\Entries\ListEntries\ListEntriesRequest;
use Daylog\Application\DTO\Entries\ListEntries\ListEntriesRequestInterface;

/**
 * Factory for building ListEntries test requests.
 *
 * Purpose:
 * Provide focused builders for UC-2 scenarios, starting from a minimal,
 * valid baseline and allowing targeted overrides per test case.
 *
 * Mechanics:
 * - Baseline uses defaults: page=1, perPage=20, sort=date DESC; no filters.
 * - Override any field by passing $overrides; keys not provided are left as defaults.
 */
final class ListEntriesTestRequestFactory
{
    /**
     * Build a baseline request with optional overrides.
     *
     * @param array<string,mixed> $overrides
     * @return ListEntriesRequestInterface
     */
    public static function fromArray(array $overrides = []): ListEntriesRequestInterface
    {
        $payload = [
            'page'      => 1,
            'perPage'   => 20,
            'sortField' => 'date',
            'sortDir'   => 'DESC',
            'date'      => null,
            'dateFrom'  => null,
            'dateTo'    => null,
            'query'     => null,
        ];

        foreach ($overrides as $key => $value) {
            $payload[$key] = $value;
        }

        $request = ListEntriesRequest::fromArray($payload);

        return $request;
    }

    /**
     * Build a request overriding a single date-like field.
     *
     * @param string $field One of: date|dateFrom|dateTo
     * @param string $value Raw string value to inject (may be invalid).
     * @return ListEntriesRequestInterface
     */
    public static function withDate(string $field, string $value): ListEntriesRequestInterface
    {
        $overrides = [$field => $value];
        $request   = self::fromArray($overrides);

        return $request;
    }
}
