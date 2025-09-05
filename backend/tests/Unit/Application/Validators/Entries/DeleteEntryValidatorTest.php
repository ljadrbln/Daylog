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

use Daylog\Tests\Support\DataProviders\IdDomainDataProvider;

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
    use IdDomainDataProvider;

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
     * @dataProvider provideInvalidUuidCases
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
}
