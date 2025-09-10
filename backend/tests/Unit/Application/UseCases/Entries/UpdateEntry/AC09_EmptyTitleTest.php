<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\UpdateEntry;

use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequest;
use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Domain\Services\UuidGenerator;

/**
 * UC-5 / AC-09 — Empty title.
 *
 * Purpose:
 * Given a title that is empty after trimming, validation must fail with
 * TITLE_REQUIRED and repository must not be touched.
 *
 * Mechanics:
 * - Build UpdateEntryRequest with valid UUID and title = ''.
 * - Domain validator throws DomainValidationException('TITLE_REQUIRED').
 * - Assert that Fake repository's saveCalls() remains 0.
 *
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry::execute
 * @group UC-UpdateEntry
 */
final class AC09_EmptyTitleTest extends BaseUpdateEntryUnitTest
{
    /**
     * Validate that empty title fails with TITLE_REQUIRED and repo remains untouched.
     *
     * @return void
     */
    public function testEmptyTitleFailsValidationAndRepoUntouched(): void
    {
        // Arrange
        $id = UuidGenerator::generate();

        $payload = [
            'id'    => $id,
            'title' => '   ', // becomes empty after trimming
        ];

        /** @var UpdateEntryRequestInterface $request */
        $request = UpdateEntryRequest::fromArray($payload);

        $repo      = $this->makeRepo();
        $errorCode = 'TITLE_REQUIRED';
        $validator = $this->makeValidatorThrows($errorCode);

        $this->expectException(DomainValidationException::class);

        // Act
        $useCase = $this->makeUseCase($repo, $validator);
        $useCase->execute($request);

        // Assert
        $saveCalls = $repo->getSaveCalls();
        $this->assertSame(0, $saveCalls);
    }
}
