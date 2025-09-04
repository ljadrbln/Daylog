<?php

declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\ListEntries;

use Daylog\Tests\Support\Helper\ListEntriesHelper;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Tests\Integration\Application\UseCases\Entries\ListEntries\BaseListEntriesIntegrationTest;

/**
 * AC-10: Given dateFrom > dateTo, validation fails with DATE_RANGE_INVALID.
 *
 * Purpose: 
 *   - Validate range order: dateFrom must not exceed dateTo; 
 *   - violation raises DomainValidationException with DATE_RANGE_INVALID.
 * 
 * Mechanics: 
 *   - Baseline request; 
 *   - override dateFrom/dateTo to reversed ranges; 
 *   - expect exception (assert DATE_RANGE_INVALID).
 *
 * @covers \Daylog\Configuration\Providers\Entries\ListEntriesProvider
 * @covers \Daylog\Application\UseCases\Entries\ListEntries
 * 
 * @group UC-ListEntries
 */
final class AC10_DateRangeOrderInvalidTest extends BaseListEntriesIntegrationTest
{
    /** 
     * AC-10: Reversed range (dateFrom > dateTo) raises DATE_RANGE_INVALID. 
     * 
     * @dataProvider provideInvalidDateRanges 
     * 
     * @param string $from 
     * @param string $to 
     * 
     * @return void
     */
    public function testDateRangeOrderFromGreaterThanToFailsWithDateRangeInvalid(
        string $from,
        string $to
    ): void {
        // Arrange
        $data = ListEntriesHelper::getData();
        $data['dateFrom'] = $from;
        $data['dateTo']   = $to;

        $request = ListEntriesHelper::buildRequest($data);

        // Expectation
        $exceptionClass = DomainValidationException::class;
        $this->expectException($exceptionClass);

        // Act
        $this->useCase->execute($request);

        // Safety (should not reach)
        $message = 'DomainValidationException was expected for reversed date range';
        $this->fail($message);
    }

    /**
     * Provide reversed date ranges (dateFrom > dateTo).
     *
     * Scenarios:
     *  - Adjacent days reversed (same month).
     *  - Cross-month reversed.
     *  - Cross-year reversed.
     *
     * @return array<string, array{string,string}>
     */
    public function provideInvalidDateRanges(): array
    {
        $cases = [
            'adjacent days reversed' => ['2025-08-12', '2025-08-11'],
            'cross month reversed'   => ['2025-09-01', '2025-08-31'],
            'cross year reversed'    => ['2026-01-01', '2025-12-31'],
        ];

        return $cases;
    }
}
