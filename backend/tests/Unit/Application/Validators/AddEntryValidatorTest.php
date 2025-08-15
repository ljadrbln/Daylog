<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\Validators;

use Codeception\Test\Unit;
use Daylog\Application\Validators\Entries\AddEntryValidator;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Application\DTO\Entries\AddEntryRequest;
use Daylog\Application\DTO\Entries\AddEntryRequestInterface;
use Daylog\Tests\Support\Helper\EntryHelper;

/**
 * Unit tests for AddEntryValidator (domain-level validation).
 *
 * Validates business rules for adding an entry:
 * - title: non-empty, within max length
 * - body:  non-empty, within max length
 * - date:  valid ISO format (YYYY-MM-DD) and valid calendar date
 *
 * Transport concerns (presence/types) are NOT checked here; they belong to Presentation.
 *
 * @covers \Daylog\Application\Validators\Entries\AddEntryValidator
 */
final class AddEntryValidatorTest extends Unit
{
    /**
     * AC-1: Valid DTO passes validation (no exception).
     *
     * @return void
     */
    public function testValidatePassesOnValidData(): void
    {
        $validator = new AddEntryValidator();

        $base = EntryHelper::getData();
        $dto  = AddEntryRequest::fromArray($base);

        $validator->validate($dto);

        $result = true;
        $this->assertTrue($result);
    }

    /**
     * AC-2..AC-n: Invalid business rules cause DomainValidationException.
     *
     * @dataProvider provideInvalidDomainCases
     *
     * @param array<string,string> $overrides
     * @return void
     */
    public function testValidateThrowsOnDomainViolations(array $overrides): void
    {
        $validator = new AddEntryValidator();

        $base = EntryHelper::getData();
        $data = array_merge($base, $overrides);

        $dto = AddEntryRequest::fromArray($data);

        $this->expectException(DomainValidationException::class);

        $validator->validate($dto);
    }

    /**
     * Provides invalid domain-level cases:
     * - empty title
     * - too long title
     * - empty body
     * - too long body
     * - invalid date format
     * - invalid calendar date
     *
     * @return array<string,array{0:array<string,string>}>
     */
    public function provideInvalidDomainCases(): array
    {
        // Create «too longs» strings; actual limits will be detemined by validator.
        $tooLongTitle = str_repeat('T', 201);     // expect TITLE_MAX = 200
        $tooLongBody  = str_repeat('B', 50001);   // expect BODY_MAX  = 50000

        $cases = [
            'title empty'             => [['title' => '']],
            'title too long'          => [['title' => $tooLongTitle]],
            'body empty'              => [['body'  => '']],
            'body too long'           => [['body'  => $tooLongBody]],
            'date invalid format'     => [['date'  => '15-08-2025']],
            'date invalid calendar'   => [['date'  => '2025-02-30']],
        ];

        return $cases;
    }
}
