<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\Validators\Entries;

use Codeception\Test\Unit;
use Daylog\Domain\Models\Entries\EntryConstraints;
use Daylog\Application\DTO\Entries\AddEntry\AddEntryRequest;
use Daylog\Application\DTO\Entries\AddEntry\AddEntryRequestInterface;
use Daylog\Application\Validators\Entries\AddEntry\AddEntryValidator;
use Daylog\Application\Validators\Entries\AddEntry\AddEntryValidatorInterface;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Tests\Support\Helper\EntryTestData;

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
 * @group UC-AddEntry
 */
final class AddEntryValidatorTest extends Unit
{
    /** @var AddEntryValidatorInterface */
    private AddEntryValidatorInterface $validator;

    protected function _before(): void
    {
        $this->validator = new AddEntryValidator();
    }

    /**
     * Validation rule: Valid DTO passes validation (no exception).
     *
     * @return void
     */
    public function testValidatePassesOnValidData(): void
    {
        $data = EntryTestData::getOne();

        /** @var AddEntryRequestInterface $request */
        $request  = AddEntryRequest::fromArray($data);

        $this->validator->validate($request);

        $this->assertTrue(true);
    }

    /**
     * Validation rules: invalid business input yields DomainValidationException
     * with the correct error code.
     *
     * @dataProvider provideInvalidDomainCases
     *
     * @param array<string,string> $overrides
     * @param string               $expectedCode
     * @return void
     */
    public function testValidateThrowsOnDomainViolations(array $overrides, string $expectedCode): void
    {
        // Arrange
        $data = EntryTestData::getOne();
        $data = array_merge($data, $overrides);

        /** @var AddEntryRequestInterface $request */
        $request = AddEntryRequest::fromArray($data);

        // Expect
        $this->expectException(DomainValidationException::class);
        $this->expectExceptionMessage($expectedCode);

        // Act
        $this->validator->validate($request);
    }

    /**
     * Provides invalid domain-level cases with expected error codes.
     *
     * @return array<string,array{0:array<string,string>,1:string}>
     */
    public function provideInvalidDomainCases(): array
    {
        $tooLongTitle = str_repeat('T', EntryConstraints::TITLE_MAX + 1);
        $tooLongBody  = str_repeat('B', EntryConstraints::BODY_MAX + 1);

        return [
            'title is empty'           => [['title' => ''], 'TITLE_REQUIRED'],
            'title is too long'        => [['title' => $tooLongTitle], 'TITLE_TOO_LONG'],
            'body is empty'            => [['body' => ''], 'BODY_REQUIRED'],
            'body is too long'         => [['body' => $tooLongBody], 'BODY_TOO_LONG'],
            'date invalid format'      => [['date' => '15-08-2025'], 'DATE_INVALID'],
            'date invalid calendar'    => [['date' => '2025-02-30'], 'DATE_INVALID'],
            'date is empty'            => [['date' => ''], 'DATE_REQUIRED'],
        ];
    }
}
