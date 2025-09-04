<?php

declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\Validators\Entries;

use Codeception\Test\Unit;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Application\Validators\Entries\ListEntries\ListEntriesValidator;
use Daylog\Application\Validators\Entries\ListEntries\ListEntriesValidatorInterface;
use Daylog\Tests\Support\Helper\ListEntriesHelper;

/**
 * Unit tests for ListEntriesValidator (UC-2).
 *
 * Purpose:
 * Validate business rules that remain after normalization:
 * - Strict date validity (YYYY-MM-DD) and real calendar dates.
 * - Date range ordering (dateFrom <= dateTo).
 * - Query length constraint (0..QUERY_MAX) after trimming.
 *
 * Mechanics:
 * - Happy path: normalized request with absent dates and in-range query passes.
 * - Error paths: each violation throws DomainValidationException with a specific code.
 *
 * @covers \Daylog\Application\Validators\Entries\ListEntries\ListEntriesValidator
 * @group UC-ListEntries
 */
final class ListEntriesValidatorTest extends Unit
{
    /** @var ListEntriesValidatorInterface */
    private ListEntriesValidatorInterface $validator;

    /**
     * Prepare SUT.
     *
     * @return void
     */
    protected function _before(): void
    {
        $this->validator = new ListEntriesValidator();
    }

    /**
     * Happy path: valid request passes without exception.
     *
     * @return void
     */
    public function testValidRequestPasses(): void
    {
        $data    = ListEntriesHelper::getData();
        $request = ListEntriesHelper::buildRequest($data);

        $this->validator->validate($request);

        $this->assertTrue(true);
    }

    /**
     * Invalid date inputs must raise DATE_INVALID.
     *
     * @dataProvider invalidDateProvider
     *
     * @param array<string,mixed> $overrides
     * @param string              $expectedCode
     * @return void
     */
    public function testInvalidDatesThrowException(array $overrides, string $expectedCode): void
    {
        $data    = ListEntriesHelper::getData();
        $data    = array_merge($data, $overrides);
        $request = ListEntriesHelper::buildRequest($data);

        $this->expectException(DomainValidationException::class);
        $this->expectExceptionMessage($expectedCode);

        $this->validator->validate($request);
    }

    /**
     * Invalid date payloads (bad format or calendar-invalid).
     *
     * @return array<string,array{0:array<string,mixed>,1:string}>
     */
    public function invalidDateProvider(): array
    {
        $cases = [
            'from date invalid format' => [
                ['dateFrom' => '2025-8-1', 'dateTo' => '2025-08-31'],
                'DATE_INVALID',
            ],
            'to date invalid format' => [
                ['dateFrom' => '2025-08-01', 'dateTo' => '31-08-2025'],
                'DATE_INVALID',
            ],
            'dateFrom nonexistent day' => [
                ['dateFrom' => '2025-02-29'],
                'DATE_INVALID',
            ],
        ];

        return $cases;
    }

    /**
     * Date range rule: dateFrom > dateTo raises DATE_RANGE_INVALID.
     *
     * @return void
     */
    public function testDateRangeInvalidThrowsException(): void
    {
        $data = ListEntriesHelper::getData();

        $overrides = [
            'dateFrom' => '2025-08-31',
            'dateTo'   => '2025-08-01',
        ];

        $merged  = array_merge($data, $overrides);
        $request = ListEntriesHelper::buildRequest($merged);

        $this->expectException(DomainValidationException::class);
        $this->expectExceptionMessage('DATE_RANGE_INVALID');

        $this->validator->validate($request);
    }

    /**
     * Invalid exact date must raise DATE_INVALID.
     *
     * @dataProvider invalidExactDateProvider
     *
     * @param array<string,mixed> $overrides
     * @param string              $expectedCode
     * @return void
     */
    public function testInvalidExactDateThrowsException(array $overrides, string $expectedCode): void
    {
        $data    = ListEntriesHelper::getData();
        $data    = array_merge($data, $overrides);
        $request = ListEntriesHelper::buildRequest($data);

        $this->expectException(DomainValidationException::class);
        $this->expectExceptionMessage($expectedCode);

        $this->validator->validate($request);
    }

    /**
     * Invalid exact date payloads (strict YYYY-MM-DD required).
     *
     * @return array<string,array{0:array<string,mixed>,1:string}>
     */
    public function invalidExactDateProvider(): array
    {
        $cases = [
            'date invalid format'  => [['date' => '15-08-2025'], 'DATE_INVALID'],
            'date nonexistent day' => [['date' => '2025-02-30'],   'DATE_INVALID'],
            'date has time suffix' => [['date' => '2025-08-02T00:00:00'], 'DATE_INVALID'],
            'wrong separator'      => [['date' => '2025/08/02'],   'DATE_INVALID'],
            'no zero padding'      => [['date' => '2025-8-2'],     'DATE_INVALID'],
        ];

        return $cases;
    }

    /**
     * Query within 0..30 (post-trim) must pass without exception.
     *
     * @dataProvider validQueryProvider
     *
     * @param array<string,mixed> $overrides
     * @return void
     */
    public function testValidQueryPassesValidation(array $overrides): void
    {
        $data    = ListEntriesHelper::getData();
        $data    = array_merge($data, $overrides);
        $request = ListEntriesHelper::buildRequest($data);

        $this->validator->validate($request);

        $assertion = true;
        $this->assertTrue(true);
    }

    /**
     * AF-4: query longer than 30 (after trimming) must raise QUERY_TOO_LONG.
     *
     * @dataProvider invalidQueryProvider
     *
     * @param array<string,mixed> $overrides
     * @return void
     */
    public function testQueryTooLongThrowsException(array $overrides): void
    {
        $data    = ListEntriesHelper::getData();
        $data    = array_merge($data, $overrides);
        $request = ListEntriesHelper::buildRequest($data);

        $this->expectException(DomainValidationException::class);
        $this->expectExceptionMessage('QUERY_TOO_LONG');

        $this->validator->validate($request);
    }

    /**
     * Provides valid query inputs according to UC-2.
     *
     * @return array<string,array{0:array<string,mixed>}>
     */
    public function validQueryProvider(): array
    {
        $exact30 = str_repeat('a', 30);

        $rows = [
            'empty string'              => [['query' => '']],
            'spaces only (trim=>empty)' => [['query' => '     ']],
            'short word'                => [['query' => 'summer']],
            'boundary length 30'        => [['query' => $exact30]],
            'trimmed within limit'      => [['query' => '  june  ']],
        ];

        return $rows;
    }

    /**
     * Provides invalid query inputs that exceed 30 chars after trimming.
     *
     * @return array<string,array{0:array<string,mixed>}>
     */
    public function invalidQueryProvider(): array
    {
        $ascii31     = str_repeat('x', 31);
        $multibyte31 = str_repeat('Я', 31);
        $trimmed31   = '  ' . str_repeat('a', 31) . '  ';

        $rows = [
            'ascii length 31'     => [['query' => $ascii31]],
            'multibyte length 31' => [['query' => $multibyte31]],
            'trimmed still > 30'  => [['query' => $trimmed31]],
        ];

        return $rows;
    }
}
