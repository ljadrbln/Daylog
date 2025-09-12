<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\UpdateEntry;

use Daylog\Tests\Support\Factory\UpdateEntryTestRequestFactory;
use Daylog\Tests\Support\Assertion\UpdateEntryErrorAssertions;

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
    use UpdateEntryErrorAssertions;

    /**
     * Validate that empty (after trimming) body triggers BODY_REQUIRED and repo remains untouched.
     *
     * @return void
     */
    public function testEmptyBodyFailsValidationAndRepoUntouched(): void
    {
        // Arrange
        $errorCode = 'BODY_REQUIRED';
        $validator = $this->makeValidatorThrows($errorCode);
        $request   = UpdateEntryTestRequestFactory::emptyBody();
        $repo      = $this->makeRepo();
        
        // Expect
        $this->expectBodyRequired();

        // Act
        $useCase = $this->makeUseCase($repo, $validator);
        $useCase->execute($request);

        // Assert
        $this->assertRepoUntouched($repo);
    }
}
