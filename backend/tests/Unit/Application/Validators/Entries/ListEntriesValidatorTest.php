<?php

declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\Validators\Entries;

use Codeception\Test\Unit;
use Daylog\Application\DTO\Entries\ListEntriesRequest;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Application\Validators\Entries\ListEntriesValidatorInterface;
use Daylog\Application\Validators\Entries\ListEntriesValidator;
use Daylog\Tests\Support\Helper\ListEntriestHelper;

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
     * AC-1: Valid request passes without exception.
     *
     * @return void
     */
    public function testValidRequestPasses(): void
    {
        $data    = ListEntriestHelper::getData();
        $request = ListEntriesRequest::fromArray($data);

        $this->validator->validate($request);
        $this->assertTrue(true);
    }

    /**
     * AC-2: Invalid dates raise DATE_INVALID.
     *
     * @dataProvider invalidDateProvider
     * @return void
     */
    public function testInvalidDatesThrowException(array $overrides): void
    {
        $data    = ListEntriestHelper::getData();
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
     * AC-3: dateFrom > dateTo raises DATE_RANGE_INVALID.
     *
     * @return void
     */
    public function testDateRangeInvalidThrowsException(): void
    {
        $data    = ListEntriestHelper::getData();
        $data    = array_merge($data, [
            'dateFrom' => '2025-08-31',
            'dateTo'   => '2025-08-01',
        ]);
        $request = ListEntriesRequest::fromArray($data);

        $this->expectException(DomainValidationException::class);

        $this->validator->validate($request);
    }

    /**
     * AC-4: invalid pagination throws PAGE_INVALID / PER_PAGE_INVALID.
     *
     * @dataProvider invalidPaginationProvider
     */
    public function testInvalidPaginationThrowsException(array $overrides): void
    {
        $data = ListEntriestHelper::getData();
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
     * AC-5: invalid sort or direction throws SORT_INVALID / DIRECTION_INVALID.
     *
     * @dataProvider invalidSortProvider
     */
    public function testInvalidSortThrowsException(array $overrides): void
    {
        $data = ListEntriestHelper::getData();
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
     * AC-6: invalid exact date throws DATE_INVALID.
     *
     * @dataProvider invalidExactDateProvider
     */
    public function testInvalidExactDateThrowsException(array $overrides): void
    {
        $data = ListEntriestHelper::getData();
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
}
