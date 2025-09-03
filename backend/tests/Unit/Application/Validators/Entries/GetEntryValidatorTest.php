<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\Validators\Entries;

use Codeception\Test\Unit;
use Daylog\Application\DTO\Entries\GetEntry\GetEntryRequest;
use Daylog\Application\DTO\Entries\GetEntry\GetEntryRequestInterface;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Application\Validators\Entries\GetEntry\GetEntryValidator;
use Daylog\Application\Validators\Entries\GetEntry\GetEntryValidatorInterface;
use Daylog\Domain\Services\UuidGenerator;

/**
 * Unit tests for GetEntryValidator (domain-level validation for UC: GetEntry).
 *
 * Purpose:
 * Ensure that the validator enforces domain rules for entry identifiers.
 * It must accept only UUIDs matching RFC-4122 (v1..v5).
 *
 * Mechanics:
 * - Happy path: generated UUID v4 is accepted.
 * - Error paths: malformed IDs (empty, short, bad chars, bad hyphens) must raise DomainValidationException.
 *
 * @covers \Daylog\Application\Validators\Entries\GetEntry\GetEntryValidator
 * @group UC-GetEntry
 */
final class GetEntryValidatorTest extends Unit
{
    /** @var GetEntryValidatorInterface */
    private GetEntryValidatorInterface $validator;

    protected function _before(): void
    {
        $this->validator = new GetEntryValidator();
    }

    /**
     * Validation rule: valid UUID passes (no exception).
     *
     * @return void
     */
    public function testValidatePassesOnValidUuid(): void
    {
        $id = UuidGenerator::generate();

        /** @var array<string,string> $data */
        $data = ['id' => $id];

        /** @var GetEntryRequestInterface $request */
        $request = GetEntryRequest::fromArray($data);

        $this->validator->validate($request);

        $assertion = true;
        $this->assertTrue($assertion);
    }

    /**
     * Validation rules: malformed IDs must trigger DomainValidationException.
     *
     * @dataProvider provideInvalidIdCases
     *
     * @param string $badId
     * @return void
     */
    public function testValidateThrowsOnInvalidId(string $badId): void
    {
        /** @var array<string,string> $data */
        $data = ['id' => $badId];

        /** @var GetEntryRequestInterface $request */
        $request = GetEntryRequest::fromArray($data);

        $exception = DomainValidationException::class;
        $this->expectException($exception);

        $this->validator->validate($request);
    }

    /**
     * Provides malformed UUID cases for negative validation paths.
     *
     * @return array<string,array{0:string}>
     */
    public function provideInvalidIdCases(): array
    {
        $cases = [
            'empty string'         => [''],
            'too short token'      => ['123'],
            'non-hex character'    => ['123e4567-e89b-12d3-a456-42661417400g'],
            'wrong length (35)'    => ['123e4567-e89b-12d3-a456-42661417400'],
            'bad hyphenation'      => ['123e4567e89b-12d3-a456-426614174000'],
        ];

        return $cases;
    }
}
