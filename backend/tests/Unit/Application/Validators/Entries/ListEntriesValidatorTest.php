<?php

declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\Validators\Entries;

use Codeception\Test\Unit;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Application\Validators\Entries\ListEntries\ListEntriesValidator;
use Daylog\Application\Validators\Entries\ListEntries\ListEntriesValidatorInterface;
use Daylog\Tests\Support\Helper\ListEntriesHelper;

/**
 * Unit test for ListEntriesValidator (UC-2).
 *
 * Purpose:
 * Validates business rules that remain after normalization:
 * - strict/local date validity, date range ordering, query length.
 *
 * Scope:
 * Pagination and sorting are enforced by the normalizer and are not re-validated here.
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
     * Validation rule: valid request passes without exception (happy path).
     *
     * Mechanics:
     * - Build request via ListEntriesHelper (normalization performed inside buildRequest()).
     * - Validator must not throw when dates are absent and query within limits.
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
     * Validation rule: invalid date inputs raise DATE_INVALID.
     *
     * Mechanics:
     * - Provide transport-level bad formats or non-existent dates.
     * - Normalized DTO still carries these values; validator must reject them.
     *
     * @param array<string,mixed> $overrides
     * @return void
     * @dataProvider invalidDateProvider
     */
    public function testInvalidDatesThrowException(array $overrides): void
    {
        $data    = ListEntriesHelper::getData();
        $data    = array_merge($data, $overrides);
        $request = ListEntriesHelper::buildRequest($data);

        $exception = DomainValidationException::class;
        $this->expectException($exception);

        $this->validator->validate($request);
    }

    /**
     * Invalid date payloads (bad format or calendar-invalid).
     *
     * @return array<string,array{0:array<string,mixed>}>
     */
    public function invalidDateProvider(): array
    {
        $cases = [
            'from date invalid format' => [[
                'dateFrom' => '2025-8-1',
                'dateTo'   => '2025-08-31',
            ]],
            'to date invalid format' => [[
                'dateFrom' => '2025-08-01',
                'dateTo'   => '31-08-2025',
            ]],
            'dateFrom nonexistent day' => [[
                'dateFrom' => '2025-02-29',
            ]],
        ];

        return $cases;
    }

    /**
     * Validation rule: dateFrom > dateTo raises DATE_RANGE_INVALID.
     *
     * Mechanics:
     * - Both dates valid but in the wrong order -> validator must throw.
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

        $data    = array_merge($data, $overrides);
        $request = ListEntriesHelper::buildRequest($data);

        $exception = DomainValidationException::class;
        $this->expectException($exception);

        $this->validator->validate($request);
    }

    /**
     * Validation rule: invalid exact date throws DATE_INVALID.
     *
     * @param array<string,mixed> $overrides
     * @return void
     * @dataProvider invalidExactDateProvider
     */
    public function testInvalidExactDateThrowsException(array $overrides): void
    {
        $data    = ListEntriesHelper::getData();
        $data    = array_merge($data, $overrides);
        $request = ListEntriesHelper::buildRequest($data);

        $exception = DomainValidationException::class;
        $this->expectException($exception);

        $this->validator->validate($request);
    }

    /**
     * Invalid exact date payloads (strict YYYY-MM-DD required).
     *
     * @return array<string,array{0:array<string,mixed>}>
     */
    public function invalidExactDateProvider(): array
    {
        $cases = [
            'date invalid format'  => [['date' => '15-08-2025']],
            'date nonexistent day' => [['date' => '2025-02-30']],
            'date has time suffix' => [['date' => '2025-08-02T00:00:00']],
            'wrong separator'      => [['date' => '2025/08/02']],
            'no zero padding'      => [['date' => '2025-8-2']],
        ];

        return $cases;
    }

    /**
     * Validation rule: `query` within 0..30 (post-trim) must pass without exception.
     *
     * Mechanics:
     * - Empty and spaces-only mean "no filter".
     * - Boundary length 30 is allowed.
     *
     * @param array<string,mixed> $overrides
     * @return void
     * @dataProvider validQueryProvider
     */
    public function testValidQueryPassesValidation(array $overrides): void
    {
        $data    = ListEntriesHelper::getData();
        $data    = array_merge($data, $overrides);
        $request = ListEntriesHelper::buildRequest($data);

        $this->validator->validate($request);
        $this->assertTrue(true);
    }

    /**
     * AF-4: `query` longer than 30 (after trimming) must raise QUERY_TOO_LONG.
     *
     * @param array<string,mixed> $overrides
     * @return void
     * @dataProvider invalidQueryProvider
     */
    public function testQueryTooLongThrowsException(array $overrides): void
    {
        $data    = ListEntriesHelper::getData();
        $data    = array_merge($data, $overrides);
        $request = ListEntriesHelper::buildRequest($data);

        $exception = DomainValidationException::class;
        $this->expectException($exception);

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
            'empty string'             => [['query' => '']],
            'spaces only (trim=>empty)' => [['query' => '     ']],
            'short word'               => [['query' => 'summer']],
            'boundary length 30'       => [['query' => $exact30]],
            'trimmed within limit'     => [['query' => '  june  ']],
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
            'ascii length 31'      => [['query' => $ascii31]],
            'multibyte length 31'  => [['query' => $multibyte31]],
            'trimmed still > 30'   => [['query' => $trimmed31]],
        ];

        return $rows;
    }
}
