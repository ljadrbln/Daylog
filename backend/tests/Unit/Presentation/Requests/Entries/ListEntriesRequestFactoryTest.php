<?php

declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\DTO\Entries;

use Codeception\Test\Unit;
use Daylog\Presentation\Requests\Entries\ListEntriesRequestFactory;
use Daylog\Application\DTO\Entries\ListEntriesRequestInterface;
use Daylog\Tests\Support\Helper\ListEntriestHelper;
use Daylog\Application\Exceptions\TransportValidationException;

/**
 * Unit tests for ListEntriesRequestFactory.
 *
 * This factory builds an ListEntriesRequest DTO from raw HTTP input.
 * It performs transport-level validation: checks for presence and correct
 * types of required fields (`page`, `perPage`, `sort`, `direction`), but does not enforce
 * business rules (those are validated separately in Application layer).
 *
 * @covers \Daylog\Presentation\Requests\ListEntriesRequestFactory
 */

final class ListEntriesRequestFactoryTest extends Unit
{
    /**
     * AC-1: Given valid input, returns a DTO with mapped values.
     *
     * @return void
     */
    public function testFromArrayReturnsDtoOnValidInput(): void
    {
        /** Arrange **/
        $factory = new ListEntriesRequestFactory();
        $input   = ListEntriestHelper::getData();

        $dto = $factory->fromArray($input);

        $this->assertInstanceOf(ListEntriesRequestInterface::class, $dto);
        $this->assertSame($input['page'],      $dto->getPage());
        $this->assertSame($input['perPage'],   $dto->getPerPage());
        $this->assertSame($input['sort'],      $dto->getSort());
        $this->assertSame($input['direction'], $dto->getDirection());
    }

    /**
     * AC-2..AC-n: For invalid type or missing field, throws TransportValidationException.
     *
     * @dataProvider provideInvalidTransportData
     *
     * @param array<string,mixed> $overrides
     * @return void
     */
    public function testFromArrayThrowsOnInvalidTransportData(array $overrides): void
    {
        $factory = new ListEntriesRequestFactory();
        $data    = ListEntriestHelper::getData();
        $data    = array_merge($data, $overrides);

        $this->expectException(TransportValidationException::class);
        $factory->fromArray($data);
    }

    /**
     * Provides overrides that break transport-level type checks.
     *
     * Notes:
     * - To simulate a missing required key (page/perPage/sort/direction),
     *   pass null. The factory reads raw via `?? null`; null is not an int/string → error.
     * - Optional filters are nullable; wrong type should trigger errors when present.
     *
     * @return array<string, array{0: array<string,mixed>}>
     */
    public static function provideInvalidTransportData(): array
    {
        $cases = [
            // Required fields: missing (null) or wrong type
            'page_missing'        => [['page' => null]],
            'perPage_missing'     => [['perPage' => null]],
            'sort_missing'        => [['sort' => null]],
            'direction_missing'   => [['direction' => null]],
            'page_not_int'        => [['page' => '1']],
            'perPage_not_int'     => [['perPage' => '10']],
            'sort_not_string'     => [['sort' => 123]],
            'direction_not_string'=> [['direction' => false]],

            // Optional filters: wrong type when provided
            'dateFrom_not_string' => [['dateFrom' => 20250813]],
            'dateTo_not_string'   => [['dateTo' => 20250813]],
            'date_not_string'     => [['date' => 20250813]],
            'query_not_string'    => [['query' => ['oops']]],

            // Multiple violations at once (order of checks verified in dedicated test if needed)
            'page_and_perPage'    => [['page' => 'x', 'perPage' => 'y']],
            'sort_and_direction'  => [['sort' => 7, 'direction' => 0]],
            'all_wrong'           => [[
                'page'      => 'p',
                'perPage'   => 'pp',
                'sort'      => 0,
                'direction' => 1,
                'dateFrom'  => 123,
                'dateTo'    => 456,
                'date'      => 789,
                'query'     => ['q'],
            ]],
        ];

        return $cases;
    }    
}
