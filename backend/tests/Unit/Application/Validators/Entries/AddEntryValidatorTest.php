<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\Validators\Entries;

use Codeception\Test\Unit;
use Daylog\Domain\Models\Entries\EntryConstraints;
use Daylog\Application\Validators\Entries\AddEntryValidator;
use Daylog\Application\Validators\Entries\AddEntryValidatorInterface;
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
        $data = EntryHelper::getData();

        /** @var AddEntryRequestInterface $request */
        $request  = AddEntryRequest::fromArray($data);

        $this->validator->validate($request);

        $this->assertTrue(true);
    }

    /**
     * Validation rules: invalid business input yields DomainValidationException.
     *
     * @dataProvider provideInvalidDomainCases
     *
     * @param array<string,string> $overrides
     * @return void
     */
    public function testValidateThrowsOnDomainViolations(array $overrides): void
    {
        $data = EntryHelper::getData();
        $data = array_merge($data, $overrides);

        /** @var AddEntryRequestInterface $request */
        $request = AddEntryRequest::fromArray($data);

        $this->expectException(DomainValidationException::class);

        $this->validator->validate($request);
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
        $tooLongTitle = str_repeat('T', EntryConstraints::TITLE_MAX+1);  // expect TITLE_MAX = 200
        $tooLongBody  = str_repeat('B', EntryConstraints::BODY_MAX+1);   // expect BODY_MAX  = 50000

        $cases = [
            'title is empty'             => [['title' => '']],
            'title is too long'          => [['title' => $tooLongTitle]],
            'body is empty'              => [['body'  => '']],
            'body is too long'           => [['body'  => $tooLongBody]],
            'date is invalid format'     => [['date'  => '15-08-2025']],
            'date is invalid calendar'   => [['date'  => '2025-02-30']],
        ];

        return $cases;
    }
}
