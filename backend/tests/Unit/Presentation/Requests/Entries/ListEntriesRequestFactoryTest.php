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
 * Purpose:
 * Build a ListEntriesRequest DTO from raw HTTP input.
 * Perform transport-level validation in fail-first style:
 * - Each field may be absent/null (handled by normalizer).
 * - If provided, the type must match expectations (numeric for page/perPage, string for others).
 * - On violation, throw TransportValidationException immediately with a single error code.
 *
 * Notes:
 * - Business rules (date validity, query length, clamping) are validated later in Application layer.
 *
 * @covers \Daylog\Presentation\Requests\Entries\ListEntriesRequestFactory
 * @group UC-ListEntries
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
        $this->assertSame($input['page'],      $dto->getPage());
        $this->assertSame($input['perPage'],   $dto->getPerPage());
        $this->assertSame($input['sortField'], $dto->getSort());
        $this->assertSame($input['sortDir'],   $dto->getDirection());
    }

    /**
     * Transport validation errors: For invalid type, throws TransportValidationException (fail-first).
     *
     * @dataProvider provideInvalidTransportData
     *
     * @param array<string,mixed> $overrides
     * @param string              $expectedCode
     * @return void
     */
    public function testFromArrayThrowsOnInvalidTransportData(array $overrides, string $expectedCode): void
    {
        // Arrange
        $data = ListEntriesHelper::getData();
        $data = array_merge($data, $overrides);

        // Expectation
        $this->expectException(TransportValidationException::class);
        $this->expectExceptionMessage($expectedCode);

        // Act
        ListEntriesRequestFactory::fromArray($data);
    }

    /**
     * Provides invalid transport-level cases for ListEntriesRequestFactory.
     *
     * Contract:
     * - page, perPage: must be numeric if provided.
     * - sortField, sortDir, dateFrom, dateTo, date, query: must be string if provided.
     *
     * @return array<string, array{0: array<string,mixed>, 1: string}>
     */
    public static function provideInvalidTransportData(): array
    {
        $cases = [
            'page not numeric (bool)'      => [['page' => true],                            'PAGE_MUST_BE_NUMERIC'],
            'perPage not numeric (array)'  => [['perPage' => []],                           'PER_PAGE_MUST_BE_NUMERIC'],
            'sortField not string (array)' => [['sortField' => []],                         'SORT_FIELD_MUST_BE_STRING'],
            'sortDir not string (object)'  => [['sortDir' => (object)['v' => 'DESC']],      'DIRECTION_MUST_BE_STRING'],
            'dateFrom not string (bool)'   => [['dateFrom' => false],                       'DATE_FROM_MUST_BE_STRING'],
            'dateTo not string (array)'    => [['dateTo' => [123]],                         'DATE_TO_MUST_BE_STRING'],
            'date not string (object)'     => [['date' => (object)['y' => '2025-09-04']],   'DATE_MUST_BE_STRING'],
            'query not string (array)'     => [['query' => ['oops']],                       'QUERY_MUST_BE_STRING'],
        ];

        return $cases;
    }
}
