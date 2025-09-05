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

use Daylog\Tests\Support\DataProviders\IdDomainDataProvider;

/**
 * Unit tests for GetEntryValidator (domain-level validation for UC: GetEntry).
 *
 * Purpose:
 * Validate that only RFC-4122 UUIDs (v1..v5) pass. Transport concerns are out of scope.
 *
 * Mechanics:
 * - Happy path: a generated UUID v4 passes with no exception.
 * - Error paths (empty/short/invalid-char/bad-hyphens/wrong-length) raise DomainValidationException
 *   with the specific error code supplied by the data provider.
 *
 * @covers \Daylog\Application\Validators\Entries\GetEntry\GetEntryValidator
 * @group UC-GetEntry
 */
final class GetEntryValidatorTest extends Unit
{
    use IdDomainDataProvider;

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

        /** @var GetEntryRequestInterface $request */
        $request = GetEntryRequest::fromArray($data);

        $exception = DomainValidationException::class;
        $this->expectException($exception);

        $message = $expectedCode;
        $this->expectExceptionMessage($message);

        $this->validator->validate($request);
    }
}
