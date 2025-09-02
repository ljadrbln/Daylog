<?php

declare(strict_types=1);

namespace Daylog\Tests\Unit\Presentation\Requests\Entries;

use Codeception\Test\Unit;
use Daylog\Presentation\Requests\Entries\ListEntriesRequestFactory;
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
 * @covers \Daylog\Presentation\Requests\ListEntriesRequestFactory
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
        // Arrange
        $input = ListEntriesHelper::getData();

        // Act
        $dto = ListEntriesRequestFactory::fromArray($input);

        // Assert
        $this->assertSame($input['page'],       $dto->getPage());
        $this->assertSame($input['perPage'],    $dto->getPerPage());
        $this->assertSame($input['sortField'],  $dto->getSort());
        $this->assertSame($input['sortDir'],    $dto->getDirection());
    }

    /**
     * Transport validation errors: for invalid TYPE only, throws TransportValidationException.
     * Missing/null is allowed and handled by the normalizer.
     *
     *
     * @dataProvider provideInvalidTransportData
     *
     * @param array<string,mixed> $overrides
     * @return void
     */
    public function testFromArrayThrowsOnInvalidTransportData(array $overrides): void
    {
        // Arrange
        $data = ListEntriesHelper::getData();
        $data = array_merge($data, $overrides);

        // Expectation
        $this->expectException(TransportValidationException::class);

        // Act
        ListEntriesRequestFactory::fromArray($data);
    }

    /**
     * Contract:
     * - page, perPage: allow null/missing; when provided, must be numeric (is_numeric).
     * - sortField, sortDir: allow null/missing; when provided, must be scalar (no arrays/objects).
     * - dateFrom, dateTo, date, query: allow null/missing; when provided, must be scalar.
     *
     * @return array<string, array{0: array<string,mixed>}>
     */
    public static function provideInvalidTransportData(): array
    {
        $cases = [
            // page/perPage: invalid when provided and NOT numeric
            'page is not numeric (bool)'       => [['page' => true]],
            'page is not numeric (array)'      => [['page' => []]],
            'page is not numeric (object)'     => [['page' => (object)['x' => 1]]],
            'perPage is not numeric (bool)'    => [['perPage' => false]],
            'perPage is not numeric (array)'   => [['perPage' => ['10']]],
            'perPage is not numeric (object)'  => [['perPage' => (object)['y' => 1]]],

            // sort/direction: invalid when provided and NOT scalar
            'sort is not scalar (array)'       => [['sortField' => ['date']]],
            'sort is not scalar (object)'      => [['sortField' => (object)['v' => 'date']]],
            'direction is not scalar (array)'  => [['sortDir' => ['DESC']]],
            'direction is not scalar (object)' => [['sortDir' => (object)['v' => 'DESC']]],

            // optional filters: invalid when provided and NOT scalar
            'dateFrom is not scalar (array)'   => [['dateFrom' => ['2025-08-13']]],
            'dateFrom is not scalar (object)'  => [['dateFrom' => (object)['v' => '2025-08-13']]],
            'dateTo is not scalar (array)'     => [['dateTo' => ['2025-08-13']]],
            'dateTo is not scalar (object)'    => [['dateTo' => (object)['v' => '2025-08-13']]],
            'date is not scalar (array)'       => [['date' => ['2025-08-13']]],
            'date is not scalar (object)'      => [['date' => (object)['v' => '2025-08-13']]],
            'query is not scalar (array)'      => [['query' => ['oops']]],
            'query is not scalar (object)'     => [['query' => (object)['v' => 'oops']]],

            // multiple violations at once
            'page and perPage invalid'         => [['page' => [], 'perPage' => true]],
            'sort and direction invalid'       => [['sortField' => ['x'], 'direction' => (object)['v' => 'DESC']]],
            'all fields wrong'                 => [[
                'page'      => (object)['p' => 1],
                'perPage'   => [],
                'sortField' => ['date'],
                'sortDir'   => (object)['v' => 'DESC'],
                'dateFrom'  => ['2025-08-13'],
                'dateTo'    => (object)['v' => '2025-08-13'],
                'date'      => [],
                'query'     => (object)['v' => 'q'],
            ]]
        ];

        return $cases;
    }
}
