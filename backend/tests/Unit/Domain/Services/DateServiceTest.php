<?php

declare(strict_types=1);

namespace Daylog\Tests\Unit\Domain\Services;

use Codeception\Test\Unit;
use Daylog\Domain\Services\DateService;

/**
 * @covers \Daylog\Domain\Services\DateService
 *
 * Verifies strict validation for ISO local dates (Y-m-d).
 */
final class DateServiceTest extends Unit
{
    /**
     * Requirement (BR-6): Valid strict dates return true.
     *
     * @dataProvider validDateProvider
     * @return void
     */
    public function testValidDatesReturnTrue(string $input): void
    {
        $result = DateService::isValidLocalDate($input);
        $this->assertTrue($result);
    }

    /**
     * Requirement (BR-6): Invalid dates or non-strict formats return false.
     *
     * @dataProvider invalidDateProvider
     * @return void
     */
    public function testInvalidDatesReturnFalse(string $input): void
    {
        $result = DateService::isValidLocalDate($input);
        $this->assertFalse($result);
    }

    /**
     * Provide strictly valid ISO local dates (Y-m-d).
     *
     * @return array<string, array{0:string}>
     */
    public function validDateProvider(): array
    {
        $cases = [
            'simple valid date'       => ['2025-08-20'],
            'edge case new year eve'  => ['1999-12-31'],
            'leap year feb 29'        => ['2024-02-29'], // leap year OK
            'month with 30 days'      => ['2025-04-30'],
            'month with 31 days'      => ['2025-01-31'],
        ];

        return $cases;
    }

    /**
     * Provide invalid calendar dates and non-strict formats.
     *
     * @return array<string, array{0:string}>
     */
    public function invalidDateProvider(): array
    {
        $cases = [
            // Calendar-invalid
            'date is feb 29 on non-leap year' => ['2025-02-29'],
            'date has month 13'               => ['2025-13-02'],
            'date has month 00'               => ['2025-00-10'],
            'date has day 00'                 => ['2025-11-00'],
            'date has day 32'                 => ['2025-11-32'],

            // Non-strict formats (must be zero-padded Y-m-d, no extras)
            'date has no zero padding'        => ['2025-8-2'],
            'date has wrong order'            => ['20-08-2025'],
            'date has wrong separator'        => ['2025/08/02'],
            'date has leading space'          => [' 2025-08-02'],
            'date has trailing space'         => ['2025-08-02 '],
            'date has time suffix'            => ['2025-08-02T00:00:00'],
            'date is empty'                   => [''],
        ];

        return $cases;
    }
}
