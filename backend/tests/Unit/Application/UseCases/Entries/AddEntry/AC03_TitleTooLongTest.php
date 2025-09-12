<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\AddEntry;

use Daylog\Application\DTO\Entries\AddEntry\AddEntryRequest;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Tests\Support\Helper\EntryTestData;

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
    /**
     * TITLE_TOO_LONG must stop execution before persistence.
     *
     * @return void
     */
    public function testTitleTooLongStopsBeforePersistence(): void
    {
        // Arrange
        $data = EntryTestData::getOne();

        /** @var \Daylog\Application\DTO\Entries\AddEntry\AddEntryRequestInterface $request */
        $request = AddEntryRequest::fromArray($data);

        $repo      = $this->makeRepo();
        $errorCode = 'TITLE_TOO_LONG';
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
