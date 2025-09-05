<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\Validators\Entries;

use Codeception\Test\Unit;
use Daylog\Application\DTO\Entries\DeleteEntry\DeleteEntryRequest;
use Daylog\Application\DTO\Entries\DeleteEntry\DeleteEntryRequestInterface;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Application\Validators\Entries\DeleteEntry\DeleteEntryValidator;
use Daylog\Application\Validators\Entries\DeleteEntry\DeleteEntryValidatorInterface;
use Daylog\Domain\Services\UuidGenerator;

/**
 * Unit tests for DeleteEntryValidator (domain-level validation for UC-4 DeleteEntry).
 *
 * Purpose:
 * Ensure only RFC-4122 UUIDs (v1..v5) pass. Transport concerns are out of scope.
 *
 * Mechanics:
 * - Happy path: a generated UUID v4 passes without exception.
 * - Error paths (empty/short/invalid-char/wrong-length/bad-hyphens) throw DomainValidationException
 *   with the expected error code provided by the data provider.
 *
 * @covers \Daylog\Application\Validators\Entries\DeleteEntry\DeleteEntryValidator
 * @group UC-DeleteEntry
 */
final class DeleteEntryValidatorTest extends Unit
{
    /** @var DeleteEntryValidatorInterface */
    private DeleteEntryValidatorInterface $validator;

    protected function _before(): void
    {
        $this->validator = new DeleteEntryValidator();
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

        /** @var DeleteEntryRequestInterface $request */
        $request = DeleteEntryRequest::fromArray($data);

        $this->validator->validate($request);

        $this->assertTrue(true);
    }

    /**
     * Validation rules: malformed IDs must trigger DomainValidationException
     * with the expected error code from the data provider.
     *
     * @dataProvider provideInvalidIdCases
     *
     * @param string $badId
     * @param string $expectedCode
     * @return void
     */
    public function testValidateThrowsOnInvalidId(string $badId, string $expectedCode): void
    {
        /** @var array<string,string> $data */
        $data = ['id' => $badId];

        /** @var DeleteEntryRequestInterface $request */
        $request = DeleteEntryRequest::fromArray($data);

        $exception = DomainValidationException::class;
        $this->expectException($exception);

        $message = $expectedCode;
        $this->expectExceptionMessage($message);

        $this->validator->validate($request);
    }

    /**
     * Provides malformed UUID cases and their expected error codes.
     *
     * Cases:
     * - empty string
     * - too short token
     * - non-hex character
     * - wrong length (35)
     * - bad hyphenation
     *
     * @return array<string,array{0:string,1:string}>
     */
    public function provideInvalidIdCases(): array
    {
        $cases = [
            'empty string'       => ['',                                        'ID_INVALID'],
            'too short token'    => ['123',                                     'ID_INVALID'],
            'non-hex character'  => ['123e4567-e89b-12d3-a456-42661417400g',    'ID_INVALID'],
            'wrong length (35)'  => ['123e4567-e89b-12d3-a456-42661417400',     'ID_INVALID'],
            'bad hyphenation'    => ['123e4567e89b-12d3-a456-426614174000',     'ID_INVALID'],
        ];

        return $cases;
    }
}
