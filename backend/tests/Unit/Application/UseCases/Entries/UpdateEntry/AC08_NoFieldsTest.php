<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\UpdateEntry;

use Daylog\Tests\Support\Factory\UpdateEntryTestRequestFactory;
use Daylog\Tests\Support\Assertion\UpdateEntryErrorAssertions;

/**
 * UC-5 / AC-08 — No fields to update.
 *
 * Purpose:
 *   Given only an id without any fields to update, the use case must fail
 *   validation with NO_FIELDS_TO_UPDATE and must not touch the repository.
 *
 * Mechanics:
 *   - Build UpdateEntryRequest with a valid UUID and no updatable fields via the shared factory.
 *   - Configure the validator mock to throw DomainValidationException('NO_FIELDS_TO_UPDATE').
 *   - Execute the use case and assert that repository save was not called.
 *
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry::execute
 * @group UC-UpdateEntry
 */
final class AC08_NoFieldsTest extends BaseUpdateEntryUnitTest
{
    use UpdateEntryErrorAssertions;

    /**
     * Validate that absence of updatable fields triggers NO_FIELDS_TO_UPDATE and repo remains untouched.
     *
     * @return void
     */
    public function testNoFieldsToUpdateFailsValidationAndRepoUntouched(): void
    {
        // Arrange
        $errorCode = 'NO_FIELDS_TO_UPDATE';
        $validator = $this->makeValidatorThrows($errorCode);
        $request   = UpdateEntryTestRequestFactory::idOnly();
        $repo      = $this->makeRepo();

        // Expect
        $this->expectNoFieldsToUpdate();

        // Act
        $useCase = $this->makeUseCase($repo, $validator);
        $useCase->execute($request);

        // Assert
        $saveCalls = $repo->getSaveCalls();
        $this->assertSame(0, $saveCalls);
    }
}
