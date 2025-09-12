<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\AddEntry;

use Daylog\Tests\Support\Assertion\EntryValidationAssertions;
use Daylog\Tests\Support\Factory\AddEntryTestRequestFactory;

/**
 * UC-1 / AC-05 — Body too long — Unit.
 *
 * Purpose:
 * Ensure BODY_TOO_LONG triggers a domain error and the repository is not touched.
 *
 * @covers \Daylog\Application\UseCases\Entries\AddEntry\AddEntry::execute
 * @group UC-AddEntry
 */
final class AC05_BodyTooLongTest extends BaseAddEntryUnitTest
{
    use EntryValidationAssertions;

    /**
     * BODY_TOO_LONG must stop execution before persistence.
     *
     * @return void
     */
    public function testBodyTooLongStopsBeforePersistence(): void
    {
        // Arrange        
        $errorCode = 'BODY_TOO_LONG';
        $validator = $this->makeValidatorThrows($errorCode);
        $request   = AddEntryTestRequestFactory::bodyTooLong();
        $repo      = $this->makeRepo();

        // Expect
        $this->expectBodyTooLong();

        // Act
        $useCase = $this->makeUseCase($repo, $validator);
        $useCase->execute($request);

        // Assert
        $this->assertRepoUntouched($repo);
    }
}
