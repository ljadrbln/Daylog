<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Presentation\Requests\Entries\DeleteEntry;

use Codeception\Test\Unit;
use Daylog\Presentation\Requests\Entries\DeleteEntry\DeleteEntryRequestFactory;
use Daylog\Application\Exceptions\TransportValidationException;
use Daylog\Tests\Support\Helper\EntryTestData;
use Daylog\Tests\Support\DataProviders\IdTransportDataProvider;

/**
 * Unit tests for DeleteEntryRequestFactoryTest.
 *
 * Purpose:
 * Build a DeleteEntryRequest DTO from raw HTTP input (e.g., $_GET/JSON).
 * Perform transport-level validation only: 'id' must be present and a string.
 * Business validation (UUID v4) is performed later in the Application layer.
 *
 * Mechanics:
 * - Happy path returns a typed DTO with mapped 'id'.
 * - Invalid transport shapes (missing/non-string 'id') raise TransportValidationException.
 *
 * @covers \Daylog\Presentation\Requests\Entries\DeleteEntry\DeleteEntryRequestFactoryTest
 * @group UC-DeleteEntry
 */
final class DeleteEntryRequestFactoryTest extends Unit
{
    use IdTransportDataProvider;

    /**
     * Happy path: Given valid input, returns a DTO with mapped id.
     *
     * @return void
     */
    public function testFromArrayReturnsDtoOnValidInput(): void
    {
        // Arrange
        $input = EntryTestData::getOne();

        // Act
        $dto = DeleteEntryRequestFactory::fromArray($input);

        // Assert
        $this->assertSame($input['id'], $dto->getId());
    }    

    /**
     * Transport validation errors: For invalid type or missing field, throws TransportValidationException.
     *
     * @dataProvider provideInvalidTransportIdData
     *
     * @param array<string,mixed> $data
     * @param string              $expectedCode
     * @return void
     */
    public function testFromArrayThrowsOnInvalidTransportData(array $data, string $expectedCode): void
    {
        // Assert
        $this->expectException(TransportValidationException::class);
        $this->expectExceptionMessage($expectedCode);

        // Act
        DeleteEntryRequestFactory::fromArray($data);
    }
}
