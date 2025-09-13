<?php
declare(strict_types=1);

namespace Daylog\Tests\Support\DataProviders;

/**
 * Centralized invalid date cases for UC-2 ListEntries validation.
 *
 * Purpose:
 * Provide reusable datasets for invalid values of date/dateFrom/dateTo that must
 * trigger DATE_INVALID. Intended for both Unit and Integration suites.
 *
 * Mechanics:
 * - Covers wrong formats (slashes, single-digit parts, reversed, alphabetic)
 *   and non-real calendar dates (Feb 30, month 13, day 00, etc.).
 */
trait ListEntriesDateDataProvider
{
    /**
     * Provide invalid date inputs for UC-2 AC-6.
     *
     * Cases:
     *  - Wrong format: 2025/01/01, 2025-1-01, 2025-01-1, 01-01-2025, abc
     *  - Non-real:     2025-02-30, 2025-13-01, 2025-00-10, 2025-04-31, 2025-12-00
     *
     * @return array<string, array{0:string,1:string}> field, value
     */
    public function provideInvalidDateInputs(): array
    {
        $cases = [
            // date: wrong format
            'date wrong format slashes'            => ['date',     '2025/01/01'],
            'date wrong format single-digit month' => ['date',     '2025-1-01'],
            'date wrong format single-digit day'   => ['date',     '2025-01-1'],
            'date wrong format reversed'           => ['date',     '01-01-2025'],
            'date alphabetic'                      => ['date',     'abc'],

            // date: non-real
            'date non-real Feb 30'                 => ['date',     '2025-02-30'],
            'date non-real month 13'               => ['date',     '2025-13-01'],
            'date non-real month 00'               => ['date',     '2025-00-10'],
            'date non-real Apr 31'                 => ['date',     '2025-04-31'],
            'date non-real day 00'                 => ['date',     '2025-12-00'],

            // dateFrom: wrong format
            'dateFrom wrong format slashes'        => ['dateFrom', '2025/01/01'],
            'dateFrom wrong format single-digit m' => ['dateFrom', '2025-1-01'],
            'dateFrom wrong format single-digit d' => ['dateFrom', '2025-01-1'],
            'dateFrom wrong format reversed'       => ['dateFrom', '01-01-2025'],
            'dateFrom alphabetic'                  => ['dateFrom', 'abc'],

            // dateFrom: non-real
            'dateFrom non-real Feb 30'             => ['dateFrom', '2025-02-30'],
            'dateFrom non-real month 13'           => ['dateFrom', '2025-13-01'],
            'dateFrom non-real month 00'           => ['dateFrom', '2025-00-10'],
            'dateFrom non-real Apr 31'             => ['dateFrom', '2025-04-31'],
            'dateFrom non-real day 00'             => ['dateFrom', '2025-12-00'],

            // dateTo: wrong format
            'dateTo wrong format slashes'          => ['dateTo',   '2025/01/01'],
            'dateTo wrong format single-digit m'   => ['dateTo',   '2025-1-01'],
            'dateTo wrong format single-digit d'   => ['dateTo',   '2025-01-1'],
            'dateTo wrong format reversed'         => ['dateTo',   '01-01-2025'],
            'dateTo alphabetic'                    => ['dateTo',   'abc'],

            // dateTo: non-real
            'dateTo non-real Feb 30'               => ['dateTo',   '2025-02-30'],
            'dateTo non-real month 13'             => ['dateTo',   '2025-13-01'],
            'dateTo non-real month 00'             => ['dateTo',   '2025-00-10'],
            'dateTo non-real Apr 31'               => ['dateTo',   '2025-04-31'],
            'dateTo non-real day 00'               => ['dateTo',   '2025-12-00'],
        ];

        return $cases;
    }
}
