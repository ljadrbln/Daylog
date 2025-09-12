<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\UpdateEntry;

use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Domain\Services\UuidGenerator;
use Daylog\Tests\Support\Factory\UpdateEntryTestRequestFactory;

/**
 * UC-5 / AC-11 — Empty body.
 *
 * Purpose:
 *   Given a body that is empty after trimming, validation must fail with
 *   BODY_REQUIRED and the repository must not be touched.
 *
 * Mechanics:
 *   - Build UpdateEntryRequest with a valid UUID and a whitespace-only body ('   ').
 *   - Configure validator mock to throw DomainValidationException('BODY_REQUIRED').
 *   - Execute the use case and assert that repository save was not called.
 *
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry::execute
 * @group UC-UpdateEntry
 */
final class AC11_EmptyBodyTest extends BaseUpdateEntryUnitTest
{
    /**
     * Validate that empty (after trimming) body triggers BODY_REQUIRED and repo remains untouched.
     *
     * @return void
     */
    public function testEmptyBodyFailsValidationAndRepoUntouched(): void
    {
        // Arrange
        $id = UuidGenerator::generate();

        /** @var UpdateEntryRequestInterface $request */
        $request = UpdateEntryTestRequestFactory::emptyBody($id);

        $repo = $this->makeRepo();

        $errorCode = 'BODY_REQUIRED';
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
