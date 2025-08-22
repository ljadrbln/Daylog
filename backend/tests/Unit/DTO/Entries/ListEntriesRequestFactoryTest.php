<?php

declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\DTO\Entries;

use Codeception\Test\Unit;
use Daylog\Application\DTO\Entries\ListEntriesRequestFactory;
use Daylog\Application\DTO\Entries\ListEntriesRequestInterface;
use Daylog\Tests\Support\Helper\ListEntriesHelper;
use Daylog\Application\Exceptions\TransportValidationException;

/**
 * Unit tests for ListEntriesRequestFactory.
 *
 * This factory builds an ListEntriesRequest DTO from raw HTTP input.
 * It performs transport-level validation: checks for presence and correct
 * types of required fields (`page`, `perPage`, `sort`, `direction`), but does not enforce
 * business rules (those are validated separately in Application layer).
 *
 * @covers \Daylog\Application\DTO\Entries\ListEntriesRequestFactory
 */

final class ListEntriesRequestFactoryTest extends Unit
{
    /**
     * Happy path: Given valid input, returns a DTO with mapped values.
     *
     * @return void
     */
    public function testFromArrayReturnsDtoOnValidInput(): void
    {
        /** Arrange **/
        $factory = new ListEntriesRequestFactory();
        $input   = ListEntriesHelper::getData();

        $dto = $factory->fromArray($input);

        $this->assertInstanceOf(ListEntriesRequestInterface::class, $dto);
        
        $this->assertSame($input['page'],      $dto->getPage());
        $this->assertSame($input['perPage'],   $dto->getPerPage());
        $this->assertSame($input['sort'],      $dto->getSort());
        $this->assertSame($input['direction'], $dto->getDirection());
    }

    /**
     * Transport validation errors: For invalid type or missing field, throws TransportValidationException.
     *
     * @dataProvider provideInvalidTransportData
     *
     * @param array<string,mixed> $overrides
     * @return void
     */
    public function testFromArrayThrowsOnInvalidTransportData(array $overrides): void
    {
        $factory = new ListEntriesRequestFactory();
        $data    = ListEntriesHelper::getData();
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
            'page is missing'            => [['page' => null]],
            'perPage is missing'         => [['perPage' => null]],
            'sort is missing'            => [['sort' => null]],
            'direction is missing'       => [['direction' => null]],
            'page is not int'            => [['page' => '1']],
            'perPage is not int'         => [['perPage' => '10']],
            'sort is not string'         => [['sort' => 123]],
            'direction is not string'    => [['direction' => false]],

            // Optional filters: wrong type when provided
            'dateFrom is not string'     => [['dateFrom' => 20250813]],
            'dateTo is not string'       => [['dateTo' => 20250813]],
            'date is not string'         => [['date' => 20250813]],
            'query is not string'        => [['query' => ['oops']]],

            // Multiple violations at once (order of checks verified in dedicated test if needed)
            'page and perPage invalid'   => [['page' => 'x', 'perPage' => 'y']],
            'sort and direction invalid' => [['sort' => 7, 'direction' => 0]],
            'all fields wrong'           => [[
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
