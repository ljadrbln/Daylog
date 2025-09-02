<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Presentation\Requests\Entries;

use Codeception\Test\Unit;
use Daylog\Presentation\Requests\Entries\GetEntryRequestFactory;
use Daylog\Application\DTO\Entries\GetEntry\GetEntryRequestInterface;
use Daylog\Application\Exceptions\TransportValidationException;
use Daylog\Tests\Support\Helper\EntryTestData;

/**
 * Unit tests for GetEntryRequestFactory.
 *
 * Purpose:
 * Build a GetEntryRequest DTO from raw HTTP input (e.g., $_GET/JSON).
 * Perform transport-level validation only: 'id' must be present and a string.
 * Business validation (UUID v4) is performed later in the Application layer.
 *
 * Mechanics:
 * - Happy path returns a typed DTO with mapped 'id'.
 * - Invalid transport shapes (missing/non-string 'id') raise TransportValidationException.
 *
 * @covers \Daylog\Presentation\Requests\Entries\GetEntryRequestFactory
 */
final class GetEntryRequestFactoryTest extends Unit
{
    /**
     * Happy path: Given valid input, returns a DTO with mapped id.
     *
     * @return void
     */
    public function testFromArrayReturnsDtoOnValidInput(): void
    {
        // Arrange
        $factory = new GetEntryRequestFactory();
        $input   = EntryTestData::getOne();

        // Act
        $dto = $factory->fromArray($input);

        // Assert
        $this->assertInstanceOf(GetEntryRequestInterface::class, $dto);
        $this->assertSame($input['id'], $dto->getId());
    }    

    /**
     * Transport validation errors: For invalid type or missing field, throws TransportValidationException.
     *
     * Cases:
     * - id missing (null)
     * - id is array
     * - id is int
     *
     * @dataProvider provideInvalidTransportData
     *
     * @param array<string,mixed> $data
     * @return void
     */
    public function testFromArrayThrowsOnInvalidTransportData(array $data): void
    {
        // Assert
        $this->expectException(TransportValidationException::class);

        // Act
        GetEntryRequestFactory::fromArray($data);
    }

    /**
     * Provides invalid transport-level cases for GetEntryRequestFactory.
     *
     * @return array<string,array{0:array<string,mixed>}>
     */
    public function provideInvalidTransportData(): array
    {
        $cases = [
            'id missing'  => [['id' => null]],
            'id is array' => [['id' => ['oops']]],
            'id is int'   => [['id' => 123]],
        ];

        return $cases;
    }
}
