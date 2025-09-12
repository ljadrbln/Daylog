<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\UpdateEntry;

use Daylog\Tests\Support\Factory\UpdateEntryTestRequestFactory;
use Daylog\Tests\Support\Assertion\UpdateEntryErrorAssertions;


/**
 * UC-5 / AC-06 — Invalid id.
 *
 * Purpose:
 *   Given a non-UUID id, validation must fail with ID_INVALID and the repository
 *   must remain untouched (no save attempts).
 *
 * Mechanics:
 *   - Build UpdateEntryRequest via UpdateEntryTestRequestFactory::titleOnly() with malformed id.
 *   - Configure validator mock to throw DomainValidationException('ID_INVALID').
 *   - Execute the use case and verify no persistence by asserting saveCalls() is zero.
 *
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry::execute
 * @group UC-UpdateEntry
 */
final class AC06_InvalidIdTest extends BaseUpdateEntryUnitTest
{
    use UpdateEntryErrorAssertions;

    /**
     * Validate that a non-UUID id triggers ID_INVALID and repo stays untouched.
     *
     * @return void
     */
    public function testInvalidIdFailsValidationAndRepoUntouched(): void
    {
        // Arrange
        $errorCode = 'ID_INVALID';
        $validator = $this->makeValidatorThrows($errorCode);
        $request   = UpdateEntryTestRequestFactory::invalidId();
        $repo      = $this->makeRepo();

        // Expect
        $this->expectIdInvalid();

        // Act
        $useCase = $this->makeUseCase($repo, $validator);
        $useCase->execute($request);

        // Assert
        $this->assertRepoUntouched($repo);
    }
}
