<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Presentation\Requests\Entries\AddEntry;

use Codeception\Test\Unit;
use Daylog\Presentation\Requests\Entries\AddEntry\AddEntryRequestFactory;
use Daylog\Application\Exceptions\TransportValidationException;
use Daylog\Tests\Support\Helper\EntryTestData;

/**
 * Unit tests for AddEntryRequestFactory.
 *
 * This factory builds an AddEntryRequest DTO from raw HTTP input.
 * It performs transport-level validation: checks for presence and correct
 * types of required fields (`title`, `body`, `date`), but does not enforce
 * business rules (those are validated separately in Application layer).
 *
 * @covers \Daylog\Presentation\Requests\AddEntry\AddEntryRequestFactory
 * @group UC-AddEntry
 * 
 */
final class AddEntryRequestFactoryTest extends Unit
{
    /**
     * Happy path: Given valid input, returns a DTO with mapped values.
     *
     * @return void
     */
    public function testFromArrayReturnsDtoOnValidInput(): void
    {
        // Arrange
        $input   = EntryTestData::getOne();

        // Act
        $dto = AddEntryRequestFactory::fromArray($input);

        // Assert
        $this->assertSame($input['title'], $dto->getTitle());
        $this->assertSame($input['body'], $dto->getBody());
        $this->assertSame($input['date'], $dto->getDate());
    }

    /**
     * Transport validation errors: For invalid type or missing field, throws TransportValidationException.
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
        $data = EntryTestData::getOne();
        $data = array_merge($data, $overrides);

        // Expectation
        $this->expectException(TransportValidationException::class);
        $this->expectExceptionMessage($expectedCode);

        // Act
        AddEntryRequestFactory::fromArray($data);
    }

    /**
     * Provides invalid transport-level cases:
     * - title is array
     * - title missing
     * - body is array
     * - body missing
     * - date missing
     * - date is array
     *
     * @return array<string,array{0:array<string,mixed>}>
     */
    public function provideInvalidTransportData(): array
    {
        return [
            'title is array' => [['title' => ['oops']], 'TITLE_MUST_BE_STRING'],
            'title missing'  => [['title' => null],     'TITLE_MUST_BE_STRING'],
            'body is array'  => [['body'  => ['oops']], 'BODY_MUST_BE_STRING'],
            'body missing'   => [['body'  => null],     'BODY_MUST_BE_STRING'],
            'date missing'   => [['date'  => null],     'DATE_MUST_BE_STRING'],
            'date is array'  => [['date'  => ['oops']], 'DATE_MUST_BE_STRING'],
        ];
    }
}
