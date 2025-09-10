<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\UpdateEntry;

use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequest;
use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Domain\Services\UuidGenerator;
use Daylog\Domain\Models\Entries\EntryConstraints;

/**
 * UC-5 / AC-10 — Title too long.
 *
 * Purpose:
 * Given a title longer than 200 characters, validation must fail with
 * TITLE_TOO_LONG and repository must not be touched.
 *
 * Mechanics:
 * - Build UpdateEntryRequest with valid UUID and overly long title (201 chars).
 * - Domain validator throws DomainValidationException('TITLE_TOO_LONG').
 * - Assert that Fake repository's saveCalls() remains 0.
 *
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry::execute
 * @group UC-UpdateEntry
 */
final class AC10_TitleTooLongTest extends BaseUpdateEntryUnitTest
{
    /**
     * Validate that too long title fails with TITLE_TOO_LONG and repo remains untouched.
     *
     * @return void
     */
    public function testTooLongTitleFailsValidationAndRepoUntouched(): void
    {
        // Arrange
        $id = UuidGenerator::generate();

        $longLength = EntryConstraints::TITLE_MAX + 1;
        $longTitle  = str_repeat('a', $longLength);
        $payload   = [
            'id'    => $id,
            'title' => $longTitle,
        ];

        /** @var UpdateEntryRequestInterface $request */
        $request = UpdateEntryRequest::fromArray($payload);

        $repo      = $this->makeRepo();
        $errorCode = 'TITLE_TOO_LONG';
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
