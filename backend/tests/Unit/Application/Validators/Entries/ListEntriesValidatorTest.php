<?php

declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\Validators\Entries;

use Codeception\Test\Unit;
use Daylog\Application\DTO\Entries\ListEntriesRequest;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Application\Validators\Entries\ListEntriesValidatorInterface;
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
        // Will be replaced with a real implementation later
        $this->validator = $this->makeEmpty(ListEntriesValidatorInterface::class);
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
        $this->expectExceptionMessage('DATE_INVALID');

        $this->validator->validate($request);
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
        $this->expectExceptionMessage('DATE_RANGE_INVALID');

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
            'bad_from_format' => [[
                'dateFrom' => '2025-8-1',
                'dateTo'   => '2025-08-31',
            ]],
            'bad_to_format' => [[
                'dateFrom' => '2025-08-01',
                'dateTo'   => '31-08-2025',
            ]],
            'nonexistent_day' => [[
                'dateFrom' => '2025-02-29',
            ]],
        ];
    }
}
