<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Presentation\Requests\Entries\UpdateEntry;

use Codeception\Test\Unit;
use Daylog\Presentation\Requests\Entries\UpdateEntry\UpdateEntryRequestFactory;
use Daylog\Application\Exceptions\TransportValidationException;
use Daylog\Tests\Support\DataProviders\IdTransportDataProvider;
use Daylog\Tests\Support\Helper\EntryTestData;
use Daylog\Tests\Support\DataProviders\EntryFieldsTransportDataProvider;

/**
 * Unit tests for UpdateEntryRequestFactory.
 *
 * Purpose:
 * Build an UpdateEntryRequest DTO from raw HTTP input for UC-4.
 * The factory performs transport-level validation only:
 * - 'id' is required and must be a string (no UUID format check here).
 * - 'title' | 'body' | 'date' are optional; if present, they must be strings.
 * Business validation (lengths, date validity, "no fields to update", etc.) is handled elsewhere.
 *
 * Mechanics:
 * - Baseline payloads are derived from EntryTestData::getOne().
 * - Tests override specific fields to trigger transport errors.
 * - Happy-path tests verify correct field mapping for partial updates.
 *
 * @covers \Daylog\Presentation\Requests\Entries\UpdateEntry\UpdateEntryRequestFactory
 * @group UC-UpdateEntry
 */
final class UpdateEntryRequestFactoryTest extends Unit
{
    use IdTransportDataProvider;
    use EntryFieldsTransportDataProvider;

    /**
     * Happy path: id + title only → returns DTO with mapped values.
     *
     * @return void
     */
    public function testFromArrayReturnsDtoOnValidInputWithTitleOnly(): void
    {
        // Arrange
        $data = EntryTestData::getOne();

        // Act
        $dto = UpdateEntryRequestFactory::fromArray($data);

        // Assert
        $this->assertSame($data['title'],  $dto->getTitle());
        $this->assertSame($data['body'],   $dto->getBody());
        $this->assertSame($data['date'],   $dto->getDate());
        $this->assertSame($data['id'],     $dto->getId());
    }

    /**
     * Transport validation errors: For invalid type or missing field, throws TransportValidationException.
     *
     * @dataProvider provideInvalidTransportEntryData
     *
     * @param array<string,mixed> $overrides
     * @param string              $expectedCode
     * @return void
     */
    public function testFromArrayThrowsOnInvalidTransportData(array $overrides, string $expectedCode): void
    {
        // Arrange
        $data = EntryTestData::getOne();
        $data = array_merge($data, $overrides);

        // Expectation
        $this->expectException(TransportValidationException::class);
        $this->expectExceptionMessage($expectedCode);

        // Act
        UpdateEntryRequestFactory::fromArray($data);
    }
}
