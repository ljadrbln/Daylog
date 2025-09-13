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
 * Provide focused builders for UC-2 scenarios (AC-1..AC-n) in a readable, intention-revealing style.
 *
 * Mechanics:
 * - Starts from ListEntriesHelper::getData() baseline.
 * - Exposes named builders for common scenarios (happy, rangeInclusive, withDate).
 * - Keeps a generic fromOverrides() for rare custom cases.
 */
final class ListEntriesTestRequestFactory
{
    /**
     * Build a happy-path request using baseline defaults.
     *
     * @return ListEntriesRequestInterface
     */
    public static function happy(): ListEntriesRequestInterface
    {
        $data    = ListEntriesHelper::getData();
        $request = ListEntriesRequest::fromArray($data);

        return $request;
    }

    /**
     * Build a request with an inclusive [dateFrom..dateTo] range.
     *
     * @param string $dateFrom Lower bound (inclusive), YYYY-MM-DD.
     * @param string $dateTo   Upper bound (inclusive), YYYY-MM-DD.
     * @return ListEntriesRequestInterface
     */
    public static function rangeInclusive(string $dateFrom, string $dateTo): ListEntriesRequestInterface
    {
        $data            = ListEntriesHelper::getData();
        $data['dateFrom'] = $dateFrom;
        $data['dateTo']   = $dateTo;

        $request = ListEntriesRequest::fromArray($data);

        return $request;
    }

    /**
     * Build a request overriding exactly one date-like field.
     *
     * @param string $field One of: date|dateFrom|dateTo.
     * @param string $value Raw value to inject (valid or invalid).
     * @return ListEntriesRequestInterface
     */
    public static function withDate(string $field, string $value): ListEntriesRequestInterface
    {
        $data         = ListEntriesHelper::getData();
        $data[$field] = $value;

        $request = ListEntriesRequest::fromArray($data);

        return $request;
    }

    /**
     * Build a request from targeted overrides (escape hatch).
     *
     * @param array<string,mixed> $overrides
     * @return ListEntriesRequestInterface
     */
    public static function fromOverrides(array $overrides): ListEntriesRequestInterface
    {
        $data = ListEntriesHelper::getData();

        foreach ($overrides as $key => $value) {
            $data[$key] = $value;
        }

        $request = ListEntriesRequest::fromArray($data);

        return $request;
    }
}
