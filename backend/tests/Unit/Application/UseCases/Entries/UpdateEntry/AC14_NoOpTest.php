<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\UpdateEntry;

use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Domain\Models\Entries\Entry;
use Daylog\Tests\Support\Helper\EntryTestData;
use Daylog\Tests\Support\Factory\UpdateEntryTestRequestFactory;

/**
 * UC-5 / AC-14 — No-op update.
 *
 * Purpose:
 *   When all provided values are identical to the current entry, domain validator
 *   must report a no-op as an error by throwing DomainValidationException with
 *   code NO_CHANGES_APPLIED. Repository must not be touched.
 *
 * Mechanics:
 *   - Seed repository with an existing entry.
 *   - Build request with identical title/body/date via factory.
 *   - Validator throws DomainValidationException('NO_CHANGES_APPLIED').
 *   - Assert repo->getSaveCalls() stays 0.
 *
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry::execute
 * @group UC-UpdateEntry
 */
final class AC14_NoOpTest extends BaseUpdateEntryUnitTest
{
    /**
     * Verify that identical values cause validator to throw NO_CHANGES_APPLIED and repo remains untouched.
     *
     * @return void
     */
    public function testNoOpUpdateThrowsAndRepoUntouched(): void
    {
        // Arrange
        $data  = EntryTestData::getOne();
        $entry = Entry::fromArray($data);

        $repo = $this->makeRepo();
        $repo->save($entry);

        /** @var UpdateEntryRequestInterface $request */
        $request = UpdateEntryTestRequestFactory::noOp($data);

        $errorCode = 'NO_CHANGES_APPLIED';
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
