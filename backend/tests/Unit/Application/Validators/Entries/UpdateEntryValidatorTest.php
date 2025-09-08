<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\Validators\Entries;

use Codeception\Test\Unit;
use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequest;
use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Application\Validators\Entries\UpdateEntry\UpdateEntryValidator;
use Daylog\Application\Validators\Entries\UpdateEntry\UpdateEntryValidatorInterface;
use Daylog\Tests\Support\DataProviders\EntryFieldsDomainDataProvider;
use Daylog\Tests\Support\Helper\EntryTestData;

/**
 * Unit tests for UpdateEntryValidator (domain-level validation).
 *
 * Purpose:
 * Verify UC-5 rules at the Application layer, excluding transport concerns:
 * - 'id' must be a UUID v4 (string ensured by transport).
 * - At least one of title/body/date must be provided.
 * - Provided title/body must be non-empty and within length limits.
 * - Provided date must be strict YYYY-MM-DD and a real calendar date.
 *
 * Mechanics:
 * - Baseline input is a valid Entry plus 'id'; then string-only overrides are applied.
 * - For "no fields to update" the DTO contains only 'id'.
 * - Expect DomainValidationException with a specific error code for invalid cases.
 *
 * @covers \Daylog\Application\Validators\Entries\UpdateEntry\UpdateEntryValidator
 * @group UC-UpdateEntry
 */
final class UpdateEntryValidatorTest extends Unit
{
    use EntryFieldsDomainDataProvider;

    /** @var UpdateEntryValidatorInterface */
    private UpdateEntryValidatorInterface $validator;

    /**
     * Prepare validator instance before each test.
     *
     * @return void
     */
    protected function _before(): void
    {
        $this->validator = new UpdateEntryValidator();
    }

    /**
     * Validation rule: Valid DTO passes validation (no exception).
     *
     * @return void
     */
    public function testValidatePassesOnValidData(): void
    {
        $data = EntryTestData::getOne();

        /** @var UpdateEntryRequestInterface $request */
        $request  = UpdateEntryRequest::fromArray($data);

        $this->validator->validate($request);

        $this->assertTrue(true);
    }

    /**
     * Validation rules: invalid business input yields DomainValidationException
     * with the correct error code.
     *
     * @dataProvider provideInvalidDomainAddEntryCases
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

        /** @var UpdateEntryRequestInterface $request */
        $request = UpdateEntryRequest::fromArray($data);

        // Expect
        $this->expectException(DomainValidationException::class);
        $this->expectExceptionMessage($expectedCode);

        // Act
        $this->validator->validate($request);
    }
}
