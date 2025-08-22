<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Presentation\Requests\Entries;

use Codeception\Test\Unit;
use Daylog\Presentation\Requests\Entries\AddEntryRequestFactory;
use Daylog\Application\DTO\Entries\AddEntry\AddEntryRequestInterface;
use Daylog\Application\Exceptions\TransportValidationException;
use Daylog\Tests\Support\Helper\EntryHelper;

/**
 * Unit tests for AddEntryRequestFactory.
 *
 * This factory builds an AddEntryRequest DTO from raw HTTP input.
 * It performs transport-level validation: checks for presence and correct
 * types of required fields (`title`, `body`, `date`), but does not enforce
 * business rules (those are validated separately in Application layer).
 *
 * @covers \Daylog\Presentation\Requests\AddEntryRequestFactory
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
        $factory = new AddEntryRequestFactory();
        $input   = EntryHelper::getData();

        $dto = $factory->fromArray($input);

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
     * @return void
     */
    public function testFromArrayThrowsOnInvalidTransportData(array $overrides): void
    {
        $factory = new AddEntryRequestFactory();
        $data    = EntryHelper::getData();
        $data    = array_merge($data, $overrides);

        $this->expectException(TransportValidationException::class);
        $factory->fromArray($data);
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
            'title is array' => [['title' => ['oops']]],
            'title missing'  => [['title' => null]],
            'body is array'  => [['body'  => ['oops']]],
            'body missing'   => [['body'  => null]],
            'date missing'   => [['date'  => null]],
            'date is array'  => [['date'  => ['oops']]],
        ];
    }
}
