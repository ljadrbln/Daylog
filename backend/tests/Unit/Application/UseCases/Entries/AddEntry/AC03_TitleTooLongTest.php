<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\AddEntry;

use Daylog\Tests\Support\Assertion\EntryValidationAssertions;
use Daylog\Tests\Support\Factory\AddEntryTestRequestFactory;

/**
 * UC-1 / AC-03 — Title too long — Unit.
 *
 * Purpose:
 * Ensure validator reports TITLE_TOO_LONG and repository is untouched.
 *
 * Mechanics:
 * - Use valid baseline; force validator to throw TITLE_TOO_LONG.
 *
 * @covers \Daylog\Application\UseCases\Entries\AddEntry\AddEntry::execute
 * @group UC-AddEntry
 */
final class AC03_TitleTooLongTest extends BaseAddEntryUnitTest
{
    use EntryValidationAssertions;

    /**
     * TITLE_TOO_LONG must stop execution before persistence.
     *
     * @return void
     */
    public function testTitleTooLongStopsBeforePersistence(): void
    {
        // Arrange
        $errorCode = 'TITLE_TOO_LONG';
        $validator = $this->makeValidatorThrows($errorCode);
        $request   = AddEntryTestRequestFactory::titleTooLong();
        $repo      = $this->makeRepo();

        // Expect
        $this->expectTitleTooLong();

        // Act
        $useCase = $this->makeUseCase($repo, $validator);
        $useCase->execute($request);

        // Assert
        $this->assertRepoUntouched($repo);
    }
}
