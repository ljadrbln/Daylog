<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\UpdateEntry;

use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Domain\Services\UuidGenerator;
use Daylog\Tests\Support\Factory\UpdateEntryTestRequestFactory;

/**
 * UC-5 / AC-13 — Invalid date.
 *
 * Purpose:
 *   Given a date that does not match YYYY-MM-DD or is not a real date,
 *   validation must fail with DATE_INVALID and repository must not be touched.
 *
 * Mechanics:
 *   - Generate a valid UUID and build a request with an invalid date (e.g., '2025-02-30').
 *   - Configure validator mock to throw DomainValidationException('DATE_INVALID').
 *   - Execute the use case and assert that repository save was not called.
 *
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry::execute
 * @group UC-UpdateEntry
 */
final class AC13_InvalidDateTest extends BaseUpdateEntryUnitTest
{
    /**
     * Validate that invalid date triggers DATE_INVALID and repo remains untouched.
     *
     * @return void
     */
    public function testInvalidDateFailsValidationAndRepoUntouched(): void
    {
        // Arrange
        $id = UuidGenerator::generate();

        /** @var UpdateEntryRequestInterface $request */
        $request = UpdateEntryTestRequestFactory::invalidDate($id);

        $repo = $this->makeRepo();

        $errorCode = 'DATE_INVALID';
        $validator = $this->makeValidatorThrows($errorCode);

        $exceptionClass = DomainValidationException::class;
        $this->expectException($exceptionClass);

        // Act
        $useCase = $this->makeUseCase($repo, $validator);
        $useCase->execute($request);

        // Assert
        $saveCalls = $repo->getSaveCalls();
        $this->assertSame(0, $saveCalls);
    }
}
