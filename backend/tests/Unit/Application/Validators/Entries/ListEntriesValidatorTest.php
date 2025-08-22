<?php

declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\Validators\Entries;

use Codeception\Test\Unit;
use Daylog\Application\DTO\Entries\ListEntries\ListEntriesRequest;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Application\Validators\Entries\ListEntries\ListEntriesValidatorInterface;
use Daylog\Application\Validators\Entries\ListEntries\ListEntriesValidator;
use Daylog\Tests\Support\Helper\ListEntriesHelper;

/**
 * @covers \Daylog\Application\Validators\Entries\ListEntriesValidatorInterface
 *
 * RED stage: business rules validation for ListEntries.
 */
final class ListEntriesValidatorTest extends Unit
{
    /** @var ListEntriesValidatorInterface */
    private ListEntriesValidatorInterface $validator;

    protected function _before(): void
    {
        $this->validator = new ListEntriesValidator();
    }

    /**
     * Validation rule: Valid request passes without exception (validator-level happy path)
     *
     * @return void
     */
    public function testValidRequestPasses(): void
    {
        $data    = ListEntriesHelper::getData();
        $request = ListEntriesRequest::fromArray($data);

        $this->validator->validate($request);
        $this->assertTrue(true);
    }

    /**
     * Validation rule: Invalid date input raises DATE_INVALID.
     *
     * @dataProvider invalidDateProvider
     * @return void
     */
    public function testInvalidDatesThrowException(array $overrides): void
    {
        $data    = ListEntriesHelper::getData();
        $data    = array_merge($data, $overrides);
        $request = ListEntriesRequest::fromArray($data);

        $this->expectException(DomainValidationException::class);

        $this->validator->validate($request);
    }

    /**
     * Invalid date payloads.
     *
     * @return array<string,array{0:array<string,mixed>}>
     */
    public function invalidDateProvider(): array
    {
        return [
            'from date is invalid format' => [[
                'dateFrom' => '2025-8-1',
                'dateTo'   => '2025-08-31',
            ]],
            'to date is invalid format' => [[
                'dateFrom' => '2025-08-01',
                'dateTo'   => '31-08-2025',
            ]],
            'date is nonexistent day' => [[
                'dateFrom' => '2025-02-29',
            ]],
        ];
    }    

    /**
     * Validation rule: dateFrom > dateTo raises DATE_RANGE_INVALID.
     *
     * @return void
     */
    public function testDateRangeInvalidThrowsException(): void
    {
        $data    = ListEntriesHelper::getData();
        $data    = array_merge($data, [
            'dateFrom' => '2025-08-31',
            'dateTo'   => '2025-08-01',
        ]);

        $request = ListEntriesRequest::fromArray($data);

        $this->expectException(DomainValidationException::class);

        $this->validator->validate($request);
    }

    /**
     * Validation rule: invalid pagination throws PAGE_INVALID / PER_PAGE_INVALID.
     *
     * @dataProvider invalidPaginationProvider
     */
    public function testInvalidPaginationThrowsException(array $overrides): void
    {
        $data = ListEntriesHelper::getData();
        $data = array_merge($data, $overrides);

        /** @var ListEntriesRequestInterface $request */
        $request = ListEntriesRequest::fromArray($data);

        $this->expectException(DomainValidationException::class);

        $this->validator->validate($request);
    }

    /** @return array<string,array{0:array<string,mixed>}> */
    public function invalidPaginationProvider(): array
    {
        return [
            'page is less than 1'      => [['page' => 0]],
            'page is negative'         => [['page' => -1]],
            'perPage is zero'          => [['perPage' => 0]],
            'perPage is too big'       => [['perPage' => 101]],
        ];
    }

    /**
     * Validation rule: invalid sort or direction throws SORT_INVALID / DIRECTION_INVALID.
     *
     * @dataProvider invalidSortProvider
     */
    public function testInvalidSortThrowsException(array $overrides): void
    {
        $data = ListEntriesHelper::getData();
        $data = array_merge($data, $overrides);

        /** @var ListEntriesRequestInterface $request */
        $request = ListEntriesRequest::fromArray($data);

        $this->expectException(DomainValidationException::class);

        $this->validator->validate($request);
    }

    /** @return array<string,array{0:array<string,mixed>}> */
    public function invalidSortProvider(): array
    {
        return [
            'sort is unknown field'      => [['sort' => 'title']],
            'sort is empty string'       => [['sort' => '']],
            'direction is unknown value' => [['direction' => 'DOWN']],
            'direction is empty string'  => [['direction' => '']],
        ];
    }

    /**
     * Validation rule: invalid exact date throws DATE_INVALID.
     *
     * @dataProvider invalidExactDateProvider
     */
    public function testInvalidExactDateThrowsException(array $overrides): void
    {
        $data = ListEntriesHelper::getData();
        $data = array_merge($data, $overrides);

        /** @var ListEntriesRequestInterface $request */
        $request = ListEntriesRequest::fromArray($data);

        $this->expectException(DomainValidationException::class);

        $this->validator->validate($request);
    }

    /** @return array<string,array{0:array<string,mixed>}> */
    public function invalidExactDateProvider(): array
    {
        return [
            'date has invalid format'   => [['date' => '15-08-2025']],
            'date is nonexistent day'   => [['date' => '2025-02-30']],
            'date has time suffix'      => [['date' => '2025-08-02T00:00:00']],
            'date has wrong separator'  => [['date' => '2025/08/02']],
            'date has no zero padding'  => [['date' => '2025-8-2']],
        ];
    }

    /**
     * Validation rule: `query` within 0..30 (post-trim) must pass without exception.
     *
     * Mechanics:
     * - Empty string means "no filter".
     * - Trimming applies before length check (BR-1).
     * - Boundary 30 chars is allowed.
     *
     * @dataProvider validQueryProvider
     *
     * @return void
     */
    public function testValidQueryPassesValidation(array $overrides): void
    {
        $data    = ListEntriesHelper::getData();
        $data    = array_merge($data, $overrides);

        /** @var ListEntriesRequest $request */
        $request = ListEntriesRequest::fromArray($data);

        $this->validator->validate($request);
        $this->assertTrue(true);
    }

    /**
     * AF-4: `query` longer than 30 (after trimming) must raise QUERY_TOO_LONG.
     *
     * Notes:
     * - We only assert the exception class here; specific error code is asserted
     *   elsewhere in integration or can be added if DomainValidationException
     *   exposes error codes accessor.
     *
     * @dataProvider invalidQueryProvider
     *
     * @return void
     */
    public function testQueryTooLongThrowsException(array $overrides): void
    {
        $data    = ListEntriesHelper::getData();
        $data    = array_merge($data, $overrides);

        /** @var ListEntriesRequest $request */
        $request = ListEntriesRequest::fromArray($data);

        $exceptionClass = DomainValidationException::class;
        $this->expectException($exceptionClass);

        $this->validator->validate($request);
    }

    /**
     * Provides valid query inputs according to UC-2:
     * - Empty and spaces-only become "no filter".
     * - Trim applies before length check.
     * - Boundary length 30 is allowed.
     *
     * @return array<string,array{0:array<string,mixed>}>
     */
    public function validQueryProvider(): array
    {
        $exact30 = str_repeat('a', 30);

        return [
            'empty string'             => [['query' => '']],
            'spaces only (trim→empty)' => [['query' => '     ']],
            'short word'               => [['query' => 'summer']],
            'boundary length 30'       => [['query' => $exact30]],
            'trimmed within limit'     => [['query' => '  june  ']],
        ];
    }

    /**
     * Provides invalid query inputs that exceed 30 chars after trimming.
     *
     * Cases:
     * - ASCII 31 chars.
     * - Multibyte 31 chars (mb_strlen check).
     * - Trimmed still > 30.
     *
     * @return array<string,array{0:array<string,mixed>}>
     */
    public function invalidQueryProvider(): array
    {
        $ascii31     = str_repeat('x', 31);
        $multibyte31 = str_repeat('Я', 31);
        $trimmed31   = '  ' . str_repeat('a', 31) . '  ';

        return [
            'ascii length 31'         => [['query' => $ascii31]],
            'multibyte length 31'     => [['query' => $multibyte31]],
            'trimmed remains > 30'    => [['query' => $trimmed31]],
        ];
    }
}
