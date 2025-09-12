<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\UpdateEntry;

use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Domain\Services\UuidGenerator;
use Daylog\Tests\Support\Factory\UpdateEntryTestRequestFactory;

/**
 * UC-5 / AC-09 — Empty title.
 *
 * Purpose:
 *   Given a title that is empty after trimming, validation must fail with
 *   TITLE_REQUIRED and the repository must not be touched.
 *
 * Mechanics:
 *   - Build UpdateEntryRequest with a valid UUID and a whitespace-only title ('   ').
 *   - Configure validator mock to throw DomainValidationException('TITLE_REQUIRED').
 *   - Execute the use case and assert that repository save was not called.
 *
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry::execute
 * @group UC-UpdateEntry
 */
final class AC09_EmptyTitleTest extends BaseUpdateEntryUnitTest
{
    /**
     * Validate that empty (after trimming) title triggers TITLE_REQUIRED and repo remains untouched.
     *
     * @return void
     */
    public function testEmptyTitleFailsValidationAndRepoUntouched(): void
    {
        // Arrange
        $id         = UuidGenerator::generate();
        $emptyTitle = '   '; // becomes empty after trimming

        /** @var UpdateEntryRequestInterface $request */
        $request = UpdateEntryTestRequestFactory::titleOnly($id, $emptyTitle);

        $repo = $this->makeRepo();

        $errorCode = 'TITLE_REQUIRED';
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
