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
     * AC-1: Valid strict dates return true.
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
     * AC-2: Invalid dates or non-strict formats return false.
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
            'simple_valid'        => ['2025-08-20'],
            'edge_new_year_eve'   => ['1999-12-31'],
            'leap_year_feb_29'    => ['2024-02-29'], // leap year OK
            'month_with_30_days'  => ['2025-04-30'],
            'month_with_31_days'  => ['2025-01-31'],
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
            'not_leap_feb_29'     => ['2025-02-29'],
            'month_13'            => ['2025-13-02'],
            'month_00'            => ['2025-00-10'],
            'day_00'              => ['2025-11-00'],
            'day_32'              => ['2025-11-32'],

            // Non-strict formats (must be zero-padded Y-m-d, no extras)
            'no_zero_padding'     => ['2025-8-2'],
            'wrong_order'         => ['20-08-2025'],
            'wrong_separator'     => ['2025/08/02'],
            'leading_space'       => [' 2025-08-02'],
            'trailing_space'      => ['2025-08-02 '],
            'with_time_suffix'    => ['2025-08-02T00:00:00'],
            'empty'               => [''],
        ];

        return $cases;
    }
}
