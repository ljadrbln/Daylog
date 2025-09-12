<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\UpdateEntry;

use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Domain\Services\UuidGenerator;
use Daylog\Tests\Support\Factory\UpdateEntryTestRequestFactory;

/**
 * UC-5 / AC-10 — Title too long.
 *
 * Purpose:
 *   Given a title longer than 200 characters, validation must fail with
 *   TITLE_TOO_LONG and repository must not be touched.
 *
 * Mechanics:
 *   - Generate a valid UUID and build a request with overly long title (201 chars).
 *   - Configure validator mock to throw DomainValidationException('TITLE_TOO_LONG').
 *   - Execute the use case and assert that repository save was not called.
 *
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry::execute
 * @group UC-UpdateEntry
 */
final class AC10_TitleTooLongTest extends BaseUpdateEntryUnitTest
{
    /**
     * Validate that too long title triggers TITLE_TOO_LONG and repo remains untouched.
     *
     * @return void
     */
    public function testTooLongTitleFailsValidationAndRepoUntouched(): void
    {
        // Arrange
        $id = UuidGenerator::generate();

        /** @var UpdateEntryRequestInterface $request */
        $request = UpdateEntryTestRequestFactory::tooLongTitle($id);

        $repo = $this->makeRepo();

        $errorCode = 'TITLE_TOO_LONG';
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
