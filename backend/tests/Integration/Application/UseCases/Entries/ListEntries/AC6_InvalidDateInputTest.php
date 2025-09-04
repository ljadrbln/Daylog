<?php

declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\ListEntries;

use Daylog\Tests\Support\Helper\ListEntriesHelper;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Tests\Integration\Application\UseCases\Entries\ListEntries\BaseListEntriesIntegrationTest;

/**
 * AC-6: A non-YYYY-MM-DD date (or non-real calendar date) causes DATE_INVALID.
 *
 * Purpose: 
 *   - Ensure invalid values in date/dateFrom/dateTo trigger DomainValidationException with DATE_INVALID (UC-2 AC-6).
 * 
 * Mechanics: 
 *   - Build baseline request; 
 *   - override one date field with invalid value; 
 *   - expect exception (optionally assert message mentions DATE_INVALID).
 *
 * @covers \Daylog\Configuration\Providers\Entries\ListEntriesProvider
 * @covers \Daylog\Application\UseCases\Entries\ListEntries
 * 
 * @group UC-ListEntries
 */
final class AC6_InvalidDateInputTest extends BaseListEntriesIntegrationTest
{
    /** 
     * AC-6: Invalid date input yields DATE_INVALID. 
     * 
     * @dataProvider provideInvalidDateInputs 
     * 
     * @param string $field 
     * @param string $value 
     * 
     * @return void 
     */
    public function testInvalidDateInputThrowsValidationException(string $field, string $value): void
    {
        // Arrange
        $data = ListEntriesHelper::getData();
        $data[$field] = $value;

        $request = ListEntriesHelper::buildRequest($data);

        // Expectation
        $this->expectException(DomainValidationException::class);
        $this->expectExceptionMessage('DATE_INVALID');

        // Act
        $this->useCase->execute($request);

        $message = 'DomainValidationException was expected for invalid date input';
        $this->fail($message);
    }

    /**
     * Provide invalid date inputs for UC-2 AC-6.
     *
     * Cases:
     *  - Wrong format: 2025/01/01, 2025-1-01, 2025-01-1, 01-01-2025, abc
     *  - Non-real:     2025-02-30, 2025-13-01, 2025-00-10, 2025-04-31, 2025-12-00
     *
     * @return array<string, array{string,string}>
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
