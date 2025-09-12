<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\UpdateEntry;

use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface;
use Daylog\Tests\Support\Assertion\UpdateEntryErrorAssertions;
use Daylog\Tests\Support\Factory\UpdateEntryTestRequestFactory;

/**
 * UC-5 / AC-05 — Missing id.
 *
 * Purpose:
 * When the id is missing (empty after trimming), domain validation must fail
 * with ID_REQUIRED and the repository must not be touched.
 *
 * Mechanics:
 * - Build UpdateEntryRequest with an empty 'id' to simulate missing value.
 * - Use validator stub that throws DomainValidationException('ID_REQUIRED').
 * - Assert that Fake repository's saveCalls() remains 0 to prove no persistence.
 *
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry::execute
 * @group UC-UpdateEntry
 */
final class AC05_MissingIdTest extends BaseUpdateEntryUnitTest
{
    use UpdateEntryErrorAssertions;

    /**
     * Validate that missing id triggers ID_REQUIRED and repo is not used.
     *
     * @return void
     */
    public function testMissingIdFailsValidationAndRepoUntouched(): void
    {
        // Arrange
        /** @var UpdateEntryRequestInterface $request */
        $request = UpdateEntryTestRequestFactory::missingId();

        $repo = $this->makeRepo();

        $errorCode = 'ID_REQUIRED';
        $validator = $this->makeValidatorThrows($errorCode);

        // Expect
        $this->expectIdRequired();

        // Act
        $useCase = $this->makeUseCase($repo, $validator);
        $useCase->execute($request);

        // Assert
        $saveCalls = $repo->getSaveCalls();
        $this->assertSame(0, $saveCalls);
    }
}
