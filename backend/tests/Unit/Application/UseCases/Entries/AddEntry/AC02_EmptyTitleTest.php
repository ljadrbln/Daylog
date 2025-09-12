<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\AddEntry;

use Daylog\Application\DTO\Entries\AddEntry\AddEntryRequest;
use Daylog\Application\Validators\Entries\AddEntry\AddEntryValidatorInterface;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Tests\Support\Helper\EntryTestData;

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
    /**
     * Validator throws TITLE_REQUIRED; repo must remain untouched.
     *
     * @return void
     */
    public function testEmptyTitleFailsWithTitleRequiredAndRepoUntouched(): void
    {
        // Arrange
        $data = EntryTestData::getOne();

        /** @var \Daylog\Application\DTO\Entries\AddEntry\AddEntryRequestInterface $request */
        $request = AddEntryRequest::fromArray($data);

        $repo      = $this->makeRepo();
        $errorCode = 'TITLE_REQUIRED';
        $validator = $this->makeValidatorThrows($errorCode);

        // Expect
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
