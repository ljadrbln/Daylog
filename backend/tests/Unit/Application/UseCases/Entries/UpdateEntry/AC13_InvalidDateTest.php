<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\UpdateEntry;

use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequest;
use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Domain\Services\UuidGenerator;

/**
 * UC-5 / AC-13 — Invalid date.
 *
 * Purpose:
 * Given a date that does not match YYYY-MM-DD or is not a real date,
 * validation must fail with DATE_INVALID and repository must not be touched.
 *
 * Mechanics:
 * - Build UpdateEntryRequest with valid UUID and invalid date (e.g., '2025-02-30').
 * - Domain validator throws DomainValidationException('DATE_INVALID').
 * - Assert that Fake repository's saveCalls() remains 0.
 *
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry::execute
 * @group UC-UpdateEntry
 */
final class AC13_InvalidDateTest extends BaseUpdateEntryUnitTest
{
    /**
     * Validate that invalid date fails with DATE_INVALID and repo remains untouched.
     *
     * @return void
     */
    public function testInvalidDateFailsValidationAndRepoUntouched(): void
    {
        // Arrange
        $id = UuidGenerator::generate();

        $payload = [
            'id'   => $id,
            'date' => '2025-02-30', // invalid calendar date
        ];

        /** @var UpdateEntryRequestInterface $request */
        $request = UpdateEntryRequest::fromArray($payload);

        $repo      = $this->makeRepo();
        $errorCode = 'DATE_INVALID';
        $validator = $this->makeValidatorThrows($errorCode);

        $this->expectException(DomainValidationException::class);

        // Act
        $useCase = $this->makeUseCase($repo, $validator);
        $useCase->execute($request);

        // Assert
        $saveCalls = $repo->getSaveCalls();
        $this->assertSame(0, $saveCalls);
    }
}
