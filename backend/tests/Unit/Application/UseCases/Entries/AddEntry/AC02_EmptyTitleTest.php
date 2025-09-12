<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\AddEntry;

use Daylog\Application\DTO\Entries\AddEntry\AddEntryRequest;
use Daylog\Tests\Support\Helper\EntryTestData;
use Daylog\Tests\Support\Assertion\EntryValidationAssertions;

/**
 * UC-1 / AC-02 — Empty title — Unit.
 *
 * Purpose:
 * Ensure that when title is effectively empty (transport provides something but
 * domain sees it empty after trim/sanitize), the validator fails with TITLE_REQUIRED
 * and repository is untouched.
 *
 * Mechanics:
 * - Build a formally "valid" payload; force validator to throw the domain code.
 * - Verify that use case propagates DomainValidationException and save() is not called.
 *
 * @covers \Daylog\Application\UseCases\Entries\AddEntry\AddEntry::execute
 * @group UC-AddEntry
 */
final class AC02_EmptyTitleTest extends BaseAddEntryUnitTest
{
    use EntryValidationAssertions;

    /**
     * Validator throws TITLE_REQUIRED; repo must remain untouched.
     *
     * @return void
     */
    public function testEmptyTitleFailsWithTitleRequiredAndRepoUntouched(): void
    {
        // Arrange        
        $data      = EntryTestData::getOne();
        $request   = AddEntryRequest::fromArray($data);
        $errorCode = 'TITLE_REQUIRED';
        $validator = $this->makeValidatorThrows($errorCode);
        $repo      = $this->makeRepo();

        // Expect
        $this->expectTitleRequired();

        // Act
        $useCase = $this->makeUseCase($repo, $validator);
        $useCase->execute($request);

        // Assert
        $this->assertRepoUntouched($repo);
    }
}
